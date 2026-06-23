@extends('layout.app')
@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Platform Analytics
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Comprehensive overview of platform subscriptions and revenue</span>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Top Metrics Row -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- User Subscriptions -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-primary h-100" style="border-top: 4px solid #4f46e5;">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">User Subscriptions</span>
                            <div class="symbol symbol-35px">
                                <div class="symbol-label bg-light-primary text-primary">
                                    <i class="fa fa-users fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-2hx fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($userSubscriptionCount) }}</span>
                            <span class="text-muted fs-7">Active Listeners</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Artist Subscriptions -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-info h-100" style="border-top: 4px solid #06b6d4;">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Artist Subscriptions</span>
                            <div class="symbol symbol-35px">
                                <div class="symbol-label bg-light-info text-info">
                                    <i class="fa fa-microphone fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-2hx fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($artistSubscriptionCount) }}</span>
                            <span class="text-muted fs-7">Active Creators</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Subscriptions -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-success h-100" style="border-top: 4px solid #10b981;">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Total Subscriptions</span>
                            <div class="symbol symbol-35px">
                                <div class="symbol-label bg-light-success text-success">
                                    <i class="fa fa-check-circle fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-2hx fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalActiveSubscriptions) }}</span>
                            <span class="text-muted fs-7">Overall Active Plans</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-warning h-100" style="border-top: 4px solid #f59e0b;">
                    <div class="card-body p-6">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <span class="fs-6 fw-semibold text-muted">Total Revenue</span>
                            <div class="symbol symbol-35px">
                                <div class="symbol-label bg-light-warning text-warning">
                                    <i class="fa fa-dollar-sign fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="fs-2hx fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($totalRevenue, 2) }}</span>
                            <span class="text-muted fs-7">Lifetime Platform Revenue</span>
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
                        <div class="card-toolbar">
                            <span class="badge badge-light-primary fw-bold fs-6 px-4 py-2">${{ number_format($currentMonthRevenue, 2) }}</span>
                        </div>
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
