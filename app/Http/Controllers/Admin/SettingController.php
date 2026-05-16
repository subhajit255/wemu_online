<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class SettingController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        if ($request->post()) {
            $message = "Settings Updated Successfully";

            DB::beginTransaction();
            try {
                $postData = array();

                if (!empty($request->instagram)) {
                    $postData['instagram'] = $request->instagram;
                }
                if (!empty($request->facebook)) {
                    $postData['facebook'] = $request->facebook;
                }
                if (!empty($request->twitter)) {
                    $postData['twitter'] = $request->twitter;
                }
                if (!empty($request->linkedin)) {
                    $postData['linkedin'] = $request->linkedin;
                }
                if (!empty($request->contact_email)) {
                    $postData['contact_email'] = $request->contact_email;
                }
                if (!empty($request->contact_number)) {
                    $postData['contact_number'] = $request->contact_number;
                }
                if (!empty($request->term_and_condition)) {
                    $postData['term_and_condition'] = $request->term_and_condition;
                }
                if (!empty($request->privacy_policy)) {
                    $postData['privacy_policy'] = $request->privacy_policy;
                }
                if (!empty($request->about_us)) {
                    $postData['about_us'] = $request->about_us;
                }
                if (!empty($request->login_welcome_title)) {
                    $postData['login_welcome_title'] = $request->login_welcome_title;
                }
                if (!empty($request->login_welcome_description)) {
                    $postData['login_welcome_description'] = $request->login_welcome_description;
                }
                if (!empty($request->income_note)) {
                    $postData['income_note'] = $request->income_note;
                }
                if (!empty($request->expense_note)) {
                    $postData['expense_note'] = $request->expense_note;
                }
                if (!empty($request->budget_note)) {
                    $postData['budget_note'] = $request->budget_note;
                }
                if (!empty($request->item_note)) {
                    $postData['item_note'] = $request->item_note;
                }
                if (!empty($request->goal_note)) {
                    $postData['goal_note'] = $request->goal_note;
                }
                if (!empty($request->flash_screen_text)) {
                    $postData['flash_screen_text'] = $request->flash_screen_text;
                }
                if (!empty($request->launching_date)) {
                    $postData['launching_date'] = $request->launching_date;
                }

                $postData['show_contact_number'] = $request->show_contact_number ?? 0;
                $postData['show_social_media'] = $request->show_social_media ?? 0;

                if (!empty($request->income_icon)) {
                    $image = $request->income_icon;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ICON_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['income_icon'] = $fileName;
                    }
                }
                if (!empty($request->expense_icon)) {
                    $image = $request->expense_icon;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ICON_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['expense_icon'] = $fileName;
                    }
                }
                if (!empty($request->budget_icon)) {
                    $image = $request->budget_icon;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ICON_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['budget_icon'] = $fileName;
                    }
                }
                if (!empty($request->item_icon)) {
                    $image = $request->item_icon;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ICON_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['item_icon'] = $fileName;
                    }
                }
                if (!empty($request->goal_icon)) {
                    $image = $request->goal_icon;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ICON_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['goal_icon'] = $fileName;
                    }
                }


                $details = Setting::updateOrCreate(['id' => 1], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.setting.update')];
            return response($data);
        }
        $details = Setting::find(1);
        return view('admin.setting.index', compact('details'));
    }
}
