@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Dashboard
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Welcome back, The Weeknd ✌️</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <select class="form-select form-select-sm form-select-solid dashboard-select">
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
            <!-- Streams -->
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Streams</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">2.4M</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                12.5%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 25 Q 10 15, 20 20 T 40 10 T 60 20 T 80 5 T 100 15" fill="none" stroke="#10b981" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Listeners -->
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Listeners</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">840K</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                8.1%
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
            <!-- Followers -->
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Followers</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">120K</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                15.3%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 30 L 20 20 L 40 25 L 60 10 L 80 15 L 100 0" fill="none" stroke="#10b981" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Revenue -->
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Total Revenue</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">$12.6K</span>
                            <span class="trend-up">
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                </svg>
                                10.2%
                            </span>
                        </div>
                        <div class="sparkline-svg">
                            <svg viewBox="0 0 100 30" preserveAspectRatio="none" style="width:100%; height:100%;">
                                <path d="M0 25 Q 25 15, 50 20 T 100 5" fill="none" stroke="#10b981" stroke-width="2" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Stream Overview & Top Songs -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Stream Overview -->
            <div class="col-lg-8">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Stream Overview</span>
                        </h3>
                        <div class="card-toolbar">
                            <select class="form-select form-select-sm form-select-solid dashboard-select" style="width: 120px;">
                                <option value="1">This Month</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="chart-placeholder position-relative">
                            <!-- SVG Mock Line Chart -->
                            <svg viewBox="0 0 800 250" style="width:100%; height:100%;">
                                <!-- Grid lines -->
                                <line x1="0" y1="50" x2="800" y2="50" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="100" x2="800" y2="100" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="150" x2="800" y2="150" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="200" x2="800" y2="200" stroke="#f3f4f6" stroke-width="1" />

                                <!-- Line -->
                                <path d="M 0 150 L 100 120 L 200 160 L 300 80 L 400 130 L 500 170 L 600 100 L 700 140 L 800 40" fill="none" stroke="#4f46e5" stroke-width="3" />

                                <!-- Points -->
                                <circle cx="100" cy="120" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="200" cy="160" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="300" cy="80" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="400" cy="130" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="500" cy="170" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="600" cy="100" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />
                                <circle cx="700" cy="140" r="4" fill="#ffffff" stroke="#4f46e5" stroke-width="2" />

                                <!-- Labels -->
                                <text x="0" y="240" fill="#9ca3af" font-size="12">Apr 28</text>
                                <text x="200" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 5</text>
                                <text x="400" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 12</text>
                                <text x="600" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 19</text>
                                <text x="800" y="240" fill="#9ca3af" font-size="12" text-anchor="end">May 26</text>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Songs -->
            <div class="col-lg-4">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Top Songs</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="#" class="text-muted fs-7 text-hover-primary">See all</a>
                        </div>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="song-list-item">
                            <div class="song-list-item-num">1</div>
                            <div class="song-list-item-img"></div>
                            <div class="song-list-item-title">Blinding Lights</div>
                            <div class="song-list-item-plays">1.2M</div>
                        </div>
                        <div class="song-list-item">
                            <div class="song-list-item-num">2</div>
                            <div class="song-list-item-img"></div>
                            <div class="song-list-item-title">Save Your Tears</div>
                            <div class="song-list-item-plays">980K</div>
                        </div>
                        <div class="song-list-item">
                            <div class="song-list-item-num">3</div>
                            <div class="song-list-item-img"></div>
                            <div class="song-list-item-title">Die For You</div>
                            <div class="song-list-item-plays">760K</div>
                        </div>
                        <div class="song-list-item">
                            <div class="song-list-item-num">4</div>
                            <div class="song-list-item-img"></div>
                            <div class="song-list-item-title">Starboy</div>
                            <div class="song-list-item-plays">680K</div>
                        </div>
                        <div class="song-list-item">
                            <div class="song-list-item-num">5</div>
                            <div class="song-list-item-img"></div>
                            <div class="song-list-item-title">Earned It</div>
                            <div class="song-list-item-plays">420K</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Recent Releases & Audience -->
        <div class="row g-5 g-xl-10">
            <!-- Recent Releases -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Recent Releases</span>
                        </h3>
                        <div class="card-toolbar">
                            <a href="#" class="text-muted fs-7 text-hover-primary">See all</a>
                        </div>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="release-item">
                            <div class="release-item-img"></div>
                            <div class="release-item-info">
                                <h4>After Hours</h4>
                                <p>Album • Mar 20, 2024</p>
                            </div>
                        </div>
                        <div class="release-item">
                            <div class="release-item-img"></div>
                            <div class="release-item-info">
                                <h4>Dawn FM</h4>
                                <p>Album • Jan 7, 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audience Location -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Audience Location</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3 d-flex">
                        <div class="w-50 pe-5">
                            <div class="audience-row">
                                <span>United States</span>
                                <span>35%</span>
                            </div>
                            <div class="audience-row">
                                <span>India</span>
                                <span>16%</span>
                            </div>
                            <div class="audience-row">
                                <span>Indonesia</span>
                                <span>12%</span>
                            </div>
                            <div class="audience-row">
                                <span>Brazil</span>
                                <span>8%</span>
                            </div>
                            <div class="audience-row">
                                <span>Germany</span>
                                <span>5%</span>
                            </div>
                            <a href="#" class="text-muted fs-7 text-hover-primary mt-4 d-inline-block">View full report</a>
                        </div>
                        <div class="w-50 d-flex align-items-center justify-content-center">
                            <!-- Static Map Placeholder SVG -->
                            <svg viewBox="0 0 400 200" fill="#e5e7eb" width="100%">
                                <!-- Simplified world map paths -->
                                <path d="M 50 50 Q 80 40 100 80 Q 70 120 40 90 Z M 200 40 Q 250 20 300 60 Q 320 120 280 140 Q 200 160 180 100 Z M 320 100 Q 360 80 380 140 Q 340 180 300 140 Z" />
                                <circle cx="80" cy="70" r="5" fill="#4f46e5" />
                                <circle cx="280" cy="80" r="5" fill="#4f46e5" />
                                <circle cx="220" cy="110" r="5" fill="#4f46e5" />
                                <circle cx="340" cy="130" r="5" fill="#4f46e5" />
                            </svg>
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