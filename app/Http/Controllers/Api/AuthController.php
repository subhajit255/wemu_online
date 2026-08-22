<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\Cms;
use App\Models\Faq;
use App\Models\Blog;
use App\Models\User;
use App\Models\Banner;
use App\Models\Contact;
use App\Models\Feature;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Category;
use App\Traits\SmsTrait;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\DeviceDetails;
use App\Traits\CommonFunction;
use App\Models\ServiceFrequency;
use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\Auth\CmsCollection;
use App\Http\Resources\Api\Auth\FaqCollection;
use App\Http\Resources\Api\Auth\BlogCollection;
use App\Http\Resources\Api\Auth\BannerCollection;
use App\Http\Resources\Api\Auth\FeatureCollection;
use App\Http\Resources\Api\Auth\ProductCollection;
use App\Http\Resources\Api\Auth\SettingCollection;
use App\Http\Resources\Api\Auth\CategoryCollection;
use App\Http\Resources\Api\Auth\ServiceFrequencyCollection;
use App\Http\Resources\Api\Auth\TodoCollection;
use App\Http\Resources\AuthResource;
use App\Models\Todo;
use App\Traits\UploadAble;
use App\Traits\StripeTrait;

class AuthController extends BaseController
{
    use CommonFunction;
    use SmsTrait;
    use NotificationTrait;
    use UploadAble;
    use StripeTrait;

