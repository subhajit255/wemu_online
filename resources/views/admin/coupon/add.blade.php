@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Coupon {{ !empty($details) ? 'Edit' : 'Add' }}</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.coupon.list') }}" class="text-muted text-hover-primary">Coupon</a>
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
                                <form id="couponForm" action="{{ route('admin.coupon.add') }}" method="POST"
                                    class="formSubmit fileUpload" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $details->id ?? null }}">
                                    <div class="row pt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name" class="label-style">Name</label>
                                                <input type="text" class="form-control" placeholder="Enter Name"
                                                    name="name" id="name" value="{{ $details->name ?? null }}"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="code" class="label-style">Code</label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" placeholder="Enter Code"
                                                        name="code" id="code" value="{{ $details->code ?? null }}"
                                                        oninput="this.value = this.value.replace(/[^A-Za-z0-9]/g, '').replace(/(\..*?)\..*/g, '$1');"
                                                        maxlength="25" required>
                                                    <button type="button" class="btn btn-secondary" id="generateCodeBtn">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="type" class="label-style">Type</label>
                                                <select class="form-control" name="type" id="type" required>
                                                    <option value="1"
                                                        {{ !empty($details) && $details->type == 1 ? 'selected' : '' }}>
                                                        Percentage</option>
                                                    <option value="2"
                                                        {{ !empty($details) && $details->type == 2 ? 'selected' : '' }}>
                                                        Fixed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="coupon_discount" class="label-style" id="discountLabel">Discount
                                                    Percentage</label>
                                                <input type="text" class="form-control number-only"
                                                    placeholder="Enter Discount" name="coupon_discount" id="coupon_discount"
                                                    value="{{ $details->coupon_discount ?? null }}" required>
                                                {{-- <small id="discountError" class="text-danger"
                                                    style="display: none;">Discount percentage must be less than
                                                    100.</small> --}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="for_all_user" class="label-style">For All Users</label>
                                                <select class="form-control" name="for_all_user" id="for_all_user" required>
                                                    <option value="1"
                                                        {{ !empty($details) && $details->for_all_user == 1 ? 'selected' : '' }}>
                                                        Yes</option>
                                                    <option value="2"
                                                        {{ !empty($details) && $details->for_all_user == 2 ? 'selected' : '' }}>
                                                        No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="start_date" class="label-style">Start Date</label>
                                                <input type="date" class="form-control" placeholder="Enter Start Date"
                                                    name="start_date" id="start_date"
                                                    value="{{ $details->start_date ?? null }}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="end_date" class="label-style">End Date</label>
                                                <input type="date" class="form-control" placeholder="Enter End Date"
                                                    name="end_date" id="end_date"
                                                    value="{{ $details->end_date ?? null }}" required>
                                            </div>
                                        </div>
                                        {{-- @dd($couponUsers); --}}
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="users" class="label-style">Users</label>
                                                <select class="form-control select22" name="users[]" id="users" multiple>
                                                    <option value="">
                                                        --- Select User ---
                                                    </option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}"
                                                            {{ !empty($details) && in_array($user->id, $couponUsers) ? 'selected' : '' }}>
                                                            {{ $user->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row pt-2">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="label-style">Description</label>
                                                <textarea class="form-control" placeholder="Enter Description" name="description" id="description" required>{!! $details->description ?? null !!}</textarea>
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
        <script>
            $(document).ready(function() {
                $(".select22").select2({
                    multiple: true,
                    placeholder: "-- Select Users --"
                });
            });

            const typeSelect = document.getElementById('type');
            const discountLabel = document.getElementById('discountLabel');
            const discountInput = document.getElementById('coupon_discount');
            const discountError = document.getElementById('discountError');

            // Update label based on type selection
            typeSelect.addEventListener('change', function() {
                if (this.value == '1') { // Percentage
                    discountLabel.textContent = 'Discount Percentage';
                } else { // Fixed
                    discountLabel.textContent = 'Discount Amount';
                    discountError.style.display = 'none'; // Hide error if switching back
                }
            });

            // Validate discount value for percentage type
            discountInput.addEventListener('input', function() {
                if (typeSelect.value == '1' && parseFloat(this.value) >= 100) {
                    discountError.style.display = 'block';
                } else {
                    discountError.style.display = 'none';
                }
            });

            document.getElementById('generateCodeBtn').addEventListener('click', function() {
                const randomCode = Math.random().toString(36).substring(2, 10).toUpperCase();
                document.getElementById('code').value = randomCode;
            });
        </script>
    @endpush
@endsection
