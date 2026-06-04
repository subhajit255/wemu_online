<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cms;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class CmsController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Cms::latest()->get();
        return view('admin.cms.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'title' => 'required|string',
                ]);
                $message = "Cms Updated Successfully";
            } else {
                $request->validate([
                    'title' => 'required|string',
                ]);
                $message = "Cms Created Successfully";
            }

            DB::beginTransaction();
            try {
                $descriptionContent = '';
                if ($request->has('blocks') && is_array($request->blocks)) {
                    $descriptionContent = json_encode(array_values($request->blocks));
                } else {
                    $descriptionContent = $request->description ?? '';
                }

                $postData = [
                    "title" => $request->title,
                    "alias" => str_replace(' ', '_', strtolower($request->title)),
                    "description" => $descriptionContent,
                    "for_home" => $request->for_home ?? 0,
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_CMS_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['file'] = $fileName;
                    }
                }
                $details = Cms::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = $th->getMessage();
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.cms.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'cms');
            $details = Cms::find($uuid);
        }
        return view('admin.cms.add', compact('details'));
    }

    public function helpSupport(Request $request)
    {
        $query = \App\Models\UserQuery::with('user')->latest();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('query', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('type') && $request->type != 'all') {
            $query->where('subject', 'like', '%' . $request->type . '%');
        }

        $queries = $query->get();
        return view('admin.helpsupport.index', compact('queries'));
    }

    public function replyQuery(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:user_queries,id',
            'reply' => 'required|string'
        ]);

        try {
            $userQuery = \App\Models\UserQuery::with('user')->findOrFail($request->id);
            $userQuery->update([
                'reply' => $request->reply,
                'reply_by' => auth()->id(),
                'status' => 1 // Replied
            ]);

            if ($userQuery->user && $userQuery->user->email) {
                \Illuminate\Support\Facades\Mail::to($userQuery->user->email)
                    ->send(new \App\Mail\EnquiryReplyMail($userQuery->query, $request->reply));
            }

            return response()->json([
                'status' => true,
                'message' => 'Reply sent successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $th->getMessage()
            ], 500);
        }
    }
}
