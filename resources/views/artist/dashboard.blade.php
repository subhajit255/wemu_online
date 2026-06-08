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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ $metrics['streams']['total'] }}</span>
                            <span class="{{ $metrics['streams']['is_positive'] ? 'trend-up' : 'trend-down' }}">
                                <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; margin-right: 4px;">
                                    @if($metrics['streams']['is_positive'])
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                    @else
                                    <path d="M12 20l-8-8h6v-8h4v8h6z" />
                                    @endif
                                </svg>
                                {{ $metrics['streams']['trend'] }}%
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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ $metrics['listeners']['total'] }}</span>
                            <span class="{{ $metrics['listeners']['is_positive'] ? 'trend-up' : 'trend-down' }}">
                                <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; margin-right: 4px;">
                                    @if($metrics['listeners']['is_positive'])
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                    @else
                                    <path d="M12 20l-8-8h6v-8h4v8h6z" />
                                    @endif
                                </svg>
                                {{ $metrics['listeners']['trend'] }}%
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
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ $metrics['followers']['total'] }}</span>
                            <span class="{{ $metrics['followers']['is_positive'] ? 'trend-up' : 'trend-down' }}">
                                <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; margin-right: 4px;">
                                    @if($metrics['followers']['is_positive'])
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                    @else
                                    <path d="M12 20l-8-8h6v-8h4v8h6z" />
                                    @endif
                                </svg>
                                {{ $metrics['followers']['trend'] }}%
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
            <!-- Countries Reached -->
            <div class="col-md-3">
                <div class="card clean-metric-card h-100">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Countries Reached</span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ $metrics['countries']['total'] }}</span>
                            <span class="{{ $metrics['countries']['is_positive'] ? 'trend-up' : 'trend-down' }}">
                                <svg viewBox="0 0 24 24" style="width: 24px; height: 24px; margin-right: 4px;">
                                    @if($metrics['countries']['is_positive'])
                                    <path d="M12 4l-8 8h6v8h4v-8h6z" />
                                    @else
                                    <path d="M12 20l-8-8h6v-8h4v8h6z" />
                                    @endif
                                </svg>
                                {{ $metrics['countries']['trend'] }}%
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
                            <select id="stream_overview_filter" class="form-select form-select-sm form-select-solid dashboard-select" style="width: 130px;">
                                <option value="last_7_days">Last 7 Days</option>
                                <option value="this_month">This Month</option>
                                <option value="this_year">This Year</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-6">
                        <div class="chart-placeholder position-relative">
                            <div id="stream_overview_chart" style="width: 100%; height: 250px;"></div>
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
                        @forelse($topSongs as $index => $log)
                        @if($log->song)
                        <div class="song-list-item">
                            <div class="song-list-item-num">{{ $index + 1 }}</div>
                            <div class="song-list-item-img" style="background-image: url('{{ $log->song->cover_image_path }}'); background-size: cover; background-position: center;"></div>
                            <div class="song-list-item-title">{{ $log->song->title }}</div>
                            <div class="song-list-item-plays">{{ number_format($log->total_plays) }}</div>
                        </div>
                        @endif
                        @empty
                        <div class="text-center text-muted py-5">
                            No songs played yet.
                        </div>
                        @endforelse
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
                        @forelse($recentReleases as $release)
                        <div class="release-item">
                            <div class="release-item-img" style="background-image: url('{{ $release->image_path }}'); background-size: cover; background-position: center;"></div>
                            <div class="release-item-info">
                                <h4>{{ $release->title }}</h4>
                                <p>Album • {{ $release->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center text-muted py-5">
                            No recent releases found.
                        </div>
                        @endforelse
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
                            @forelse($audienceLocations as $location)
                            @php
                            $percentage = $totalAudience > 0 ? round(($location->total / $totalAudience) * 100) : 0;
                            @endphp
                            <div class="audience-row">
                                <span>{{ $location->country }}</span>
                                <span>{{ $percentage }}%</span>
                            </div>
                            @empty
                            <div class="text-center text-muted py-5">
                                No audience data available.
                            </div>
                            @endforelse
                            @if(count($audienceLocations) > 0)
                            <a href="{{ route('artist.analytics.index') }}" class="text-muted fs-7 text-hover-primary mt-4 d-inline-block">View full report</a>
                            @endif
                        </div>
                        <div class="w-50 d-flex align-items-center justify-content-center">
                            <div id="audience_location_chart" style="width: 100%; min-height: 200px;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        try {
            // Stream Overview Chart
            var streamDates = JSON.parse('{!! addslashes(json_encode(array_values($chartDates ?? []))) !!}');
            var streamCounts = JSON.parse('{!! addslashes(json_encode(array_values($chartStreams ?? []))) !!}');

            if (streamDates.length > 0 && typeof ApexCharts !== 'undefined') {
                var streamOptions = {
                    series: [{
                        name: 'Streams',
                        data: streamCounts
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        },
                        background: 'transparent'
                    },
                    colors: ['#4f46e5'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 3
                    },
                    xaxis: {
                        categories: streamDates,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#9ca3af'
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return val ? val.toFixed(0) : 0;
                            },
                            style: {
                                colors: '#9ca3af'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f3f4f6',
                        strokeDashArray: 4,
                    },
                    theme: {
                        mode: (document.documentElement.getAttribute('data-bs-theme') === 'dark') ? 'dark' : 'light'
                    }
                };
                var streamChart = new ApexCharts(document.querySelector("#stream_overview_chart"), streamOptions);
                streamChart.render();

                document.getElementById('stream_overview_filter').addEventListener('change', function(e) {
                    var filter = e.target.value;
                    fetch("{{ route('artist.analytics.streams') }}?filter=" + filter)
                        .then(response => response.json())
                        .then(res => {
                            if (res.status && res.data && res.data.chart) {
                                streamChart.updateSeries([{
                                    name: 'Streams',
                                    data: res.data.chart.data
                                }]);
                                streamChart.updateOptions({
                                    xaxis: {
                                        categories: res.data.chart.labels
                                    }
                                });
                            }
                        })
                        .catch(err => console.error("Error fetching stream data:", err));
                });
            } else {
                document.querySelector("#stream_overview_chart").innerHTML = '<div class="text-center text-muted d-flex align-items-center justify-content-center h-100">No stream data available</div>';
            }

            // Audience Location Chart
            var audienceLabels = JSON.parse('{!! addslashes(json_encode(isset($audienceLocations) ? $audienceLocations->pluck("country")->values()->toArray() : [])) !!}');
            var audienceSeries = JSON.parse('{!! addslashes(json_encode(isset($audienceLocations) ? $audienceLocations->pluck("total")->map(fn($val) => (int)$val)->values()->toArray() : [])) !!}');

            if (audienceSeries.length > 0 && typeof ApexCharts !== 'undefined') {
                var audienceOptions = {
                    series: audienceSeries,
                    labels: audienceLabels,
                    chart: {
                        type: 'donut',
                        height: 220,
                        background: 'transparent'
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '65%'
                            }
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    stroke: {
                        show: false
                    },
                    theme: {
                        mode: (document.documentElement.getAttribute('data-bs-theme') === 'dark') ? 'dark' : 'light'
                    }
                };
                var audienceChart = new ApexCharts(document.querySelector("#audience_location_chart"), audienceOptions);
                audienceChart.render();
            } else {
                document.querySelector("#audience_location_chart").innerHTML = '<div class="text-center text-muted d-flex align-items-center justify-content-center h-100" style="min-height: 200px;">No audience data</div>';
            }
        } catch (e) {
            console.error("Error rendering charts:", e);
        }
    });
</script>
<script src="{{ asset('assets/js/widgets.bundle.js') }}"></script>
@endpush
@endsection