@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Audience
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Understand your listeners</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <select class="form-select form-select-sm form-select-solid dashboard-select" onchange="window.location.href='?filter='+this.value">
                <option value="this_month" {{ (isset($filter) && $filter === 'this_month') ? 'selected' : '' }}>This Month</option>
                <option value="last_month" {{ (isset($filter) && $filter === 'last_month') ? 'selected' : '' }}>Last Month</option>
                <option value="this_year" {{ (isset($filter) && $filter === 'this_year') ? 'selected' : '' }}>This Year</option>
            </select>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!-- Top Metrics Row -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted d-block mb-2">Total Listeners</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalListeners) }}</span>
                            <!-- Trend logic can be added later -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted d-block mb-2">New Listeners</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($newListeners) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted d-block mb-2">Countries</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($countriesCount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted d-block mb-2">Cities</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($citiesCount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="row g-5 g-xl-10">
            <div class="col-lg-12">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6 pt-3 d-flex flex-wrap">
                        <div class="w-100 w-lg-50 pe-lg-5 pt-5">
                            <h3 class="card-title mb-5">
                                <span class="fw-bold fs-4 text-dark">Top Countries</span>
                            </h3>
                            @forelse($topCountries as $index => $country)
                            <div class="audience-list-item d-flex justify-content-between align-items-center mb-4">
                                <div class="d-flex align-items-center">
                                    <span class="text-muted fw-bold me-4">{{ $index + 1 }}</span>
                                    <span class="text-dark fw-semibold">{{ $country->country }}</span>
                                </div>
                                <span class="text-dark fw-bold">{{ number_format($country->total) }}</span>
                            </div>
                            @empty
                            <p class="text-muted">No data available.</p>
                            @endforelse
                            <a href="#" class="text-primary fs-7 text-hover-primary mt-4 d-inline-block fw-semibold">View detailed report</a>
                        </div>
                        <div class="w-100 w-lg-50 d-flex align-items-center justify-content-center p-5">
                            <!-- Interactive World Map -->
                            <div id="audience-map" class="map-placeholder w-100" style="height: 350px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<!-- JSVectorMap CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css" />
<!-- JSVectorMap JS -->
<script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
<script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @php
            $countryCodeMap = [
                'United States' => 'US', 'India' => 'IN', 'Indonesia' => 'ID', 
                'Brazil' => 'BR', 'Germany' => 'DE', 'United Kingdom' => 'GB', 
                'Canada' => 'CA', 'Australia' => 'AU'
            ];
        @endphp

        var audienceData = {
            @foreach($topCountries as $country)
            "{{ $countryCodeMap[$country->country] ?? 'US' }}": '{{ number_format($country->total) }}',
            @endforeach
        };
        
        var mapValues = {
            @foreach($topCountries as $country)
            "{{ $countryCodeMap[$country->country] ?? 'US' }}": {{ $country->total }},
            @endforeach
        };

        var map = new jsVectorMap({
            selector: "#audience-map",
            map: "world",
            zoomOnScroll: false,
            zoomButtons: true,
            regionStyle: {
                initial: {
                    fill: "#e5e7eb",
                    fillOpacity: 1,
                    stroke: '#ffffff',
                    strokeWidth: 0.5,
                },
                hover: {
                    fill: "#a855f7",
                    cursor: 'pointer'
                }
            },
            visualizeData: {
                scale: ['#d8b4fe', '#8b5cf6'],
                values: mapValues
            },
            onRegionTooltipShow: function(event, tooltip, code) {
                if (audienceData[code]) {
                    tooltip.text(tooltip.text() + ' - ' + audienceData[code] + ' Listeners');
                } else {
                    tooltip.text(tooltip.text() + ' - 0 Listeners');
                }
            }
        });
    });
</script>
@endpush
@endsection
