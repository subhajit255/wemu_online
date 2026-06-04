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
        $query = Faq::latest();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $details = $query->get();
        return view('admin.faq.index', compact('details'));
    }
    public function add(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $message = "FAQ Updated Successfully";
                $request->validate([
                    'question' => 'required|string|unique:faqs,question,' . $id,
                    'answer' => 'required|string'
                ]);
            } else {
                $message = "FAQ Created Successfully";
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
                
                // By default, set new FAQs to active
                if (empty($id)) {
                    $postData['is_active'] = 1;
                }
                
                $details = Faq::updateOrCreate(['id' => $id], $postData);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = array('Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine());
                $message = config('constants.CATCH_ERROR_MSG') ?? 'An error occurred';
                return $this->responseJson($status, $code, $message, $response);
            }
            return $this->responseJson(true, 200, $message, $details);
        }
    }
}
