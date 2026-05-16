<?php

namespace App\Http\Controllers\Admin;

use App\Models\LandingPageContent;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\NotifyMe;
use Illuminate\Support\Facades\Mail;

class LandingPageController extends BaseController
{
    use CommonFunction;
    use UploadAble;
    public function index(Request $request)
    {
        if ($request->post()) {
            $message = "Content Updated Successfully";

            DB::beginTransaction();
            try {
                $postData = array();

                // Hero Section
                if (!empty($request->hero_title_one)) {
                    $postData['hero_title_one'] = $request->hero_title_one;
                }
                if (!empty($request->hero_title_two)) {
                    $postData['hero_title_two'] = $request->hero_title_two;
                }
                if (!empty($request->hero_content)) {
                    $postData['hero_content'] = $request->hero_content;
                }
                if (!empty($request->app_url)) {
                    $postData['app_url'] = $request->app_url;
                }

                // Feature Section
                if (!empty($request->feature_title)) {
                    $postData['feature_title'] = $request->feature_title;
                }
                if (!empty($request->feature_description)) {
                    $postData['feature_description'] = $request->feature_description;
                }
                if (!empty($request->feature_sub_title_one)) {
                    $postData['feature_sub_title_one'] = $request->feature_sub_title_one;
                }
                if (!empty($request->feature_sub_desc_one)) {
                    $postData['feature_sub_desc_one'] = $request->feature_sub_desc_one;
                }
                if (!empty($request->feature_sub_title_two)) {
                    $postData['feature_sub_title_two'] = $request->feature_sub_title_two;
                }
                if (!empty($request->feature_sub_desc_two)) {
                    $postData['feature_sub_desc_two'] = $request->feature_sub_desc_two;
                }
                if (!empty($request->feature_sub_title_three)) {
                    $postData['feature_sub_title_three'] = $request->feature_sub_title_three;
                }
                if (!empty($request->feature_sub_desc_three)) {
                    $postData['feature_sub_desc_three'] = $request->feature_sub_desc_three;
                }
                if (!empty($request->feature_sub_title_four)) {
                    $postData['feature_sub_title_four'] = $request->feature_sub_title_four;
                }
                if (!empty($request->feature_sub_desc_four)) {
                    $postData['feature_sub_desc_four'] = $request->feature_sub_desc_four;
                }

                // How It Works Section
                if (!empty($request->hiw_title_one)) {
                    $postData['hiw_title_one'] = $request->hiw_title_one;
                }
                if (!empty($request->hiw_desc_one)) {
                    $postData['hiw_desc_one'] = $request->hiw_desc_one;
                }
                if (!empty($request->hiw_title_two)) {
                    $postData['hiw_title_two'] = $request->hiw_title_two;
                }
                if (!empty($request->hiw_desc_two)) {
                    $postData['hiw_desc_two'] = $request->hiw_desc_two;
                }
                if (!empty($request->hiw_title_three)) {
                    $postData['hiw_title_three'] = $request->hiw_title_three;
                }
                if (!empty($request->hiw_desc_three)) {
                    $postData['hiw_desc_three'] = $request->hiw_desc_three;
                }

                // Goal Section
                if (!empty($request->goal_title)) {
                    $postData['goal_title'] = $request->goal_title;
                }
                if (!empty($request->goal_content)) {
                    $postData['goal_content'] = $request->goal_content;
                }

                // FAQs
                if (!empty($request->faqs)) {
                    $postData['faqs'] = json_encode($request->faqs);
                }

                // Testimonial Section
                if (!empty($request->testimonial_title_one)) {
                    $postData['testimonial_title_one'] = $request->testimonial_title_one;
                }
                if (!empty($request->testimonial_title_two)) {
                    $postData['testimonial_title_two'] = $request->testimonial_title_two;
                }
                if (!empty($request->testimonial_title_content)) {
                    $postData['testimonial_title_content'] = $request->testimonial_title_content;
                }
                if (!empty($request->testimonial_ios_app_link)) {
                    $postData['testimonial_ios_app_link'] = $request->testimonial_ios_app_link;
                }
                if (!empty($request->testimonial_android_app_link)) {
                    $postData['testimonial_android_app_link'] = $request->testimonial_android_app_link;
                }

                // Handle testimonials with image upload
                if (!empty($request->testimonials) && is_array($request->testimonials)) {
                    $testimonials = [];
                    foreach ($request->testimonials as $index => $testimonial) {
                        $testimonialData = [
                            'title_one' => $testimonial['title_one'] ?? '',
                            'title_two' => $testimonial['title_two'] ?? '',
                            'title_content' => $testimonial['title_content'] ?? '',
                        ];

                        // Handle image upload
                        if (isset($testimonial['image']) && is_object($testimonial['image'])) {
                            $image = $testimonial['image'];
                            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                            $destinationPath = public_path('uploads/testimonials');
                            $image->move($destinationPath, $imageName);
                            chmod($destinationPath . '/' . $imageName, 0777); // Set file permission to 777
                            $testimonialData['image'] = $imageName;
                        } elseif (isset($testimonial['image']) && is_string($testimonial['image'])) {
                            // If already saved image name exists (for edit)
                            $testimonialData['image'] = $testimonial['image'];
                        } else {
                            $testimonialData['image'] = '';
                        }

                        $testimonials[] = $testimonialData;
                    }
                    $postData['testimonials'] = json_encode($testimonials);
                }

                // Footer
                // if (!empty($request->footer_hours_desc)) {
                //     $postData['footer_hours_desc'] = $request->footer_hours_desc;
                // }

                $details = LandingPageContent::updateOrCreate(['id' => 1], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }
            $data = ['status' => true, 'message' => $message, 'data' => $details ?? null, 'url' => route('admin.landing.page.update')];
            return response($data);
        }
        $details = LandingPageContent::find(1);
        return view('admin.landing.index', compact('details'));
    }
    public function notify(Request $request)
    {
        $details = NotifyMe::all();
        return view('admin.landing.notify', compact('details'));
    }
    public function notifySend(Request $request)
    {
        $details = NotifyMe::where('is_send', 0)->get();
        if (!empty($details)) {
            $linkAndroid = env('ANDROID_APP_LINK');
            $linkIOS = env('IOS_APP_LINK');
            foreach ($details as $detail) {
                $sendMail = Mail::send('mail.app-live', ['linkAndroid' => $linkAndroid, 'linkIOS' => $linkIOS], function ($message) use ($detail) {
                    $message->to($detail->email);
                    $message->subject('Congratulations! Your app is live now.');
                });
                if ($sendMail) {
                    $detail->update(['is_send' => 1]);
                }
            }
            return response()->json(['status' => 'success', 'message' => 'Notification sent successfully!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Notification already sent!']);
        }
    }
}
