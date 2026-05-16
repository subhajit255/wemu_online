<?php

namespace App\Http\Controllers\Admin;

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
                    'title' => 'required|string|unique:subscriptions,title,' . $id,
                    'type' => 'required|string|digits_between:1,4',
                    'activity_count' => 'required|numeric',
                    'mrp' => 'required|numeric'
                ]);
            } else {
                $message = "Subscription Created Successfully";
                $request->validate([
                    'title' => 'required|string|unique:subscriptions,title',
                    'type' => 'required|string|digits_between:1,4',
                    'activity_count' => 'required|numeric',
                    'mrp' => 'required|numeric'
                ]);
            }

            DB::beginTransaction();
            try {
                $price = $request->mrp;
                if (!empty($request->discount)) {
                    $price = (float)$request->mrp - ((float)$request->mrp * (int) $request->discount / 100);
                }
                $postData = [
                    "title" => $request->title,
                    "type" => $request->type,
                    "activity_count" => $request->activity_count,
                    "mrp" => $request->mrp,
                    "discount" => $request->discount,
                    "price" => $price ?? $request->mrp,
                    "description" => $request->description,
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
