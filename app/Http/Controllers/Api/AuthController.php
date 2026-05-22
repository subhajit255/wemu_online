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

class AuthController extends BaseController
{
    use CommonFunction;
    use SmsTrait;
    use NotificationTrait;
    use UploadAble;

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
    public function myProfile()
    {
        try {
            return $this->responseJson(true, 200, 'My Profile', new AuthResource(auth()->user()));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getFile() . '--' . $e->getLine());
            return $this->responseJson(false, 500, config('constants.CATCH_ERROR_MSG'), []);
        }
    }
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
    public function forgotPassword(Request $request)
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
