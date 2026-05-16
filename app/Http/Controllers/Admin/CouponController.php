<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class CouponController extends BaseController
{
    public function index(Request $request)
    {
        $details = Coupon::latest()->get();
        return view('admin.coupon.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            // dd($request->all());
            $id = $request->id ?? NULL;
            $request->validate([
                'name' => "required|string|unique:coupons,name,$id",
                'description' => 'required|string',
                'code' => "required|string|unique:coupons,code,$id",
                'type' => 'required|in:1,2',
                'coupon_discount' => 'required|numeric',
                'for_all_user' => 'required|in:1,2',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date',
                'users' => 'required_if:for_all_user,2|array',
                'users.*' => 'required_if:for_all_user,2|exists:users,id',
            ]);

            $message = (!empty($id)) ? "Coupon Updated Successfully" : "Coupon Created Successfully";

            DB::beginTransaction();
            try {
                $postData = [
                    "name" => $request->name,
                    "description" => $request->description,
                    "code" => $request->code,
                    "type" => $request->type,
                    "coupon_discount" => $request->coupon_discount,
                    "for_all_user" => $request->for_all_user,
                    "start_date" => $request->start_date,
                    "end_date" => $request->end_date,
                ];

                $details = Coupon::updateOrCreate(['id' => $id], $postData);


                if ($request->for_all_user == 2) {
                    $user_ids = $request->users;
                    if (!empty($user_ids)) {
                        $details->users()->sync($user_ids);
                    }
                } else {
                    $details->users()->sync([]);
                }

                DB::Commit();
                // dd($details);
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.coupon.list')];
            return response($data);
        }

        $details = $couponUsers = [];
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'coupons');
            $details = Coupon::find($uuid);
            $couponUsers = DB::table('user_coupons')->where('coupon_id', $uuid)->pluck('user_id')->toArray();
        }
        $users = User::where(['is_active' => 1, 'user_type' => 3])->get();
        // dd($user);
        return view('admin.coupon.add', compact('details', 'users', 'couponUsers'));
    }
}
