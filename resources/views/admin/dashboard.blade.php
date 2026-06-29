@extends('layout.app')
@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Admin Dashboard
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Welcome back, {{ auth()->user()->name ?? 'Administrator' }} 👑</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <span class="badge badge-light-success d-flex align-items-center gap-2 fs-7 py-2 px-3 me-2">
                <span class="glow-dot-online"></span> WEMU Servers: Online
            </span>
            <select class="form-select form-select-sm form-select-solid dashboard-select" style="width: 130px;">
                <option value="1">This Month</option>
                <option value="2">Last Month</option>
                <option value="3">This Year</option>
            </select>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Row 1: Metrics -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Total Streams -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-primary h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Total Streams</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalStreams) }}</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; margin-right: 4px;">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                +14.3%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 25 Q 10 15, 20 20 T 40 10 T 60 20 T 80 5 T 100 15" fill="none" stroke="#4f46e5" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Active Users -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-success h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Active Users</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($activeUsers) }}</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; margin-right: 4px;">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                +12.4%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 20 Q 15 25, 30 15 T 50 20 T 70 10 T 85 25 T 100 5" fill="none" stroke="#10b981" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Artists -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-info h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Total Artists</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalUserCount) }}</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; margin-right: 4px;">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                +9.8%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 30 L 20 20 L 40 25 L 60 10 L 80 15 L 100 0" fill="none" stroke="#06b6d4" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Platform Revenue -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-warning h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Platform Revenue</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($platformRevenue, 2) }}</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: currentColor; margin-right: 4px;">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                +18.7%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 25 Q 25 15, 50 20 T 100 5" fill="none" stroke="#f59e0b" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Revenue Overview & Top Artists -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Platform Revenue Overview -->
            <div class="col-lg-8">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Platform Revenue Overview</span>
                        </h3>
                        <div class="card-toolbar">
                            <select class="form-select form-select-sm form-select-solid dashboard-select" style="width: 150px;" onchange="window.location.href = '?revenue_filter=' + this.value;">
                                <option value="6_months" {{ $revenueFilter == '6_months' ? 'selected' : '' }}>Last 6 Months</option>
                                <option value="current_month" {{ $revenueFilter == 'current_month' ? 'selected' : '' }}>Current Month</option>
                                <option value="current_year" {{ $revenueFilter == 'current_year' ? 'selected' : '' }}>Current Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="chart-placeholder position-relative">
                            <!-- SVG Premium Line Chart -->
                            <svg viewBox="0 0 800 250" style="width:100%; height:100%;">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="var(--bs-primary, #4f46e5)" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="var(--bs-primary, #4f46e5)" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>
                                
                                <!-- Y-Axis Grid Lines & Labels -->
                                @php
                                    $ySteps = [
                                        ['y' => 200, 'val' => 0],
                                        ['y' => 155, 'val' => $chartMaxRevenue * 0.25],
                                        ['y' => 110, 'val' => $chartMaxRevenue * 0.5],
                                        ['y' => 65,  'val' => $chartMaxRevenue * 0.75],
                                        ['y' => 20,  'val' => $chartMaxRevenue]
                                    ];
                                @endphp
                                @foreach($ySteps as $step)
                                    <text x="0" y="{{ $step['y'] + 4 }}" fill="var(--bs-gray-500, #9ca3af)" font-size="11">{{ $step['val'] < 1000 ? round($step['val']) : round($step['val']/1000, 1) . 'k' }}</text>
                                    <line x1="30" y1="{{ $step['y'] }}" x2="800" y2="{{ $step['y'] }}" stroke="var(--bs-border-color, #f3f4f6)" stroke-width="1" stroke-dasharray="4 4" />
                                @endforeach

                                <!-- Filled Area under curve -->
                                <path d="{{ $svgFillPath }}" fill="url(#chartGradient)" />

                                <!-- Dynamic Smooth Line -->
                                <path d="{{ $svgPath }}" fill="none" stroke="var(--bs-primary, #4f46e5)" stroke-width="3" />

                                <!-- Data Badges and X-Axis Labels -->
                                @foreach($chartPoints as $index => $point)
                                    <!-- Marker Badge -->
                                    <g class="chart-point" data-bs-toggle="tooltip" data-bs-placement="top" title="Exact Revenue: ${{ number_format($point['revenue'], 2) }}" style="cursor: pointer;">
                                        <rect x="{{ $point['x'] - 14 }}" y="{{ $point['y'] - 9 }}" width="28" height="18" rx="4" fill="var(--bs-primary, #4f46e5)" />
                                        <text x="{{ $point['x'] }}" y="{{ $point['y'] + 4 }}" fill="#ffffff" font-size="10" font-weight="bold" text-anchor="middle">
                                            {{ $point['revenue'] < 1000 ? round($point['revenue']) : round($point['revenue']/1000, 1) . 'k' }}
                                        </text>
                                        <title>Exact Revenue: ${{ number_format($point['revenue'], 2) }}</title>
                                    </g>
                                    
                                    <!-- X-Axis Label -->
                                    <text x="{{ $point['x'] }}" y="235" fill="var(--bs-gray-500, #9ca3af)" font-size="11" font-weight="500" text-anchor="{{ $index == 0 ? 'start' : ($index == count($chartPoints) - 1 ? 'end' : 'middle') }}">
                                        {{ $point['label'] }}
                                    </text>
                                @endforeach
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Artists -->
            <div class="col-lg-4">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Top Artists</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="#" class="text-muted fs-7 text-hover-primary">See all</a>
                        </div>
                    </div>
                    <div class="card-body p-6 pt-3">
                        @php $colors = ['#ff9a9e', '#a1c4fd', '#f6d365', '#84fab0', '#cfd9df']; @endphp
                        @foreach($topArtists as $index => $artist)
                        <div class="song-list-item">
                            <div class="song-list-item-num">{{ $index + 1 }}</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, {{ $colors[$index % count($colors)] }} 0%, #ffffff 100%);">
                                {{ strtoupper(substr($artist->name, 0, 2)) }}
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">{{ $artist->name }}</div>
                                <div class="text-muted fs-8">{{ $artist->profile?->primaryGenre?->name ?? 'Artist' }}</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">{{ number_format($artist->streams_count) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Pending Artist Approvals & Subscription Distribution -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Pending Artist Approvals -->
            <div class="col-lg-7">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Pending Artist Approvals</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-warning fw-bold fs-8">{{ $pendingArtistsCount }} Pending Request(s)</span>
                        </div>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="approval-list-container">
                            @forelse($pendingArtists as $artist)
                            <!-- {{ $artist->name }} -->
                            <div class="approval-item">
                                <div class="d-flex align-items-center">
                                    @if($artist->profile_image)
                                        <div class="approval-avatar" style="background-image: url('{{ $artist->image_path }}'); background-size: cover; background-position: center;"></div>
                                    @else
                                        <div class="approval-avatar" style="background: linear-gradient(135deg, #818cf8 0%, #4f46e5 100%);">
                                            {{ strtoupper(substr($artist->name, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="d-flex flex-column ms-4">
                                        <a href="{{ route('admin.artist.view', $artist->uuid) }}" class="text-gray-800 text-hover-primary fs-6 fw-bold">{{ $artist->name }}</a>
                                        <span class="text-muted fs-8">{{ $artist->email }} • {{ $artist->profile?->primaryGenre?->name ?? 'No Genre' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted fs-8 d-none d-md-inline me-2">Requested {{ $artist->created_at->format('M d, Y') }}</span>
                                    <button class="btn btn-approve approve-artist-btn" data-uuid="{{ $artist->uuid }}">Approve</button>
                                    <button class="btn btn-reject reject-artist-btn" data-uuid="{{ $artist->uuid }}">Reject</button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <span class="text-muted">No pending requests</span>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Distribution -->
            <div class="col-lg-5">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Subscription Breakdown</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3 d-flex align-items-center">
                        <div class="pe-4" style="width: 55%; flex-shrink: 0;">
                            @php $progressColors = ['primary', 'info', 'warning', 'success', 'danger']; @endphp
                            @foreach($subscriptionBreakdown as $index => $plan)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-7 fw-semibold text-gray-700 text-truncate pe-2">{{ $plan['name'] }}</span>
                                    <span class="fs-7 fw-bold text-dark">{{ $plan['percentage'] }}%</span>
                                </div>
                                <div class="h-6px mx-0 w-100 bg-light-{{ $progressColors[$index % count($progressColors)] }} rounded">
                                    <div class="bg-{{ $progressColors[$index % count($progressColors)] }} rounded h-6px" style="width: {{ $plan['percentage'] }}%;"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="d-flex align-items-center justify-content-center" style="width: 45%; flex-shrink: 0;">
                            <!-- Premium circular Donut Chart SVG -->
                            <svg viewBox="0 0 100 100" width="100%" height="100%">
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f3f4f6" stroke-width="12" class="donut-bg" />
                                @php
                                    $circumference = 251.327;
                                    $currentOffset = 0;
                                    $hexColors = ['#4f46e5', '#06b6d4', '#f59e0b', '#10b981', '#dc3545'];
                                @endphp
                                @foreach($subscriptionBreakdown as $index => $plan)
                                    @if($plan['percentage'] > 0)
                                        @php
                                            $dashLength = ($plan['percentage'] / 100) * $circumference;
                                        @endphp
                                        <circle cx="50" cy="50" r="40" fill="transparent" stroke="{{ $hexColors[$index % count($hexColors)] }}" stroke-width="12"
                                            stroke-dasharray="{{ $dashLength }} {{ $circumference }}" stroke-dashoffset="{{ -$currentOffset }}" transform="rotate(-90 50 50)" />
                                        @php
                                            $currentOffset += $dashLength;
                                        @endphp
                                    @endif
                                @endforeach
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 4: Region Specific Users (Global Reach) -->
        <div class="row g-5 g-xl-10">
            <!-- Global User Distribution -->
            <div class="col-lg-8">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Global User Distribution</span>
                            <span class="text-muted fs-7 pt-1">Active streaming listeners across geographic regions</span>
                        </h3>
                        <div class="card-toolbar">
                            <span class="badge badge-light-primary fw-bold px-3 py-2">5 Continents Active</span>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="row g-4">
                            @foreach($globalDistribution as $region => $data)
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">{{ $region }}</div>
                                        <span class="badge badge-light-{{ $data['color'] }} fs-9 fw-bold">{{ $data['percentage'] }}%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">{{ number_format($data['count']) }}</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            {{ $data['growth'] }}
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-{{ $data['color'] }} rounded">
                                        <div class="bg-{{ $data['color'] }} rounded h-4px" style="width: {{ $data['percentage'] }}%;"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regional Active Hubs -->
            <div class="col-lg-4">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Regional Active Hubs</span>
                            <span class="text-muted fs-7 pt-1">Active streaming infrastructure zones</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3 d-flex flex-column justify-content-between h-100">
                        <div class="mb-4 d-flex align-items-center justify-content-center" style="height: 150px;">
                            <!-- Futuristic Glowing Regional Hub SVG Map -->
                            <svg viewBox="0 0 200 100" width="100%" height="100%">
                                <path d="M 20 40 Q 50 20 80 40 T 140 20 T 180 50 Q 140 80 100 60 T 20 40" fill="none" stroke="#e5e7eb" stroke-width="1.5" stroke-dasharray="2 2" />
                                <!-- Active Nodes with pulsing indicators -->
                                <circle cx="40" cy="30" r="3" fill="#10b981" />
                                <circle cx="40" cy="30" r="6" fill="none" stroke="#10b981" stroke-width="1" opacity="0.5" />

                                <circle cx="90" cy="35" r="3" fill="#10b981" />
                                <circle cx="90" cy="35" r="6" fill="none" stroke="#10b981" stroke-width="1" opacity="0.5" />

                                <circle cx="150" cy="40" r="3" fill="#10b981" />
                                <circle cx="150" cy="40" r="6" fill="none" stroke="#10b981" stroke-width="1" opacity="0.5" />

                                <circle cx="65" cy="55" r="3" fill="#10b981" />
                                <circle cx="65" cy="55" r="6" fill="none" stroke="#10b981" stroke-width="1" opacity="0.5" />
                            </svg>
                        </div>
                        <div class="mt-auto">
                            @foreach($activeHubs as $index => $hub)
                            <div class="d-flex align-items-center justify-content-between {{ $index < count($activeHubs) - 1 ? 'border-bottom pb-2 mb-2' : '' }}">
                                <span class="fs-7 fw-semibold text-gray-700">{{ $hub['name'] }}</span>
                                <span class="badge badge-light-success fs-9 fw-bold">{{ $hub['status'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Reject Artist Modal -->
<div class="modal fade" id="rejectArtistModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title d-flex align-items-center fs-3 fw-bold text-dark">
                    <div class="d-flex align-items-center justify-content-center bg-light-danger rounded-circle me-3" style="width: 24px; height: 24px;">
                        <svg viewBox="0 0 24 24" style="width: 14px; height: 14px; fill: #dc3545;">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </div>
                    Reject Artist Verification
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <form id="rejectArtistForm">
                    <input type="hidden" id="rejectArtistUuid" name="uuid">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-gray-800 fs-6">Rejection Reason</label>
                        <textarea class="form-control form-control-solid bg-light text-gray-600" id="rejectionReason" name="rejection_reason" rows="4" placeholder="Enter clear explanation e.g. Government ID blurry, selfie does not match ID, missing links..." style="border-radius: 8px;" required></textarea>
                        <div class="form-text text-muted fs-8 mt-2">This comment will be visible to the artist on their registration status dashboard.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-end gap-2">
                <button type="button" class="btn btn-light fw-bold" style="background-color: #f8f9fa; color: #6c757d;" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger fw-bold d-flex align-items-center gap-2" id="confirmRejectBtn">
                    <div class="d-flex align-items-center justify-content-center bg-white rounded-circle" style="width: 16px; height: 16px;">
                        <svg viewBox="0 0 24 24" style="width: 10px; height: 10px; fill: #dc3545;">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        </svg>
                    </div>
                    Confirm Rejection
                </button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.approve-artist-btn').click(function() {
            let uuid = $(this).data('uuid');
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to approve this artist!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('admin/artist/approve') }}/" + uuid,
                        type: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire('Approved!', response.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        }
                    });
                }
            });
        });

        // Rejection Modal Logic
        let rejectModal = new bootstrap.Modal(document.getElementById('rejectArtistModal'));
        
        $('.reject-artist-btn').click(function() {
            let uuid = $(this).data('uuid');
            $('#rejectArtistUuid').val(uuid);
            $('#rejectionReason').val('');
            rejectModal.show();
        });

        $('#confirmRejectBtn').click(function() {
            let uuid = $('#rejectArtistUuid').val();
            let reason = $('#rejectionReason').val().trim();
            
            if (!reason) {
                Swal.fire('Warning', 'Rejection reason is required', 'warning');
                return;
            }

            let btn = $(this);
            btn.prop('disabled', true).text('Rejecting...');

            $.ajax({
                url: "{{ url('admin/artist/reject') }}/" + uuid,
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    rejection_reason: reason
                },
                success: function(response) {
                    btn.prop('disabled', false).html('<i class="fa fa-times-circle me-1"></i> Confirm Rejection');
                    if (response.status) {
                        rejectModal.hide();
                        Swal.fire('Rejected!', response.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html('<i class="fa fa-times-circle me-1"></i> Confirm Rejection');
                    Swal.fire('Error!', xhr.responseJSON?.message || 'Something went wrong', 'error');
                }
            });
        });
    });
</script>
@endpush
@endsection