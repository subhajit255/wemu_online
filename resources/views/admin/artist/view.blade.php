@extends('layout.app')

@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid artist-review-page">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Artist Verification Dashboard
                </h1>
                <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.artist.list') }}" class="text-muted text-hover-primary">Artists</a>
                    </li>
                    <li class="breadcrumb-item">
                        <span class="bullet bg-gray-400 w-5px h-2px"></span>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        Profile Review
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Main Content Container -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container container-xxl">
            
            @php
                // Check cover banner image
                $coverBanner = asset('assets/media/misc/pattern-4.jpg');
                if($detail->profile && $detail->profile->cover_banner) {
                    $bannerPath = 'storage/profile/' . $detail->profile->cover_banner;
                    if(file_exists(public_path($bannerPath))) {
                        $coverBanner = asset($bannerPath);
                    }
                }

                // Determine verification status details
                $verificationStatus = 0; // Default to pending
                $rejectionReason = null;
                if ($detail->verification) {
                    $verificationStatus = $detail->verification->verification_status;
                    $rejectionReason = $detail->verification->rejection_reason;
                }

                // Get favorite genre names
                $favGenres = [];
                if ($detail->preference && $detail->preference->favorite_genres) {
                    $favGenres = \App\Models\Genre::whereIn('id', $detail->preference->favorite_genres)->pluck('title')->toArray();
                }
            @endphp

            <!-- Beautiful Banner Profile Header -->
            <div class="artist-banner-container">
                <img src="{{ $coverBanner }}" alt="Artist Cover Banner" class="artist-banner-img" />
                <div class="artist-profile-avatar-container">
                    <img src="{{ $detail->image_path }}" alt="Artist Profile Avatar" class="artist-profile-avatar" />
                </div>
            </div>

            <!-- Premium Profile Header Card -->
            <div class="card mb-6 shadow-sm">
                <div class="card-body pt-9 pb-6">
                    <div class="d-flex flex-wrap flex-sm-nowrap">
                        <!-- Spacer for desktop avatar overlap -->
                        <div class="me-7 mb-4 w-150px d-none d-sm-block" style="min-width: 140px;"></div>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="text-gray-900 fs-1 fw-bold me-3">{{ $detail->profile->display_name ?? ($detail->name ?? 'Unnamed Artist') }}</span>
                                        @if($verificationStatus == 1)
                                            <span class="badge badge-light-success fw-bold px-3 py-1 fs-8"><i class="fa-solid fa-circle-check text-success me-1"></i> Verified Artist</span>
                                        @elseif($verificationStatus == 2)
                                            <span class="badge badge-light-danger fw-bold px-3 py-1 fs-8"><i class="fa-solid fa-circle-xmark text-danger me-1"></i> Verification Rejected</span>
                                        @else
                                            <span class="badge badge-light-warning fw-bold px-3 py-1 fs-8"><i class="fa-solid fa-clock text-warning me-1"></i> Pending Verification</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Account handles / details -->
                                    <div class="d-flex flex-wrap fw-semibold fs-6 mb-4 pe-2 gap-4">
                                        <span class="d-flex align-items-center text-gray-500">
                                            <i class="fa-solid fa-user me-2 text-primary"></i> @ {{ $detail->username ?? 'handle' }}
                                        </span>
                                        <span class="d-flex align-items-center text-gray-500">
                                            <i class="fa-solid fa-envelope me-2 text-success"></i> {{ $detail->email ?? 'N/A' }}
                                        </span>
                                        <span class="d-flex align-items-center text-gray-500">
                                            <i class="fa-solid fa-phone me-2 text-info"></i> {{ getPhoneCode($detail->id) }}{{ $detail->mobile_number ?? 'N/A' }}
                                        </span>
                                        <span class="d-flex align-items-center text-gray-500">
                                            <i class="fa-solid fa-location-dot me-2 text-danger"></i> {{ $detail->profile->country ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layout: Two Columns -->
            <div class="row g-6 g-xl-9">
                
                <!-- Left Column: Verification Status & Artist Info Panel -->
                <div class="col-lg-4 col-xl-3">
                    
                    <!-- Verification Action Control Panel -->
                    <div class="card mb-6 shadow-sm">
                        <div class="card-body p-6">
                            <h4 class="fw-bold text-gray-900 mb-4">Verification Status</h4>
                            
                            <div class="text-center p-6 mb-6 verification-status-panel 
                                @if($verificationStatus == 1) status-panel-approved 
                                @elseif($verificationStatus == 2) status-panel-rejected 
                                @else status-panel-pending @endif">
                                
                                @if($verificationStatus == 1)
                                    <i class="fa-solid fa-circle-check fs-1 text-success mb-3"></i>
                                    <div class="fs-5 fw-bold text-success">Verified & Approved</div>
                                    <div class="fs-8 mt-1 text-success opacity-75">
                                        Verified at: {{ $detail->verification->verified_at ? \Carbon\Carbon::parse($detail->verification->verified_at)->format('d M Y H:i') : 'N/A' }}
                                    </div>
                                @elseif($verificationStatus == 2)
                                    <i class="fa-solid fa-circle-xmark fs-1 text-danger mb-3"></i>
                                    <div class="fs-5 fw-bold text-danger">Verification Rejected</div>
                                @else
                                    <div class="mb-3 d-inline-block">
                                        <i class="fa-solid fa-clock fs-1 text-warning pulse-warning-glow"></i>
                                    </div>
                                    <div class="fs-5 fw-bold text-warning">Pending Review</div>
                                    <div class="fs-8 mt-1 text-warning opacity-75">Needs admin action</div>
                                @endif
                            </div>

                            @if($verificationStatus == 2 && $rejectionReason)
                                <div class="alert alert-light-danger border border-danger border-dashed d-flex flex-column p-4 mb-6 rounded-3">
                                    <span class="fw-bold fs-7 text-danger mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Rejection Reason</span>
                                    <span class="fs-7 text-gray-700 italic">"{{ $rejectionReason }}"</span>
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-grid gap-3">
                                @if($verificationStatus != 1)
                                    <button type="button" class="btn btn-premium-approve fw-bold btn-sm py-3" id="btnApproveArtist">
                                        <i class="fa-solid fa-circle-check me-2"></i> Approve Artist
                                    </button>
                                @endif

                                @if($verificationStatus != 2)
                                    <button type="button" class="btn btn-premium-reject btn-sm fw-bold py-3" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        <i class="fa-solid fa-circle-xmark me-2"></i> Reject Artist
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="card mb-6 shadow-sm">
                        <div class="card-body p-6">
                            <h4 class="fw-bold text-gray-900 mb-4">Quick Statistics</h4>
                            <div class="d-flex flex-column gap-4">
                                <div class="stats-item">
                                    <div class="symbol symbol-40px bg-light-primary me-4">
                                        <span class="symbol-label"><i class="fa-solid fa-music text-primary fs-5"></i></span>
                                    </div>
                                    <div>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $detail->songs->count() }}</div>
                                        <div class="fs-7 text-gray-500 fw-semibold">Tracks Released</div>
                                    </div>
                                </div>
                                <div class="stats-item">
                                    <div class="symbol symbol-40px bg-light-success me-4">
                                        <span class="symbol-label"><i class="fa-solid fa-compact-disc text-success fs-5"></i></span>
                                    </div>
                                    <div>
                                        <div class="fs-6 fw-bold text-gray-900">{{ $detail->albums->count() }}</div>
                                        <div class="fs-7 text-gray-500 fw-semibold">Albums Published</div>
                                    </div>
                                </div>
                                <div class="stats-item">
                                    <div class="symbol symbol-40px bg-light-info me-4">
                                        <span class="symbol-label"><i class="fa-solid fa-calendar-days text-info fs-5"></i></span>
                                    </div>
                                    <div>
                                        <div class="fs-6 fw-bold text-gray-900">
                                            {{ \Carbon\Carbon::parse($detail->created_at)->format('d M Y') }}
                                        </div>
                                        <div class="fs-7 text-gray-500 fw-semibold">Joined Date</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Registration Onboarding Details & Tabs -->
                <div class="col-lg-8 col-xl-9">
                    
                    <div class="card shadow-sm">
                        
                        <!-- Modern Tab Navigation -->
                        <div class="card-header border-0 pt-6">
                            <ul class="nav nav-tabs wemu-tab-nav border-0 w-100 flex-nowrap overflow-auto" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tab_profile" role="tab">
                                        <i class="fa-solid fa-user me-2"></i> Profile & Bio
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_preferences" role="tab">
                                        <i class="fa-solid fa-sliders me-2"></i> Preferences & Socials
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_verification" role="tab">
                                        <i class="fa-solid fa-id-card me-2"></i> ID Verification
                                    </a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tab_catalog" role="tab">
                                        <i class="fa-solid fa-music me-2"></i> Music Catalog
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="card-body p-8">
                            <div class="tab-content">
                                
                                <!-- TAB 1: Profile & Bio -->
                                <div class="tab-pane fade show active" id="tab_profile" role="tabpanel">
                                    
                                    <!-- General Profile Grid -->
                                    <h5 class="fw-bold text-gray-900 mb-4 fs-6"><i class="fa-solid fa-circle-info text-primary me-2"></i> Account & Stage Details</h5>
                                    <div class="row g-6 mb-8">
                                        
                                        <!-- Legal Name -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(59, 130, 246, 0.08); color: #3b82f6;">
                                                    <i class="fa-solid fa-signature"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Legal Name</div>
                                                    <div class="detail-info-value">{{ $detail->name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Stage Name -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(139, 92, 246, 0.08); color: #8b5cf6;">
                                                    <i class="fa-solid fa-microphone"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Stage Name / Display Name</div>
                                                    <div class="detail-info-value">{{ $detail->profile->display_name ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Username -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(236, 72, 153, 0.08); color: #ec4899;">
                                                    <i class="fa-solid fa-at"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Username / Handle</div>
                                                    <div class="detail-info-value">@ {{ $detail->username ?? 'handle' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                                                    <i class="fa-solid fa-envelope"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Email Address</div>
                                                    <div class="detail-info-value">{{ $detail->email ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mobile -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(14, 165, 233, 0.08); color: #0ea5e9;">
                                                    <i class="fa-solid fa-phone"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Mobile Number</div>
                                                    <div class="detail-info-value">
                                                        {{ getPhoneCode($detail->id) }}{{ $detail->mobile_number ?? 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Country -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(239, 68, 68, 0.08); color: #ef4444;">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Country</div>
                                                    <div class="detail-info-value">{{ $detail->profile->country ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Primary Genre -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                                    <i class="fa-solid fa-music"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Primary Genre</div>
                                                    <div class="detail-info-value">
                                                        <span class="badge badge-light-primary fw-bold px-3 py-1.5 fs-7">
                                                            {{ $detail->profile->primaryGenre->title ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sub Genre -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(20, 184, 166, 0.08); color: #14b8a6;">
                                                    <i class="fa-solid fa-sliders"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Sub Genre</div>
                                                    <div class="detail-info-value">
                                                        <span class="badge badge-light-info fw-bold px-3 py-1.5 fs-7">
                                                            {{ $detail->profile->subGenre->title ?? 'N/A' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Artist Type -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(107, 114, 128, 0.08); color: #6b7280;">
                                                    <i class="fa-solid fa-user-tie"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Artist Type</div>
                                                    <div class="detail-info-value">
                                                        <span class="badge badge-light-dark fw-bold px-3 py-1.5 fs-7">
                                                            {{ $detail->profile->artist_type ?? 'INDEPENDENT' }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Years Active -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(79, 70, 229, 0.08); color: #4f46e5;">
                                                    <i class="fa-solid fa-calendar-check"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Years Active</div>
                                                    <div class="detail-info-value">{{ $detail->profile->years_of_active ?? 0 }} years</div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Record Label -->
                                        @if(($detail->profile->artist_type ?? '') === 'SIGNED')
                                            <div class="col-md-6">
                                                <div class="info-card">
                                                    <div class="info-card-icon" style="background: rgba(244, 63, 94, 0.08); color: #f43f5e;">
                                                        <i class="fa-solid fa-building-shield"></i>
                                                    </div>
                                                    <div class="info-card-content">
                                                        <div class="detail-info-label">Record Label</div>
                                                        <div class="detail-info-value">{{ $detail->profile->label ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Website -->
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(6, 182, 212, 0.08); color: #06b6d4;">
                                                    <i class="fa-solid fa-globe"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Website</div>
                                                    <div class="detail-info-value">
                                                        @if($detail->profile && $detail->profile->website)
                                                            <a href="{{ $detail->profile->website }}" target="_blank" class="text-primary fw-bold text-hover-underline">
                                                                <i class="fa-solid fa-globe me-1"></i> Visit Website
                                                            </a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Artist Bio -->
                                    <h5 class="fw-bold text-gray-900 mb-3 fs-6"><i class="fa-solid fa-feather text-primary me-2"></i> Artist Biography</h5>
                                    <div class="p-6 artist-bio-block rounded-3 fs-6 lh-lg border border-dashed">
                                        {!! nl2br(e($detail->profile->bio ?? 'No biography provided.')) !!}
                                    </div>

                                </div>

                                <!-- TAB 2: Preferences & Socials -->
                                <div class="tab-pane fade" id="tab_preferences" role="tabpanel">
                                    
                                    <!-- Music Preferences -->
                                    <h5 class="fw-bold text-gray-900 mb-4 fs-6"><i class="fa-solid fa-heart text-primary me-2"></i> Music & Streaming Preferences</h5>
                                    <div class="row g-6 mb-8">
                                        
                                        <div class="col-12">
                                            <div class="detail-info-label mb-3">Favorite Genres</div>
                                            <div class="d-flex flex-wrap gap-3">
                                                @forelse($favGenres as $genreName)
                                                    <span class="genre-pill">
                                                        <i class="fa-solid fa-music me-1 fs-8 opacity-75"></i> {{ $genreName }}
                                                    </span>
                                                @empty
                                                    <span class="text-gray-500 italic fs-7">No favorite genres selected</span>
                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                                                    <i class="fa-solid fa-arrows-spin"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Release Frequency</div>
                                                    <div class="detail-info-value">
                                                        @php
                                                            $freq = $detail->profile->release_frequency ?? ($detail->preference->release_frequency ?? null);
                                                            $freqLabel = 'N/A';
                                                            if($freq == 1) $freqLabel = 'Weekly';
                                                            elseif($freq == 2) $freqLabel = 'Monthly';
                                                            elseif($freq == 3) $freqLabel = 'Occasionally';
                                                        @endphp
                                                        <span class="badge badge-light-success fw-bold px-3 py-1.5 fs-7">{{ $freqLabel }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-card-icon" style="background: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                                                    <i class="fa-solid fa-id-card-clip"></i>
                                                </div>
                                                <div class="info-card-content">
                                                    <div class="detail-info-label">Preference Profile Type</div>
                                                    <div class="detail-info-value">
                                                        <span class="badge badge-light-warning fw-bold px-3 py-1.5 fs-7">
                                                            {{ $detail->preference->artist_type ?? ($detail->profile->artist_type ?? 'INDEPENDENT') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    @php
                                        $hasSocials = $detail->socialLink && (
                                            $detail->socialLink->instagram_url || 
                                            $detail->socialLink->youtube_url || 
                                            $detail->socialLink->tiktok_url || 
                                            $detail->socialLink->facebook_url || 
                                            $detail->socialLink->twitter_url || 
                                            $detail->socialLink->spotify_url || 
                                            $detail->socialLink->apple_music_url
                                        );
                                    @endphp

                                    <!-- Social Platforms -->
                                    <h5 class="fw-bold text-gray-900 mb-4 fs-6"><i class="fa-solid fa-share-nodes text-primary me-2"></i> Connected Social Links</h5>
                                    @if($hasSocials)
                                        <div class="row g-4">
                                            @if($detail->socialLink && $detail->socialLink->instagram_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->instagram_url }}" target="_blank" class="social-brand-card social-brand-instagram p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-instagram"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">Instagram</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->youtube_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->youtube_url }}" target="_blank" class="social-brand-card social-brand-youtube p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-youtube"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">YouTube</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->tiktok_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->tiktok_url }}" target="_blank" class="social-brand-card social-brand-tiktok p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-tiktok"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">TikTok</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->facebook_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->facebook_url }}" target="_blank" class="social-brand-card social-brand-facebook p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-facebook-f"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">Facebook</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->twitter_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->twitter_url }}" target="_blank" class="social-brand-card social-brand-twitter p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-x-twitter"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">Twitter</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->spotify_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->spotify_url }}" target="_blank" class="social-brand-card social-brand-spotify p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-spotify"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">Spotify</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif

                                            @if($detail->socialLink && $detail->socialLink->apple_music_url)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ $detail->socialLink->apple_music_url }}" target="_blank" class="social-brand-card social-brand-applemusic p-4 d-flex align-items-center gap-3">
                                                        <div class="social-brand-icon">
                                                            <i class="fa-brands fa-apple"></i>
                                                        </div>
                                                        <div class="social-brand-info">
                                                            <div class="social-brand-name">Apple Music</div>
                                                            <div class="social-brand-status">Connected</div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="p-6 bg-light rounded-3 text-center border border-dashed">
                                            <span class="text-gray-500 italic fs-6">No social platforms linked during registration.</span>
                                        </div>
                                    @endif

                                </div>

                                <!-- TAB 3: ID Verification Documents -->
                                <div class="tab-pane fade" id="tab_verification" role="tabpanel">
                                    
                                    <h5 class="fw-bold text-gray-900 mb-2 fs-6"><i class="fa-solid fa-shield-halved text-primary me-2"></i> Official Verification Documents</h5>
                                    <p class="text-gray-500 fs-7 mb-6">Click any document to open a full-resolution review lightbox.</p>
                                    
                                    @php
                                        $idFront = ($detail->verification && $detail->verification->government_id_front) ? asset('storage/verification/' . $detail->verification->government_id_front) : asset('assets/media/svg/files/blank-image.svg');
                                        $idBack = ($detail->verification && $detail->verification->government_id_back) ? asset('storage/verification/' . $detail->verification->government_id_back) : asset('assets/media/svg/files/blank-image.svg');
                                        $selfie = ($detail->verification && $detail->verification->selfie_image) ? asset('storage/verification/' . $detail->verification->selfie_image) : asset('assets/media/svg/files/blank-image.svg');
                                        
                                        $hasFront = ($detail->verification && $detail->verification->government_id_front) ? true : false;
                                        $hasBack = ($detail->verification && $detail->verification->government_id_back) ? true : false;
                                        $hasSelfie = ($detail->verification && $detail->verification->selfie_image) ? true : false;
                                    @endphp

                                    <div class="row g-6">
                                        
                                        <!-- ID Front Card -->
                                        <div class="col-md-4">
                                            <div class="card @if($hasFront) verification-doc-card shadow-sm @else bg-light opacity-75 border-dashed @endif h-100" @if($hasFront) onclick="openLightbox('{{ $idFront }}', 'Government ID - Front')" @endif>
                                                <div class="card-body p-4 text-center">
                                                    <div class="detail-info-label text-start mb-3">Govt. ID (Front)</div>
                                                    <div class="position-relative w-100 rounded overflow-hidden verification-doc-img-wrapper" style="height: 180px;">
                                                        <img src="{{ $idFront }}" alt="Govt ID Front" class="w-100 h-100 @if($hasFront) object-fit-cover @else p-4 object-fit-contain @endif" />
                                                        @if($hasFront)
                                                            <div class="verification-doc-overlay">
                                                                <i class="fa-solid fa-magnifying-glass-plus text-white"></i>
                                                                <span>Zoom Document</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ID Back Card -->
                                        <div class="col-md-4">
                                            <div class="card @if($hasBack) verification-doc-card shadow-sm @else bg-light opacity-75 border-dashed @endif h-100" @if($hasBack) onclick="openLightbox('{{ $idBack }}', 'Government ID - Back')" @endif>
                                                <div class="card-body p-4 text-center">
                                                    <div class="detail-info-label text-start mb-3">Govt. ID (Back)</div>
                                                    <div class="position-relative w-100 rounded overflow-hidden verification-doc-img-wrapper" style="height: 180px;">
                                                        <img src="{{ $idBack }}" alt="Govt ID Back" class="w-100 h-100 @if($hasBack) object-fit-cover @else p-4 object-fit-contain @endif" />
                                                        @if($hasBack)
                                                            <div class="verification-doc-overlay">
                                                                <i class="fa-solid fa-magnifying-glass-plus text-white"></i>
                                                                <span>Zoom Document</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Selfie Verification Card -->
                                        <div class="col-md-4">
                                            <div class="card @if($hasSelfie) verification-doc-card shadow-sm @else bg-light opacity-75 border-dashed @endif h-100" @if($hasSelfie) onclick="openLightbox('{{ $selfie }}', 'Selfie Verification')" @endif>
                                                <div class="card-body p-4 text-center">
                                                    <div class="detail-info-label text-start mb-3">Verification Selfie</div>
                                                    <div class="position-relative w-100 rounded overflow-hidden verification-doc-img-wrapper" style="height: 180px;">
                                                        <img src="{{ $selfie }}" alt="Verification Selfie" class="w-100 h-100 @if($hasSelfie) object-fit-cover @else p-4 object-fit-contain @endif" />
                                                        @if($hasSelfie)
                                                            <div class="verification-doc-overlay">
                                                                <i class="fa-solid fa-magnifying-glass-plus text-white"></i>
                                                                <span>Zoom Selfie</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    @if(!$detail->verification)
                                        <div class="alert alert-light-warning border border-warning border-dashed d-flex align-items-center p-5 mt-6 rounded-3">
                                            <i class="fa-solid fa-triangle-exclamation fs-3 text-warning me-3"></i>
                                            <div class="fs-6 fw-bold text-warning">This artist has not uploaded any verification documents yet.</div>
                                        </div>
                                    @endif

                                </div>

                                <!-- TAB 4: Music Catalog -->
                                <div class="tab-pane fade" id="tab_catalog" role="tabpanel">
                                    
                                    <h5 class="fw-bold text-gray-900 mb-4 fs-6"><i class="fa-solid fa-music text-primary me-2"></i> Track List & Releases</h5>
                                    
                                    <!-- Catalog Table (Completely Preserved & Seamlessly Styled) -->
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 song-table mb-0">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-transparent">
                                                    <th class="ps-6 min-w-200px rounded-start">Track</th>
                                                    <th class="min-w-150px">Album</th>
                                                    <th class="text-center min-w-100px">Status</th>
                                                    <th class="text-center min-w-100px">Likes</th>
                                                    <th class="text-center min-w-100px">Streams</th>
                                                    <th class="text-center min-w-100px">Duration</th>
                                                    <th class="text-end min-w-150px pe-6 rounded-end">Release Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @forelse($detail->songs as $song)
                                                    <tr>
                                                        <td class="ps-6">
                                                            <div class="d-flex align-items-center">
                                                                <div class="symbol symbol-40px me-3 shadow-sm" style="border-radius: 6px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05);">
                                                                    <img src="{{ $song->cover_image_path }}" alt="{{ $song->title }}" class="w-40px h-40px object-fit-cover">
                                                                </div>
                                                                <div class="d-flex justify-content-start flex-column">
                                                                    <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $song->title }}</span>
                                                                    @if($song->is_explicit)
                                                                        <span class="badge badge-light-danger fw-bold px-2 py-0.5 fs-9 align-self-start" style="letter-spacing: 0.5px; transform: scale(0.85); origin: left center;">EXPLICIT</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($song->album)
                                                                <span class="text-dark fw-semibold d-block fs-7">{{ $song->album->title }}</span>
                                                            @else
                                                                <span class="text-muted fw-semibold d-block fs-7">Single</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge badge-light-{{ $song->status == 1 ? 'success' : 'warning' }} fw-bold px-3 py-1.5 fs-8">
                                                                {{ $song->status == 1 ? 'Published' : 'Draft' }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="text-danger fw-bold fs-7">
                                                                {{ $song->likes_count ?? 0 }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="text-primary fw-bold fs-7">
                                                                {{ $song->play_count ?? 0 }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="text-dark fw-semibold d-block fs-7">
                                                                @php
                                                                    $minutes = floor($song->duration / 60);
                                                                    $seconds = $song->duration % 60;
                                                                    $durationStr = '';
                                                                    if ($minutes > 0) {
                                                                        $durationStr .= $minutes . 'm ';
                                                                    }
                                                                    $durationStr .= $seconds . 's';
                                                                @endphp
                                                                {{ trim($durationStr) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end pe-6">
                                                            <span class="text-dark fw-semibold d-block fs-7">
                                                                {{ \Carbon\Carbon::parse($song->created_at)->format('M d, Y') }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center py-16">
                                                            <div class="d-flex flex-column align-items-center gap-4">
                                                                <div class="wemu-empty-icon">
                                                                    <i class="fa-solid fa-music text-primary fs-1"></i>
                                                                </div>
                                                                <p class="text-gray-500 fw-bold fs-5 m-0">No songs released yet</p>
                                                                <p class="text-muted fs-7 m-0">This artist's music catalog is currently empty.</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-light py-4 px-6 border-0">
                    <h5 class="modal-title fw-bold text-gray-900"><i class="fa-solid fa-circle-xmark text-danger me-2"></i> Reject Artist Verification</h5>
                    <button type="button" class="btn-close" data-bs-redirect="modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-6">
                    <form id="rejectArtistForm">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason" class="form-label fw-bold text-gray-800 fs-7 mb-2">Rejection Reason</label>
                            <textarea class="form-control bg-light border-0 px-4 py-3" id="rejection_reason" name="rejection_reason" rows="4" placeholder="Enter clear explanation e.g. Government ID blurry, selfie does not match ID, missing links..." required></textarea>
                            <div class="fs-8 text-muted mt-2">This comment will be visible to the artist on their registration status dashboard.</div>
                        </div>
                        <div class="d-flex justify-content-end gap-3 pt-3">
                            <button type="button" class="btn btn-light fw-bold btn-sm px-5" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger fw-bold btn-sm px-5" id="btnSubmitReject">
                                <span class="indicator-label"><i class="fa-solid fa-circle-xmark me-2"></i> Confirm Rejection</span>
                                <span class="indicator-progress d-none">
                                    Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gorgeous ID Review Lightbox Modal -->
    <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content lightbox-modal-content position-relative">
                <button type="button" class="lightbox-modal-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div class="modal-body p-8 d-flex flex-column align-items-center justify-content-center">
                    <img id="lightboxImage" src="" alt="Zoomed ID Document" class="img-fluid lightbox-modal-img mb-4" />
                    <h5 id="lightboxCaption" class="text-white fw-bold m-0 fs-5">Document Review</h5>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // High fidelity document zoom viewer
        function openLightbox(imgSrc, captionText) {
            $('#lightboxImage').attr('src', imgSrc);
            $('#lightboxCaption').text(captionText);
            const myModal = new bootstrap.Modal(document.getElementById('lightboxModal'));
            myModal.show();
        }

        $(document).ready(function() {
            // Handle Artist Verification Approval via AJAX
            $('#btnApproveArtist').on('click', function(e) {
                e.preventDefault();
                const $btn = $(this);
                
                // Confirm action
                if (!confirm('Are you sure you want to approve this artist\'s verification? They will receive full artist upload privileges.')) {
                    return;
                }

                // Show spinner loading state
                $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Approving...');

                $.ajax({
                    url: "{{ route('admin.artist.approve', $detail->uuid) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: res.message,
                                    confirmButtonText: 'Great'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                alert(res.message);
                                window.location.reload();
                            }
                        } else {
                            alert(res.message || 'Something went wrong.');
                            $btn.prop('disabled', false).html('<i class="fa-solid fa-circle-check me-2"></i> Approve Artist');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Error occurred during approval.';
                        alert(errorMsg);
                        $btn.prop('disabled', false).html('<i class="fa-solid fa-circle-check me-2"></i> Approve Artist');
                    }
                });
            });

            // Handle Artist Verification Rejection via AJAX
            $('#rejectArtistForm').on('submit', function(e) {
                e.preventDefault();
                
                const $btn = $('#btnSubmitReject');
                const $label = $btn.find('.indicator-label');
                const $progress = $btn.find('.indicator-progress');
                const reason = $('#rejection_reason').val();

                if (!reason || reason.trim() === '') {
                    alert('Please enter a rejection reason.');
                    return;
                }

                // Show loading
                $btn.prop('disabled', true);
                $label.addClass('d-none');
                $progress.removeClass('d-none');

                $.ajax({
                    url: "{{ route('admin.artist.reject', $detail->uuid) }}",
                    type: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        rejection_reason: reason
                    },
                    success: function(res) {
                        if (res.status) {
                            $('#rejectModal').modal('hide');
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Rejected',
                                    text: res.message,
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                alert(res.message);
                                window.location.reload();
                            }
                        } else {
                            alert(res.message || 'Something went wrong.');
                            $btn.prop('disabled', false);
                            $label.removeClass('d-none');
                            $progress.addClass('d-none');
                        }
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON?.message || 'Error occurred during rejection.';
                        alert(errorMsg);
                        $btn.prop('disabled', false);
                        $label.removeClass('d-none');
                        $progress.addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
