<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Webhook;
use App\Models\UserSubscription;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        $event = null;

        try {
            if ($endpoint_secret) {
                $event = Webhook::constructEvent(
                    $payload, $sig_header, $endpoint_secret
                );
            } else {
                // For local testing without secret, parse directly (Not recommended for prod)
                $event = json_decode($payload);
            }
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if (!isset($event->type)) {
             return response()->json(['success' => true]);
        }

        // Handle the event
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            default:
                Log::info('Received unhandled Stripe event type ' . $event->type);
        }

        return response()->json(['success' => true]);
    }

    protected function handlePaymentSucceeded($invoice)
    {
        $subscriptionId = $invoice->subscription;
        $customerId = $invoice->customer;
        
        if (!$subscriptionId) {
            return;
        }

        $userSubscription = UserSubscription::where('stripe_id', $subscriptionId)->first();
        if (!$userSubscription) {
            return; // Subscription not found locally
        }

        // Fetch the Stripe Subscription to get the updated period
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET_KEY'));
            $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);
            
            $userSubscription->update([
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                'status' => 1,
                'stripe_status' => $stripeSubscription->status
            ]);

            // Create Transaction record
            Transaction::create([
                'user_id' => $userSubscription->user_id,
                'payment_type' => 'stripe',
                'payment_method_id' => $invoice->payment_intent ?? null,
                'amount' => $invoice->amount_paid / 100,
                'currency' => strtoupper($invoice->currency),
                'payment_status' => 'completed',
                'idempotency_key' => $invoice->id,
                'payment_details' => json_encode([
                    'invoice_id' => $invoice->id,
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                ]),
                'description' => 'Stripe recurring payment for subscription ' . $userSubscription->subscription_id
            ]);
            
        } catch (\Exception $e) {
            Log::error("Stripe webhook payment succeeded processing error: " . $e->getMessage());
        }
    }

    protected function handlePaymentFailed($invoice)
    {
        $subscriptionId = $invoice->subscription;
        if (!$subscriptionId) {
            return;
        }

        $userSubscription = UserSubscription::where('stripe_id', $subscriptionId)->first();
        if ($userSubscription) {
            $userSubscription->update([
                'status' => 0,
                'stripe_status' => 'past_due' // Or get the actual status from Stripe
            ]);
            
             Transaction::create([
                'user_id' => $userSubscription->user_id,
                'payment_type' => 'stripe',
                'payment_method_id' => $invoice->payment_intent ?? null,
                'amount' => $invoice->amount_due / 100,
                'currency' => strtoupper($invoice->currency),
                'payment_status' => 'failed',
                'idempotency_key' => $invoice->id . '_fail',
                'description' => 'Stripe payment failed for subscription ' . $userSubscription->subscription_id
            ]);
        }
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $userSubscription = UserSubscription::where('stripe_id', $subscription->id)->first();
        if ($userSubscription) {
            $userSubscription->update([
                'status' => 3, // Cancelled
                'stripe_status' => 'canceled',
                'ended_at' => now()
            ]);
        }
    }
}
