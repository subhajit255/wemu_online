<?php

namespace App\Http\Controllers\Web;

use App\Models\LandingPageContent;
use App\Models\NotifyMe;
use App\Models\Setting;
use App\Http\Controllers\BaseController;
use App\Models\Faq;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    public function landingPage()
    {
        $settings = Setting::first();
        $details = LandingPageContent::find(1);
        // $faqs = Faq::where('is_active', 1)->get();
        return view('frontend.pages.landing-page', compact('settings', 'details'));
    }
    public function termAndConditions()
    {
        $settings = Setting::first();
        $cms = \App\Models\Cms::where('alias', 'wemu_terms_of_use')->first();
        $sections = $cms ? json_decode($cms->description, true) : [];
        return view('frontend.pages.terms', compact('sections', 'settings', 'cms'));
    }
    public function privacyPolicy()
    {
        $settings = Setting::first();
        $data = $settings->privacy_policy;
        return view('frontend.pages.page', compact('data', 'settings'));
    }
    public function comingSoon()
    {
        $settings = Setting::first();
        return view('frontend.pages.coming-soon', compact('settings'));
    }
    public function notifyMe(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        try {
            if (NotifyMe::where('email', $request->email)->exists()) {
                return response()->json(['status' => false, 'message' => 'You have already subscribed us']);
            }

            $notifyMail = new NotifyMe();
            $notifyMail->email = $request->email;
            $notifyMail->save();
            return response()->json(['status' => true, 'message' => 'You have successfully subscribed us', 'swal' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }
}
