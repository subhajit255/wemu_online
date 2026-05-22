@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Artist {{ !empty($details) ? 'Edit' : 'Add' }}</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.artist.list') }}" class="text-muted text-hover-primary">Artist</a>
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
                                <form id="userForm" action="{{ route('admin.artist.add') }}" method="POST"
                                    class="formSubmit fileUpload" enctype="multipart/form-data">
                                    <input type="hidden" name="id" name="id" value="{{ $details->id ?? null }}">
                                    <div class="row pt-2">
                                        <div class="col-md-6">
                                            <label>
                                                <span class="label_title">Artist Profile Image</span>
                                            </label>
                                            <div class="fv-row">
                                                @if (!empty($details->profile_image))
                                                    <style>
                                                        .image-input-placeholder {
                                                            background-image: url({{ $details->image_path }});
                                                        }

                                                        [data-bs-theme="dark"] .image-input-placeholder {
                                                            background-image: url({{ $details->image_path }});
                                                        }
                                                    </style>
                                                @else
                                                    <style>
                                                        .image-input-placeholder {
                                                            background-image: url("{{ asset('/assets/media/svg/files/blank-image.svg') }}");
                                                        }

                                                        [data-bs-theme="dark"] .image-input-placeholder {
                                                            background-image: url("{{ asset('/assets/media/svg/files/blank-image-dark.svg') }}");
                                                        }
                                                    </style>
                                                @endif
                                                <div class="image-input image-input-empty image-input-outline image-input-placeholder"
                                                    data-kt-image-input="true">
                                                    <div class="image-input-wrapper w-125px h-125px"></div>
                                                    <label
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="change" data-bs-toggle="tooltip"
                                                        title="Add image">
                                                        <div class="img_edit_btn_icon">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </div>
                                                        <input type="file" name="file" accept=".png, .jpg, .jpeg"
                                                            id="file" />
                                                        <input type="hidden" name="avatar_remove" />
                                                    </label>
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="cancel" data-bs-toggle="tooltip"
                                                        title="Cancel image">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </span>
                                                    <span
                                                        class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow"
                                                        data-kt-image-input-action="remove" data-bs-toggle="tooltip"
                                                        title="Remove logo">
                                                        <i class="bi bi-x fs-2"></i>
                                                    </span>
                                                </div>
                                                <div class="form-text" style="font-size: 10px; color: #000 !important;">
                                                    Allowed file types: png, jpg, jpeg.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="name" class="label-style">Artist Name</label>
                                                        <span class="text-danger">*</span>
                                                        <input type="text" class="form-control fromAlias"
                                                            placeholder="Enter Artist Name" name="name" id="name"
                                                            value="{{ $details->name ?? null }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 pt-4">
                                                    <div class="form-group">
                                                        <label for="username" class="label-style">Username / Handle</label>
                                                        <input type="text" class="form-control toAlias"
                                                            placeholder="Enter username handle" name="username" id="username"
                                                            value="{{ $details->username ?? null }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row pt-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile_number" class="label-style">Mobile Number</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control number-only"
                                                    placeholder="Enter mobile number" maxlength="10" name="mobile_number"
                                                    id="mobile_number" value="{{ $details->mobile_number ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email" class="label-style">Email Address</label>
                                                <span class="text-danger">*</span>
                                                <input type="text" class="form-control" placeholder="Enter Email Address"
                                                    name="email" id="email" value="{{ $details->email ?? null }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="button add-btn-div-save-style">
                                        <button type="submit" id="submitBtn" class="btn btn-dark">
                                            <span
                                                class="indicator-label">{{ !empty($details) ? 'Update' : 'Save' }}</span>
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
        <script></script>
    @endpush
@endsection
