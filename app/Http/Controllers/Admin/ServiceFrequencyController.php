<?php

namespace App\Http\Controllers\Admin;

use App\Models\ServiceFrequency;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ServiceFrequencyController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = ServiceFrequency::latest()->get();
        return view('admin.frequency.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "Service Frequency Updated Successfully";
            } else {
                $message = "Service Frequency Created Successfully";
            }

            $request->validate([
                'name' => 'required|string',
                'day_count' => 'required|numeric',
            ]);

            DB::beginTransaction();
            try {
                $postData = [
                    "name" => $request->name,
                    "day_count" => $request->day_count,
                ];
                $details = ServiceFrequency::updateOrCreate(['id' => $id], $postData);
                DB::Commit();

            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.frequency.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'service_frequencies');
            $details = ServiceFrequency::find($uuid);
        }
        return view('admin.frequency.add', compact('details'));
    }
}
