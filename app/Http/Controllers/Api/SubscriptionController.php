<?php

namespace App\Http\Controllers\Api;

use \App\Mail\SubscriptionPurchasedMail;
use \Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\SubscriptionCollection;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\UserSubscription;
use App\Traits\StripeTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends BaseController
{
    use StripeTrait;

    /**
     * @OA\Get(
     *     path="/api/subscriptions",
     *     summary="Get available subscriptions",
     *     tags={"Subscription"},
     *     @OA\Response(response=200, description="Subscriptions fetched successfully")
     * )
     */
    public function subscriptions(Request $request)
    {
        try {
            $subscription = Subscription::where(['available_for' => 1, 'status' => 1])->get();
            return $this->responseJson(true, 200, 'Subscription fetched successfully', $subscription);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/my-current-subscription",
     *     summary="Get current user subscription",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Subscription fetched successfully")
     * )
     */
    public function myCurrentSubscription(Request $request)
    {
        try {
            $subscription = UserSubscription::where(['user_id' => auth()->user()->id, 'status' => 1])->latest()->first();
            return $this->responseJson(true, 200, 'Subscription fetched successfully', $subscription ? new SubscriptionCollection($subscription) : null);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/subscription/purchase",
     *     summary="Purchase a subscription",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="subscription_id", type="integer"),
     *                 @OA\Property(property="payment_method_id", type="string"),
     *                 required={"subscription_id", "payment_method_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Subscription purchased successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subscription_id' => 'required|exists:subscriptions,id',
            'payment_method_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $subscription = Subscription::find($request->subscription_id);

            if (!$subscription->stripe_price_id) {
                return $this->responseJson(false, 400, 'This plan is not configured for Stripe billing yet.', []);
            }

            // 1. Create or get Stripe Customer
            if (!$user->stripe_id) {
                $customer = $this->createCustomer($user->email, $user->name, [
                    'metadata' => ['user_id' => (string) $user->id]
                ]);
                $user->update(['stripe_id' => $customer->id]);
            }

            // 2. Attach Payment Method and Set Default
            $this->attachPaymentMethodToCustomer($user->stripe_id, $request->payment_method_id);

            // 3. Create Stripe Subscription (starts immediately from purchase time)
            $stripeSubscription = $this->createSubscription($user->stripe_id, $subscription->stripe_price_id, [
                'metadata' => [
                    'user_id' => (string) $user->id,
                    'subscription_id' => (string) $subscription->id,
                ],
            ]);

            // 5. Create local UserSubscription (Pending or Active)
            $isActive = in_array($stripeSubscription->status, ['active', 'trialing']);
            $currentPeriodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);
            $currentPeriodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);

            if ($isActive) {
                // Cancel old active subscriptions
                UserSubscription::where('user_id', $user->id)->update(['status' => 0]);
            }

            $userSubscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'stripe_id' => $stripeSubscription->id,
                'stripe_status' => $stripeSubscription->status,
                'current_period_start' => $currentPeriodStart,
                'current_period_end' => $currentPeriodEnd,
                'started_on' => $currentPeriodStart->toDateString(),
                'ended_at' => $currentPeriodEnd->toDateString(),
                'ends_at' => $currentPeriodEnd,
                'status' => $isActive ? 1 : 0,
            ]);

            // Update user subscription_type based on plan max_users
            $subType = 'individual';
            if ($subscription->max_users == 2) {
                $subType = 'duo';
            } elseif ($subscription->max_users > 2) {
                $subType = 'family';
            }
            $user->update(['subscription_type' => $subType]);

            // 6. Log Transaction
            $paymentIntent = null;
            $transactionId = null;
            if (isset($stripeSubscription->latest_invoice->payment_intent)) {
                $paymentIntent = $stripeSubscription->latest_invoice->payment_intent;
                $transactionId = $paymentIntent->id;
            }

            $txIdentifier = $transactionId ?? $stripeSubscription->id;
            Transaction::updateOrInsert(
                ['transaction_id' => $txIdentifier],
                [
                    'user_id' => $user->id,
                    'payment_type' => 'subscription',
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => $subscription->price, // Storing amount from local subscription plan
                    'currency' => strtoupper($subscription->currency ?? config('services.stripe.currency', 'usd')),
                    'payment_status' => $isActive ? 'completed' : 'pending',
                    'idempotency_key' => $txIdentifier,
                    'description' => 'Subscription purchase: ' . ($subscription->name ?? $subscription->title ?? 'WEMU Premium'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::commit();

            if ($isActive) {
                try {
                    $intervalText = '/mo';
                    if ($subscription->interval) {
                        $intervalText = '/' . ($subscription->interval_count > 1 ? $subscription->interval_count . ' ' : '') . $subscription->interval;
                    }
                    $currencySymbol = '$';
                    if ($subscription->currency === 'EUR') {
                        $currencySymbol = '€';
                    } elseif ($subscription->currency === 'GBP') {
                        $currencySymbol = '£';
                    } elseif ($subscription->currency === 'INR') {
                        $currencySymbol = '₹';
                    }
                    $formattedPrice = $currencySymbol . number_format($subscription->price, 2);

                    $subscriptionDetails = [
                        'plan_name' => $subscription->name ?? $subscription->title ?? 'WEMU Premium',
                        'start_date' => $currentPeriodStart->format('F d, Y'),
                        'end_date' => $currentPeriodEnd->format('F d, Y'),
                        'order_ref' => $transactionId ?? $stripeSubscription->id,
                        'order_date' => $currentPeriodStart->format('M d, Y - h:i A'),
                        'plan_price' => $formattedPrice . $intervalText,
                        'total_paid' => $formattedPrice,
                        'plan_description' => $subscription->description ?? 'Unlimited ad-free music, valid for ' . ($subscription->interval_count > 1 ? $subscription->interval_count . ' ' : '') . ($subscription->interval ?? 'month') . '.',
                    ];
                    Mail::to($user->email)->queue(new SubscriptionPurchasedMail($user, $subscriptionDetails));
                } catch (\Exception $e) {
                    logger('Subscription Purchased Mail Error: ' . $e->getMessage());
                }
            }

            // 6. Handle Payment Intent Action Required
            $clientSecret = null;
            if ($stripeSubscription->status === 'incomplete' && $stripeSubscription->latest_invoice->payment_intent) {
                $clientSecret = $stripeSubscription->latest_invoice->payment_intent->client_secret;
            }

            return $this->responseJson(true, 200, 'Subscription process initiated', [
                'subscription_uuid' => $userSubscription->uuid,
                'stripe_status' => $stripeSubscription->status,
                'client_secret' => $clientSecret,
                'is_active' => $isActive
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();

            // Log Failed Transaction
            try {
                Transaction::create([
                    'user_id' => auth()->id(),
                    'payment_type' => 'subscription',
                    'payment_method_id' => $request->payment_method_id,
                    'amount' => Subscription::find($request->subscription_id)?->price ?? 0,
                    'payment_status' => 'failed',
                    'description' => 'Failed subscription purchase',
                    'payment_details' => $e->getMessage()
                ]);
            } catch (\Throwable $err) {
                // Ignore transaction logging failure
            }

            return $this->responseJson(false, 400, $e->getMessage(), []);
        } catch (\Throwable $th) {
            DB::rollBack();
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, config('constants.CATCH_ERROR_MSG', 'Something went wrong'), []);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/user/subscription/cancel",
     *     summary="Cancel current subscription",
     *     tags={"Subscription"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Subscription canceled successfully"),
     *     @OA\Response(response=404, description="No active subscription found")
     * )
     */
    public function cancel(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();

            // Get current active subscription
            $activeSubscription = UserSubscription::where(['user_id' => $user->id, 'status' => 1])->first();

            if (!$activeSubscription) {
                return $this->responseJson(false, 404, 'No active subscription found', []);
            }

            // Cancel in Stripe if Stripe ID exists
            if ($activeSubscription->stripe_id) {
                $this->cancelSubscription($activeSubscription->stripe_id);
            }

            // Update local subscription status
            $activeSubscription->update([
                'status' => 0,
                'stripe_status' => 'canceled'
            ]);

            DB::commit();
            return $this->responseJson(true, 200, 'Subscription canceled successfully. You are now on the Free plan.', []);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            DB::rollBack();
            return $this->responseJson(false, 400, $e->getMessage(), []);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, config('constants.CATCH_ERROR_MSG', 'Something went wrong'), []);
        }
    }
}
