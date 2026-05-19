<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\UserBudget;
use App\Models\UserIncome;
use App\Traits\UploadAble;
use App\Models\UserExpense;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class UserController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        $query = User::where('user_type', 3)->latest();
        if (($request->type == 1 || $request->type == 2) && $request->type != null) {
            $query->where('is_active', $request->type == 2 ? 0 : 1);
        }
        if ($search = $request->search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('mobile_number', $search)
                ->orWhere('email', $search);
        }
        $details = $query->paginate(10);
        return view('admin.user.index', compact('details'));
    }
    public function add(Request $request)
    {
        $user = array();
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'mobile_number' => 'required|numeric|unique:users,mobile_number,' . $id,
                    'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                ]);
                $message = "User Updated Successfully";
            } else {
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email',
                    'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
                    'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                ]);
                $message = "User Created Successfully";
            }

            DB::beginTransaction();
            try {
                $password = '12345678';
                $postData = [
                    "name" => $request->name,
                    'username' => $request->username,
                    "mobile_number" => $request->mobile_number,
                    "email" => $request->email,
                    'user_type' => 3,
                    'is_approve' => 1,
                    'is_verified' => 1,
                    "password" => bcrypt($password)
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['profile_image'] = $fileName;
                    }
                }
                $user = User::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }

            $data = ['status' => true, 'message' => $message, 'data' => $user, 'url' => route('admin.user.list')];
            return response($data);
        }

        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'users');
            $details = User::find($uuid);
        }
        return view('admin.user.add', compact('details'));
    }
    public function view(Request $request)
    {
        $detail = User::where('uuid', $request->uuid)->first();
        $userCurrentMonthBudget = userBudgetCategoryWise($detail->id);
        $userPreviousMonthBudget = userBudgetCategoryWise($detail->id, Carbon::now()->subMonth()->month);
        return view('admin.user.view', compact('detail', 'userCurrentMonthBudget', 'userPreviousMonthBudget'));
    }
}
