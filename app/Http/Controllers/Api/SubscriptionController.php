<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\SubscriptionCollection;

class SubscriptionController extends BaseController
{
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
}
