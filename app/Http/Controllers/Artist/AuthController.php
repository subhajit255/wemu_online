<?php

namespace App\Http\Controllers\Artist;

use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            if (!$user || $user->user_type != 3 || !Hash::check($request->password, $user->password)) {
                $data = ['status' => false, 'message' => 'Incorrect Details or Unauthorized Access. Please try again', 'data' => null, 'url' => route('artist.login')];
            } else {
                $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update(['verification_code' => $otp]);
                session(['verify_user_id' => $user->id]);
                $data = ['status' => true, 'message' => 'Please verify OTP sent to your mobile', 'data' => null, 'url' => route('artist.otp.verify')];
            }
            return response($data);
        }
        return view('auth.login');
    }

    public function register(Request $request)
    {
        if ($request->post()) {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required',
                'password' => 'required|confirmed'
            ]);
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $artistRole = Role::where('slug', 'artist')->first();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile,
                'user_type' => $artistRole->id,
                'password' => bcrypt($request->password),
                'verification_code' => $otp,
                'registration_ip' => request()->ip(),
            ]);
            if ($user) {
                $user->roles()->sync($artistRole);
                $data = ['status' => true, 'message' => 'Registration successful', 'data' => null, 'url' => route('artist.login')];
            } else {
                $data = ['status' => false, 'message' => 'Registration failed', 'data' => null, 'url' => route('artist.register')];
            }
            return response($data);
        }
    }

    public function otpVerify(Request $request)
    {
        if ($request->post()) {
            $request->validate([
                'otp' => 'required|numeric|digits:6'
            ]);

            $userId = session('verify_user_id');
            if (!$userId) {
                return response(['status' => false, 'message' => 'Session expired', 'data' => null, 'url' => route('artist.login')]);
            }

            $user = User::find($userId);
            if ($user && $user->verification_code == $request->otp) {
                $user->update(['verification_code' => null]);
                auth()->login($user);
                session()->forget('verify_user_id');
                $url = \Illuminate\Support\Facades\Route::has('artist.dashboard') ? route('artist.dashboard') : url('/');
                return response(['status' => true, 'message' => 'Verification successful', 'data' => null, 'url' => $url]);
            }

            return response(['status' => false, 'message' => 'Invalid OTP', 'data' => null]);
        }
        $userId = session('verify_user_id');
        $verificationCode = null;
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                $verificationCode = $user->verification_code;
            }
        }

        return view('auth.otp-verify', compact('verificationCode'));
    }

    public function dashboard()
    {
        return view('artist.dashboard');
    }
}
