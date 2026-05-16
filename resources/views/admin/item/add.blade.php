@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Item {{ !empty($details) ? 'Edit' : 'Add' }}</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.item.list') }}" class="text-muted text-hover-primary">
                                    Item</a>
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
                                <form id="itemForm" action="{{ route('admin.item.add') }}" method="POST"
                                    class="formSubmit fileUpload" enctype="multipart/form-data">
                                    <input type="hidden" name="uuid" value="{{ $details->uuid ?? null }}">
                                    <input type="hidden" id="remove_image" name="remove_image">
                                    <div class="row pt-2">
                                        <div class="col-md-12 pt-4 pb-4 themeGardant">
                                            <h3>Item Details --></h3>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="user_id" class="label-style">User</label>
                                                <select class="form-control" name="user_id" id="user_id">
                                                    <option value="">Select User</option>
                                                    @foreach ($users ?? [] as $user)
                                                        <option value="{{ $user->uuid }}"
                                                            {{ !empty($details) && $details->user_id == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name ?? 'N/A' }} ({{ getPhoneCode($user->id) }}{{ $user->mobile_number ?? 'N/A' }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="category_id" class="label-style">Category</label>
                                                <select class="form-control" name="category_id" id="category_id">
                                                    <option value="">Select Category</option>
                                                    @foreach ($categories ?? [] as $category)
                                                        <option value="{{ $category->uuid }}"
                                                            {{ !empty($details) && $details->category_id == $category->id ? 'selected' : '' }}>
                                                            {{ $category->title }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="name" class="label-style">Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Name"
                                                    name="name" id="name" value="{{ $details->name ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="price" class="label-style">Price</label>
                                                <input type="text" class="form-control number-only" placeholder="Enter Price"
                                                    name="price" id="price" value="{{ $details->price ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="date" class="label-style">Purchase Date</label>
                                                <input type="date" class="form-control" name="date" id="date"
                                                    value="{{ $details->date ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="brand_name" class="label-style">Brand Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Brand Name"
                                                    name="brand_name" id="brand_name"
                                                    value="{{ $details->brand_name ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="model_no" class="label-style">Model No</label>
                                                <input type="text" class="form-control" placeholder="Enter Model No"
                                                    name="model_no" id="model_no"
                                                    value="{{ $details->model_no ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="serial_no" class="label-style">Serial No</label>
                                                <input type="text" class="form-control" placeholder="Enter Serial No"
                                                    name="serial_no" id="serial_no"
                                                    value="{{ $details->serial_no ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="is_expense" class="label-style">Is Expense</label>
                                                <select class="form-control" name="is_expense" id="is_expense">
                                                    <option value="0"
                                                        {{ !empty($details) && $details->is_expense == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                    <option value="1"
                                                        {{ !empty($details) && $details->is_expense == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="supplier_name" class="label-style">Supplier Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Supplier Name" name="supplier_name" id="supplier_name" value="{{ $details->supplier_name ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4 pb-10">
                                            <div class="form-group">
                                                <label for="supplier_contact_number" class="label-style">Supplier Contact Number</label>
                                                <input type="text" class="form-control number-only" placeholder="Enter Supplier Contact Number" name="supplier_contact_number" id="supplier_contact_number" value="{{ $details->supplier_contact_number ?? null }}" maxlength="10">
                                            </div>
                                        </div>

                                        <div class="col-md-12 pt-4 pb-4 themeGardant">
                                            <h3>Service Details --></h3>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="service_frequency_id" class="label-style">Service
                                                    Frequency</label>
                                                <select class="form-control" name="service_frequency_id"
                                                    id="service_frequency_id">
                                                    <option value="">Select Service Frequency</option>
                                                    @foreach ($serviceFrequencies ?? [] as $frequency)
                                                        <option value="{{ $frequency->uuid }}"
                                                            {{ !empty($details->serviceDetail) && $details->serviceDetail?->service_frequency_id == $frequency->id ? 'selected' : '' }}>
                                                            {{ $frequency->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="last_service_date" class="label-style">Last Service
                                                    Date</label>
                                                <input type="date" class="form-control" name="last_service_date"
                                                    id="last_service_date"
                                                    value="{{ $details->serviceDetail?->last_service_date ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-4 pt-4">
                                            <div class="form-group">
                                                <label for="set_remainder_service" class="label-style">Set Reminder</label>
                                                <select class="form-control" name="set_remainder_service" id="set_remainder_service">
                                                    <option value="0"
                                                        {{ !empty($details->serviceDetail) && $details->serviceDetail?->set_remainder == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                    <option value="1"
                                                        {{ !empty($details->serviceDetail) && $details->serviceDetail?->set_remainder == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 pt-4 pb-10">
                                            <div class="form-group">
                                                <label for="comment_service" class="label-style">Comments</label>
                                                <textarea class="form-control" name="comment_service" id="comment_service" rows="3">{{ $details->serviceDetail?->comments ?? null }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12 pt-4 pb-4 themeGardant">
                                            <h3>Warranty Details --></h3>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="expiry_date" class="label-style">Expiry Date</label>
                                                <input type="date" class="form-control" name="expiry_date" id="expiry_date" value="{{ $details->warrantyDetail?->expiry_date ?? null }}">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="include" class="label-style">Include</label>
                                                <select class="form-control" name="include" id="include">
                                                    <option value="">Select Includes</option>
                                                    <option value="1" {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->include == 1 ? 'selected' : '' }}>Repair</option>
                                                    <option value="2" {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->include == 2 ? 'selected' : '' }}>Replace</option>
                                                    <option value="3" {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->include == 3 ? 'selected' : '' }}>Both</option>
                                                    <option value="4" {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->include == 4 ? 'selected' : '' }}>None</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="customer_care_number" class="label-style">Customer Care Number</label>
                                                <input type="text" class="form-control number-only" placeholder="Enter Customer Care Number" name="customer_care_number" id="customer_care_number" value="{{ $details->warrantyDetail?->customer_care_number ?? null }}" maxlength="10">
                                            </div>
                                        </div>

                                        <div class="col-md-6 pt-4">
                                            <div class="form-group">
                                                <label for="set_remainder_warranty" class="label-style">Set Reminder</label>
                                                <select class="form-control" name="set_remainder_warranty" id="set_remainder_warranty">
                                                    <option value="0"
                                                        {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->set_remainder == 0 ? 'selected' : '' }}>
                                                        No</option>
                                                    <option value="1"
                                                        {{ !empty($details->warrantyDetail) && $details->warrantyDetail?->set_remainder == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-md-12 pt-4 pb-10">
                                            <div class="form-group">
                                                <label for="comment_warranty" class="label-style">Comments</label>
                                                <textarea class="form-control" name="comment_warranty" id="comment_warranty" rows="3">{{ $details->warrantyDetail?->comments ?? null }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-12 pt-4 pb-4 themeGardant">
                                            <h3>Item Images --></h3>
                                        </div>

                                        <div class="row pt-4">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="file" class="form-label">Item Image</label>
                                                    <input class="form-control" type="file" id="file" name="file[]"
                                                        multiple>
                                                </div>
                                            </div>
                                            <div class="col-md-6 card">
                                                <div id="previewImages">
                                                    @if (!empty($details->images))
                                                        @foreach ($details->images as $image)
                                                            <div id="imgCls_{{ $loop->iteration }}">
                                                                <img style="width:100px"
                                                                    src="{{ asset('storage/item') . '/' . $image->image }}"
                                                                    alt="">
                                                                <a href="javascript:void(0)" class="removeImage"
                                                                    data-id="{{ $image->id }}">
                                                                    <i class="fa fa-trash-o pt-4" aria-hidden="true"
                                                                        onclick="rMdiv('{{ $loop->iteration }}')"></i>
                                                                </a>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
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
        <script src="{{ asset('assets/js/item.js') }}"></script>
    @endpush
@endsection
