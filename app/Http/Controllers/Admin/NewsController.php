<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class NewsController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = News::latest()->get();
        return view('admin.news.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'title' => 'required|string',
                    'url' => 'required|string',
                    'description' => 'required|string'
                ]);
                $message = "News Updated Successfully";
            } else {
                $request->validate([
                    'title' => 'required|string',
                    'url' => 'required|string',
                    'description' => 'required|string',
                    'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                ]);
                $message = "News Created Successfully";
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "title" => $request->title,
                    "url" => $request->url,
                    "description" => $request->description,
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_NEWS_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['file'] = $fileName;
                    }
                }
                $details = News::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
                
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.news.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'news');
            $details = News::find($uuid);
        }
        return view('admin.news.add', compact('details'));
    }
}
