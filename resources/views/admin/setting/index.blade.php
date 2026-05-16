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
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Setting {{ !empty($details) ? 'Update' : 'Add' }}</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}"
                                                class="text-muted text-hover-primary">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.setting.update') }}"
                                                class="text-muted text-hover-primary">Setting</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="card-body pt-6">
                                <div class="container">
                                    <form id="uomForm" action="{{ route('admin.setting.update') }}" method="POST"
                                        class="formSubmit fileUpload" enctype="multipart/form-data">
                                        <input type="hidden" name="id" name="id"
                                            value="{{ $details->id ?? null }}">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="login_welcome_title" class="label-style">Login Welcome
                                                        Text</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter login welcome title" name="login_welcome_title"
                                                        id="login_welcome_title"
                                                        value="{{ $details->login_welcome_title ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="login_welcome_description" class="label-style">Login Welcome
                                                        Description</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <textarea class="form-control" placeholder="Enter login welcome description" name="login_welcome_description"
                                                        id="login_welcome_description" cols="30" rows="4">{{ $details->login_welcome_description ?? null }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="login_welcome_title" class="label-style">Flash Screen
                                                        Text</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter Flash Screen title" name="flash_screen_text"
                                                        id="flash_screen_text"
                                                        value="{{ $details->flash_screen_text ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="login_welcome_title" class="label-style">Social
                                                    Media</label>
                                                <div class="col-md-2" style="padding-top: 11px">
                                                    <div class="form-check">
                                                        <input class="form-check-input checkValChange" type="checkbox"
                                                            name="show_social_media" id="show_social_media"
                                                            value="{{ $details->show_social_media ?? 0 }}"
                                                            {{ $details->show_social_media == 1 ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="show_social_media">
                                                            {{ $details->show_social_media == 1 ? 'Show' : 'Hide' }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-4">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="instagram" class="label-style">Instagram</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter instagram URL" name="instagram" id="instagram"
                                                        value="{{ $details->instagram ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="facebook" class="label-style">Facebook</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter facebook URL" name="facebook" id="facebook"
                                                        value="{{ $details->facebook ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="twitter" class="label-style">Twitter</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter twitter URL" name="twitter" id="twitter"
                                                        value="{{ $details->twitter ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="linkedin" class="label-style">Linkedin</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter linkedin URL" name="linkedin" id="linkedin"
                                                        value="{{ $details->linkedin ?? null }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="contact_email" class="label-style">Contact Email</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter contact email" name="contact_email"
                                                        id="contact_email" value="{{ $details->contact_email ?? null }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="contact_number" class="label-style">Contact Number</label>
                                                    {{-- <span class="text-danger">*</span> --}}
                                                    <div class="input-group">
                                                        <span class="input-group-text">+61</span>
                                                        <input type="text" class="form-control number-only"
                                                            placeholder="Enter contact number" name="contact_number"
                                                            id="contact_number" maxlength="10"
                                                            value="{{ $details->contact_number ?? null }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" style="padding-top: 37px">
                                                <div class="form-check">
                                                    <input class="form-check-input checkValChange" type="checkbox"
                                                        name="show_contact_number" id="show_contact_number"
                                                        value="{{ $details->show_contact_number ?? 0 }}"
                                                        {{ $details->show_contact_number == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="show_contact_number">
                                                        {{ $details->show_contact_number == 1 ? 'Show' : 'Hide' }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="term_and_condition" class="label-style">Term and
                                                        Condition</label>
                                                    <textarea class="form-control" name="term_and_condition" id="term_and_condition" cols="30" rows="4">{{ $details->term_and_condition ?? null }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="privacy_policy" class="label-style">Privacy Policy</label>
                                                    <textarea class="form-control" name="privacy_policy" id="privacy_policy" cols="30" rows="4">{{ $details->privacy_policy ?? null }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-4">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="about_us" class="label-style">About us</label>
                                                    <textarea class="form-control" name="about_us" id="about_us" cols="30" rows="4">{{ $details->about_us ?? null }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <hr>
                                        <h4 class="pt-4">Introduction -></h4>

                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="income_note" class="label-style">Income Note</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter income note" name="income_note"
                                                        id="income_note" value="{{ $details->income_note ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="income_icon" class="label-style">Income Icon</label>
                                                    <input type="file" class="form-control" name="income_icon"
                                                        id="income_icon" accept="image/*"
                                                        onchange="previewImage(this, 'income_icon_preview')">
                                                    <div class="pt-2">
                                                        <img id="income_icon_preview"
                                                            src="{{ $details->income_icon_path }}" alt="Income Icon"
                                                            style="max-width: 80px; max-height: 80px; {{ empty($details->income_icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="expense_note" class="label-style">Expense Note</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter expense note" name="expense_note"
                                                        id="expense_note" value="{{ $details->expense_note ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="expense_icon" class="label-style">Expense Icon</label>
                                                    <input type="file" class="form-control" name="expense_icon"
                                                        id="expense_icon" accept="image/*"
                                                        onchange="previewImage(this, 'expense_icon_preview')">
                                                    <div class="pt-2">
                                                        <img id="expense_icon_preview"
                                                            src="{{ $details->expense_icon_path }}" alt="Expense Icon"
                                                            style="max-width: 80px; max-height: 80px; {{ empty($details->expense_icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row pt-3">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="budget_note" class="label-style">Budget Note</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter budget note" name="budget_note"
                                                        id="budget_note" value="{{ $details->budget_note ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="budget_icon" class="label-style">Budget Icon</label>
                                                    <input type="file" class="form-control" name="budget_icon"
                                                        id="budget_icon" accept="image/*"
                                                        onchange="previewImage(this, 'budget_icon_preview')">
                                                    <div class="pt-2">
                                                        <img id="budget_icon_preview"
                                                            src="{{ $details->budget_icon_path }}" alt="Budget Icon"
                                                            style="max-width: 80px; max-height: 80px; {{ empty($details->budget_icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="item_note" class="label-style">Item Note</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter item note" name="item_note" id="item_note"
                                                        value="{{ $details->item_note ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="item_icon" class="label-style">Item Icon</label>
                                                    <input type="file" class="form-control" name="item_icon"
                                                        id="item_icon" accept="image/*"
                                                        onchange="previewImage(this, 'item_icon_preview')">
                                                    <div class="pt-2">
                                                        <img id="item_icon_preview" src="{{ $details->item_icon_path }}"
                                                            alt="Item Icon"
                                                            style="max-width: 80px; max-height: 80px; {{ empty($details->item_icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="goal_note" class="label-style">Goal Note</label>
                                                    <input type="text" class="form-control"
                                                        placeholder="Enter goal note" name="goal_note" id="goal_note"
                                                        value="{{ $details->goal_note ?? null }}">
                                                </div>
                                                <div class="form-group pt-2">
                                                    <label for="goal_icon" class="label-style">Goal Icon</label>
                                                    <input type="file" class="form-control" name="goal_icon"
                                                        id="goal_icon" accept="image/*"
                                                        onchange="previewImage(this, 'goal_icon_preview')">
                                                    <div class="pt-2">
                                                        <img id="goal_icon_preview" src="{{ $details->goal_icon_path }}"
                                                            alt="goal Icon"
                                                            style="max-width: 80px; max-height: 80px; {{ empty($details->goal_icon) ? 'display:none;' : '' }}">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="launching_date" class="label-style">Launching Date</label>
                                                    <input type="datetime-local" class="form-control"
                                                        placeholder="Enter launching date" name="launching_date"
                                                        id="launching_date"
                                                        value="{{ $details->launching_date ? (new DateTime($details->launching_date))->format('Y-m-d\TH:i') : '' }}">
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
    </div>

    @push('script')
        <script src="{{ asset('assets/js/custom_js/cdn/ckeditor.js') }}"></script>
        <script>
            function previewImage(input, previewId) {
                const preview = document.getElementById(previewId);
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(input.files[0]);
                } else {
                    preview.src = '';
                    preview.style.display = 'none';
                }
            }
            $(document).ready(function() {
                let termAndConditionEditorInstance;
                ClassicEditor
                    .create(document.querySelector('#term_and_condition'))
                    .then(editor => {
                        termAndConditionEditorInstance = editor;
                        console.log('Editor initialized', editor);
                    })
                    .catch(error => {
                        console.error('Error initializing CKEditor', error);
                    });

                let privacyPolicyEditorInstance;
                ClassicEditor
                    .create(document.querySelector('#privacy_policy'))
                    .then(editor => {
                        privacyPolicyEditorInstance = editor;
                        console.log('Editor initialized', editor);
                    })
                    .catch(error => {
                        console.error('Error initializing CKEditor', error);
                    });

                let aboutUsEditorInstance;
                ClassicEditor
                    .create(document.querySelector('#about_us'))
                    .then(editor => {
                        aboutUsEditorInstance = editor;
                        console.log('Editor initialized', editor);
                    })
                    .catch(error => {
                        console.error('Error initializing CKEditor', error);
                    });

                $(document).on("click", "#submitBtn", function() {
                    document.getElementById('term_and_condition').value = termAndConditionEditorInstance
                        .getData();
                    document.getElementById('privacy_policy').value = privacyPolicyEditorInstance.getData();
                    document.getElementById('about_us').value = aboutUsEditorInstance.getData();
                });
            });
        </script>
    @endpush
@endsection
