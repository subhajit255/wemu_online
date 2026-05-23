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

class SubscriptionController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Subscription::latest()->get();
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
                    'interval' => 'required|numeric|in:1,2',
                    'price' => 'required|numeric',
                    'currency' => 'required|string',
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
                    'interval' => 'required|numeric|in:1,2',
                    'price' => 'required|numeric',
                    'currency' => 'required|string',
                ]);
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "name" => $request->name,
                    "slug" => Str::slug($request->name),
                    "available_for" => $request->available_for,
                    "interval" => $request->interval,
                    "price" => $request->price,
                    "currency" => $request->currency,
                    "description" => $request->description,
                    "features" => $request->features,
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
