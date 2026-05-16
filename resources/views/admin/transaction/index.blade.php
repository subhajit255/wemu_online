@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Transaction List</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Transaction List</li>
                        </ul>
                    </div>
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <form action="{{ route('admin.transaction.list') }}" method="GET" id="searchFrm">
                                <input type="hidden" name="payment_status_val" id="payment_status_val">
                                <div class="input-group">
                                    &nbsp;&nbsp;
                                    <label for="pagination" style="margin: 11px;">Pagination :</label>
                                    <select name="pagination" id="pagination" class="form-control">
                                        <option value="25" {{ Request::get('pagination') == 25 ? 'selected' : '' }}>25
                                        </option>
                                        <option value="50" {{ Request::get('pagination') == 50 ? 'selected' : '' }}>50
                                        </option>
                                        <option value="75" {{ Request::get('pagination') == 75 ? 'selected' : '' }}>75
                                        </option>
                                        <option value="100" {{ Request::get('pagination') == 100 ? 'selected' : '' }}>100
                                        </option>
                                    </select>
                                    <input type="text" class="form-control" placeholder="Search User"
                                        aria-label="Search Student" aria-describedby="button-search" id="search"
                                        name="search" value="{{ Request::get('search') ?? '' }}">
                                    <input type="text" class="form-control" id="daterange" name="daterange"
                                        value="{{ $startDate }} - {{ $endDate }}" />
                                    <button class="btn btn-primary searchBtn" type="submit"
                                        id="button-search">Search</button>
                                </div>
                            </form>
                            &nbsp;&nbsp;
                            <button type="button" class="btn btn-info goTo"
                                data-action="{{ route('admin.transaction.list') }}">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">

                        <div class="card-header border-0">
                            <div class="card-title" style="display: inline-block; margin-left: 71%;">
                                Payment Status -->
                                <div class="d-flex align-items-center position-relative my-1">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input paymentStatus" type="radio" name="payment_status" id="all" value="" {{ Request::get('payment_status_val') == '' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="all">All</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input paymentStatus" type="radio" name="payment_status" id="paid" value="2" {{ Request::get('payment_status_val') == '2' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="paid">Paid</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input paymentStatus" type="radio" name="payment_status" id="pending" value="1" {{ Request::get('payment_status_val') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="pending">Pending</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input paymentStatus" type="radio" name="payment_status" id="failed" value="3" {{ Request::get('payment_status_val') == '3' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="failed">Failed</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive card-body pt-0">
                            <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th>Sl No</th>
                                        <th>User</th>
                                        <th>Subscription</th>
                                        <th>Transaction Date</th>
                                        <th>Amount</th>
                                        <th>Payment Status</th>
                                        <th class="text-end min-w-125px">
                                            <center>
                                                Details
                                            </center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="fw-semibold text-gray-600">
                                    @forelse ($details as $detail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $detail->user?->name ?? 'N/A' }}<br>
                                                <i class="fa-solid fa-phone"></i> {{ getPhoneCode($detail->user?->id) }}
                                                {{ $detail->user?->mobile_number ?? 'N/A' }}<br>
                                                <i class="fa-regular fa-envelope"></i>
                                                {{ $detail->user?->email ?? 'N/A' }}
                                            </td>
                                            <td>{{ $detail->subscription?->title ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('Y-m-d') ?? 'N/A' }}
                                            </td>
                                            <td>{{ getCurrency() }}{{ $detail->amount ?? 'N/A' }}</td>
                                            <td>
                                                <center>
                                                    @if ($detail->payment_status == 1)
                                                        <span class="badge badge-warning">Pending</span>
                                                    @elseif ($detail->payment_status == 2)
                                                        <span class="badge badge-success">Paid</span>
                                                    @elseif ($detail->payment_status == 3)
                                                        <span class="badge badge-danger">Failed</span>
                                                    @elseif ($detail->payment_status == 4)
                                                        <span class="badge badge-orange">Refund</span>
                                                    @else
                                                        <span class="badge badge-secondary">Unknown</span>
                                                    @endif
                                                </center>
                                            </td>
                                            <td class="text-end">
                                                <div class="menu-item px-3">
                                                    <a class="btn btn-light"
                                                        href="{{ route('admin.transaction.details', $detail->uuid) }}">View</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">No data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {!! $details->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $(document).on("click", ".searchBtn", function() {
            if ($('#search').val() == '' || $('#daterange').val() == '') {
                toastr.info("Searching...");
            } else {
                $('#searchFrm').submit();
            }
        });
        $(document).on("change", "#pagination", function() {
            $('#searchFrm').submit();
        });

        $(document).on("change", ".paymentStatus", function() {
            const paymentStatus = $(this).val();
            $('#payment_status_val').val(paymentStatus);
            $('#searchFrm').submit();
        });
    </script>
@endpush
