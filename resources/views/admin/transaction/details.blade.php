@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Transaction Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.transaction.list') }}">Transaction</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">Transaction Details</li>
                        </ul>
                    </div>

                    @if ($detail->payment_status == 2)
                        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                            <div class="row">
                                <div class="col-md-12">
                                    <a class="btn btn-light" target="_blank"
                                        href="{{ route('admin.transaction.invoice', $detail->uuid) }}"><i
                                            class="fa-solid fa-download"></i> Invoice</a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card mt-4 p-4">
                        <h5>User Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">User Name : </label>
                                <b>{{ $detail->user->name ?? '' }}</b>
                            </div>
                            <div class="col-md-6">
                                <label for="">Phone Number : </label>
                                <b>{{ getPhoneCode($detail->user?->id) }} {{ $detail->user->mobile_number ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Email : </label>
                                <b>{{ $detail->user->email ?? '' }}</b>
                            </div>
                            <div class="col-md-6">
                                <label for="">Subscription : </label>
                                <b>{{ $detail->subscription->title ?? '' }}</b>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4 p-4">
                        <h5>Transaction Details</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="">Transaction Id : </label>
                                <b>{{ $detail->transaction_id ?? '' }}
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
                                </b>
                            </div>
                            <div class="col-md-3">
                                <label for="">Amount : </label>
                                <b>Rs. {{ $detail->amount ?? 'N/A' }}
                                    {{ $detail->payment_status == 4 ? '(refunded)' : '' }}</b>
                            </div>
                            <div class="col-md-3">
                                <label for="">Timestamp : </label>
                                <b>{{ \Carbon\Carbon::parse($detail->created_at)->format('d-m-Y h:i a') ?? 'N/A' }}</b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script></script>
@endpush
