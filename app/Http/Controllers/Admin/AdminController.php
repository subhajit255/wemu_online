<?php

namespace App\Http\Controllers\Admin;

use App\Models\Item;
use App\Models\User;
use App\Models\Category;
use App\Models\UserGoal;
use App\Traits\UploadAble;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\UserGoalTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;

class AdminController extends BaseController
{
    use UploadAble;
    public function dashboard()
    {
        $totalUserCount = User::where('user_type', 3)->count();
        $totalTodaysUserCount = User::where('user_type', 3)->whereDate('created_at', date('Y-m-d'))->count();
        // $totalItemsCount = Item::count();
        // $totalTodayItemsCount = Item::whereDate('created_at', date('Y-m-d'))->count();
        // $totalCategoriesCount = Category::count();
        // $totalTodayCategoriesCount = Category::whereDate('created_at', date('Y-m-d'))->count();
        // $totalUserGoalCount = UserGoal::count();
        // $totalUserGoalTaskCount = UserGoalTask::count();
        // $totalIncome = Transaction::where('payment_status', 2)->sum('amount');
        // $totalIncomeThisMonth = Transaction::where('payment_status', 2)
        //     ->whereBetween('created_at', [
        //         now()->startOfMonth()->toDateTimeString(),
        //         now()->endOfMonth()->toDateTimeString()
        //     ])
        //     ->sum('amount');
        // $transactions = Transaction::where('payment_status', 2)->latest()->paginate(10);
        // return view('admin.dashboard', compact('totalUserCount', 'totalTodaysUserCount', 'totalItemsCount', 'totalTodayItemsCount', 'totalCategoriesCount', 'totalTodayCategoriesCount', 'totalUserGoalCount', 'totalUserGoalTaskCount', 'transactions', 'totalIncome', 'totalIncomeThisMonth'));
        return view('admin.dashboard', compact('totalUserCount', 'totalTodaysUserCount'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'admin_name' => 'required|string',
            'admin_email' => 'required|email',
            'admin_mobile_number' => 'required|digits:10|numeric',
        ]);
        if ($request->post()) {
            // dd($request->all());
            $postData = [
                "name" => $request->admin_name,
                'username' => $this->createUserName($request->admin_name),
                "mobile_number" => $request->admin_mobile_number,
                "email" => $request->admin_email,
            ];
            if (!empty($request->admin_profile_image)) {
                $image = $request->admin_profile_image;
                $type = $image->getClientOriginalExtension();
                $fileName = uniqid() . '.' . $type;
                $isFileUploaded = $this->uploadOne($image, config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                if ($isFileUploaded) {
                    $postData['profile_image'] = $fileName;
                }
            }
            $data = User::updateOrCreate(['id' => Auth::user()->id], $postData);
        }
        $message = "Updated Successfully";
        $data = ['status' => true, 'message' => $message, 'data' => $postData];
        return response($data);
    }

    public function passwordUpdate(Request $request)
    {
        if ($request->post()) {
            $request->validate([
                'old_password' => 'required|min:8',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required|min:8|same:new_password',
            ]);
            $old_pass = Auth::user()->password;
            if (empty($request->old_password)) {
                $hash_old_pass = '';
            } else {
                $hash_old_pass = $request->old_password;
            }
            $check = Hash::check($hash_old_pass, $old_pass);

            if (empty($request->old_password)) {
                $message = "Provide Old Password";
                $data = ['status' => false, 'message' => $message, 'data' => ''];
            } else if ($check !== true) {
                $message = "Provided Old Password is Wrong";
                $data = ['status' => false, 'message' => $message, 'data' => ''];
            } else if (empty($request->new_password)) {
                $message = "Provide New Password";
                $data = ['status' => false, 'message' => $message, 'data' => ''];
            } else if (empty($request->confirm_password)) {
                $message = "Provide Confirm Password";
                $data = ['status' => false, 'message' => $message, 'data' => ''];
            } else if ($request->confirm_password !== $request->new_password) {
                $message = "New Password & Confirm Password Have to be Same";
                $data = ['status' => false, 'message' => $message, 'data' => ''];
            } else {
                $postData = [
                    "password" => Hash::make($request->confirm_password),
                ];
                $data = User::updateOrCreate(['id' => Auth::user()->id], $postData);
                $message = "Admin Password Updated Successfully";
                $data = ['status' => true, 'message' => $message, 'data' => $postData];
            }
        }
        return response($data);
    }

    public function readNotification(Request $request)
    {
        Notification::find($request->notificationId)->update(['is_read' => 1]);
    }
}
