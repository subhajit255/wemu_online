@extends('layout.app')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Promotion
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Promote your music and grow</span>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        
        <div class="row g-5 g-xl-8 mb-10">
            <!-- Playlist Pitching -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm bg-body promo-card">
                    <div class="card-body p-9 d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fa-solid fa-rocket fs-2x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1">Playlist Pitching</h3>
                                <div class="text-muted fs-6">Submit your song to our editorial playlists</div>
                            </div>
                        </div>
                        <div class="mt-auto pt-4">
                            <a href="#" class="fw-bold text-primary text-hover-dark fs-6">Pitch a Song <i class="fa-solid fa-arrow-right ms-2 fs-7"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ad Campaigns -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm bg-body promo-card">
                    <div class="card-body p-9 d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fa-solid fa-chart-column fs-2x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1">Ad Campaigns</h3>
                                <div class="text-muted fs-6">Run ads to reach more listeners</div>
                            </div>
                        </div>
                        <div class="mt-auto pt-4">
                            <a href="#" class="fw-bold text-primary text-hover-dark fs-6">Create Campaign <i class="fa-solid fa-arrow-right ms-2 fs-7"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pre-save Campaign -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm bg-body promo-card">
                    <div class="card-body p-9 d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fa-solid fa-calendar-check fs-2x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1">Pre-save Campaign</h3>
                                <div class="text-muted fs-6">Grow your audience before release</div>
                            </div>
                        </div>
                        <div class="mt-auto pt-4">
                            <a href="#" class="fw-bold text-primary text-hover-dark fs-6">Create Pre-save <i class="fa-solid fa-arrow-right ms-2 fs-7"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Smart Links -->
            <div class="col-md-6">
                <div class="card h-100 border-0 shadow-sm bg-body promo-card">
                    <div class="card-body p-9 d-flex flex-column">
                        <div class="d-flex align-items-center mb-5">
                            <div class="symbol symbol-50px me-5">
                                <span class="symbol-label bg-light-primary">
                                    <i class="fa-solid fa-link fs-2x text-primary"></i>
                                </span>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1">Smart Links</h3>
                                <div class="text-muted fs-6">Share your music everywhere</div>
                            </div>
                        </div>
                        <div class="mt-auto pt-4">
                            <a href="#" class="fw-bold text-primary text-hover-dark fs-6">Create Link <i class="fa-solid fa-arrow-right ms-2 fs-7"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-5">
            <h2 class="fw-bold text-dark mb-6">Additional Flows</h2>
            <div class="card border-0 shadow-sm bg-body promo-card opacity-50 cursor-not-allowed">
                <div class="card-body p-9 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-5">
                            <span class="symbol-label bg-light">
                                <i class="fa-solid fa-cube fs-2x text-muted"></i>
                            </span>
                        </div>
                        <div>
                            <h3 class="fw-bold text-dark mb-1">More tools coming soon</h3>
                            <div class="text-muted fs-6">We are working on expanding your promotional toolkit.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('style')
<style>
    .promo-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px;
    }
    .promo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }
</style>
@endpush

@endsection
