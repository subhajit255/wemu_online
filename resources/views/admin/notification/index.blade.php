@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">

            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">
                        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                                    <!--begin::Title-->
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Notifications</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Notifications</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>Sl No</th>
                                            <th style="width:40%">Notification</th>
                                            <th>Customer Name</th>
                                            <th>Email</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details as $detail)
                                            @php
                                                $route = route('admin.contact.list');
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td style="cursor:pointer" onclick="readNotification({{ $detail->id }})"
                                                    class="goToo" data-action="{{ $route }}">
                                                    {{ $detail->title ?? 'N/A' }} {!! $detail->is_read == 1
                                                        ? ' <span class="badge badge-light-success">Read</span>'
                                                        : ' <span class="badge badge-light-danger">Un-Read</span>' !!}</td>
                                                <td>
                                                    {{ $detail->contact->name ?? 'N/A' }}<br>
                                                    M : +{{ $detail->contact->phone_code ?? '61' }} {{ $detail->contact->mobile_number ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    {{ $detail->contact->email ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    <a href="{{ $route }}">
                                                        Go <i class="fa-solid fa-right-long"></i>
                                                    </a>
                                                </td>
                                                {{-- <td>
                                                <div class="form-check form-switch">
                                                    <input type="checkbox" data-uuid="{{ $detail->uuid }}" data-table="notifications"
                                                        class="form-check-input isVerified"
                                                        id="status_{{ $detail->id }}"
                                                        value="{{ $detail->is_active ?? 0 }}"{{ $detail->is_active == 1 ? 'checked' : '' }}>
                                                    <label class="custom-control-label"
                                                        for="status_{{ $detail->id }}">{{ $detail->is_active == 1 ? 'Active' : 'In-Active' }}</label>
                                                </div>
                                            </td> --}}
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center">No Notification Found</td>
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
    </div>
@endsection

@push('script')
    <script>
        function readNotification(notificationId) {
            $.ajax({
                method: 'post',
                url: "{{ route('admin.read.notification') }}",
                data: {
                    notificationId
                },
                success: function(res) {

                }
            });
        }
    </script>
@endpush
