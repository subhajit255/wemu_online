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
            <select id="time_filter" class="form-select form-select-sm form-select-solid dashboard-select">
                <option value="1">This Month</option>
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
                                <span id="total_streams_count" class="fs-1 fw-bold text-dark me-3">0</span>
                                <span id="growth_percentage" class="trend-up fs-7 fw-semibold">
                                    <i class="fa fa-arrow-up"></i> +0.0%
                                </span>
                            </div>
                        </h3>
                    </div>
                    <div class="card-body p-6">
                        <div id="streams_chart" class="chart-placeholder position-relative w-100" style="height: 250px;">
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
@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var chart = null;

        function renderChart(labels, data) {
            if (chart) {
                chart.destroy();
            }

            var options = {
                series: [{
                    name: "Streams",
                    data: data
                }],
                chart: {
                    height: 250,
                    type: 'area',
                    toolbar: { show: false }
                },
                colors: ['#8b5cf6'],
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 3 },
                xaxis: {
                    categories: labels,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
                },
                yaxis: {
                    labels: { style: { colors: '#9ca3af', fontSize: '12px' } }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                    yaxis: { lines: { show: true } }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.3,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                }
            };

            chart = new ApexCharts(document.querySelector("#streams_chart"), options);
            chart.render();
        }

        function fetchChartData() {
            let filter = document.getElementById('time_filter').value;
            fetch("{{ route('artist.analytics.streams') }}?filter=" + filter)
                .then(response => response.json())
                .then(res => {
                    if (res.status) {
                        document.getElementById('total_streams_count').innerText = res.data.total_streams;
                        
                        let growthHtml = '';
                        if (res.data.is_positive) {
                            growthHtml = '<span class="text-success"><i class="fa fa-arrow-up text-success"></i> ' + res.data.growth_percentage + '</span>';
                        } else {
                            growthHtml = '<span class="text-danger"><i class="fa fa-arrow-down text-danger"></i> ' + res.data.growth_percentage + '</span>';
                        }
                        document.getElementById('growth_percentage').innerHTML = growthHtml;
                        
                        renderChart(res.data.chart.labels, res.data.chart.data);
                    }
                })
                .catch(error => console.error("Error fetching analytics data:", error));
        }

        document.getElementById('time_filter').addEventListener('change', fetchChartData);
        
        // Initial load
        fetchChartData();
    });
</script>
@endpush
