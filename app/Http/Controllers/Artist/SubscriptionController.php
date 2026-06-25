<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Traits\StripeTrait;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    use StripeTrait;

    public function index()
    {
        $subscriptions = UserSubscription::with('subscription')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('artist.subscription.index', compact('subscriptions'));
    }

    public function cancel(Request $request, $id)
    {
        $user = auth()->user();
        $userSubscription = UserSubscription::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($userSubscription->stripe_id && $userSubscription->stripe_status !== 'canceled') {
            try {
                $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                // Cancel at period end
                $stripe->subscriptions->update($userSubscription->stripe_id, [
                    'cancel_at_period_end' => true,
                ]);

                // Update local DB to reflect pending cancellation
                $userSubscription->update([
                    'stripe_status' => 'pending_cancel',
                ]);
                
                return redirect()->back()->with('success', 'Your subscription will be cancelled at the end of the current billing period.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
            }
        } else {
            // For free or non-stripe subscriptions
            $userSubscription->update([
                'status' => 0,
                'stripe_status' => 'canceled',
                'ended_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Your subscription has been cancelled.');
        }
    }

    public function plans()
    {
        $settings = \App\Models\Setting::find(1);
        $artistSubscriptionEnabled = $settings ? $settings->artist_subscription : 0;
        
        if (!$artistSubscriptionEnabled) {
            return redirect()->route('artist.subscription.index')->with('error', 'Artist subscriptions are currently disabled.');
        }

        $hasActiveSubscription = UserSubscription::where('user_id', auth()->id())
            ->where('status', 1)
            ->exists();

        $query = Subscription::where('status', 1)
            ->where(function ($q) {
                $q->where('available_for', 2)->orWhere('is_default', 1);
            });

        if ($hasActiveSubscription) {
            $query->where(function($q) {
                $q->whereNotNull('price')->where('price', '>', 0);
            });
        }

        $subscriptions = $query->orderBy('sort_order', 'asc')->get();

        return view('artist.subscription.plans', compact('subscriptions'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscriptions,id',
        ]);

        $user = auth()->user();
        $subscription = Subscription::find($request->plan_id);

        if ($subscription->price > 0 && $subscription->stripe_price_id) {
            if (!$user->stripe_id) {
                $customer = $this->createCustomer($user->email, $user->name);
                $user->update(['stripe_id' => $customer->id]);
            }

            // Check if user has an active subscription that ends in the future
            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 1)
                ->where('ends_at', '>', now())
                ->latest()
                ->first();

            $subscriptionData = [];
            if ($activeSubscription && $activeSubscription->ends_at) {
                $trialEnd = \Carbon\Carbon::parse($activeSubscription->ends_at)->timestamp;
                // Stripe requires trial_end to be at least 48 hours in the future
                if ($trialEnd > now()->addHours(48)->timestamp) {
                    $subscriptionData['trial_end'] = $trialEnd;
                }
            }

            // Save the intention to switch plan
            session(['pending_subscription_plan_id' => $subscription->id]);

            $sessionUrl = $this->createCheckoutSession(
                $user->stripe_id,
                $subscription->stripe_price_id,
                route('artist.subscription.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                route('artist.subscription.plans'),
                [],
                $subscriptionData
            );

            return redirect($sessionUrl->url);
        } else {
            // Free plan
            $this->applyFreeSubscription($user, $subscription);
            return redirect()->route('artist.subscription.index')->with('success', 'You have successfully switched to the ' . $subscription->name . ' plan.');
        }
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('artist.subscription.plans');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);
            $user = auth()->user();
            
            if ($session->payment_status === 'paid' || $session->status === 'complete') {
                $subscriptionId = $session->subscription;
                $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);
                
                $planId = session('pending_subscription_plan_id');
                $localSubscription = Subscription::find($planId);
                
                if ($localSubscription) {
                    $existing = UserSubscription::where('stripe_id', $subscriptionId)->first();
                    
                    if (!$existing) {
                        // Cancel old active subscriptions at their period end
                        $activeSubs = UserSubscription::where('user_id', $user->id)
                            ->where('status', 1)
                            ->get();
                            
                        foreach($activeSubs as $sub) {
                            if ($sub->stripe_id) {
                                try {
                                    $stripe->subscriptions->update($sub->stripe_id, [
                                        'cancel_at_period_end' => true,
                                    ]);
                                } catch (\Exception $e) {
                                    // ignore
                                }
                            }
                            // Only update stripe_status to pending_cancel so user keeps access until ends_at
                            $sub->update(['stripe_status' => 'pending_cancel']);
                        }

                        $endDate = null;
                        if ($localSubscription->interval) {
                            $intervalCount = $localSubscription->interval_count > 0 ? $localSubscription->interval_count : 1;
                            if ($localSubscription->interval === 'month') {
                                $endDate = now()->addMonths($intervalCount);
                            } elseif ($localSubscription->interval === 'year') {
                                $endDate = now()->addYears($intervalCount);
                            } elseif ($localSubscription->interval === 'week') {
                                $endDate = now()->addWeeks($intervalCount);
                            } elseif ($localSubscription->interval === 'day') {
                                $endDate = now()->addDays($intervalCount);
                            }
                        } else {
                            $endDate = $stripeSubscription->cancel_at ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null;
                        }

                        UserSubscription::create([
                            'uuid' => \Webpatser\Uuid\Uuid::generate(4)->string,
                            'user_id' => $user->id,
                            'subscription_id' => $localSubscription->id,
                            'stripe_id' => $subscriptionId,
                            'stripe_status' => $stripeSubscription->status,
                            'trial_ends_at' => $stripeSubscription->trial_end ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
                            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                            'ends_at' => $endDate,
                            'status' => in_array($stripeSubscription->status, ['active', 'trialing']) ? 1 : 0,
                            'transaction_id' => $session->payment_intent ?? $session->invoice ?? $sessionId,
                            'started_on' => now(),
                        ]);

                        $transactionId = $session->payment_intent ?? $session->invoice ?? $sessionId;

                        DB::table('transactions')->insert([
                            'user_id' => $user->id,
                            'transaction_id' => $transactionId,
                            'payment_type' => 'stripe',
                            'amount' => $session->amount_total / 100,
                            'currency' => strtoupper($session->currency),
                            'payment_status' => 'completed',
                            'payment_details' => json_encode($session->toArray()),
                            'description' => 'Subscription payment for ' . $localSubscription->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        
                        $user->update(['subscription_type' => $localSubscription->id]);
                        session()->forget('pending_subscription_plan_id');
                    }
                }
                
                return redirect()->route('artist.subscription.index')->with('success', 'Subscription updated successfully!');
            }
        } catch (\Exception $e) {
            return redirect()->route('artist.subscription.index')->with('error', 'Error processing subscription: ' . $e->getMessage());
        }

        return redirect()->route('artist.subscription.index')->with('error', 'Payment was not successful.');
    }
    
    private function applyFreeSubscription($user, $subscription)
    {
        // Cancel old active subscriptions
        $activeSubs = UserSubscription::where('user_id', $user->id)
            ->where('status', 1)
            ->get();
            
        foreach($activeSubs as $sub) {
            if ($sub->stripe_id) {
                try {
                    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                    $stripe->subscriptions->cancel($sub->stripe_id);
                } catch (\Exception $e) {
                    // ignore
                }
            }
            $sub->update(['status' => 0, 'ended_at' => now()]);
        }
        
        $endDate = null;
        if ($subscription->interval) {
            $intervalCount = $subscription->interval_count > 0 ? $subscription->interval_count : 1;
            if ($subscription->interval === 'month') {
                $endDate = now()->addMonths($intervalCount);
            } elseif ($subscription->interval === 'year') {
                $endDate = now()->addYears($intervalCount);
            } elseif ($subscription->interval === 'week') {
                $endDate = now()->addWeeks($intervalCount);
            } elseif ($subscription->interval === 'day') {
                $endDate = now()->addDays($intervalCount);
            }
        }

        UserSubscription::create([
            'uuid' => \Webpatser\Uuid\Uuid::generate(4)->string,
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'status' => 1,
            'started_on' => now(),
            'ends_at' => $endDate,
        ]);
        
        $user->update(['subscription_type' => $subscription->id]);
    }
}
