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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">48.2M</span>
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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">2.1M</span>
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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">8,420</span>
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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">$142.5K</span>
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
                            <select class="form-select form-select-sm form-select-solid dashboard-select" style="width: 150px;">
                                <option value="1">Last 6 Months</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="chart-placeholder position-relative">
                            <!-- SVG Premium Line Chart -->
                            <svg viewBox="0 0 800 250" style="width:100%; height:100%;">
                                <defs>
                                    <linearGradient id="chartGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#4f46e5" stop-opacity="0.25" />
                                        <stop offset="100%" stop-color="#4f46e5" stop-opacity="0.0" />
                                    </linearGradient>
                                </defs>
                                <!-- Grid lines -->
                                <line x1="0" y1="50" x2="800" y2="50" stroke="#f3f4f6" stroke-width="1" class="chart-grid-line" />
                                <line x1="0" y1="100" x2="800" y2="100" stroke="#f3f4f6" stroke-width="1" class="chart-grid-line" />
                                <line x1="0" y1="150" x2="800" y2="150" stroke="#f3f4f6" stroke-width="1" class="chart-grid-line" />
                                <line x1="0" y1="200" x2="800" y2="200" stroke="#f3f4f6" stroke-width="1" class="chart-grid-line" />

                                <!-- Filled Area under curve -->
                                <path d="M 0 200 C 100 180, 150 120, 200 110 C 250 100, 300 130, 400 80 C 500 50, 600 90, 700 60 C 750 40, 800 20, 800 20 L 800 200 Z" fill="url(#chartGradient)" />

                                <!-- Smooth Bezier Line -->
                                <path d="M 0 200 C 100 180, 150 120, 200 110 C 250 100, 300 130, 400 80 C 500 50, 600 90, 700 60 C 750 40, 800 20, 800 20" fill="none" stroke="#4f46e5" stroke-width="3" />

                                <!-- Points -->
                                <circle cx="200" cy="110" r="4.5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />
                                <circle cx="400" cy="80" r="4.5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />
                                <circle cx="700" cy="60" r="4.5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />
                                <circle cx="800" cy="20" r="4.5" fill="#ffffff" stroke="#4f46e5" stroke-width="2.5" />

                                <!-- Labels -->
                                <text x="0" y="235" fill="#9ca3af" font-size="11" font-weight="500">Jan</text>
                                <text x="200" y="235" fill="#9ca3af" font-size="11" font-weight="500" text-anchor="middle">Feb</text>
                                <text x="400" y="235" fill="#9ca3af" font-size="11" font-weight="500" text-anchor="middle">Mar</text>
                                <text x="600" y="235" fill="#9ca3af" font-size="11" font-weight="500" text-anchor="middle">Apr</text>
                                <text x="700" y="235" fill="#9ca3af" font-size="11" font-weight="500" text-anchor="middle">May</text>
                                <text x="800" y="235" fill="#9ca3af" font-size="11" font-weight="500" text-anchor="end">Jun</text>
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
                        <!-- Artist 1 -->
                        <div class="song-list-item">
                            <div class="song-list-item-num">1</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);">
                                TS
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">Taylor Swift</div>
                                <div class="text-muted fs-8">Pop / Country</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">42.1M</div>
                        </div>
                        <!-- Artist 2 -->
                        <div class="song-list-item">
                            <div class="song-list-item-num">2</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                                TW
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">The Weeknd</div>
                                <div class="text-muted fs-8">R&B / Pop</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">38.4M</div>
                        </div>
                        <!-- Artist 3 -->
                        <div class="song-list-item">
                            <div class="song-list-item-num">3</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
                                DR
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">Drake</div>
                                <div class="text-muted fs-8">Hip Hop / Rap</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">31.9M</div>
                        </div>
                        <!-- Artist 4 -->
                        <div class="song-list-item">
                            <div class="song-list-item-num">4</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);">
                                BE
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">Billie Eilish</div>
                                <div class="text-muted fs-8">Alternative Pop</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">27.5M</div>
                        </div>
                        <!-- Artist 5 -->
                        <div class="song-list-item">
                            <div class="song-list-item-num">5</div>
                            <div class="song-list-item-img gradient-avatar" style="background: linear-gradient(135deg, #cfd9df 0%, #e2ebf0 100%);">
                                PM
                            </div>
                            <div class="song-list-item-title ms-3">
                                <div class="fw-semibold text-dark">Post Malone</div>
                                <div class="text-muted fs-8">Hip Hop / Rock</div>
                            </div>
                            <div class="song-list-item-plays fw-bold">24.2M</div>
                        </div>
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
                            <span class="badge badge-light-warning fw-bold fs-8">3 Pending Request(s)</span>
                        </div>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="approval-list-container">
                            <!-- Travis Scott -->
                            <div class="approval-item">
                                <div class="d-flex align-items-center">
                                    <div class="approval-avatar" style="background: linear-gradient(135deg, #818cf8 0%, #4f46e5 100%);">
                                        TS
                                    </div>
                                    <div class="d-flex flex-column ms-4">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Travis Scott</a>
                                        <span class="text-muted fs-8">travis@wemu.com • Hip Hop / Rap</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted fs-8 d-none d-md-inline me-2">Requested May 19, 2026</span>
                                    <button class="btn btn-approve">Approve</button>
                                    <button class="btn btn-reject">Reject</button>
                                </div>
                            </div>
                            <!-- Dua Lipa -->
                            <div class="approval-item">
                                <div class="d-flex align-items-center">
                                    <div class="approval-avatar" style="background: linear-gradient(135deg, #22d3ee 0%, #0891b2 100%);">
                                        DL
                                    </div>
                                    <div class="d-flex flex-column ms-4">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Dua Lipa</a>
                                        <span class="text-muted fs-8">dua@wemu.com • Dance / Pop</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted fs-8 d-none d-md-inline me-2">Requested May 18, 2026</span>
                                    <button class="btn btn-approve">Approve</button>
                                    <button class="btn btn-reject">Reject</button>
                                </div>
                            </div>
                            <!-- Olivia Rodrigo -->
                            <div class="approval-item">
                                <div class="d-flex align-items-center">
                                    <div class="approval-avatar" style="background: linear-gradient(135deg, #f472b6 0%, #db2777 100%);">
                                        OR
                                    </div>
                                    <div class="d-flex flex-column ms-4">
                                        <a href="#" class="text-gray-800 text-hover-primary fs-6 fw-bold">Olivia Rodrigo</a>
                                        <span class="text-muted fs-8">olivia@wemu.com • Pop / Rock</span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <span class="text-muted fs-8 d-none d-md-inline me-2">Requested May 17, 2026</span>
                                    <button class="btn btn-approve">Approve</button>
                                    <button class="btn btn-reject">Reject</button>
                                </div>
                            </div>
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
                        <div class="w-60 pe-5">
                            <!-- Progress 1 -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-7 fw-semibold text-gray-700">Premium Individual</span>
                                    <span class="fs-7 fw-bold text-dark">45%</span>
                                </div>
                                <div class="h-6px mx-0 w-100 bg-light-primary rounded">
                                    <div class="bg-primary rounded h-6px" style="width: 45%;"></div>
                                </div>
                            </div>
                            <!-- Progress 2 -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-7 fw-semibold text-gray-700">Premium Family</span>
                                    <span class="fs-7 fw-bold text-dark">28%</span>
                                </div>
                                <div class="h-6px mx-0 w-100 bg-light-info rounded">
                                    <div class="bg-info rounded h-6px" style="width: 28%;"></div>
                                </div>
                            </div>
                            <!-- Progress 3 -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-7 fw-semibold text-gray-700">Premium Student</span>
                                    <span class="fs-7 fw-bold text-dark">12%</span>
                                </div>
                                <div class="h-6px mx-0 w-100 bg-light-warning rounded">
                                    <div class="bg-warning rounded h-6px" style="width: 12%;"></div>
                                </div>
                            </div>
                            <!-- Progress 4 -->
                            <div class="mb-0">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="fs-7 fw-semibold text-gray-700">Free Tier (Ad-Supported)</span>
                                    <span class="fs-7 fw-bold text-dark">15%</span>
                                </div>
                                <div class="h-6px mx-0 w-100 bg-light-success rounded">
                                    <div class="bg-success rounded h-6px" style="width: 15%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="w-40 d-flex align-items-center justify-content-center">
                            <!-- Premium circular Donut Chart SVG -->
                            <svg viewBox="0 0 100 100" width="100%" height="100%">
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f3f4f6" stroke-width="12" class="donut-bg" />
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#4f46e5" stroke-width="12"
                                    stroke-dasharray="113 251" stroke-dashoffset="0" transform="rotate(-90 50 50)" />
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#06b6d4" stroke-width="12"
                                    stroke-dasharray="70.3 251" stroke-dashoffset="-113" transform="rotate(-90 50 50)" />
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#f59e0b" stroke-width="12"
                                    stroke-dasharray="30.1 251" stroke-dashoffset="-183.3" transform="rotate(-90 50 50)" />
                                <circle cx="50" cy="50" r="40" fill="transparent" stroke="#10b981" stroke-width="12"
                                    stroke-dasharray="37.6 251" stroke-dashoffset="-213.4" transform="rotate(-90 50 50)" />
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
                            <!-- North America -->
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">North America</div>
                                        <span class="badge badge-light-success fs-9 fw-bold">40%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">840,000</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            +6.2%
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-primary rounded">
                                        <div class="bg-primary rounded h-4px" style="width: 40%;"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Europe -->
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">Europe</div>
                                        <span class="badge badge-light-info fs-9 fw-bold">26%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">546,000</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            +4.8%
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-info rounded">
                                        <div class="bg-info rounded h-4px" style="width: 26%;"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Asia Pacific -->
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">Asia Pacific</div>
                                        <span class="badge badge-light-warning fs-9 fw-bold">18%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">378,000</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            +8.5%
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-warning rounded">
                                        <div class="bg-warning rounded h-4px" style="width: 18%;"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Latin America -->
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">Latin America</div>
                                        <span class="badge badge-light-success fs-9 fw-bold">12%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">252,000</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            +5.1%
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-success rounded">
                                        <div class="bg-success rounded h-4px" style="width: 12%;"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- Middle East & Africa -->
                            <div class="col-md-6 col-xxl-4">
                                <div class="region-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">Middle East & Africa</div>
                                        <span class="badge badge-light-danger fs-9 fw-bold">4%</span>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">84,000</span>
                                            <span class="text-muted fs-8 ms-1">users</span>
                                        </div>
                                        <span class="text-success fs-8 fw-bold d-flex align-items-center">
                                            <svg viewBox="0 0 24 24" style="width:10px; height:10px; fill:currentColor; margin-right:2px;">
                                                <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                            </svg>
                                            +3.2%
                                        </span>
                                    </div>
                                    <div class="h-4px mx-0 w-100 bg-light-danger rounded">
                                        <div class="bg-danger rounded h-4px" style="width: 4%;"></div>
                                    </div>
                                </div>
                            </div>
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
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                <span class="fs-7 fw-semibold text-gray-700">US East Edge Node (N. Virginia)</span>
                                <span class="badge badge-light-success fs-9 fw-bold">Active</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between border-bottom pb-2 mb-2">
                                <span class="fs-7 fw-semibold text-gray-700">EU Central Node (Frankfurt)</span>
                                <span class="badge badge-light-success fs-9 fw-bold">Active</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="fs-7 fw-semibold text-gray-700">AP South Node (Singapore)</span>
                                <span class="badge badge-light-success fs-9 fw-bold">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('script')
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
@endpush
@endsection