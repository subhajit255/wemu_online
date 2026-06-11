<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class SettingController extends BaseController
{
    public function index(Request $request)
    {
        if ($request->post()) {
            $message = "Settings Updated Successfully";

            DB::beginTransaction();
            try {
                $postData = [];
                $postData['artist_subscription'] = $request->has('artist_subscription') ? 1 : 0;

                $details = Setting::updateOrCreate(['id' => 1], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG') ?? "An error occurred";
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.setting.update')];
            return response($data);
        }
        $details = Setting::find(1) ?? new Setting();
        return view('admin.setting.index', compact('details'));
    }
}
