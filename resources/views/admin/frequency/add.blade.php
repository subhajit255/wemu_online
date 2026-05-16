@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Service Frequency {{ !empty($details) ? 'Edit' : 'Add' }}</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.frequency.list') }}" class="text-muted text-hover-primary">Service
                                    Frequency</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card">
                        <div class="card-body pt-6">
                            <div class="container">
                                <form id="frequencyForm" action="{{ route('admin.frequency.add') }}" method="POST"
                                    class="formSubmit fileUpload" enctype="multipart/form-data">
                                    <input type="hidden" name="id" name="id" value="{{ $details->id ?? null }}">
                                    <div class="row pt-2">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="label-style">Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Name"
                                                    name="name" id="name" value="{{ $details->name ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="day_count" class="label-style">Days Counts</label>
                                                <input type="text" class="form-control number-only" placeholder="Enter Days Count"
                                                    name="day_count" id="day_count" value="{{ $details->day_count ?? null }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="button add-btn-div-save-style">
                                        <button type="submit" id="submitBtn" class="btn btn-dark">
                                            <span class="indicator-label">{{ !empty($details) ? 'Update' : 'Save' }}</span>
                                            <span class="indicator-progress">Please wait...
                                                <span
                                                    class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
    @endpush
@endsection
