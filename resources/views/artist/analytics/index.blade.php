@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Analytics
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Detailed performance insights</span>
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
        <!-- Top Row: Streams Chart -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-lg-12">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title flex-column">
                            <span class="card-label fw-bold fs-6 text-muted mb-2">Streams</span>
                            <div class="d-flex align-items-center">
                                <span class="fs-1 fw-bold text-dark me-3">2.4M</span>
                                <span class="trend-up fs-7 fw-semibold">
                                    <i class="fa fa-arrow-up"></i> 12.3%
                                </span>
                            </div>
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div class="chart-placeholder position-relative w-100" style="height: 250px;">
                            <svg viewBox="0 0 1000 250" style="width:100%; height:100%;">
                                <!-- Grid lines -->
                                <line x1="0" y1="50" x2="1000" y2="50" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="100" x2="1000" y2="100" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="150" x2="1000" y2="150" stroke="#f3f4f6" stroke-width="1" />
                                <line x1="0" y1="200" x2="1000" y2="200" stroke="#f3f4f6" stroke-width="1" />

                                <!-- Line -->
                                <path d="M 0 180 L 150 170 L 300 130 L 450 130 L 600 50 L 750 110 L 900 80 L 1000 50" fill="none" stroke="#8b5cf6" stroke-width="3" />

                                <!-- Points -->
                                <circle cx="150" cy="170" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />
                                <circle cx="300" cy="130" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />
                                <circle cx="450" cy="130" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />
                                <circle cx="600" cy="50" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />
                                <circle cx="750" cy="110" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />
                                <circle cx="900" cy="80" r="4" fill="#ffffff" stroke="#8b5cf6" stroke-width="2" />

                                <!-- Labels -->
                                <text x="150" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">Apr 26</text>
                                <text x="300" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 3</text>
                                <text x="450" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 10</text>
                                <text x="600" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 17</text>
                                <text x="750" y="240" fill="#9ca3af" font-size="12" text-anchor="middle">May 24</text>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Donut Charts -->
        <div class="row g-5 g-xl-10">
            <!-- Sources -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-5 text-dark">Sources</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="w-50 d-flex justify-content-center">
                            <svg viewBox="0 0 100 100" style="width: 150px; height: 150px;">
                                <!-- Playlist (45%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#8b5cf6" stroke-width="20" stroke-dasharray="113 251" transform="rotate(-90 50 50)" />
                                <!-- Search (25%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#0ea5e9" stroke-width="20" stroke-dasharray="62.8 251" transform="rotate(72 50 50)" />
                                <!-- Radio (10%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#10b981" stroke-width="20" stroke-dasharray="25.1 251" transform="rotate(162 50 50)" />
                                <!-- Other (20%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#cbd5e1" stroke-width="20" stroke-dasharray="50.2 251" transform="rotate(198 50 50)" />
                                <circle r="30" cx="50" cy="50" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="w-50 ps-5">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-primary me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Playlist</span>
                                </div>
                                <span class="text-dark fw-bold">45%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-info me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Search</span>
                                </div>
                                <span class="text-dark fw-bold">25%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-success me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Radio</span>
                                </div>
                                <span class="text-dark fw-bold">10%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-secondary me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Other</span>
                                </div>
                                <span class="text-dark fw-bold">20%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Devices -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title">
                            <span class="card-label fw-bold fs-5 text-dark">Devices</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="w-50 d-flex justify-content-center">
                            <svg viewBox="0 0 100 100" style="width: 150px; height: 150px;">
                                <!-- Mobile (60%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#8b5cf6" stroke-width="20" stroke-dasharray="150.7 251" transform="rotate(-90 50 50)" />
                                <!-- Desktop (20%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#0ea5e9" stroke-width="20" stroke-dasharray="50.2 251" transform="rotate(126 50 50)" />
                                <!-- Tablet (15%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#10b981" stroke-width="20" stroke-dasharray="37.6 251" transform="rotate(198 50 50)" />
                                <!-- Other (5%) -->
                                <circle r="40" cx="50" cy="50" fill="transparent" stroke="#cbd5e1" stroke-width="20" stroke-dasharray="12.5 251" transform="rotate(252 50 50)" />
                                <circle r="30" cx="50" cy="50" fill="#ffffff" />
                            </svg>
                        </div>
                        <div class="w-50 ps-5">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-primary me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Mobile</span>
                                </div>
                                <span class="text-dark fw-bold">60%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-info me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Desktop</span>
                                </div>
                                <span class="text-dark fw-bold">20%</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-success me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Tablet</span>
                                </div>
                                <span class="text-dark fw-bold">15%</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <span class="bullet bullet-dot bg-secondary me-2 w-10px h-10px"></span>
                                    <span class="text-muted fw-semibold">Other</span>
                                </div>
                                <span class="text-dark fw-bold">5%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
