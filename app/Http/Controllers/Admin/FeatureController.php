<?php

namespace App\Http\Controllers\Admin;

use App\Models\Feature;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class FeatureController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Feature::latest()->get();
        return view('admin.feature.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "Feature Updated Successfully";
                $request->validate([
                    'title' => 'required|string|unique:features,title,' . $id,
                    'icon' => 'required|string',
                    'description' => 'required|string'
                ]);
            } else {
                $message = "Feature Created Successfully";
                $request->validate([
                    'title' => 'required|string|unique:features,title',
                    'icon' => 'required|string',
                    'description' => 'required|string'
                ]);
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "title" => $request->title,
                    "icon" => $request->icon,
                    "description" => $request->description,
                ];
                $details = Feature::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
                
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.feature.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'features');
            $details = Feature::find($uuid);
        }
        return view('admin.feature.add', compact('details'));
    }
}
