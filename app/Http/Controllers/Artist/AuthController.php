<?php

namespace App\Http\Controllers\Artist;

use App\Models\Role;
use App\Models\User;
use App\Models\Genre;
use App\Models\Album;
use App\Models\ArtistProfile;
use App\Models\ArtistFollower;
use App\Models\AudienceLog;
use App\Models\StreamLog;
use App\Models\SocialLink;
use App\Models\ArtistVerification;
use App\Models\ArtistPreference;
use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\StripeTrait;

class AuthController extends BaseController
{
    use StripeTrait;
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

        // Clear active stale session if the user explicitly navigates to the login page
        if (auth()->check()) {
            auth()->logout();
        }
        session()->forget('verify_user_id');

        return view('auth.login');
    }

    public function register(Request $request)
    {
        $genres = Genre::where('is_active', 1)->get();
        $settings = \App\Models\Setting::find(1);
        $artistSubscriptionEnabled = $settings ? $settings->artist_subscription : 0;
        $subscriptions = [];
        if ($artistSubscriptionEnabled) {
            $subscriptions = \App\Models\Subscription::where('status', 1)
                ->where(function ($q) {
                    $q->where('available_for', 2)->orWhere('is_default', 1);
                })
                ->orderBy('sort_order', 'asc')
                ->get();
        }

        if ($request->isMethod('get')) {
            if ($request->has('reset') || $request->query('reset') == 1) {
                if (auth()->check()) {
                    auth()->logout();
                }
                session()->forget('verify_user_id');
            }

            $user = auth()->user();
            if ($user && $user->user_type == 3) {
                if ($user->completed_steps >= 9 || $user->added_by) {
                    return redirect()->route('artist.dashboard');
                }

                $profile = ArtistProfile::firstOrCreate(['user_id' => $user->id]);
                $socials = SocialLink::firstOrCreate(['user_id' => $user->id]);
                $verification = ArtistVerification::firstOrCreate(['user_id' => $user->id]);
                $preferences = ArtistPreference::firstOrCreate(['user_id' => $user->id]);

                $activeStep = $user->completed_steps ? ($user->completed_steps + 1) : 3;
                if ($activeStep == 8 && !$artistSubscriptionEnabled) {
                    $activeStep = 9;
                }
                if ($activeStep > 10) $activeStep = 10;

                return view('auth.register', compact('genres', 'user', 'profile', 'socials', 'verification', 'preferences', 'activeStep', 'artistSubscriptionEnabled', 'subscriptions'));
            }

            $verifyUserId = session('verify_user_id');
            $verifyUser = $verifyUserId ? User::find($verifyUserId) : null;
            $activeStep = ($verifyUser && !$verifyUser->email_verified) ? 2 : 1;

            return view('auth.register', compact('genres', 'activeStep', 'verifyUser', 'artistSubscriptionEnabled', 'subscriptions'));
        }

        if ($request->isMethod('post')) {
            $step = $request->input('step', 1);

            // Resend OTP handler
            if ($step === 'resend') {
                $userId = $request->input('user_id');
                $u = User::find($userId);
                if (!$u) {
                    return response(['status' => false, 'message' => 'User not found.']);
                }
                $newOtp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $u->update(['verification_code' => $newOtp]);
                // TODO: send email with $newOtp
                return response(['status' => true, 'message' => 'OTP resent successfully.', 'otp' => $newOtp]);
            }

            switch ($step) {
                case 1:
                    $request->validate([
                        'stage_name' => 'required|string|max:255',
                        'name'       => 'required|string|max:255',
                        'email'      => 'required|email|unique:users,email',
                        'mobile'     => 'required|unique:users,mobile_number',
                        'password'   => 'required|string|min:6|confirmed',
                        'country'    => 'required|string|max:100',
                        'agree_terms' => 'required|accepted',
                        'own_rights' => 'required|accepted',
                    ]);

                    $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $artistRole = Role::where('slug', 'artist')->first();
                    $roleId = $artistRole ? $artistRole->id : 3;

                    $user = User::create([
                        'name'              => $request->name,
                        'email'             => $request->email,
                        'mobile_number'     => $request->mobile,
                        'user_type'         => $roleId,
                        'password'          => bcrypt($request->password),
                        'verification_code' => $otp,
                        'registration_ip'   => $request->ip(),
                        'completed_steps'   => 1,
                        // NOTE: 'pin' is only varchar(10) — DO NOT store country here
                    ]);

                    if ($user) {
                        if ($artistRole) {
                            $user->roles()->sync($artistRole);
                        }

                        ArtistProfile::create([
                            'user_id'      => $user->id,
                            'display_name' => $request->stage_name,
                            'country'      => $request->country,  // stored properly here
                        ]);

                        session(['verify_user_id' => $user->id]);

                        return response([
                            'status' => true,
                            'message' => 'Account created successfully! An OTP has been sent.',
                            'next_step' => 2,
                            'user_id' => $user->id,
                            'otp' => $otp
                        ]);
                    }

                    return response(['status' => false, 'message' => 'Registration failed. Please try again.']);

                case 2:
                    $request->validate([
                        'otp' => 'required|numeric|digits:6',
                        'user_id' => 'required|exists:users,id'
                    ]);

                    $user = User::find($request->user_id);
                    if ($user && $user->verification_code == $request->otp) {
                        $user->update([
                            'verification_code' => null,
                            'email_verified' => 1,
                            'completed_steps' => 2
                        ]);

                        auth()->login($user);
                        session()->forget('verify_user_id');

                        return response([
                            'status' => true,
                            'message' => 'Verification successful! Logging you in...',
                            'next_step' => 3
                        ]);
                    }

                    return response(['status' => false, 'message' => 'Invalid OTP. Please try again.']);

                case 3:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'bio' => 'required|string|max:5000',
                        'primary_genre_id' => 'required|exists:genres,id',
                        'sub_genre_id' => 'nullable|exists:genres,id',
                        'label' => 'nullable|string|max:255',
                        'years_of_active' => 'required|integer|min:0'
                    ]);

                    ArtistProfile::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'bio' => $request->bio,
                            'primary_genre_id' => $request->primary_genre_id,
                            'sub_genre_id' => $request->sub_genre_id,
                            'label' => $request->label,
                            'years_of_active' => $request->years_of_active
                        ]
                    );

                    $user->update(['completed_steps' => 3]);

                    return response([
                        'status' => true,
                        'message' => 'Artist information saved!',
                        'next_step' => 4
                    ]);

                case 4:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'display_name' => 'required|string|max:255',
                        'website' => 'nullable|url|max:255',
                        'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
                        'cover_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
                    ]);

                    $profile = ArtistProfile::firstOrCreate(['user_id' => $user->id]);

                    $profileData = [
                        'display_name' => $request->display_name,
                        'website' => $request->website,
                    ];

                    if ($request->hasFile('profile_image')) {
                        $file = $request->file('profile_image');
                        $filename = time() . '_profile_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('storage/profile'), $filename);
                        $profileData['profile_image'] = $filename;
                        $user->update(['profile_image' => $filename]);
                    }

                    if ($request->hasFile('cover_banner')) {
                        $file = $request->file('cover_banner');
                        $filename = time() . '_banner_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('storage/banner'), $filename);
                        $profileData['cover_banner'] = $filename;
                    }

                    $profile->update($profileData);
                    $user->update(['completed_steps' => 4]);

                    return response([
                        'status' => true,
                        'message' => 'Profile setup completed!',
                        'next_step' => 5
                    ]);

                case 5:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'instagram_url' => 'nullable|url|max:255',
                        'youtube_url' => 'nullable|url|max:255',
                        'tiktok_url' => 'nullable|url|max:255',
                        'facebook_url' => 'nullable|url|max:255',
                        'twitter_url' => 'nullable|url|max:255',
                        'spotify_url' => 'nullable|url|max:255',
                        'apple_music_url' => 'nullable|url|max:255',
                    ]);

                    SocialLink::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'instagram_url' => $request->instagram_url,
                            'youtube_url' => $request->youtube_url,
                            'tiktok_url' => $request->tiktok_url,
                            'facebook_url' => $request->facebook_url,
                            'twitter_url' => $request->twitter_url,
                            'spotify_url' => $request->spotify_url,
                            'apple_music_url' => $request->apple_music_url,
                        ]
                    );

                    $user->update(['completed_steps' => 5]);

                    return response([
                        'status' => true,
                        'message' => 'Social links saved successfully!',
                        'next_step' => 6
                    ]);

                case 6:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $verification = ArtistVerification::firstOrCreate(['user_id' => $user->id]);

                    $rules = [
                        'government_id_front' => ($verification->government_id_front) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
                        'government_id_back' => ($verification->government_id_back) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
                        'selfie_image' => ($verification->selfie_image) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
                    ];

                    $request->validate($rules);

                    $verifData = [];

                    if ($request->hasFile('government_id_front')) {
                        $file = $request->file('government_id_front');
                        $filename = time() . '_id_front_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('storage/verification'), $filename);
                        $verifData['government_id_front'] = $filename;
                    }

                    if ($request->hasFile('government_id_back')) {
                        $file = $request->file('government_id_back');
                        $filename = time() . '_id_back_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('storage/verification'), $filename);
                        $verifData['government_id_back'] = $filename;
                    }

                    if ($request->hasFile('selfie_image')) {
                        $file = $request->file('selfie_image');
                        $filename = time() . '_selfie_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('storage/verification'), $filename);
                        $verifData['selfie_image'] = $filename;
                    }

                    $verifData['verification_status'] = 0;
                    $verification->update($verifData);

                    $user->update(['completed_steps' => 6]);

                    return response([
                        'status' => true,
                        'message' => 'Identity verification documents uploaded!',
                        'next_step' => 7
                    ]);

                case 7:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'favorite_genres' => 'required|array|min:1|max:3',
                        'favorite_genres.*' => 'exists:genres,id',
                        'release_frequency' => 'required|in:1,2,3',
                        'artist_type' => 'required|in:INDEPENDENT,SIGNED',
                    ]);

                    ArtistPreference::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'favorite_genres' => $request->favorite_genres,
                            'release_frequency' => $request->release_frequency,
                            'artist_type' => $request->artist_type,
                        ]
                    );

                    $profile = ArtistProfile::firstOrCreate(['user_id' => $user->id]);
                    $profile->update([
                        'artist_type' => $request->artist_type,
                        'release_frequency' => $request->release_frequency,
                    ]);

                    $settings = \App\Models\Setting::find(1);
                    $artistSubscriptionEnabled = $settings ? $settings->artist_subscription : 0;
                    $nextStep = $artistSubscriptionEnabled ? 8 : 9;

                    $user->update(['completed_steps' => ($nextStep == 9 ? 8 : 7)]);

                    return response([
                        'status' => true,
                        'message' => 'Preferences saved successfully!',
                        'next_step' => $nextStep
                    ]);

                case 8:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'plan' => 'required|exists:subscriptions,id',
                    ]);

                    $settings = \App\Models\Setting::find(1);
                    $artistSubscriptionEnabled = $settings ? $settings->artist_subscription : 0;

                    if ($artistSubscriptionEnabled) {
                        $subscription = \App\Models\Subscription::find($request->plan);

                        if ($subscription && $subscription->price > 0 && $subscription->stripe_price_id) {
                            // Do NOT update completed_steps to 8 yet to ensure they pay!
                            $user->update([
                                'subscription_type' => $request->plan,
                            ]);

                            $existingSub = \Illuminate\Support\Facades\DB::table('user_subscriptions')
                                ->where('user_id', $user->id)
                                ->where('subscription_id', $subscription->id)
                                ->where('status', 1)
                                ->first();

                            if (!$existingSub) {
                                if (!$user->stripe_id) {
                                    $customer = $this->createCustomer($user->email, $user->name);
                                    $user->update(['stripe_id' => $customer->id]);
                                }

                                $sessionUrl = $this->createCheckoutSession(
                                    $user->stripe_id,
                                    $subscription->stripe_price_id,
                                    route('artist.register.checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                                    route('artist.register') . '?reset=0'
                                );

                                return response([
                                    'status' => true,
                                    'message' => 'Redirecting to payment gateway...',
                                    'url' => $sessionUrl->url
                                ]);
                            }
                        }
                    }

                    // Free plan or already paid or disabled
                    $user->update([
                        'subscription_type' => $request->plan,
                        'completed_steps' => 8
                    ]);

                    return response([
                        'status' => true,
                        'message' => 'Subscription plan selected!',
                        'next_step' => 9
                    ]);

                case 9:
                    $user = auth()->user();
                    if (!$user) {
                        return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
                    }

                    $request->validate([
                        'confirm_rights' => 'required|accepted',
                        'agree_wemu_terms' => 'required|accepted',
                        'agree_copyright' => 'required|accepted',
                        'agree_revenue' => 'required|accepted',
                    ]);

                    $settings = \App\Models\Setting::find(1);
                    $artistSubscriptionEnabled = $settings ? $settings->artist_subscription : 0;

                    if ($artistSubscriptionEnabled && $user->subscription_type) {
                        $subscription = \App\Models\Subscription::find($user->subscription_type);

                        if ($subscription) {
                            if ($subscription->price > 0 && $subscription->stripe_price_id) {
                                // Ensure they actually paid
                                $existingSub = \Illuminate\Support\Facades\DB::table('user_subscriptions')
                                    ->where('user_id', $user->id)
                                    ->where('status', 1)
                                    ->first();

                                if (!$existingSub) {
                                    return response(['status' => false, 'message' => 'Please complete your subscription payment first.', 'next_step' => 8]);
                                }
                            } else {
                                // Free subscription
                                $existingSub = \Illuminate\Support\Facades\DB::table('user_subscriptions')
                                    ->where('user_id', $user->id)
                                    ->where('subscription_id', $subscription->id)
                                    ->first();

                                if (!$existingSub) {
                                    \Illuminate\Support\Facades\DB::table('user_subscriptions')->insert([
                                        'uuid' => \Webpatser\Uuid\Uuid::generate(4)->string,
                                        'user_id' => $user->id,
                                        'subscription_id' => $subscription->id,
                                        'status' => 1,
                                        'started_on' => now(),
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                        }
                    }

                    $user->update(['completed_steps' => 9]);

                    return response([
                        'status' => true,
                        'message' => 'Registration completed successfully!',
                        'next_step' => 10
                    ]);
            }
        }
    }

    public function otpVerify(Request $request)
    {
        if ($request->post()) {
            if ($request->input('action') === 'resend') {
                $userId = session('verify_user_id');
                if (!$userId) {
                    return response(['status' => false, 'message' => 'Session expired', 'data' => null, 'url' => route('artist.login')]);
                }
                $user = User::find($userId);
                if ($user) {
                    $newOtp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                    $user->update(['verification_code' => $newOtp]);
                    return response(['status' => true, 'message' => 'A new verification code has been sent!', 'otp' => $newOtp]);
                }
                return response(['status' => false, 'message' => 'User not found.']);
            }

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
        $user = auth()->user();
        if ($user && $user->user_type == 3) {
            if ($user->added_by) {
                // Fall through to show dashboard
            } elseif ($user->completed_steps < 9) {
                return redirect()->route('artist.register');
            } elseif ($user->is_approve == 0) {
                $verification = ArtistVerification::where('user_id', $user->id)->first();
                if ($verification && $verification->verification_status == 2) {
                    return view('auth.reverify', compact('user', 'verification'));
                }
                return view('auth.pending');
            }
        }

        $userId = $user ? $user->id : 0;
        $mainArtistId = $user && $user->added_by ? $user->added_by : $userId;
        $teamIds = User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

        $recentReleases = Album::whereIn('user_id', $teamIds)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $audienceLocations = AudienceLog::whereIn('artist_id', $teamIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotNull('country')
            ->select('country', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $totalAudience = AudienceLog::whereIn('artist_id', $teamIds)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereNotNull('country')
            ->count();

        $streamsData = StreamLog::whereIn('artist_id', $teamIds)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->select(\Illuminate\Support\Facades\DB::raw('DATE(created_at) as date'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $chartDates = [];
        $chartStreams = [];
        for ($i = 6; $i >= 0; $i--) {
            $dateStr = now()->subDays($i)->format('Y-m-d');
            $chartDates[] = now()->subDays($i)->format('M d');
            $chartStreams[] = $streamsData->get($dateStr, 0);
        }

        $topSongs = StreamLog::whereIn('artist_id', $teamIds)
            ->select('song_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_plays'))
            ->groupBy('song_id')
            ->orderBy('total_plays', 'desc')
            ->take(5)
            ->with('song')
            ->get();

        // Top Metrics
        $calcMetric = function ($model, $teamIds, $isDistinctCountry = false) {
            $query = clone $model;
            $query = $query->whereIn('artist_id', $teamIds);

            $currentMonthQuery = (clone $query)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            $lastMonthQuery = (clone $query)->whereMonth('created_at', now()->subMonth()->month)->whereYear('created_at', now()->subMonth()->year);

            if ($isDistinctCountry) {
                $currentMonth = $currentMonthQuery->whereNotNull('country')->distinct('country')->count('country');
                $lastMonth = $lastMonthQuery->whereNotNull('country')->distinct('country')->count('country');
            } else {
                $currentMonth = $currentMonthQuery->count();
                $lastMonth = $lastMonthQuery->count();
            }

            if ($lastMonth > 0) {
                $growth = (($currentMonth - $lastMonth) / $lastMonth) * 100;
            } elseif ($currentMonth > 0) {
                $growth = 100;
            } else {
                $growth = 0;
            }

            if ($growth > 100) {
                $growth = 100;
            }

            $formatNumber = function ($number) {
                if ($number >= 1000000) return round($number / 1000000, 1) . 'M';
                if ($number >= 1000) return round($number / 1000, 1) . 'K';
                return $number;
            };

            return [
                'total' => $formatNumber($currentMonth),
                'trend' => number_format($growth, 1),
                'is_positive' => $growth >= 0
            ];
        };

        $metrics = [
            'streams' => $calcMetric(new StreamLog, $teamIds),
            'listeners' => $calcMetric(new AudienceLog, $teamIds),
            'followers' => $calcMetric(new ArtistFollower, $teamIds),
            'countries' => $calcMetric(new AudienceLog, $teamIds, true),
        ];

        return view('artist.dashboard', compact('recentReleases', 'audienceLocations', 'totalAudience', 'chartDates', 'chartStreams', 'topSongs', 'metrics'));
    }

    public function reverifySubmit(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response(['status' => false, 'message' => 'Session expired. Please log in.', 'url' => route('artist.login')]);
        }

        $verification = ArtistVerification::where('user_id', $user->id)->first();
        if (!$verification) {
            return response(['status' => false, 'message' => 'Verification record not found.']);
        }

        $rules = [
            'government_id_front' => ($verification->government_id_front) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
            'government_id_back' => ($verification->government_id_back) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
            'selfie_image' => ($verification->selfie_image) ? 'nullable|image|mimes:jpeg,png,jpg|max:5120' : 'required|image|mimes:jpeg,png,jpg|max:5120',
        ];

        $request->validate($rules);

        $verifData = [];

        if ($request->hasFile('government_id_front')) {
            $file = $request->file('government_id_front');
            $filename = time() . '_id_front_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/verification'), $filename);
            $verifData['government_id_front'] = $filename;
        }

        if ($request->hasFile('government_id_back')) {
            $file = $request->file('government_id_back');
            $filename = time() . '_id_back_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/verification'), $filename);
            $verifData['government_id_back'] = $filename;
        }

        if ($request->hasFile('selfie_image')) {
            $file = $request->file('selfie_image');
            $filename = time() . '_selfie_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/verification'), $filename);
            $verifData['selfie_image'] = $filename;
        }

        $verifData['verification_status'] = 0;
        $verifData['rejection_reason'] = null;
        $verification->update($verifData);

        return response([
            'status' => true,
            'message' => 'Documents uploaded successfully! Awaiting admin approval.',
            'url' => route('artist.dashboard')
        ]);
    }

    public function checkoutSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        if (!$sessionId) {
            return redirect()->route('artist.register');
        }

        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        try {
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            $user = auth()->user();
            if (!$user) {
                // Recover user if session cookie was lost during redirect
                $user = \App\Models\User::where('stripe_id', $session->customer)->first();
                if ($user) {
                    auth()->login($user);
                } else {
                    return redirect()->route('artist.login')->with('error', 'Session expired. Please log in again.');
                }
            }

            if ($session->payment_status === 'paid' || $session->status === 'complete') {
                $subscriptionId = $session->subscription; // This is the Stripe subscription ID (sub_xxx)

                // Fetch the actual subscription from Stripe to get period dates
                $stripeSubscription = $stripe->subscriptions->retrieve($subscriptionId);

                // Get the local subscription record
                $localSubscription = \App\Models\Subscription::find($user->subscription_type);

                if ($localSubscription) {
                    $existing = \Illuminate\Support\Facades\DB::table('user_subscriptions')
                        ->where('stripe_id', $subscriptionId)
                        ->first();

                    if (!$existing) {
                        \Illuminate\Support\Facades\DB::table('user_subscriptions')->insert([
                            'uuid' => \Webpatser\Uuid\Uuid::generate(4)->string,
                            'user_id' => $user->id,
                            'subscription_id' => $localSubscription->id,
                            'stripe_id' => $subscriptionId,
                            'stripe_status' => $stripeSubscription->status,
                            'trial_ends_at' => $stripeSubscription->trial_end ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->trial_end) : null,
                            'current_period_start' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_start),
                            'current_period_end' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
                            'ends_at' => $stripeSubscription->cancel_at ? \Carbon\Carbon::createFromTimestamp($stripeSubscription->cancel_at) : null,
                            'status' => in_array($stripeSubscription->status, ['active', 'trialing']) ? 1 : 0,
                            'transaction_id' => $session->payment_intent ?? $session->invoice ?? $sessionId,
                            'started_on' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        // Try to get payment intent or invoice
                        $transactionId = $session->payment_intent ?? $session->invoice ?? $sessionId;

                        \Illuminate\Support\Facades\DB::table('transactions')->insert([
                            'user_id' => $user->id,
                            'transaction_id' => $transactionId,
                            'payment_type' => 'stripe',
                            'amount' => $session->amount_total / 100,
                            'currency' => strtoupper($session->currency),
                            'payment_status' => 'completed',
                            'payment_details' => json_encode($session->toArray()),
                            'description' => 'Subscription payment for ' . $localSubscription->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $user->update(['completed_steps' => 8]);
                return redirect()->route('artist.register')->with('success', 'Payment successful! Please accept the terms to complete registration.');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Stripe Checkout verification failed: ' . $e->getMessage());
        }

        return redirect()->route('artist.register')->with('error', 'Payment verification failed. Please try again.');
    }

    public function logout()
    {
        auth()->logout();
        session()->flush();
        return redirect()->route('artist.login');
    }
}
