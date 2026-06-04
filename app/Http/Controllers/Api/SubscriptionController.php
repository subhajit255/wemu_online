<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Traits\StripeTrait;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Api\SubscriptionCollection;
use App\Models\Transaction;

class SubscriptionController extends BaseController
{
    use StripeTrait;

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
                $customer = $this->createCustomer($user->email, $user->name);
                $user->update(['stripe_id' => $customer->id]);
            }

            // 2. Attach Payment Method and Set Default
            $this->attachPaymentMethodToCustomer($user->stripe_id, $request->payment_method_id);

            // 3. Create Stripe Subscription
            $stripeParams = [];
            if ($subscription->trial_days && $subscription->trial_days > 0) {
                $stripeParams['trial_end'] = now()->addDays($subscription->trial_days)->timestamp;
            }

            $stripeSubscription = $this->createSubscription($user->stripe_id, $subscription->stripe_price_id, $stripeParams);

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
                'status' => $isActive ? 1 : 0,
            ]);

            // 6. Log Transaction
            $paymentIntent = null;
            $transactionId = null;
            if (isset($stripeSubscription->latest_invoice->payment_intent)) {
                $paymentIntent = $stripeSubscription->latest_invoice->payment_intent;
                $transactionId = $paymentIntent->id;
            }

            Transaction::create([
                'user_id' => $user->id,
                'transaction_id' => $transactionId ?? $stripeSubscription->id,
                'payment_type' => 'subscription',
                'payment_method_id' => $request->payment_method_id,
                'amount' => $subscription->price, // Storing amount from local subscription plan
                'currency' => config('services.stripe.currency', 'usd'),
                'payment_status' => $isActive ? 'success' : 'pending',
                'description' => 'Subscription purchase: ' . $subscription->title,
            ]);

            DB::commit();

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
}
