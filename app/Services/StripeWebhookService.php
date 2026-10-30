<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeWebhookService
{
    /**
     * Process incoming Stripe webhook request.
     */
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature') ?? ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? null);
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            if ($endpointSecret) {
                $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
            } else {
                if (config('app.env') === 'production') {
                    Log::error('Stripe webhook received but STRIPE_WEBHOOK_SECRET is not configured in production.');
                    return response()->json(['error' => 'Webhook secret not configured'], 500);
                }
                Log::warning('Stripe webhook parsed without signature verification (development mode).');
                $event = json_decode($payload);
            }
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 400);
        }

        $eventType = $event->type ?? null;
        if (!$eventType) {
            return response()->json(['status' => true, 'message' => 'No event type']);
        }

        Log::info("Stripe Webhook Received: {$eventType}");

        switch ($eventType) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handleInvoicePaymentFailed($event->data->object);
                break;

            case 'customer.subscription.updated':
                $this->handleCustomerSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleCustomerSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info("Unhandled Stripe event type: {$eventType}");
                break;
        }

        return response()->json(['status' => true, 'message' => 'Webhook Handled']);
    }

    /**
     * Handle checkout.session.completed (Artist Stripe Checkout flow)
     */
    protected function handleCheckoutSessionCompleted($session)
    {
        try {
            if (($session->mode ?? null) !== 'subscription') {
                return;
            }

            $subscriptionId = $session->subscription ?? null;
            $customerId = $session->customer ?? null;

            if (!$subscriptionId) {
                return;
            }

            // Check if user subscription already recorded
            $existing = UserSubscription::where('stripe_id', $subscriptionId)->first();
            if ($existing) {
                if ($existing->status !== 1) {
                    $existing->update(['status' => 1, 'stripe_status' => 'active']);
                }
                return;
            }

            $user = null;
            if (!empty($session->metadata->user_id)) {
                $user = User::find($session->metadata->user_id);
            }
            if (!$user && $customerId) {
                $user = User::where('stripe_id', $customerId)->first();
            }

            if (!$user) {
                Log::warning("Stripe checkout.session.completed: user not found for customer {$customerId}");
                return;
            }

            $stripe = new StripeClient(config('services.stripe.secret'));
            $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);

            // Find local plan
            $localPlan = null;
            if (!empty($session->metadata->subscription_id)) {
                $localPlan = Subscription::find($session->metadata->subscription_id);
            }
            if (!$localPlan && !empty($user->subscription_type) && is_numeric($user->subscription_type)) {
                $localPlan = Subscription::find($user->subscription_type);
            }
            if (!$localPlan && isset($stripeSubscription->items->data[0]->price->id)) {
                $localPlan = Subscription::where('stripe_price_id', $stripeSubscription->items->data[0]->price->id)->first();
            }

            $periodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);
            $periodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

            $endDate = $periodEnd;
            if ($localPlan && $localPlan->interval) {
                $intervalCount = $localPlan->interval_count > 0 ? $localPlan->interval_count : 1;
                $baseDate = $stripeSubscription->trial_end ? Carbon::createFromTimestamp($stripeSubscription->trial_end) : now();
                if ($localPlan->interval === 'month') {
                    $endDate = (clone $baseDate)->addMonths($intervalCount);
                } elseif ($localPlan->interval === 'year') {
                    $endDate = (clone $baseDate)->addYears($intervalCount);
                } elseif ($localPlan->interval === 'week') {
                    $endDate = (clone $baseDate)->addWeeks($intervalCount);
                } elseif ($localPlan->interval === 'day') {
                    $endDate = (clone $baseDate)->addDays($intervalCount);
                }
            }

            DB::beginTransaction();

            // Deactivate previous subscriptions
            UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->update(['status' => 0, 'ended_at' => now()]);

            $userSub = UserSubscription::create([
                'uuid' => (string) \Webpatser\Uuid\Uuid::generate(4),
                'user_id' => $user->id,
                'subscription_id' => $localPlan?->id,
                'stripe_id' => $subscriptionId,
                'stripe_status' => $stripeSubscription->status,
                'trial_ends_at' => $stripeSubscription->trial_end ? Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'ends_at' => $endDate,
                'status' => in_array($stripeSubscription->status, ['active', 'trialing']) ? 1 : 0,
                'transaction_id' => $session->payment_intent ?? $session->invoice ?? $session->id,
                'started_on' => now(),
            ]);

            $txId = $session->payment_intent ?? $session->invoice ?? $session->id;
            Transaction::firstOrCreate(
                ['transaction_id' => $txId],
                [
                    'user_id' => $user->id,
                    'payment_type' => 'stripe',
                    'amount' => ($session->amount_total ?? 0) / 100,
                    'currency' => strtoupper($session->currency ?? 'USD'),
                    'payment_status' => 'completed',
                    'idempotency_key' => $session->id,
                    'payment_details' => json_encode($session),
                    'description' => 'Subscription checkout: ' . ($localPlan?->name ?? 'Premium Plan'),
                ]
            );

            if ($user->user_type == 3) {
                // Artist
                $updateData = ['subscription_type' => $localPlan?->id];
                if ($user->completed_steps < 8) {
                    $updateData['completed_steps'] = 8;
                }
                $user->update($updateData);
            } else {
                // Normal User
                $subType = 'individual';
                if ($localPlan && $localPlan->max_users == 2) {
                    $subType = 'duo';
                } elseif ($localPlan && $localPlan->max_users > 2) {
                    $subType = 'family';
                }
                $user->update(['subscription_type' => $subType]);
            }

            DB::commit();
            Log::info("Stripe checkout.session.completed processed successfully for user {$user->id}, subscription {$subscriptionId}");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe checkout.session.completed processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle invoice.payment_succeeded (both initial & recurring cycle renewals)
     */
    protected function handleInvoicePaymentSucceeded($invoice)
    {
        try {
            $subscriptionId = $invoice->subscription ?? null;
            if (!$subscriptionId) {
                return;
            }

            $userSub = UserSubscription::where('stripe_id', $subscriptionId)->first();

            // Period dates from invoice
            $line = $invoice->lines->data[0] ?? null;
            $periodStart = ($line && isset($line->period->start)) ? Carbon::createFromTimestamp($line->period->start) : now();
            $periodEnd = ($line && isset($line->period->end)) ? Carbon::createFromTimestamp($line->period->end) : now()->addMonth();

            if ($userSub) {
                DB::beginTransaction();

                $userSub->update([
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                    'ends_at' => $periodEnd,
                    'status' => 1,
                    'stripe_status' => 'active',
                ]);

                $txId = $invoice->payment_intent ?? $invoice->id;
                $existingTx = Transaction::where('transaction_id', $txId)
                    ->orWhere('idempotency_key', $invoice->id)
                    ->first();

                if ($existingTx) {
                    if ($existingTx->payment_status !== 'completed') {
                        $existingTx->update([
                            'payment_status' => 'completed',
                            'payment_details' => json_encode($invoice),
                        ]);
                    }
                } else {
                    $isRecurring = ($invoice->billing_reason ?? '') === 'subscription_cycle';
                    Transaction::create([
                        'user_id' => $userSub->user_id,
                        'transaction_id' => $txId,
                        'payment_type' => $isRecurring ? 'stripe_recurring' : 'stripe',
                        'payment_method_id' => $invoice->payment_intent ?? null,
                        'amount' => ($invoice->amount_paid ?? 0) / 100,
                        'currency' => strtoupper($invoice->currency ?? 'USD'),
                        'payment_status' => 'completed',
                        'idempotency_key' => $invoice->id,
                        'payment_details' => json_encode($invoice),
                        'description' => ($isRecurring ? 'Recurring subscription payment for ' : 'Subscription payment for ') . ($userSub->subscription?->name ?? 'Plan'),
                    ]);
                }

                DB::commit();
                Log::info("Stripe invoice.payment_succeeded processed for subscription {$subscriptionId}");
            } else {
                // Subscription not yet in local database (e.g. 3DS completed via mobile app or webhook arrived before redirect)
                $customerId = $invoice->customer ?? null;
                $user = User::where('stripe_id', $customerId)->first();

                if ($user) {
                    $stripe = new StripeClient(config('services.stripe.secret'));
                    $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);
                    $priceId = $stripeSubscription->items->data[0]->price->id ?? null;
                    $localPlan = Subscription::where('stripe_price_id', $priceId)->first();

                    DB::beginTransaction();

                    UserSubscription::where('user_id', $user->id)
                        ->where('status', 1)
                        ->update(['status' => 0, 'ended_at' => now()]);

                    UserSubscription::create([
                        'uuid' => (string) \Webpatser\Uuid\Uuid::generate(4),
                        'user_id' => $user->id,
                        'subscription_id' => $localPlan?->id,
                        'stripe_id' => $subscriptionId,
                        'stripe_status' => $stripeSubscription->status,
                        'current_period_start' => $periodStart,
                        'current_period_end' => $periodEnd,
                        'ends_at' => $periodEnd,
                        'status' => 1,
                        'transaction_id' => $invoice->payment_intent ?? $invoice->id,
                        'started_on' => now(),
                    ]);

                    Transaction::create([
                        'user_id' => $user->id,
                        'transaction_id' => $invoice->payment_intent ?? $invoice->id,
                        'payment_type' => 'stripe',
                        'payment_method_id' => $invoice->payment_intent ?? null,
                        'amount' => ($invoice->amount_paid ?? 0) / 100,
                        'currency' => strtoupper($invoice->currency ?? 'USD'),
                        'payment_status' => 'completed',
                        'idempotency_key' => $invoice->id,
                        'payment_details' => json_encode($invoice),
                        'description' => 'Subscription payment for ' . ($localPlan?->name ?? 'Plan'),
                    ]);

                    DB::commit();
                    Log::info("Provisioned new subscription via invoice.payment_succeeded for user {$user->id}");
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe invoice.payment_succeeded processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle invoice.payment_failed
     */
    protected function handleInvoicePaymentFailed($invoice)
    {
        try {
            $subscriptionId = $invoice->subscription ?? null;
            if (!$subscriptionId) {
                return;
            }

            $userSub = UserSubscription::where('stripe_id', $subscriptionId)->first();
            if ($userSub) {
                $userSub->update([
                    'status' => 0,
                    'stripe_status' => 'past_due',
                ]);

                Transaction::create([
                    'user_id' => $userSub->user_id,
                    'transaction_id' => $invoice->payment_intent ?? $invoice->id,
                    'payment_type' => 'stripe_recurring_failed',
                    'payment_method_id' => $invoice->payment_intent ?? null,
                    'amount' => ($invoice->amount_due ?? 0) / 100,
                    'currency' => strtoupper($invoice->currency ?? 'USD'),
                    'payment_status' => 'failed',
                    'idempotency_key' => $invoice->id . '_failed',
                    'payment_details' => json_encode($invoice),
                    'description' => 'Failed subscription payment for ' . ($userSub->subscription?->name ?? 'Plan'),
                ]);

                Log::warning("Stripe invoice.payment_failed logged for subscription {$subscriptionId}");
            }
        } catch (\Exception $e) {
            Log::error('Stripe invoice.payment_failed processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle customer.subscription.updated
     */
    protected function handleCustomerSubscriptionUpdated($stripeSubscription)
    {
        try {
            $subscriptionId = $stripeSubscription->id ?? null;
            if (!$subscriptionId) {
                return;
            }

            $userSub = UserSubscription::where('stripe_id', $subscriptionId)->first();
            if (!$userSub) {
                return;
            }

            $status = 0;
            switch ($stripeSubscription->status) {
                case 'active':
                case 'trialing':
                    $status = 1;
                    break;
                case 'canceled':
                    $status = 3;
                    break;
                case 'unpaid':
                case 'incomplete_expired':
                    $status = 2;
                    break;
                case 'past_due':
                case 'incomplete':
                default:
                    $status = 0;
                    break;
            }

            $periodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);
            $periodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

            $cancelAt = null;
            if ($stripeSubscription->cancel_at) {
                $cancelAt = Carbon::createFromTimestamp($stripeSubscription->cancel_at);
            } elseif (!empty($stripeSubscription->cancel_at_period_end)) {
                $cancelAt = $periodEnd;
            }

            // If subscription is cancelling at period end, user still has active access until periodEnd
            $isPendingCancel = !empty($stripeSubscription->cancel_at_period_end);
            $finalStatus = $isPendingCancel ? 1 : $status;
            $endsAt = $cancelAt ?? $periodEnd;

            $userSub->update([
                'stripe_status' => $isPendingCancel ? 'pending_cancel' : $stripeSubscription->status,
                'status' => $finalStatus,
                'ends_at' => $endsAt,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
                'trial_ends_at' => $stripeSubscription->trial_end ? Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
            ]);

            Log::info("Updated subscription {$subscriptionId} to status: {$stripeSubscription->status} (isPendingCancel: " . ($isPendingCancel ? 'yes' : 'no') . ")");
        } catch (\Exception $e) {
            Log::error('Stripe customer.subscription.updated processing error: ' . $e->getMessage());
        }
    }

    /**
     * Handle customer.subscription.deleted
     */
    protected function handleCustomerSubscriptionDeleted($stripeSubscription)
    {
        try {
            $subscriptionId = $stripeSubscription->id ?? null;
            if (!$subscriptionId) {
                return;
            }

            $userSub = UserSubscription::where('stripe_id', $subscriptionId)->first();
            if ($userSub) {
                $userSub->update([
                    'status' => 3, // Cancelled
                    'stripe_status' => 'canceled',
                    'ended_at' => now(),
                    'ends_at' => now(),
                ]);

                // If user is normal user, revert subscription_type
                if ($userSub->user && $userSub->user->user_type != 3) {
                    $userSub->user->update(['subscription_type' => 'free']);
                }

                Log::info("Cancelled subscription {$subscriptionId} in local database.");
            }
        } catch (\Exception $e) {
            Log::error('Stripe customer.subscription.deleted processing error: ' . $e->getMessage());
        }
    }
}

