<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\Subscription;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Traits\StripeTrait;

class SubscriptionController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    use StripeTrait;
    public function index(Request $request)
    {
        $query = Subscription::latest();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $details = $query->get();
        return view('admin.subscription.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "Subscription Updated Successfully";
                $request->validate([
                    'name' => [
                        'required',
                        'string',
                        Rule::unique('subscriptions', 'name')->ignore($id)->whereNull('deleted_at')
                    ],
                    'available_for' => 'required|numeric|in:1,2',
                    'interval' => 'required|string|in:month,year,week',
                    'interval_count' => 'required|numeric|min:1',
                    'price' => 'required|numeric',
                    'currency' => 'required|string',
                    'max_users' => 'required|numeric|min:1',
                    'trial_days' => 'nullable|numeric|min:0',
                ]);
            } else {
                $message = "Subscription Created Successfully";
                $request->validate([
                    'name' => [
                        'required',
                        'string',
                        Rule::unique('subscriptions', 'name')->whereNull('deleted_at')
                    ],
                    'available_for' => 'required|numeric|in:1,2',
                    'interval' => 'required|string|in:month,year,week',
                    'interval_count' => 'required|numeric|min:1',
                    'price' => 'required|numeric',
                    'currency' => 'required|string',
                    'max_users' => 'required|numeric|min:1',
                    'trial_days' => 'nullable|numeric|min:0',
                ]);
            }

            DB::beginTransaction();
            try {
                $stripePriceId = null;
                $stripeProductId = null;
                $pricingChanged = true;

                if (!empty($id)) {
                    $existingSubscription = Subscription::find($id);
                    if ($existingSubscription) {
                        $stripePriceId = $existingSubscription->stripe_price_id;
                        $stripeProductId = $existingSubscription->stripe_product_id;

                        // Check if the actual pricing components changed
                        if (
                            $existingSubscription->price == $request->price &&
                            $existingSubscription->currency == $request->currency &&
                            $existingSubscription->interval == $request->interval &&
                            $existingSubscription->interval_count == ($request->interval_count ?? 1)
                        ) {
                            $pricingChanged = false; // No need to create a new Stripe Price
                        }
                    }
                }

                if ($pricingChanged && $request->price > 0) {
                    $metadata = [
                        'slug' => Str::slug($request->name),
                        'available_for' => $request->available_for == 1 ? 'user' : 'artist',
                        'max_users' => $request->max_users ?? 1,
                        'trial_days' => $request->trial_days ?? 0,
                        'requires_verification' => $request->has('requires_verification') ? 'yes' : 'no',
                    ];

                    $stripePrice = $this->createStripePrice(
                        $request->name,
                        $request->price,
                        $request->currency,
                        $request->interval,
                        $request->interval_count ?? 1,
                        $metadata
                    );
                    $stripePriceId = $stripePrice->id;
                    $stripeProductId = $stripePrice->product;
                }

                $postData = [
                    "name" => $request->name,
                    "slug" => Str::slug($request->name),
                    "tagline" => $request->tagline,
                    "available_for" => $request->available_for,
                    "interval" => $request->interval,
                    "interval_count" => $request->interval_count,
                    "price" => $request->price,
                    "currency" => $request->currency,
                    "description" => $request->description,
                    "features" => $request->features,
                    "stripe_product_id" => $stripeProductId,
                    "stripe_price_id" => $stripePriceId,
                    "max_users" => $request->max_users,
                    "trial_days" => $request->trial_days ?? 0,
                    "requires_verification" => $request->has('requires_verification') ? 1 : 0,
                ];
                $details = Subscription::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.subscription.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'subscriptions');
            $details = Subscription::find($uuid);
        }
        return view('admin.subscription.add', compact('details'));
    }
}
