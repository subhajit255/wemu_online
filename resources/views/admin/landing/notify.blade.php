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
                                        Notify Me List</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Notify Me List</li>
                                    </ul>
                                </div>

                                <button type="submit" id="submitBtn" class="btn btn-dark">
                                    <span class="indicator-label">Send All</span>
                                    <span class="indicator-progress">Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>Sl No</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->email ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input disabled type="checkbox" data-uuid="{{ $detail->uuid }}"
                                                            data-table="faqs" class="form-check-input"
                                                            id="status_{{ $detail->id }}"
                                                            value="{{ $detail->is_send ?? 0 }}"{{ $detail->is_send == 1 ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="status_{{ $detail->id }}">{{ $detail->is_send == 1 ? 'Send' : 'Pending' }}</label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).on("click", "#submitBtn", function() {
                toastr.info('Please wait... DON\'T REFRESH THE PAGE. It will take some time to send notification to all users.');
                $.ajax({
                    url: "{{ route('admin.landing.page.notify.send') }}",
                    method: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        } else {
                            toastr.error(res.message);
                        }
                    },
                    error: function(err) {
                        toastr.error(err.statusText);
                    }
                });
            });
        </script>
    @endpush
@endsection