    /**
     * @OA\Post(
     *     path="/api/signup",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="User's full name"),
     *                 @OA\Property(property="email", type="string", format="email", description="User's email"),
     *                 @OA\Property(property="password", type="string", format="password", description="Minimum 6 characters"),
     *                 @OA\Property(property="confirm_password", type="string", format="password", description="Must match password"),
     *                 @OA\Property(property="mobile_number", type="string", description="8-13 digits"),
     *                 @OA\Property(property="phone_code", type="integer", description="Country code, e.g., 61"),
     *                 required={"name", "email", "password", "confirm_password", "mobile_number", "phone_code"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Something went wrong")
     * )
     */
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13|unique:users,mobile_number',
            'phone_code' => 'required|numeric',
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string',
            'password' => 'required|min:6|string',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $userRole = Role::where('slug', 'user')->first();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => $userRole->id,
                'password' => bcrypt($request->password),
                'mobile_number' => $request->mobile_number,
                'phone_code' => $request->phone_code,
                'verification_code' => $otp,
                'registration_ip' => request()->ip(),
            ]);

            try {
                $customer = $this->createCustomer($user->email, $user->name);
                $user->update(['stripe_id' => $customer->id]);
            } catch (\Exception $e) {
                logger('Stripe customer creation failed during signup: ' . $e->getMessage());
            }

            if ($user) {
                $user->roles()->sync($userRole);
                try {
                    // Mail::send('mail.verify-otp', ['otp' => $otp], function ($message) use ($request) {
                    //     $message->to($request->email);
                    //     $message->subject('Verification OTP');
                    // });
                    // $mobileNumber = ($request->phone_code ?? 61) . $request->mobile_number;
                    // sendSms($mobileNumber, $otp);
                } catch (\Exception $e) {
                    //skip mail error
                }

                DB::commit();
                $status = true;
                $code = 200;
                $response = ['verification_code' => $otp];
                $message = 'OTP send Successfully';
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = 'Something went wrong';
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login using mobile number",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="mobile_number", type="string", description="8-13 digits"),
     *                 @OA\Property(property="phone_code", type="integer", description="Country code, e.g., 61"),
     *                 required={"mobile_number", "phone_code"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent successfully / Account not found / Account inactive"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13',
            'phone_code' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            $digits = 6;
            $otp = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);
            $condition = ["mobile_number" => $request->mobile_number];
            $userExist = User::where($condition)->first();
            $userRole = Role::where('slug', 'user')->first();
            if ($userExist) {
                if ($userExist->user_type != $userRole->id) {
                    $status = false;
                    $code = 200;
                    $response = [];
                    $message = 'Account not found';
                    return $this->responseJson($status, $code, $message, $response);
                }

                if ($userExist->is_active == 0) {
                    $status = false;
                    $code = 200;
                    $response = [];
                    $message = "Your account is not active. Contact to admin";
                    return $this->responseJson($status, $code, $message, $response);
                }

                // if ($userExist->phone_code != $request->phone_code) {
                //     $status = false;
                //     $code = 200;
                //     $response = [];
                //     $message = 'Country code mismatch, Please select correct country code';
                //     return $this->responseJson($status, $code, $message, $response);
                // }
                $userEmail = $userExist->email;

                // try {
                //     Mail::send('mail.verify-otp', ['otp' => $otp], function ($message) use ($userEmail) {
                //         $message->to($userEmail);
                //         $message->subject('Verification OTP');
                //     });
                //     $mobileNumber = ($userExist->phone_code ?? 61) . $userExist->mobile_number;
                //     sendSms($mobileNumber, $otp);
                // } catch (\Exception $e) {
                //     //skip mail error
                // }

                $user = User::find($userExist->id)->update(['verification_code' => $otp]);
            } else {
                $status = false;
                $code = 200;
                $response = [];
                $message = 'Account Not Found, Please Signup';
                return $this->responseJson($status, $code, $message, $response);
            }

            if ($user) {
                DB::Commit();
                $status = true;
                $code = 200;
                $response = ['verification_code' => $otp];
                $message = 'OTP send Successfully';
                return $this->responseJson($status, $code, $message, $response);
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
            return $this->responseJson($status, $code, $message, $response);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/login/verification",
     *     summary="Verify OTP for login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="mobile_number", type="string", description="Registered mobile number"),
     *                 @OA\Property(property="phone_code", type="integer"),
     *                 @OA\Property(property="verification_code", type="string", description="6 digit OTP"),
     *                 @OA\Property(property="device_token", type="string"),
     *                 @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}),
     *                 required={"mobile_number", "phone_code", "verification_code"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful (Returns Bearer token)"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=400, description="Invalid OTP")
     * )
     */
    public function loginVerification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13',
            'verification_code' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $user = User::where(['mobile_number' => $request->mobile_number, 'verification_code' => $request->verification_code])->first();
            if ($user) {
                $user->update([
                    'is_verified' => 1,
                    'fcm_token' => $request->fcm_token ?? null,
                    'device_type' => $request->device_type ?? 1,
                    'verification_code' => null,
                ]);
                DB::Commit();
                $token = $user->createToken('Login Successfully')->accessToken;
                $userEmail = $user->email;

                if ($token) {

                    $status = true;
                    $code = 200;
                    $response = ['token' => $token, 'user' => new AuthResource($user)];
                    $message = 'OTP Verify Successfully';
                } else {
                    $status = false;
                    $code = 500;
                    $response = [];
                    $message = 'Something went wrong';
                }
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = 'OTP doesn\'t match';
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/login-email",
     *     summary="Login using email and password",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="device_token", type="string"),
     *                 @OA\Property(property="device_type", type="string", enum={"android", "ios", "web"}),
     *                 required={"email", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful (Returns Bearer token)"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function loginViaEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
                $user = auth()->user();
                $userRole = Role::where('slug', 'user')->first();

                if ($user->user_type != $userRole->id) {
                    $status = false;
                    $code = 200;
                    $response = [];
                    $message = 'Account not found';
                    return $this->responseJson($status, $code, $message, $response);
                }

                if ($user->is_active == 0) {
                    $status = false;
                    $code = 200;
                    $response = [];
                    $message = "Your account is not active. Contact to admin";
                    return $this->responseJson($status, $code, $message, $response);
                }

                $user->update([
                    'fcm_token' => $request->fcm_token ?? null,
                    'device_type' => $request->device_type ?? 1,
                ]);

                DB::commit();
                $token = $user->createToken('Login Successfully')->accessToken;

                if ($token) {
                    $status = true;
                    $code = 200;
                    $response = ['token' => $token, 'user' => new AuthResource($user)];
                    $message = 'Login Successfully';
                } else {
                    $status = false;
                    $code = 500;
                    $response = [];
                    $message = 'Something went wrong';
                }
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = 'Invalid email or password';
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/my-profile",
     *     summary="Get current user profile",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Profile fetched successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function myProfile()
    {
        try {
            return $this->responseJson(true, 200, 'My Profile', new AuthResource(auth()->user()));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
            return $this->responseJson(false, 500, config('constants.CATCH_ERROR_MSG'), []);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/update-profile",
     *     summary="Update user profile",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="dob", type="string", format="date", description="YYYY-MM-DD"),
     *                 @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}),
     *                 @OA\Property(property="profile_image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = auth()->user();
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id . ',id,deleted_at,NULL',
                'mobile_number' => 'required|numeric|digits_between:8,13|unique:users,mobile_number,' . $user->id . ',id,deleted_at,NULL',
                'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
            ]);
            if ($validator->fails()) {
                return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
            }

            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ];

            if ($request->hasFile('profile_image')) {
                $image = $request->file('profile_image');
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $uploaded = $this->uploadOne($image, config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                if ($uploaded) {
                    // Delete old profile image if it exists
                    if ($user->profile_image) {
                        $this->deleteOne(config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH') . '/' . $user->profile_image);
                    }
                    $updateData['profile_image'] = $uploaded;
                }
            }

            $user->update($updateData);

            return $this->responseJson(true, 200, 'Profile updated successfully', new AuthResource($user));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
            return $this->responseJson(false, 500, config('constants.CATCH_ERROR_MSG'), (object)[]);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the user",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Logout successfully"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function logout(Request $request)
    {
        $token = auth()->user()->token();
        $tokenRevoke = $token->revoke();
        if ($tokenRevoke) {
            $status = true;
            $code = 200;
            $response = [];
            $message = 'You have been successfully logged out!';
        } else {
            $status = false;
            $code = 500;
            $response = [];
            $message = 'Something went wrong';
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/forgot/password",
     *     summary="Request a password reset OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 required={"email"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent to email"),
     *     @OA\Response(response=422, description="Validation error / Email not found")
     * )
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);
        if ($validator->fails()) {
            $status = false;
            $code = 422;
            $response = [];
            $message = $validator->errors()->first();

            return $this->responseJson($status, $code, $message, $response);
        } else {
            DB::beginTransaction();
            try {
                $userDetails = User::where('email', $request->email)->first();
                if (! $userDetails || empty($userDetails->email)) {
                    $status = false;
                    $code = 422;
                    $response = [];
                    $message = 'Invalid Email Id !!';
                } else {
                    $otp = generateOTP(4);
                    User::where('id', $userDetails->id)->update([
                        'verification_code' => $otp,
                    ]);
                    DB::commit();
                    $status = true;
                    $code = 200;
                    $response = ['otp' => $otp];
                    $message = 'OTP Sent Successfully !!';
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                $status = false;
                $code = 500;
                $response = [];
                $message = config('constants.CATCH_ERROR_MSG');
            }

            return $this->responseJson($status, $code, $message, $response);
        }
    }
    public function forgotPasswordByPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        } else {
            DB::beginTransaction();
            try {
                $userDetails = User::select('*')->where('mobile_number', $request->mobile_number)->first();
                if (empty($userDetails->mobile_number)) {
                    $status = false;
                    $code = 422;
                    $response = [];
                    $message = 'Invalid Phone Number Id !!';
                } else {
                    $otp = generateOTP(4);
                    User::where('id', $userDetails->id)->update([
                        'verification_code' => $otp
                    ]);

                    // try {
                    //     Mail::send('mail.verify-otp', ['otp' => $otp], function ($message) use ($userDetails) {
                    //         $message->to($userDetails->email);
                    //         $message->subject('Verification OTP');
                    //     });
                    //     $mobileNumber = ($userDetails->phone_code ?? 61) . $userDetails->mobile_number;
                    //     sendSms($mobileNumber, $otp);
                    // } catch (\Exception $e) {
                    //     //skip mail error
                    // }
                    DB::commit();
                    $status = true;
                    $code = 200;
                    $response = ['otp' => $otp];
                    $message = 'OTP Sent Successfully !!';
                }
            } catch (\Throwable $th) {
                DB::rollBack();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
            }
            return $this->responseJson($status, $code, $message, $response);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/reset/password",
     *     summary="Reset password using OTP",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="mobile_number", type="string"),
     *                 @OA\Property(property="phone_code", type="integer"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password"),
     *                 required={"mobile_number", "phone_code", "password", "password_confirmation"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6|string',
            'confirm_password' => 'required|same:new_password',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            $status = false;
            $code = 422;
            $response = [];
            $message = $validator->errors()->first();

            return $this->responseJson($status, $code, $message, $response);
        }

        DB::beginTransaction();
        try {
            if ($request->email) {
                $condition = ['email' => $request->email];
                $userFound = User::where($condition)->first();
            }
            if ($userFound) {
                $otpUpdate = User::find($userFound->id)->update(['password' => Hash::make($request->new_password), 'verification_code' => null]);
                if ($otpUpdate) {
                    DB::Commit();
                    $status = true;
                    $code = 200;
                    $response = [];
                    $message = 'Password Reset Successfully, Now you can login';
                } else {
                    $status = false;
                    $code = 422;
                    $response = [];
                    $message = 'Something went wrong';
                }
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = 'User not found';
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = [];
            $message = config('constants.CATCH_ERROR_MSG');
        }

        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/change/password",
     *     summary="Change account password",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="old_password", type="string", format="password"),
     *                 @OA\Property(property="new_password", type="string", format="password"),
     *                 @OA\Property(property="confirm_password", type="string", format="password"),
     *                 required={"old_password", "new_password", "confirm_password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Password changed successfully"),
     *     @OA\Response(response=400, description="Old password incorrect")
     * )
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:6|string',
            'confirm_password' => 'required|same:new_password',
            'mobile_number' => 'required|numeric|digits_between:8,13'
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            if ($request->mobile_number) {
                $condition = ['mobile_number' => $request->mobile_number];
                $userFound = User::where($condition)->first();
            }
            if ($userFound) {
                $otpUpdate = User::find($userFound->id)->update(['password' => Hash::make($request->new_password)]);
                if ($otpUpdate) {
                    DB::Commit();
                    $status = true;
                    $code = 200;
                    $response = [];
                    $message = "Password Created Successfully";
                } else {
                    $status = false;
                    $code = 422;
                    $response = [];
                    $message = 'Something went wrong';
                }
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = 'User not found';
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function bannerList()
    {
        try {
            $banner = Banner::where('is_active', 1)->get();
            if (!empty($banner) && count($banner) > 0) {
                $status = true;
                $code = 200;
                $response = BannerCollection::collection($banner);
                $message = "Banner List Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function blogList()
    {
        try {
            $blog = Blog::where('is_active', 1)->get();
            if (!empty($blog) && count($blog) > 0) {
                $status = true;
                $code = 200;
                $response = BlogCollection::collection($blog);
                $message = "Blog List Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/category/list",
     *     summary="List categories",
     *     tags={"General"},
     *     @OA\Response(response=200, description="Categories fetched")
     * )
     */
    public function categoryList(Request $request)
    {
        try {
            $categoryId = $request->category_id ?? null;
            if ($categoryId) {
                $id = uuidtoid($request->category_id, 'categories');
                $category = Category::where(['is_active' => 1, 'parent_id' => $id])->get();
            } else {
                $category = Category::where(['is_active' => 1, 'parent_id' => null])->get();
            }
            if (!empty($category) && count($category) > 0) {
                $status = true;
                $code = 200;
                $response = CategoryCollection::collection($category);
                $message = "Category List Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    public function productList(Request $request)
    {
        try {
            $productId = $request->product_id ?? null;
            if ($productId) {
                $id = uuidtoid($request->product_id, 'products');
                $product = Product::find($id);
                $categoryWiseOtherProduct = Product::whereNotIn('id', [$product->id])->where('parent_id', $product->parent_id)->get();
                $parentCategory = $product?->parentCategory?->title ?? null;
                $category = $product?->category?->title ?? null;
                $returnData = ['category' => $category, 'parent_category' => $parentCategory, 'product' => new ProductCollection($product), 'category_wise_other_product' => ProductCollection::collection($categoryWiseOtherProduct)];
            } else {
                $product = Product::where(['is_active' => 1])->get();
                $returnData = ProductCollection::collection($product);
            }
            if (!empty($product)) {
                $status = true;
                $code = 200;
                $response = $returnData;
                $message = "Product List Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/setting",
     *     summary="Get app settings",
     *     tags={"General"},
     *     @OA\Response(response=200, description="Settings fetched")
     * )
     */
    public function setting()
    {
        try {
            $setting = Setting::find(1);
            if (!empty($setting)) {
                $status = true;
                $code = 200;
                $response = new SettingCollection($setting);
                $message = "Setting Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/feature",
     *     summary="Get app features",
     *     tags={"General"},
     *     @OA\Response(response=200, description="Features fetched")
     * )
     */
    public function feature()
    {
        try {
            $feature = Feature::where('is_active', 1)->get();
            if (!empty($feature)) {
                $status = true;
                $code = 200;
                $response = FeatureCollection::collection($feature);
                $message = "Feature Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/cms",
     *     summary="Get CMS content",
     *     tags={"General"},
     *     @OA\Response(response=200, description="CMS content fetched")
     * )
     */
    public function cms(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alias' => 'nullable|exists:cms,alias',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $returnArr = [];
            if (empty($request->alias)) {
                $cms = Cms::where('is_active', 1)->get();
                $returnArr = CmsCollection::collection($cms);
            } else {
                $cms = Cms::where('alias', $request->alias)->first();
                $returnArr = new CmsCollection($cms);
            }
            if (!empty($cms)) {
                $status = true;
                $code = 200;
                $response = $returnArr;
                $message = "Cms Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/service/frequency/list",
     *     summary="Get service frequencies",
     *     tags={"General"},
     *     @OA\Response(response=200, description="List of frequencies")
     * )
     */
    public function serviceFrequencyList()
    {
        try {
            $serviceFrequency = ServiceFrequency::where('is_active', 1)->get();
            if (!empty($serviceFrequency)) {
                $status = true;
                $code = 200;
                $response = ServiceFrequencyCollection::collection($serviceFrequency);
                $message = "Service Frequency Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }

    /**
     * @OA\Post(
     *     path="/api/contact-us",
     *     summary="Submit contact us form",
     *     tags={"General"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="subject", type="string"),
     *                 @OA\Property(property="message", type="string"),
     *                 required={"name", "email", "subject", "message"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Form submitted successfully")
     * )
     */
    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'mobile_number' => 'required|numeric|digits_between:8,13',
            'enquiry' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $contact = Contact::create($request->all());
            if ($contact) {
                $this->saveNotification([
                    "title" => 'My Name is ' . $request->name . ' and I have a query, Can you help me on this.',
                    "description" => $request->enquiry,
                    "user_id" => null,
                    "for" => 1,
                    "type" => 2,
                    "is_read" => 0,
                    "contact_id" => $contact->id
                ]);
                DB::commit();
                $status = true;
                $code = 200;
                $response = $contact;
                $message = "Your feedback has been submitted successfully, Will get back to you soon.";
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = "Something Went Wrong";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/forgot/pin",
     *     summary="Request OTP to reset PIN",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="mobile_number", type="string"),
     *                 @OA\Property(property="phone_code", type="integer"),
     *                 required={"mobile_number", "phone_code"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="OTP sent")
     * )
     */
    public function forgotPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_code' => 'required|numeric',
            'mobile_number' => 'required|numeric|digits_between:8,13',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $user = User::where('mobile_number', $request->mobile_number)->first();
            if ($user) {
                if ($user->phone_code != $request->phone_code) {
                    $status = false;
                    $code = 200;
                    $response = [];
                    $message = 'Country code mismatch, Please select correct country code';
                    return $this->responseJson($status, $code, $message, $response);
                }
                $otp = generateOTP(4);
                $user->update(['verification_code' => $otp]);
                try {
                    Mail::send('mail.verify-otp', ['otp' => $otp], function ($message) use ($user) {
                        $message->to($user->email);
                        $message->subject('Verification OTP');
                    });
                    $mobileNumber = ($user->phone_code ?? 61) . $user->mobile_number;
                    sendSms($mobileNumber, $otp);
                } catch (\Exception $e) {
                    //skip mail error
                }
                DB::commit();
                $status = true;
                $code = 200;
                $response = ['otp' => $otp];
                $message = "OTP Sent Successfully";
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = "User not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/verify/pin",
     *     summary="Verify PIN",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="mobile_number", type="string"),
     *                 @OA\Property(property="phone_code", type="integer"),
     *                 @OA\Property(property="pin", type="string"),
     *                 required={"mobile_number", "phone_code", "pin"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="PIN verified")
     * )
     */
    public function verifyPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13',
            'otp' => 'required|numeric|digits:4',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $user = User::where('mobile_number', $request->mobile_number)->first();
            if ($user) {
                if ($user->verification_code === $request->otp) {
                    $user->update(['verification_code' => null]);
                    DB::commit();
                    $status = true;
                    $code = 200;
                    $response = [];
                    $message = "OTP Verified Successfully, Now you can change your pin";
                } else {
                    $status = false;
                    $code = 422;
                    $response = [];
                    $message = "OTP doesn't match";
                }
            } else {
                $status = false;
                $code = 422;
                $response = [];
                $message = "User not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/change/pin",
     *     summary="Change security PIN",
     *     tags={"Auth"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="old_pin", type="string"),
     *                 @OA\Property(property="new_pin", type="string"),
     *                 @OA\Property(property="confirm_pin", type="string"),
     *                 required={"old_pin", "new_pin", "confirm_pin"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="PIN changed successfully")
     * )
     */
    public function changePin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|numeric|digits_between:8,13',
            'pin' => 'required|numeric|digits:4',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $user = User::where('mobile_number', $request->mobile_number)->first();
            if ($user) {
                $user->update(['pin' => $request->pin]);
                DB::commit();
                $status = true;
                $code = 200;
                $response = [];
                $message = "Pin Changed Successfully";
            } else {
                $status = false;
                $code = 500;
                $response = [];
                $message = "User not found";
            }
        } catch (\Throwable $th) {
            DB::rollback();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/faq",
     *     summary="Get Frequently Asked Questions",
     *     tags={"General"},
     *     @OA\Response(response=200, description="FAQs fetched")
     * )
     */
    public function faq()
    {
        try {
            $faq = Faq::where('is_active', 1)->get();
            if (!empty($faq) && $faq->isNotEmpty()) {
                $status = true;
                $code = 200;
                $response = FaqCollection::collection($faq);
                $message = "FAQ Fetched";
            } else {
                $status = true;
                $code = 200;
                $response = [];
                $message = "No Data Found";
            }
        } catch (\Throwable $th) {
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/todo/add",
     *     summary="Add a new Todo item",
     *     tags={"Todo"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string"),
     *                 @OA\Property(property="description", type="string"),
     *                 required={"title"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Todo added successfully")
     * )
     */
    public function todoAdd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'nullable|exists:todos,id',
            'title' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $todo = Todo::updateOrCreate(['id' => $request->id], [
                'title' => $request->title,
                'status' => 1,
            ]);
            DB::commit();
            $status = true;
            $code = 200;
            $response = new TodoCollection($todo);
            $message = $request->id ? "Todo Updated Successfully" : "Todo Added Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Post(
     *     path="/api/todo/delete",
     *     summary="Delete a Todo item",
     *     tags={"Todo"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="todo_id", type="integer"),
     *                 required={"todo_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Todo deleted successfully")
     * )
     */
    public function todoDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'todo_id' => 'required|exists:todos,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $todo = Todo::find($request->todo_id);
            $todo->delete();
            DB::commit();
            $status = true;
            $code = 200;
            $response = [];
            $message = "Todo Deleted Successfully";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
    /**
     * @OA\Get(
     *     path="/api/todo/list",
     *     summary="List Todo items",
     *     tags={"Todo"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(response=200, description="Todos fetched successfully")
     * )
     */
    public function todoList(Request $request)
    {
        DB::beginTransaction();
        try {
            $todo = Todo::all();
            $status = true;
            $code = 200;
            $response = TodoCollection::collection($todo);
            $message = "Todo List";
        } catch (\Throwable $th) {
            DB::rollBack();
            $status = false;
            $code = 500;
            $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
            $message = config('constants.CATCH_ERROR_MSG');
        }
        return $this->responseJson($status, $code, $message, $response);
    }
}
