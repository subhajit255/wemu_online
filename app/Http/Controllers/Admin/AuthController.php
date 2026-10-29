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
use Illuminate\Support\Facades\Hash;
use App\Mail\ResetPasswordOtpMail;

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
        if ($request->isMethod('post')) {
            $request->validate([
                'email' => 'required|email',
            ]);

            $user = User::where('email', $request->email)->whereNull('deleted_at')->first();

            if (!$user || !in_array($user->user_type, [1, 2])) {
                return response([
                    'status' => false,
                    'message' => 'No admin account found with that email address.',
                ]);
            }

            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->update(['verification_code' => $otp]);

            session([
                'reset_password_user_id' => $user->id,
                'reset_password_email' => $user->email,
            ]);
            session()->forget('reset_password_verified');

            try {
                Mail::to($user->email)->send(new ResetPasswordOtpMail($otp, $user));
            } catch (\Exception $e) {
                logger('Error sending admin password reset OTP mail: ' . $e->getMessage());
            }

            return response([
                'status' => true,
                'message' => 'A verification code has been sent to your email.',
                'url' => route('admin.forgot.password.verify.otp'),
                'otp' => config('app.debug') ? $otp : null,
            ]);
        }

        session()->forget(['reset_password_user_id', 'reset_password_email', 'reset_password_verified']);

        return view('auth.forgot-password');
    }

    public function forgotPasswordVerifyOtp(Request $request)
    {
        $userId = session('reset_password_user_id');
        if (!$userId) {
            if ($request->ajax() || $request->isMethod('post')) {
                return response([
                    'status' => false,
                    'message' => 'Session expired. Please start over.',
                    'url' => route('admin.forgot.password'),
                ]);
            }
            return redirect()->route('admin.forgot.password');
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget(['reset_password_user_id', 'reset_password_email', 'reset_password_verified']);
            if ($request->ajax() || $request->isMethod('post')) {
                return response([
                    'status' => false,
                    'message' => 'User not found. Please try again.',
                    'url' => route('admin.forgot.password'),
                ]);
            }
            return redirect()->route('admin.forgot.password');
        }

        if ($request->isMethod('post')) {
            if ($request->input('action') === 'resend') {
                $newOtp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update(['verification_code' => $newOtp]);

                try {
                    Mail::to($user->email)->send(new ResetPasswordOtpMail($newOtp, $user));
                } catch (\Exception $e) {
                    logger('Error resending admin password reset OTP: ' . $e->getMessage());
                }

                return response([
                    'status' => true,
                    'message' => 'A new verification code has been sent to your email.',
                    'otp' => config('app.debug') ? $newOtp : null,
                ]);
            }

            $request->validate([
                'otp' => 'required|numeric|digits:6',
            ]);

            if ($user->verification_code && $user->verification_code == $request->otp) {
                session(['reset_password_verified' => true]);

                return response([
                    'status' => true,
                    'message' => 'OTP verified successfully.',
                    'url' => route('admin.reset.password'),
                ]);
            }

            return response([
                'status' => false,
                'message' => 'Invalid or expired OTP. Please try again.',
            ]);
        }

        $email = $user->email;
        $verificationCode = config('app.debug') ? $user->verification_code : null;

        return view('auth.forgot-password-otp', compact('email', 'verificationCode'));
    }

    public function resetPassword(Request $request)
    {
        $userId = session('reset_password_user_id');
        $isVerified = session('reset_password_verified');

        if (!$userId || !$isVerified) {
            if ($request->ajax() || $request->isMethod('post')) {
                return response([
                    'status' => false,
                    'message' => 'Session expired or unverified. Please start over.',
                    'url' => route('admin.forgot.password'),
                ]);
            }
            return redirect()->route('admin.forgot.password');
        }

        $user = User::find($userId);
        if (!$user) {
            session()->forget(['reset_password_user_id', 'reset_password_email', 'reset_password_verified']);
            if ($request->ajax() || $request->isMethod('post')) {
                return response([
                    'status' => false,
                    'message' => 'User not found.',
                    'url' => route('admin.forgot.password'),
                ]);
            }
            return redirect()->route('admin.forgot.password');
        }

        if ($request->isMethod('post')) {
            if ($request->has('new_password') && !$request->has('password')) {
                $request->merge(['password' => $request->new_password]);
            }
            if ($request->has('confirm_password') && !$request->has('password_confirmation')) {
                $request->merge(['password_confirmation' => $request->confirm_password]);
            }

            $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user->update([
                'password' => Hash::make($request->password),
                'verification_code' => null,
                'remember_token' => null,
            ]);

            session()->forget(['reset_password_user_id', 'reset_password_email', 'reset_password_verified']);

            return response([
                'status' => true,
                'message' => 'Password reset successfully! Please log in with your new password.',
                'url' => route('admin.login'),
            ]);
        }

        return view('auth.reset-password');
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
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ], [
                'email.required' => 'Please enter the user email address.',
                'email.email' => 'Please enter a valid email address.',
            ]);

            if ($validator->fails()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false,
                        'type' => 'validation_error',
                        'message' => $validator->errors()->first('email'),
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $email = trim(strtolower($request->email));
            $user = User::withTrashed()->where('email', $email)->first();

            // 1. Check if user exists in db by that email
            if (!$user) {
                $message = 'User not found';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false,
                        'type' => 'not_found',
                        'message' => $message,
                    ]);
                }
                return back()->with('error_msg', $message)->withInput();
            }

            // Safeguard super admin account
            if ($user->user_type == 1) {
                $message = 'Super admin account cannot be deleted';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false,
                        'type' => 'forbidden',
                        'message' => $message,
                    ]);
                }
                return back()->with('error_msg', $message)->withInput();
            }

            // 2. Check if user is_active is 0
            if ((int)$user->is_active === 0) {
                $message = 'Already deleted';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false,
                        'type' => 'already_deleted',
                        'is_deleted' => true,
                        'message' => $message,
                    ]);
                }
                return back()->with('warning_msg', $message)->withInput();
            }

            // 3. If exists and is_active is not 0: change its is_active to 0
            $user->is_active = 0;
            $user->save();

            $message = 'User deleted';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'type' => 'just_deleted',
                    'is_deleted' => true,
                    'message' => $message,
                ]);
            }

            return back()->with('success_msg', $message);
        }

        return view('auth.delete-account');
    }
}
