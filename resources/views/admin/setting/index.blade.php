@extends('layout.app')
@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                System Settings
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Settings</li>
            </ul>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="row g-5 g-xl-10">
            <div class="col-xl-6 mb-xl-10">
                
                <!-- Settings Card -->
                <div class="card clean-metric-card border-0 shadow-sm">
                    <div class="card-header border-0 pt-6 pb-0">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-3 mb-1">Global Preferences</span>
                            <span class="text-muted fw-semibold fs-7">Manage core platform functionality</span>
                        </h3>
                    </div>
                    
                    <div class="card-body py-8">
                        <form id="settingForm" action="{{ route('admin.setting.update') }}" method="POST" class="formSubmit">
                            @csrf
                            <input type="hidden" name="id" value="{{ $details->id ?? null }}">

                            <!-- Toggle Setting Item -->
                            <div class="d-flex flex-stack bg-light-primary rounded p-5 mb-7">
                                <!-- Text -->
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-35px me-3">
                                            <div class="symbol-label bg-primary bg-opacity-10">
                                                <i class="fa-solid fa-crown text-primary fs-4"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark text-hover-primary fs-5">Artist Subscriptions</span>
                                            <span class="text-muted fw-semibold d-block fs-7">Enable or disable the subscription feature for artists</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Toggle -->
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input h-30px w-50px cursor-pointer" type="checkbox" name="artist_subscription" id="artist_subscription" value="1" {{ (!isset($details->artist_subscription) || $details->artist_subscription == 1) ? 'checked' : '' }} />
                                    <label class="form-check-label" for="artist_subscription">
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="d-flex justify-content-end pt-5 border-top border-gray-200">
                                <button type="submit" id="submitBtn" class="btn btn-primary px-8 py-3 fw-bold">
                                    <span class="indicator-label">Save Changes</span>
                                    <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- End Card -->

            </div>
        </div>

    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        // Handle form submission purely for the UI button state, if the global formSubmit handles ajax it's fine.
        // The standard class "formSubmit" handles the ajax request in this project.
    });
</script>
@endpush
@endsection
