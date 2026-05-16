<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        if ($request->post()) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required'
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user || !in_array($user->user_type, [1, 2])) {
                return response(['status' => false, 'message' => 'Unauthorized Access. Admin only.', 'data' => null, 'url' => route('admin.login')]);
            }

            if (!auth()->attempt($request->only(['email', 'password']))) {
                $data = ['status' => false, 'message' => 'Incorrect Details. Please try again', 'data' => null, 'url' => route('admin.login')];
            } else {
                $data = ['status' => true, 'message' => 'You have successfully logged in', 'data' => null, 'url' => route('admin.dashboard')];
            }
            return response($data);
        }
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            $data = ['status' => true, 'message' => 'User Not Found', 'data' => $user ?? null];
        } else {
            $token = Str::random(60);
            $user->update(['remember_token' => $token]);
            $resetLink = route('admin.reset.password', $token);

            // Mail::send('mail.reset-password', ['link' => $resetLink], function ($message) use ($request) {
            //     $message->to($request->email);
            //     $message->subject('Reset Password Link');
            // });
            $data = ['status' => true, 'message' => 'Check your email for resetting your password', 'data' => $user ?? null];
        }

        return response($data);
    }

    public function resetPassword(Request $request)
    {
        $token = '';
        $linkExpire = true;
        $user = User::where('remember_token', $request->reset_token)->first();
        if ($request->post()) {
            $request->validate([
                'new_password' => 'required|min:8',
                'password_confirmation' => 'required|min:8|same:new_password',
            ]);
            DB::beginTransaction();
            try {
                $update = $user->update(['remember_token' => null, 'password' => bcrypt($request->new_password)]);
                DB::commit();
                $data = ['status' => true, 'message' => 'Password Reset Successfully. Please Login.', 'data' => $user ?? null, 'url' => route('admin.login')];
            } catch (\Throwable $th) {
                DB::rollBack();
                $data = ['status' => true, 'message' => 'Something Went Wrong', 'data' => $user ?? null, 'url' => route('admin.login')];
            }
            return response($data);
        }
        if (!empty($user)) {
            $linkExpire = false;
        }
        $token = $request->token;
        return view('auth.login', compact('token', 'linkExpire'));
    }

    public function verifyEmail(Request $request)
    {
        $emailVerified = false;
        $user = User::where('uuid', $request->uuid)->first();
        if($user){
            $user->update(['email_verified' => 1]);
            $emailVerified = true;
        }
        return view('auth.verify-email', compact('emailVerified'));
    }
    public function deleteAccount(Request $request)
    {
        if ($request->post()) {
            $request->validate([
                'mobile_number' => 'required|digits:10|exists:users,mobile_number',
            ]);
            $user = User::where('mobile_number', $request->mobile_number)->where('user_type', '!=', 1)->first();
            if ($user) {
                $meassage = "User deleted successfully";
            } else {
                $meassage = "User not found";
            }
            return response(['status' => true, 'message' => $meassage, 'data' => null]);
        }

        return view('auth.delete-account');
    }
}
