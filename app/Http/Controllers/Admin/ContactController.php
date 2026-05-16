<?php

namespace App\Http\Controllers\Admin;

use App\Models\Contact;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;

class ContactController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $details = Contact::latest()->get();
        return view('admin.contact.index', compact('details'));
    }
    public function contactReply(Request $request)
    {
        $request->validate([
            'reply' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
            $contact = Contact::where('uuid', $request->contact_id)->first();
            $toContactEmail = $contact->email;
            $enquiry = $contact->enquiry;
            $replySend = $contact->update(['reply' => $request->reply, 'is_replied' => 2]);
            if ($replySend) {
                // Mail::send('mail.enquiry-reply', ['enquiry' => $enquiry, 'reply' => $request->reply], function ($message) use ($toContactEmail) {
                //     $message->to($toContactEmail);
                //     $message->subject('Your Enquiry Feedback');
                // });
                DB::commit();
                $message = 'Reply sent successfully';
            } else {
                $message = 'Something went wrong';
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = array('Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine());
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
        $data = ['status' => true, 'message' => $message, 'data' => null, 'url' => route('admin.contact.list')];
        return response($data);
    }
}
