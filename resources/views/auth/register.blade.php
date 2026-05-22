<!DOCTYPE html>
<html lang="en" class="register-page-html">

<head>
    <title>WEMU - Artist Registration</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" />
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('assets/css/cdn/toastr.css') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}?v={{ time() }}">
</head>

<body class="register-page-body">
    <div class="bg-mesh"></div>

    <div class="page-wrapper">
        <div class="wizard-container">

            <!-- Brand -->
            <div class="brand-header">
                <div class="brand-title">WEMU</div>
                <div class="brand-subtitle">Artist Onboarding Portal</div>
            </div>

            <!-- 10-Step Stepper -->
            <div class="stepper-wrap" id="stepperWrap">
                <div class="stepper-row">
                    @php
                        $stepLabels = [1=>'Signup',2=>'Verify',3=>'Info',4=>'Profile',5=>'Socials',6=>'ID Verif',7=>'Prefs',8=>'Plan',9=>'Terms',10=>'Done'];
                    @endphp
                    @for ($i = 1; $i <= 10; $i++)
                        <div class="step-node" id="node-{{ $i }}" data-s="{{ $i }}">
                            <div class="step-circle">{{ $i }}</div>
                            <div class="step-lbl">{{ $stepLabels[$i] }}</div>
                        </div>
                        @if ($i < 10)
                            <div class="step-line" id="line-{{ $i }}"></div>
                        @endif
                    @endfor
                </div>
            </div>

            <!-- Card -->
            <div class="wizard-card">
                <form id="regForm" action="{{ route('artist.register') }}" method="POST" enctype="multipart/form-data" autocomplete="off" novalidate>
                    @csrf
                    <input type="hidden" name="step" id="stepInput" value="{{ $activeStep ?? 1 }}">
                    <input type="hidden" name="user_id" id="userIdInput"
                        value="{{ isset($user) ? $user->id : (isset($verifyUser) ? $verifyUser->id : '') }}">

                    <!-- ╔══════════════════════════════════════╗
                         ║  SCROLLABLE FORM CONTENT AREA        ║
                         ╚══════════════════════════════════════╝ -->
                    <div class="steps-scroll" id="stepsScrollArea">

                    {{-- ==============================
                         STEP 1: SIGNUP
                         ============================== --}}
                    <div class="step-section" id="sec-1" data-step="1">
                        <h2 class="step-heading">Create your artist account</h2>
                        <p class="step-sub">Fill in your details to join the WEMU artist community</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Artist / Stage Name <span class="req">*</span></label>
                                    <input type="text" class="wemu-input" name="stage_name" placeholder="e.g. DJ Phantom"
                                        value="{{ isset($profile) ? $profile->display_name : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Full Legal Name <span class="req">*</span></label>
                                    <input type="text" class="wemu-input" name="name" placeholder="e.g. John Smith"
                                        value="{{ isset($user) ? $user->name : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Email Address <span class="req">*</span></label>
                                    <input type="email" class="wemu-input" name="email" placeholder="artist@example.com"
                                        value="{{ isset($user) ? $user->email : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Mobile Number <span class="req">*</span></label>
                                    <input type="text" class="wemu-input" name="mobile" placeholder="+1 234 567 8900"
                                        value="{{ isset($user) ? $user->mobile_number : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Password <span class="req">*</span></label>
                                    <div class="pwd-wrap">
                                        <input type="password" class="wemu-input" name="password" id="regPwd" placeholder="Min. 6 characters" />
                                        <button type="button" class="pwd-toggle" onclick="togglePwd('regPwd','eyeReg')">
                                            <i class="fas fa-eye" id="eyeReg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Confirm Password <span class="req">*</span></label>
                                    <div class="pwd-wrap">
                                        <input type="password" class="wemu-input" name="password_confirmation" id="regPwdConf" placeholder="Repeat password" />
                                        <button type="button" class="pwd-toggle" onclick="togglePwd('regPwdConf','eyeConf')">
                                            <i class="fas fa-eye" id="eyeConf"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="field-group">
                                    <label class="field-label">Country <span class="req">*</span></label>
                                    <select class="wemu-input" name="country">
                                        <option value="">Select country…</option>
                                        @foreach([
                                            'Australia','United States','United Kingdom',
                                            'India','Canada','New Zealand','South Africa',
                                            'Germany','France','Brazil','Nigeria','Kenya'
                                        ] as $c)
                                            <option value="{{ $c }}"
                                                {{ (isset($user) && $user->pin == $c) ? 'selected' : '' }}>
                                                {{ $c }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:20px; margin-bottom:18px;">
                            <label class="chk-wrap">
                                <input type="checkbox" name="agree_terms" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I agree to the <a href="#" style="color:#a5b4fc;">Terms &amp; Conditions</a> of WEMU</span>
                            </label>
                            <label class="chk-wrap">
                                <input type="checkbox" name="own_rights" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I confirm that I own or have licensed the rights to music I upload</span>
                            </label>
                        </div>

                        <div class="divider-or">or continue with</div>
                        <div class="oauth-row">
                            <a href="javascript:void(0)" class="oauth-btn">
                                <i class="fab fa-google" style="color:#ea4335;"></i> Google
                            </a>
                            <a href="javascript:void(0)" class="oauth-btn">
                                <i class="fab fa-apple" style="color:#fff;"></i> Apple
                            </a>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 2: OTP VERIFICATION
                         ============================== --}}
                    <div class="step-section" id="sec-2" data-step="2">
                        <h2 class="step-heading">Verify your account</h2>
                        <p class="step-sub" id="otpDesc">We've sent a 6-digit code to your email address. Enter it below to continue.</p>

                        <div style="text-align:center; margin-bottom:10px;">
                            <div style="width:64px;height:64px;border-radius:50%;background:rgba(168,85,247,0.12);border:2px solid rgba(168,85,247,0.3);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                                <i class="fas fa-shield-alt" style="font-size:26px;color:#a855f7;"></i>
                            </div>
                        </div>

                        <div class="otp-wrapper" id="otpBoxes">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                            <input class="otp-digit" type="text" maxlength="1" pattern="[0-9]*" inputmode="numeric" autocomplete="off">
                        </div>
                        <input type="hidden" name="otp" id="otpHidden">

                        <div class="timer-row">
                            <span id="timerText">Resend code in <strong id="timerCount">00:30</strong></span>
                            <a id="resendBtn" class="disabled" onclick="resendOtp()">Resend</a>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 3: ARTIST INFO
                         ============================== --}}
                    <div class="step-section" id="sec-3" data-step="3">
                        <h2 class="step-heading">Artist Information</h2>
                        <p class="step-sub">Tell the world about your music and background</p>

                        <div class="field-group">
                            <label class="field-label">Artist Bio <span class="req">*</span></label>
                            <textarea class="wemu-input" name="bio" placeholder="Write about your music, influences, and journey as an artist...">{{ isset($profile) ? $profile->bio : '' }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Primary Genre <span class="req">*</span></label>
                                    <select class="wemu-input" name="primary_genre_id" id="primaryGenre">
                                        <option value="">Select genre…</option>
                                        @foreach ($genres->whereNull('parent_id') as $g)
                                            <option value="{{ $g->id }}"
                                                {{ (isset($profile) && $profile->primary_genre_id == $g->id) ? 'selected' : '' }}>
                                                {{ $g->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Sub-Genre <span style="color:rgba(255,255,255,0.35);font-weight:400;">(optional)</span></label>
                                    <select class="wemu-input" name="sub_genre_id" id="subGenre">
                                        <option value="">Select sub-genre…</option>
                                        @foreach ($genres->whereNotNull('parent_id') as $g)
                                            <option value="{{ $g->id }}" data-parent="{{ $g->parent_id }}"
                                                {{ (isset($profile) && $profile->sub_genre_id == $g->id) ? 'selected' : '' }}>
                                                {{ $g->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Label / Brand <span style="color:rgba(255,255,255,0.35);font-weight:400;">(optional)</span></label>
                                    <input type="text" class="wemu-input" name="label" placeholder="e.g. Sony Music"
                                        value="{{ isset($profile) ? $profile->label : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Years Active <span class="req">*</span></label>
                                    <select class="wemu-input" name="years_of_active">
                                        <option value="">Select…</option>
                                        @for ($y = 0; $y <= 30; $y++)
                                            <option value="{{ $y }}"
                                                {{ (isset($profile) && $profile->years_of_active == $y) ? 'selected' : '' }}>
                                                {{ $y == 0 ? 'Less than 1 year' : ($y == 1 ? '1 year' : $y . ' years') }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 4: PROFILE SETUP
                         ============================== --}}
                    <div class="step-section" id="sec-4" data-step="4">
                        <h2 class="step-heading">Profile Setup</h2>
                        <p class="step-sub">Customize how your profile appears to listeners on WEMU</p>

                        <div class="row g-4" style="margin-bottom:28px;">
                            <div class="col-md-5 text-center">
                                <label class="field-label" style="display:block;margin-bottom:12px;">Profile Photo</label>
                                <div class="dropzone dropzone-circle" id="dzProfile">
                                    <input type="file" name="profile_image" accept="image/*"
                                        onchange="dzPreview(this,'prevProfile','dzProfile')">
                                    <div class="dz-inner">
                                        <i class="fas fa-camera dz-icon"></i>
                                        <div class="dz-label">Upload Photo</div>
                                        <div class="dz-hint">JPG, PNG · Max 5MB</div>
                                    </div>
                                    <img id="prevProfile" alt="" class="dz-preview"
                                        @if(isset($profile) && $profile->profile_image)
                                            src="{{ asset('storage/profile/'.$profile->profile_image) }}"
                                            style="display:block;"
                                        @else
                                            style="display:none;"
                                        @endif>
                                    <div class="dz-overlay">
                                        <i class="fas fa-pencil-alt" style="font-size:20px;"></i>
                                        <span>Change</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <label class="field-label" style="display:block;margin-bottom:12px;">Cover Banner</label>
                                <div class="dropzone dropzone-rect" id="dzBanner">
                                    <input type="file" name="cover_banner" accept="image/*"
                                        onchange="dzPreview(this,'prevBanner','dzBanner')">
                                    <div class="dz-inner">
                                        <i class="fas fa-image dz-icon"></i>
                                        <div class="dz-label">Upload Cover Banner</div>
                                        <div class="dz-hint">JPG, PNG · Max 10MB · 1500×500 recommended</div>
                                    </div>
                                    <img id="prevBanner" alt="" class="dz-preview"
                                        @if(isset($profile) && $profile->cover_banner)
                                            src="{{ asset('storage/banner/'.$profile->cover_banner) }}"
                                            style="display:block;"
                                        @else
                                            style="display:none;"
                                        @endif>
                                    <div class="dz-overlay">
                                        <i class="fas fa-pencil-alt" style="font-size:20px;"></i>
                                        <span>Change</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Display Name <span class="req">*</span></label>
                                    <input type="text" class="wemu-input" name="display_name" placeholder="How fans see your name"
                                        value="{{ isset($profile) ? $profile->display_name : '' }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field-group">
                                    <label class="field-label">Website <span style="color:rgba(255,255,255,0.35);font-weight:400;">(optional)</span></label>
                                    <input type="text" class="wemu-input" name="website" placeholder="https://yoursite.com"
                                        value="{{ isset($profile) ? $profile->website : '' }}" />
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 5: SOCIAL LINKS
                         ============================== --}}
                    <div class="step-section" id="sec-5" data-step="5">
                        <h2 class="step-heading">Social Links</h2>
                        <p class="step-sub">Connect your social channels so fans can follow you everywhere</p>

                        @php
                            $sLinks = [
                                ['name'=>'instagram_url','icon'=>'fa-instagram','cls'=>'c-instagram','ph'=>'https://instagram.com/yourname'],
                                ['name'=>'youtube_url','icon'=>'fa-youtube','cls'=>'c-youtube','ph'=>'https://youtube.com/c/yourchannel'],
                                ['name'=>'tiktok_url','icon'=>'fa-tiktok','cls'=>'c-tiktok','ph'=>'https://tiktok.com/@yourname'],
                                ['name'=>'facebook_url','icon'=>'fa-facebook-f','cls'=>'c-facebook','ph'=>'https://facebook.com/yourpage'],
                                ['name'=>'twitter_url','icon'=>'fa-twitter','cls'=>'c-twitter','ph'=>'https://twitter.com/yourhandle'],
                                ['name'=>'spotify_url','icon'=>'fa-spotify','cls'=>'c-spotify','ph'=>'https://open.spotify.com/artist/id'],
                                ['name'=>'apple_music_url','icon'=>'fa-apple','cls'=>'c-apple','ph'=>'https://music.apple.com/artist/name'],
                            ];
                        @endphp

                        @foreach($sLinks as $sl)
                            <div class="social-row">
                                <div class="social-icon-col {{ $sl['cls'] }}">
                                    <i class="fab {{ $sl['icon'] }}"></i>
                                </div>
                                <input type="text" name="{{ $sl['name'] }}"
                                    placeholder="{{ $sl['ph'] }}"
                                    value="{{ isset($socials) ? $socials->{$sl['name']} : '' }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- ==============================
                         STEP 6: IDENTITY VERIFICATION
                         ============================== --}}
                    <div class="step-section" id="sec-6" data-step="6">
                        <h2 class="step-heading">Identity Verification</h2>
                        <p class="step-sub">Secure verification helps protect you and the WEMU community</p>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="field-label" style="display:block;margin-bottom:10px;">
                                    Government ID — Front <span class="req">*</span>
                                </label>
                                <div class="dropzone dropzone-rect" id="dzIdFront">
                                    <input type="file" name="government_id_front" accept="image/*"
                                        onchange="dzPreview(this,'prevIdFront','dzIdFront')">
                                    <div class="dz-inner">
                                        <i class="fas fa-id-card dz-icon"></i>
                                        <div class="dz-label">Upload Front</div>
                                        <div class="dz-hint">Clear, unobstructed photo</div>
                                    </div>
                                    <img id="prevIdFront" alt="" class="dz-preview"
                                        @if(isset($verification) && $verification->government_id_front)
                                            src="{{ asset('storage/verification/'.$verification->government_id_front) }}"
                                            style="display:block;"
                                        @else
                                            style="display:none;"
                                        @endif>
                                    <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label" style="display:block;margin-bottom:10px;">
                                    Government ID — Back <span class="req">*</span>
                                </label>
                                <div class="dropzone dropzone-rect" id="dzIdBack">
                                    <input type="file" name="government_id_back" accept="image/*"
                                        onchange="dzPreview(this,'prevIdBack','dzIdBack')">
                                    <div class="dz-inner">
                                        <i class="fas fa-id-card-alt dz-icon"></i>
                                        <div class="dz-label">Upload Back</div>
                                        <div class="dz-hint">Clear, unobstructed photo</div>
                                    </div>
                                    <img id="prevIdBack" alt="" class="dz-preview"
                                        @if(isset($verification) && $verification->government_id_back)
                                            src="{{ asset('storage/verification/'.$verification->government_id_back) }}"
                                            style="display:block;"
                                        @else
                                            style="display:none;"
                                        @endif>
                                    <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="field-label" style="display:block;margin-bottom:10px;">
                                    Selfie Holding ID <span class="req">*</span>
                                </label>
                                <div class="dropzone dropzone-rect" id="dzSelfie">
                                    <input type="file" name="selfie_image" accept="image/*"
                                        onchange="dzPreview(this,'prevSelfie','dzSelfie')">
                                    <div class="dz-inner">
                                        <i class="fas fa-portrait dz-icon"></i>
                                        <div class="dz-label">Upload Selfie</div>
                                        <div class="dz-hint">Face and ID must both be clearly visible</div>
                                    </div>
                                    <img id="prevSelfie" alt="" class="dz-preview"
                                        @if(isset($verification) && $verification->selfie_image)
                                            src="{{ asset('storage/verification/'.$verification->selfie_image) }}"
                                            style="display:block;"
                                        @else
                                            style="display:none;"
                                        @endif>
                                    <div class="dz-overlay"><i class="fas fa-pencil-alt" style="font-size:18px;"></i><span>Change</span></div>
                                </div>
                                <p style="text-align:center;font-size:12px;color:rgba(255,255,255,0.35);margin-top:10px;">JPG or PNG only · Max 5MB per file</p>
                            </div>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 7: MUSIC PREFERENCES
                         ============================== --}}
                    <div class="step-section" id="sec-7" data-step="7">
                        <h2 class="step-heading">Music Preferences</h2>
                        <p class="step-sub">Help us personalize your WEMU experience and reach</p>

                        <div style="margin-bottom:32px;">
                            <label class="field-label" style="display:block;margin-bottom:14px;">
                                Favorite Genres <span class="req">*</span>
                                <span style="font-weight:400;color:rgba(255,255,255,0.4);font-size:12px;"> — pick up to 3</span>
                            </label>
                            <div class="pills-wrap" id="genrePills">
                                @foreach ($genres->whereNull('parent_id') as $g)
                                    @php
                                        $isFav = false;
                                        if (isset($preferences) && $preferences->favorite_genres) {
                                            $fa = is_string($preferences->favorite_genres)
                                                ? json_decode($preferences->favorite_genres, true)
                                                : (array)$preferences->favorite_genres;
                                            $isFav = in_array($g->id, $fa ?? []);
                                        }
                                    @endphp
                                    <div class="g-pill {{ $isFav ? 'chosen' : '' }}" data-id="{{ $g->id }}">{{ $g->title }}</div>
                                @endforeach
                            </div>
                            <div class="pills-counter">Selected: <span id="pillCount">{{ isset($preferences) && $preferences->favorite_genres ? count(is_string($preferences->favorite_genres) ? json_decode($preferences->favorite_genres,true) : (array)$preferences->favorite_genres) : 0 }}</span> / 3</div>
                            <div id="pillsHidden">
                                @if (isset($preferences) && $preferences->favorite_genres)
                                    @php $fa2 = is_string($preferences->favorite_genres) ? json_decode($preferences->favorite_genres, true) : (array)$preferences->favorite_genres; @endphp
                                    @foreach (($fa2 ?? []) as $fid)
                                        <input type="hidden" name="favorite_genres[]" value="{{ $fid }}" id="hg-{{ $fid }}">
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="field-label" style="display:block;margin-bottom:14px;">Release Frequency <span class="req">*</span></label>
                                <div style="display:flex;flex-direction:column;gap:14px;">
                                    @foreach([1=>'Weekly — every week',2=>'Monthly — once a month',3=>'Occasionally — a few times a year'] as $val=>$lbl)
                                        <label class="chk-wrap radio-wrap">
                                            <input type="radio" name="release_frequency" value="{{ $val }}"
                                                {{ (isset($profile) && $profile->release_frequency == $val) || ($val == 1 && !isset($profile)) ? 'checked' : '' }}>
                                            <span class="chk-box"><span class="chk-icon"></span></span>
                                            <span class="chk-text">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="field-label" style="display:block;margin-bottom:14px;">Artist Type <span class="req">*</span></label>
                                <div style="display:flex;flex-direction:column;gap:14px;">
                                    @foreach(['INDEPENDENT'=>'Independent Artist','SIGNED'=>'Signed to a Label'] as $val=>$lbl)
                                        <label class="chk-wrap radio-wrap">
                                            <input type="radio" name="artist_type" value="{{ $val }}"
                                                {{ (isset($profile) && $profile->artist_type == $val) || ($val == 'INDEPENDENT' && !isset($profile)) ? 'checked' : '' }}>
                                            <span class="chk-box"><span class="chk-icon"></span></span>
                                            <span class="chk-text">{{ $lbl }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 8: CHOOSE YOUR PLAN
                         ============================== --}}
                    <div class="step-section" id="sec-8" data-step="8">
                        <h2 class="step-heading">Choose Your Plan</h2>
                        <p class="step-sub">Select the plan that fits your publishing needs — you can upgrade anytime</p>

                        @php
                            $curPlan = isset($user) && !empty($user->subscription_type) ? $user->subscription_type : 'free';
                        @endphp

                        <div class="plan-list">
                            <!-- Free -->
                            <div class="plan-item {{ $curPlan == 'free' ? 'chosen' : '' }}" id="pi-free" onclick="pickPlan('free')">
                                <div class="plan-radio-dot"></div>
                                <div class="plan-info">
                                    <div class="plan-title">Free</div>
                                    <div class="plan-desc">Perfect for getting started on WEMU</div>
                                    <div class="plan-feats">
                                        <span><i class="fas fa-check"></i> Up to 5 tracks</span>
                                        <span><i class="fas fa-check"></i> Basic analytics</span>
                                        <span><i class="fas fa-check"></i> Community access</span>
                                    </div>
                                </div>
                                <div class="plan-price-col">
                                    <span class="plan-price">$0</span>
                                    <span class="plan-period">/ month</span>
                                </div>
                            </div>

                            <!-- Pro (highlighted) -->
                            <div class="plan-item plan-item-pro {{ $curPlan == 'pro' ? 'chosen' : '' }}" id="pi-pro" onclick="pickPlan('pro')">
                                <div class="plan-radio-dot"></div>
                                <div class="plan-info">
                                    <div class="plan-title">Pro <span class="plan-badge">Recommended</span></div>
                                    <div class="plan-desc">For serious independent artists and producers</div>
                                    <div class="plan-feats">
                                        <span><i class="fas fa-check"></i> Unlimited tracks</span>
                                        <span><i class="fas fa-check"></i> Advanced analytics</span>
                                        <span><i class="fas fa-check"></i> Promotion tools</span>
                                        <span><i class="fas fa-check"></i> Priority support</span>
                                    </div>
                                </div>
                                <div class="plan-price-col">
                                    <span class="plan-price">$99</span>
                                    <span class="plan-period">/ month</span>
                                </div>
                            </div>

                            <!-- Label -->
                            <div class="plan-item {{ $curPlan == 'label' ? 'chosen' : '' }}" id="pi-label" onclick="pickPlan('label')">
                                <div class="plan-radio-dot"></div>
                                <div class="plan-info">
                                    <div class="plan-title">Label</div>
                                    <div class="plan-desc">For labels and multi-artist organizations</div>
                                    <div class="plan-feats">
                                        <span><i class="fas fa-check"></i> Manage multiple artists</span>
                                        <span><i class="fas fa-check"></i> Team access &amp; roles</span>
                                        <span><i class="fas fa-check"></i> Dedicated account manager</span>
                                    </div>
                                </div>
                                <div class="plan-price-col">
                                    <span class="plan-price">$499</span>
                                    <span class="plan-period">/ month</span>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="plan" id="planInput" value="{{ $curPlan }}">
                    </div>

                    {{-- ==============================
                         STEP 9: TERMS & AGREEMENT
                         ============================== --}}
                    <div class="step-section" id="sec-9" data-step="9">
                        <h2 class="step-heading">Terms &amp; Agreement</h2>
                        <p class="step-sub">Please review and accept the following policies before completing registration</p>

                        <div style="display:flex;flex-direction:column;gap:20px;margin-top:8px;">
                            <label class="chk-wrap">
                                <input type="checkbox" name="confirm_rights" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I confirm that I own or have obtained the necessary rights to all music content I publish on WEMU.</span>
                            </label>
                            <label class="chk-wrap">
                                <input type="checkbox" name="agree_wemu_terms" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I have read and agree to the <a href="#" style="color:#a5b4fc;">WEMU Terms &amp; Conditions</a> and <a href="#" style="color:#a5b4fc;">Privacy Policy</a>.</span>
                            </label>
                            <label class="chk-wrap">
                                <input type="checkbox" name="agree_copyright" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I agree to the WEMU Copyright and Content Originality Policy, understanding that violations may result in account suspension.</span>
                            </label>
                            <label class="chk-wrap">
                                <input type="checkbox" name="agree_revenue" value="1">
                                <span class="chk-box"></span>
                                <span class="chk-text">I agree to the Revenue Sharing and Stream Royalty Distribution policy as outlined in the artist agreement.</span>
                            </label>
                        </div>
                    </div>

                    {{-- ==============================
                         STEP 10: SUCCESS
                         ============================== --}}
                    <div class="step-section" id="sec-10" data-step="10" style="text-align:center;padding:20px 0;">
                        <div class="success-ring">
                            <i class="fas fa-check"></i>
                        </div>
                        <h2 class="step-heading" style="margin-bottom:12px;">Welcome to WEMU!</h2>
                        <p class="step-sub" style="max-width:460px;margin:0 auto 32px;">
                            Your artist account is ready. Our team will verify your uploaded documents shortly.
                            You can now explore your dashboard or upload your first track.
                        </p>
                        <div style="display:flex;flex-direction:column;gap:14px;max-width:340px;margin:0 auto;">
                            <a href="{{ route('artist.dashboard') }}" class="btn-wemu btn-primary-grad" style="width:100%;justify-content:center;">
                                <i class="fas fa-th-large"></i> Go to Dashboard
                            </a>
                            <a href="{{ route('artist.songs.storeOrUpdate') }}" class="btn-wemu btn-ghost" style="width:100%;justify-content:center;">
                                <i class="fas fa-music"></i> Upload First Song
                            </a>
                        </div>
                    </div>{{-- /sec-10 --}}

                    </div>{{-- /steps-scroll --}}

                    <!-- ═══════════════════════════════════════
                         CARD FOOTER — Always visible, never scrolls
                         ═══════════════════════════════════════ -->
                    <div class="card-footer" id="wizFooter">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:16px;">
                            <button type="button" class="btn-wemu btn-ghost" id="btnPrev" onclick="prevStep()">
                                <i class="fas fa-arrow-left"></i> Previous
                            </button>
                            <button type="submit" class="btn-wemu btn-primary-grad" id="btnNext">
                                <span class="btn-label">Continue <i class="fas fa-arrow-right"></i></span>
                                <span class="btn-spinner">
                                    <span class="spinner-border spinner-border-sm"></span> Please wait…
                                </span>
                            </button>
                        </div>
                        <div class="back-link" id="backLink" style="margin-top:14px;">
                            <a href="{{ route('artist.login') }}">
                                <i class="fas fa-arrow-left"></i> Already have an account? Sign In
                            </a>
                        </div>
                    </div>{{-- /card-footer --}}

                </form>
            </div>{{-- /wizard-card --}}

        </div>{{-- /wizard-container --}}
    </div>{{-- /page-wrapper --}}

    <!-- Preloader -->
    <div id="preloader"><div class="spin-ring"></div></div>

    <script>var baseUrl="{{ url('/') }}";</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/toastr.js') }}"></script>

    <script>
    /* ============================================================
       WEMU Artist Registration Wizard — JS
    ============================================================ */
    var currentStep = parseInt("{{ $activeStep ?? 1 }}") || 1;
    var otpTimer    = null;

    /* ---- Boot ---- */
    $(window).on('load', function() {
        $('#preloader').fadeOut(350);
        goStep(currentStep);
    });

    /* ---- Step navigation ---- */
    function goStep(n) {
        currentStep = n;
        $('#stepInput').val(n);

        // Show correct section
        $('.step-section').removeClass('is-active');
        $('#sec-' + n).addClass('is-active');

        // Stepper nodes
        for (var i = 1; i <= 10; i++) {
            var $node = $('#node-' + i);
            var $line = $('#line-' + i);
            $node.removeClass('is-active is-done');
            $line.removeClass('is-done');
            if (i === n)           $node.addClass('is-active');
            else if (i < n)      { $node.addClass('is-done'); if ($line.length) $line.addClass('is-done'); }
        }

        // Footer buttons visibility
        if (n === 10) {
            // On success screen: hide prev/next buttons but keep footer (contains back link)
            $('#btnPrev').hide();
            $('#btnNext').hide();
            $('#backLink').hide();
            $('#stepperWrap').hide();
        } else {
            $('#btnPrev').show();
            $('#btnNext').show();
            $('#stepperWrap').show();
            $('#btnPrev').css('visibility', (n === 1) ? 'hidden' : 'visible');
            $('#backLink').toggle(n === 1);
        }

        // Step-specific triggers
        if (n === 2) startTimer();

        // Scroll the content area back to top (not the page)
        var scrollEl = document.getElementById('stepsScrollArea');
        if (scrollEl) scrollEl.scrollTop = 0;
    }

    function prevStep() {
        if (currentStep > 1) goStep(currentStep - 1);
    }

    /* ---- Password toggle ---- */
    function togglePwd(inputId, iconId) {
        var inp = document.getElementById(inputId);
        var ico = document.getElementById(iconId);
        if (inp.type === 'password') {
            inp.type = 'text';
            ico.className = 'fas fa-eye-slash';
        } else {
            inp.type = 'password';
            ico.className = 'fas fa-eye';
        }
    }

    /* ---- Dropzone file preview ---- */
    function dzPreview(input, previewId, dropzoneId) {
        var f = input.files[0];
        if (!f) return;
        var reader = new FileReader();
        reader.onload = function(e) {
            var $dz     = $('#' + dropzoneId);
            var $prev   = $('#' + previewId);
            var $inner  = $dz.find('.dz-inner');

            // Set the preview image and show it
            $prev.attr('src', e.target.result).css('display', 'block');

            // Hide the upload icon/text
            $inner.css({ opacity: 0, pointerEvents: 'none' });

            // Mark dropzone as having a preview (controls overlay visibility)
            $dz.addClass('has-preview');

            // Change border to solid
            $dz.css({ borderStyle: 'solid', borderColor: 'rgba(168,85,247,0.4)' });
        };
        reader.readAsDataURL(f);
    }

    /* ---- Init: mark dropzones that already have loaded images ---- */
    $(document).ready(function() {
        $('.dz-preview').each(function() {
            var src = $(this).attr('src');
            if (src && src.length > 0) {
                var $dz = $(this).closest('.dropzone');
                $dz.addClass('has-preview');
                $dz.find('.dz-inner').css({ opacity: 0 });
                $dz.css({ borderStyle: 'solid', borderColor: 'rgba(168,85,247,0.4)' });
                $(this).css('display', 'block');
            }
        });
    });

    /* ---- OTP input handling ---- */
    $(document).on('keyup', '.otp-digit', function(e) {
        var $this = $(this);
        var key   = e.which || e.keyCode;
        var val   = $this.val().replace(/\D/g, '');
        $this.val(val);

        if (val) {
            $this.addClass('has-val');
            $this.nextAll('.otp-digit').first().focus();
        } else {
            $this.removeClass('has-val');
        }
        if (key === 8 && !val) {
            $this.prevAll('.otp-digit').first().focus();
        }

        // Concatenate all into hidden input
        var otp = '';
        $('.otp-digit').each(function() { otp += $(this).val(); });
        $('#otpHidden').val(otp);
    });

    $(document).on('paste', '.otp-digit', function(e) {
        e.preventDefault();
        var pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text').replace(/\D/g,'').slice(0,6);
        var boxes  = $('.otp-digit');
        for (var i = 0; i < pasted.length && i < 6; i++) {
            boxes.eq(i).val(pasted[i]).addClass('has-val');
        }
        boxes.eq(Math.min(pasted.length, 5)).focus();
        var otp = '';
        boxes.each(function() { otp += $(this).val(); });
        $('#otpHidden').val(otp);
    });

    /* ---- OTP timer ---- */
    function startTimer() {
        clearInterval(otpTimer);
        var secs = 30;
        $('#timerText').show();
        $('#resendBtn').addClass('disabled');
        tick();
        otpTimer = setInterval(function() {
            secs--;
            tick();
            if (secs <= 0) {
                clearInterval(otpTimer);
                $('#timerText').hide();
                $('#resendBtn').removeClass('disabled');
            }
        }, 1000);

        function tick() {
            var m = Math.floor(secs / 60), s = secs % 60;
            $('#timerCount').text((m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s);
        }
    }

    function resendOtp() {
        var userId = $('#userIdInput').val();
        if (!userId) { toastr.warning('No account found. Please complete Step 1.'); return; }

        $.ajax({
            type: 'POST',
            url: '{{ route("artist.register") }}',
            data: { step: 'resend', user_id: userId, _token: $('meta[name="csrf-token"]').attr('content') },
            success: function(r) {
                if (r.otp) console.log('OTP:', r.otp);
                toastr.success('A new verification code has been sent!');
                startTimer();
            },
            error: function() { toastr.error('Failed to resend code.'); }
        });
    }

    /* ---- Genre pills ---- */
    $(document).on('click', '.g-pill', function() {
        var $p = $(this);
        var id = $p.data('id');
        if ($p.hasClass('chosen')) {
            $p.removeClass('chosen');
            $('#hg-' + id).remove();
        } else {
            if ($('.g-pill.chosen').length >= 3) {
                toastr.warning('You can select up to 3 favorite genres.');
                return;
            }
            $p.addClass('chosen');
            $('#pillsHidden').append('<input type="hidden" name="favorite_genres[]" value="' + id + '" id="hg-' + id + '">');
        }
        $('#pillCount').text($('.g-pill.chosen').length);
    });

    /* ---- Plan selection ---- */
    function pickPlan(key) {
        $('.plan-item').removeClass('chosen');
        $('#pi-' + key).addClass('chosen');
        $('#planInput').val(key);
    }

    /* ---- Sub-genre filtering ---- */
    $('#primaryGenre').on('change', function() {
        var pid = $(this).val();
        $('#subGenre option').each(function() {
            var opt = $(this);
            if (!opt.val()) { opt.show(); return; }
            pid ? opt.toggle(opt.data('parent') == pid) : opt.show();
        });
        $('#subGenre').val('');
    });

    /* ---- Form AJAX submit ---- */
    $('#regForm').on('submit', function(e) {
        e.preventDefault();

        var $btn = $('#btnNext');
        $btn.addClass('loading').prop('disabled', true);

        var fd = new FormData(this);
        fd.set('step', currentStep);

        $.ajax({
            type: 'POST',
            url: '{{ route("artist.register") }}',
            data: fd,
            cache: false,
            processData: false,
            contentType: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(resp) {
                $btn.removeClass('loading').prop('disabled', false);

                if (resp.status) {
                    toastr.success(resp.message || 'Saved!');
                    if (resp.user_id) $('#userIdInput').val(resp.user_id);
                    if (resp.otp)     console.log('OTP:', resp.otp);  // dev only
                    if (resp.next_step) goStep(resp.next_step);
                } else {
                    toastr.error(resp.message || 'Something went wrong.');
                }
            },
            error: function(xhr) {
                $btn.removeClass('loading').prop('disabled', false);
                var j = xhr.responseJSON;
                if (j && j.errors) {
                    $.each(j.errors, function(k, msgs) {
                        toastr.warning(Array.isArray(msgs) ? msgs[0] : msgs);
                    });
                } else {
                    toastr.error('A technical error occurred. Please try again.');
                }
            }
        });
    });
    </script>
</body>
</html>
