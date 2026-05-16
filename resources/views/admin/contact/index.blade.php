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
                                        Help & Support</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Help & Support</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <div class="table-responsive">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>Sl No</th>
                                            <th style="width:20%">Name</th>
                                            <th style="width:20%">Phone Number</th>
                                            <th style="width:40%">Enquiry</th>
                                            @can('reply-help-support')
                                                <th>Reply</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->name ?? 'N/A' }}
                                                    {!! $detail->is_replied == 2
                                                        ? ' <span class="badge badge-light-success">Replied</span>'
                                                        : ' <span class="badge badge-light-danger">New</span>' !!}<br>
                                                    {{ $detail->email ?? 'N/A' }}
                                                </td>
                                                <td> +{{ $detail->phone_code ?? '61' }} {{ $detail->mobile_number ?? 'N/A' }}
                                                </td>
                                                <td>{!! Str::limit($detail->enquiry, 150, '...') ?? 'N/A' !!}</td>
                                                @can('reply-help-support')
                                                    <td>
                                                        <a href="javascript:void(0)" class="sendReply" aria-hidden="true"
                                                            data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                            data-uuid="{{ $detail->uuid }}" data-reply="{{ $detail->reply }}"
                                                            data-enquiry="{{ $detail->enquiry }}">Reply <i
                                                                class="fa-solid fa-arrow-right"></i></a>
                                                    </td>
                                                @endcan
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
            $(document).ready(function() {
                $(document).on('click', '.sendReply', function() {
                    $('#reply').val('');
                    var uuid = $(this).data('uuid');
                    var reply = $(this).data('reply');
                    var enquiry = $(this).data('enquiry');
                    $('#enquiry').html(enquiry);
                    $('#contact_id').val(uuid);
                    if (reply != null) {
                        $('#reply').val(reply);
                    }
                });
            });
        </script>
    @endpush
@endsection

@section('pageModal')
    <div class="modal fade modal-xl" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Reply To Customer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.contact.reply') }}" class="formSubmit" id="contactReply" method="POST">
                    <input type="hidden" name="contact_id" id="contact_id">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <label for="enquiry" class="form-label">Enquiry :</label>
                                <p id="enquiry" class="form-control" readonly></p>
                            </div>
                            <div class="col-md-12">
                                <textarea class="form-control" name="reply" id="reply" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submitBtn" class="btn btn-dark">
                            <span class="indicator-label">Send</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

