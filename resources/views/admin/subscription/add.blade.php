@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Subscription {{ !empty($details) ? 'Edit' : 'Add' }}</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.subscription.list') }}"
                                    class="text-muted text-hover-primary">Subscription</a>
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
                                <form id="subscriptionForm" action="{{ route('admin.subscription.add') }}" method="POST"
                                    class="formSubmit fileUpload" enctype="multipart/form-data">
                                    <input type="hidden" name="id" name="id" value="{{ $details->id ?? null }}">
                                    <div class="row pt-2">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title" class="label-style">Title</label>
                                                <span class="asterisk_sign">*</span>
                                                <input type="text" class="form-control" placeholder="Enter Title"
                                                    name="title" id="title" value="{{ $details->title ?? null }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="type" class="label-style">Type</label>
                                                <span class="asterisk_sign">*</span>
                                                <select name="type" id="type" class="form-control">
                                                    <option value="">--- Select Type ---</option>
                                                    <option value="1" {{ !empty($details) && $details->type == 1 ? 'selected' : null }}>Monthly</option>
                                                    <option value="2" {{ !empty($details) && $details->type == 2 ? 'selected' : null }}>Quarterly</option>
                                                    <option value="3" {{ !empty($details) && $details->type == 3 ? 'selected' : null }}>Half Yearly</option>
                                                    <option value="4" {{ !empty($details) && $details->type == 4 ? 'selected' : null }}>Yearly</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="activity_count" class="label-style">Activity Count</label>
                                                <span class="asterisk_sign">*</span>
                                                <input type="text" class="form-control number-only"
                                                    placeholder="Enter Activity Count" name="activity_count"
                                                    id="activity_count" value="{{ $details->activity_count ?? null }}"
                                                    maxlength="4">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="mrp" class="label-style">Actual Price</label>
                                                <span class="asterisk_sign">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control number-only calculateDiscount"
                                                        placeholder="Enter Actual Price" name="mrp" id="mrp"
                                                        value="{{ $details->mrp ?? null }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="discount" class="label-style">Discount (%)</label>
                                                <span class="asterisk_sign">*</span>
                                                <input type="text" class="form-control number-only calculateDiscount"
                                                    placeholder="Enter Discount" name="discount" id="discount"
                                                    value="{{ $details->discount ?? null }}" maxlength="2">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="price" class="label-style">Purchase Price</label>
                                                <span class="asterisk_sign">*</span>
                                                <div class="input-group">
                                                    <span class="input-group-text">$</span>
                                                    <input type="text" class="form-control" placeholder="Enter Purchase Price"
                                                        name="price" id="price" value="{{ $details->price ?? null }}"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row pt-4">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="label-style">Description</label>
                                                <textarea class="form-control" name="description" id="description" cols="30" rows="4">{{ $details->description ?? null }}</textarea>
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

            $(document).on("keyup", ".calculateDiscount", function() {
                const mrp = parseFloat($('#mrp').val());
                const discount = parseFloat($('#discount').val());
                if($.isNumeric(mrp) && $.isNumeric(discount)) {
                    const price = parseFloat(mrp) - (parseFloat(mrp) * (parseFloat(discount) / 100));
                    $('#price').val(price.toFixed(2));
                } else {
                    $('#price').val('');
                }
            });
        </script>
    @endpush
@endsection
