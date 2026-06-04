@extends('layout.app')
@section('content')


<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">
        <div id="kt_app_content" class="app-content flex-column-fluid">
            <div id="kt_app_content_container" class="app-container container-xxl" style="padding-top: 30px;">
                
                <!-- Header -->
                <div class="d-flex flex-stack mb-8">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            {{ !empty($details) ? 'Edit Subscription' : 'Add Subscription' }}
                        </h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">Dashboard</li>
                            <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                            <li class="breadcrumb-item text-muted"><a href="{{ route('admin.subscription.list') }}" class="text-muted text-hover-primary">Subscriptions</a></li>
                            <li class="breadcrumb-item"><span class="bullet bg-gray-400 w-5px h-2px"></span></li>
                            <li class="breadcrumb-item text-muted">{{ !empty($details) ? 'Edit' : 'Add' }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="form-card">
                    <form id="subscriptionForm" action="{{ route('admin.subscription.add') }}" method="POST" class="formSubmit fileUpload" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" value="{{ $details->id ?? null }}">
                        
                        <div class="row g-5">
                            <div class="col-md-6 modern-form-group">
                                <label for="name">Plan Name <span class="text-danger">*</span></label>
                                <input type="text" class="modern-input" placeholder="e.g. Pro Artist Monthly" name="name" id="name" value="{{ $details->name ?? null }}">
                            </div>
                            <div class="col-md-6 modern-form-group">
                                <label for="tagline">Tagline</label>
                                <input type="text" class="modern-input" placeholder="e.g. Up to 6 Premium accounts" name="tagline" id="tagline" value="{{ $details->tagline ?? null }}">
                            </div>
                            <div class="col-md-3 modern-form-group">
                                <label for="available_for">Audience <span class="text-danger">*</span></label>
                                <select name="available_for" id="available_for" class="modern-input modern-select">
                                    <option value="">Select Audience</option>
                                    <option value="1" {{ !empty($details) && $details->available_for == 1 ? 'selected' : null }}>User</option>
                                    <option value="2" {{ !empty($details) && $details->available_for == 2 ? 'selected' : null }}>Artist</option>
                                </select>
                            </div>
                            <div class="col-md-2 modern-form-group">
                                <label for="interval">Billing Interval <span class="text-danger">*</span></label>
                                <select name="interval" id="interval" class="modern-input modern-select">
                                    <option value="">Select Interval</option>
                                    <option value="month" {{ (!empty($details) && $details->interval == 'month') || empty($details) ? 'selected' : null }}>Monthly</option>
                                    <option value="year" {{ !empty($details) && $details->interval == 'year' ? 'selected' : null }}>Yearly</option>
                                    <option value="week" {{ !empty($details) && $details->interval == 'week' ? 'selected' : null }}>Weekly</option>
                                </select>
                            </div>
                            <div class="col-md-2 modern-form-group">
                                <label for="interval_count">Interval Count <span class="text-danger">*</span></label>
                                <input type="number" class="modern-input" placeholder="e.g. 1" name="interval_count" id="interval_count" value="{{ $details->interval_count ?? 1 }}">
                            </div>
                            
                            <div class="col-md-6 modern-form-group">
                                <label for="price">Price <span class="text-danger">*</span></label>
                                <div class="modern-input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="modern-input number-only" placeholder="0.00" name="price" id="price" value="{{ $details->price ?? null }}">
                                </div>
                            </div>
                            <div class="col-md-3 modern-form-group">
                                <label for="currency">Currency <span class="text-danger">*</span></label>
                                <select name="currency" id="currency" class="modern-input modern-select">
                                    <option value="USD" {{ !empty($details) && $details->currency == 'USD' ? 'selected' : null }}>USD ($)</option>
                                    <option value="EUR" {{ !empty($details) && $details->currency == 'EUR' ? 'selected' : null }}>EUR (€)</option>
                                    <option value="GBP" {{ !empty($details) && $details->currency == 'GBP' ? 'selected' : null }}>GBP (£)</option>
                                    <option value="INR" {{ !empty($details) && $details->currency == 'INR' ? 'selected' : null }}>INR (₹)</option>
                                </select>
                            </div>

                            <div class="col-md-3 modern-form-group">
                                <label for="max_users">Max Users <span class="text-danger">*</span></label>
                                <input type="number" class="modern-input" placeholder="1 for Individual, 2 for Duo" name="max_users" id="max_users" value="{{ $details->max_users ?? 1 }}">
                            </div>
                            <div class="col-md-3 modern-form-group">
                                <label for="trial_days">Trial Days</label>
                                <input type="number" class="modern-input" placeholder="e.g. 30" name="trial_days" id="trial_days" value="{{ $details->trial_days ?? 0 }}">
                            </div>


                            <div class="col-12 mt-6">
                                <div class="form-check form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" value="1" id="requires_verification" name="requires_verification" {{ !empty($details) && $details->requires_verification ? 'checked' : '' }} />
                                    <label class="form-check-label fw-semibold text-gray-700" for="requires_verification">
                                        Requires Student/Third-Party Verification
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 modern-form-group mt-6">
                                <label for="description">Short Description</label>
                                <textarea class="modern-input" placeholder="A brief summary of the plan..." name="description" id="description" cols="30" rows="3">{{ $details->description ?? null }}</textarea>
                            </div>
                            
                            <div class="col-12 modern-form-group mt-6">
                                <label for="features">Features & Perks</label>
                                <textarea class="modern-input" name="features" id="features" cols="30" rows="6">{{ $details->features ?? null }}</textarea>
                            </div>
                        </div>

                        <div class="mt-8 d-flex justify-content-end">
                            <button type="submit" id="submitBtn" class="btn-purple-glow">
                                <span class="indicator-label"><i class="fa-solid fa-check-circle"></i> {{ !empty($details) ? 'Update Plan' : 'Create Plan' }}</span>
                                <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
    @push('script')
        <script src="{{ asset('assets/js/custom_js/cdn/ckeditor.js') }}"></script>
        <script>
            ClassicEditor
                .create(document.querySelector('#description'))
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        let editorData = editor.getData();
                        $('#description').val(editorData);
                    });
                })
                .catch(err => {
                    console.error(err.stack);
                });
                
            ClassicEditor
                .create(document.querySelector('#features'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo']
                })
                .then(editor => {
                    editor.model.document.on('change:data', () => {
                        let editorData = editor.getData();
                        $('#features').val(editorData);
                    });
                })
                .catch(err => {
                    console.error(err.stack);
                });
        </script>
    @endpush
@endsection
