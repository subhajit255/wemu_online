<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Broadcast;
use App\Traits\UploadAble;
use App\Models\BroadCastLog;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Models\PushNotificationQueue;
use App\Http\Controllers\BaseController;

class BroadcastController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Broadcast::latest()->get();
        return view('admin.broadcast.index', compact('details'));
    }
    public function log(Request $request)
    {
        $details = Broadcast::where('uuid', $request->uuid)->first();
        // dd($details->toArray());
        return view('admin.broadcast.log', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'title' => 'required|string',
                    'body' => 'required|string',
                    'file' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:10000',
                ]);
                $message = "Broadcast Updated Successfully";
            } else {
                $request->validate([
                    'title' => 'required|string',
                    'body' => 'required|string',
                    'file' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:10000',
                ]);
                $message = "Broadcast Created Successfully";
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "title" => $request->title,
                    "body" => $request->body,
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_BROADCAST_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['image'] = $fileName;
                    }
                }
                $details = Broadcast::updateOrCreate(['id' => $id], $postData);
                DB::Commit();

                $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.broadcast.list')];
                return response($data);

            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'broadcasts');
            $details = Broadcast::find($uuid);
        }
        return view('admin.broadcast.add', compact('details'));
    }

    public function send(Request $request)
    {
        if ($request->post()) {
            DB::beginTransaction();
            try {
                $users = null;
                $broadCast = Broadcast::find($request->id);
                $users = User::whereNotNull('fcm_token')->where(['user_type' => 3, 'is_active' => 1])->get();

                if (!empty($users)) {
                    $dataToStore = [];
                    foreach ($users as $user) {
                        $dataToStore[] = ['fcm_token' => $user->fcm_token, 'title' => $broadCast->title, 'body' => $broadCast->body, 'type' => $broadCast->type ?? null];
                    }
                    PushNotificationQueue::insert($dataToStore);
                }

                BroadCastLog::create([
                    'broadcast_id' => $broadCast->id,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                ]);

                DB::Commit();

                $data = ['status' => true, 'message' => 'Broadcast Send Successfully', 'data' => null];
                return response($data);

            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
        }
        $details = Broadcast::where('uuid', $request->uuid)->first();
        return view('admin.broadcast.send', compact('details'));
    }
}
