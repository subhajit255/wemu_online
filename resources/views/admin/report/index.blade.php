@extends('layout.app')
@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                System Reports
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Reports</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <form method="GET" action="{{ route('admin.report.index') }}" class="d-flex align-items-center" id="filter-form">
                <select name="period" class="form-select form-select-sm form-select-solid w-150px" onchange="document.getElementById('filter-form').submit()">
                    <option value="all" {{ request('period') == 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </form>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Row 1: Top Metrics -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-primary h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Revenue</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($totalRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-success h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">This Month Revenue</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($thisMonthRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-info h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Artists</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalArtists) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-warning h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Listeners</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalListeners) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Current Month Revenue Chart -->
            <div class="col-lg-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5 border-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 text-dark">Current Month Revenue</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Daily breakdown for {{ now()->format('F Y') }}</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end p-0">
                        <div id="current_month_chart" class="w-100 min-h-300px"></div>
                    </div>
                </div>
            </div>

            <!-- Current Year Revenue Chart -->
            <div class="col-lg-6">
                <div class="card card-flush h-lg-100">
                    <div class="card-header pt-5 border-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 text-dark">Current Year Revenue</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Monthly breakdown for {{ now()->format('Y') }}</span>
                        </h3>
                    </div>
                    <div class="card-body d-flex align-items-end p-0">
                        <div id="current_year_chart" class="w-100 min-h-300px"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Most Liked Songs & Most Followed Artists -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Most Liked Songs -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Most Liked Songs</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        @forelse($mostLikedSongs as $index => $item)
                        <div class="song-list-item mb-3 d-flex align-items-center">
                            <div class="song-list-item-num fw-bold text-muted me-3" style="width: 20px;">{{ $index + 1 }}</div>
                            <div class="song-list-item-img gradient-avatar d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); font-size: 1.2rem;">
                                ❤️
                            </div>
                            <div class="song-list-item-title ms-3 flex-grow-1">
                                <div class="fw-semibold text-dark">{{ $item->song ? $item->song->title : 'Unknown' }}</div>
                                <div class="text-muted fs-8">{{ $item->song && $item->song->artist ? $item->song->artist->name : 'Unknown Artist' }}</div>
                            </div>
                            <div class="song-list-item-plays fw-bold text-dark">{{ number_format($item->total_likes) }} likes</div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-5">No likes recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Most Followed Artists -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Most Followed Artists</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        @forelse($mostFollowedArtists as $index => $item)
                        <div class="song-list-item mb-3 d-flex align-items-center">
                            <div class="song-list-item-num fw-bold text-muted me-3" style="width: 20px;">{{ $index + 1 }}</div>
                            <div class="song-list-item-img gradient-avatar d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); font-size: 1.2rem;">
                                🧑‍🎤
                            </div>
                            <div class="song-list-item-title ms-3 flex-grow-1">
                                <div class="fw-semibold text-dark">{{ $item->artist ? $item->artist->name : 'Unknown Artist' }}</div>
                            </div>
                            <div class="song-list-item-plays fw-bold text-dark">{{ number_format($item->total_followers) }} followers</div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-5">No followers recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Top Searches -->
        <div class="row g-5 g-xl-10">
            <div class="col-lg-12">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Top Search Keywords</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="row g-4">
                            @forelse($topSearches as $item)
                            <div class="col-md-4">
                                <div class="region-card border rounded p-4" style="background-color: #f9f9f9; transition: all 0.3s ease;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">"{{ $item->keyword }}"</div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">{{ number_format($item->search_count) }}</span>
                                            <span class="text-muted fs-8 ms-1">searches</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-muted text-center py-5">No search keywords recorded yet</div>
                            @endforelse
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
        // --- Current Month Revenue Chart (Area Chart) ---
        var monthData = @json(array_values($dailyRevenueData ?? []));
        var monthLabels = @json($monthLabels ?? []);

        if (document.querySelector("#current_month_chart") && monthData.length > 0) {
            var monthOptions = {
                series: [{
                    name: 'Revenue',
                    data: monthData
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: { show: false },
                    zoom: { enabled: false },
                    fontFamily: 'inherit'
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
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: monthLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#a1a5b7', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#a1a5b7', fontSize: '12px' },
                        formatter: function (val) { return "$" + val }
                    }
                },
                grid: {
                    borderColor: '#f1f1f4',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                tooltip: {
                    y: { formatter: function (val) { return "$" + val } }
                }
            };

            var monthChart = new ApexCharts(document.querySelector("#current_month_chart"), monthOptions);
            monthChart.render();
        }

        // --- Current Year Revenue Chart (Bar Chart) ---
        var yearData = @json(array_values($monthlyRevenueData ?? []));
        var yearLabels = @json($yearLabels ?? []);

        if (document.querySelector("#current_year_chart") && yearData.length > 0) {
            var yearOptions = {
                series: [{
                    name: 'Revenue',
                    data: yearData
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '45%',
                    }
                },
                colors: ['#06b6d4'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: yearLabels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: {
                        style: { colors: '#a1a5b7', fontSize: '12px' }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#a1a5b7', fontSize: '12px' },
                        formatter: function (val) { return "$" + val }
                    }
                },
                grid: {
                    borderColor: '#f1f1f4',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                tooltip: {
                    y: { formatter: function (val) { return "$" + val } }
                }
            };

            var yearChart = new ApexCharts(document.querySelector("#current_year_chart"), yearOptions);
            yearChart.render();
        }
    });
</script>
@endpush
@endsection
