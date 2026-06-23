<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // Add this to .env

        try {
            if ($endpointSecret) {
                $event = Webhook::constructEvent(
                    $payload,
                    $sigHeader,
                    $endpointSecret
                );
            } else {
                // If no secret is configured, just parse the json for local testing (NOT RECOMMENDED for production)
                $event = json_decode($payload);
            }
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type ?? $event->type) {
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;

                // Only process recurring subscription payments (or initial if you want)
                if ($invoice->billing_reason == 'subscription_cycle') {
                    $this->handleSubscriptionCycle($invoice);
                }
                break;

            case 'customer.subscription.updated':
            case 'customer.subscription.deleted':
                $subscription = $event->data->object;
                $this->handleSubscriptionChange($subscription);
                break;

            default:
                // Unexpected event type
                Log::info('Received unknown Stripe event: ' . ($event->type ?? 'unknown'));
        }

        return response('Webhook Handled', 200);
    }

    private function handleSubscriptionCycle($invoice)
    {
        try {
            $subscriptionId = $invoice->subscription;
            $customerId = $invoice->customer;

            // Find local user subscription
            $userSub = \App\Models\UserSubscription::where('stripe_id', $subscriptionId)->first();

            if ($userSub) {
                // Update dates
                $periodStart = \Carbon\Carbon::createFromTimestamp($invoice->lines->data[0]->period->start);
                $periodEnd = \Carbon\Carbon::createFromTimestamp($invoice->lines->data[0]->period->end);

                $userSub->update([
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                    'status' => 1,
                ]);

                // Record transaction
                \App\Models\Transaction::create([
                    'user_id' => $userSub->user_id,
                    'transaction_id' => $invoice->payment_intent ?? $invoice->id,
                    'payment_type' => 'stripe_recurring',
                    'amount' => $invoice->amount_paid / 100,
                    'currency' => strtoupper($invoice->currency),
                    'payment_status' => 'completed',
                    'payment_details' => json_encode($invoice),
                    'description' => 'Recurring subscription payment',
                ]);

                Log::info("Successfully processed recurring payment for subscription {$subscriptionId}");
            } else {
                Log::warning("Received payment_succeeded for unknown subscription {$subscriptionId}");
            }
        } catch (\Exception $e) {
            Log::error('Error processing subscription cycle: ' . $e->getMessage());
        }
    }

    private function handleSubscriptionChange($stripeSubscription)
    {
        try {
            $subscriptionId = $stripeSubscription->id;

            $status = in_array($stripeSubscription->status, ['active', 'trialing']) ? 1 : 0;
            $cancelAt = $stripeSubscription->cancel_at ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null;
            $periodStart = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start);
            $periodEnd = \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end);

            \App\Models\UserSubscription::where('stripe_id', $subscriptionId)
                ->update([
                    'stripe_status' => $stripeSubscription->status,
                    'status' => $status,
                    'ends_at' => $cancelAt,
                    'current_period_start' => $periodStart,
                    'current_period_end' => $periodEnd,
                ]);

            Log::info("Successfully updated subscription state for {$subscriptionId} to {$stripeSubscription->status}");
        } catch (\Exception $e) {
            Log::error('Error processing subscription change: ' . $e->getMessage());
        }
    }
}
