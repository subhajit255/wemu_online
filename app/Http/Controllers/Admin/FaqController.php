<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class FaqController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Faq::latest()->get();
        return view('admin.faq.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "Faq Updated Successfully";
                $request->validate([
                    'question' => 'required|string|unique:faqs,question,' . $id,
                    'answer' => 'required|string'
                ]);
            } else {
                $message = "Faq Created Successfully";
                $request->validate([
                    'question' => 'required|string|unique:faqs,question',
                    'answer' => 'required|string'
                ]);
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "question" => $request->question,
                    "answer" => $request->answer,
                ];
                $details = Faq::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = array('Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine());
            $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.faq.list')];
            return response($data);
        }
        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'faqs');
            $details = Faq::find($uuid);
        }
        return view('admin.faq.add', compact('details'));
    }
}
