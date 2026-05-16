@extends('layout.app')
@section('content')
    <style>
        .iclass {
            font-size: 2rem !important;
        }
    </style>
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar whitee-bg  " class="app-toolbar py-3 py-lg-6" style="background-color:#fff">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Expense Details</h1>
                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                <a href="{{ route('admin.user.list') }}">User</a>
                            </li>
                            <li class="breadcrumb-item">
                                <span class="bullet bg-gray-400 w-5px h-2px"></span>
                            </li>
                            <li class="breadcrumb-item text-muted">
                                Expense Details
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-dark addDetail" data-bs-toggle="modal"
                                data-bs-target="#addExpenseMdl">Add
                                Expense</button>
                        </div>
                    </div>

                </div>
            </div>
            <div id="kt_app_content" class="app-content flex-column-fluid">
                <div id="kt_app_content_container" class="app-container container-xxl">
                    <div class="card mt-4 p-4 with-bg">
                        <h5>User Details -></h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Name : </label>
                                <b>{{ $detail->name ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Email : </label>
                                <b>{{ $detail->email ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Mobile Number : </label>
                                <b>{{ getPhoneCode($detail->id) }}{{ $detail->mobile_number ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Expense Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpensePreviousMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userExpenseCurrentMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mt-4 p-4">
                                <h5 class="margin-left">{{ $detail->name ?? 'N/A' }}'s Income Details -></h5>
                                <hr>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                            id="kt_customers_table">
                                            <thead>
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Sl No</th>
                                                    <th>Category</th>
                                                    <th>Service Frequency</th>
                                                    <th>Name</th>
                                                    <th>Price</th>
                                                    <th>Date</th>
                                                    {{-- <th>Bill</th> --}}
                                                    {{-- <th>E-Script URL</th> --}}
                                                    <th>Recurring</th>
                                                    <th>Tax Deductible</th>
                                                    {{-- <th>Remainder Date</th>
                                                    <th>Remainder Time</th> --}}
                                                    <th class="text-end min-w-70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @forelse ($detail->userExpense ?? [] as $expense)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $expense->category?->title ?? 'N/A' }}</td>
                                                        <td>{{ $expense->serviceFrequency?->name ?? '---' }}</td>
                                                        <td>{{ $expense->name ?? 'N/A' }}</td>
                                                        <td>{{ getCurrency() }}{{ $expense->price ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('jS M') ?? 'N/A' }}
                                                        </td>
                                                        {{-- <td>{{ $expense->bill ?? 'N/A' }}</td> --}}
                                                        {{-- <td>{{ $expense->e_script_url ?? 'N/A' }}</td> --}}
                                                        <td>
                                                            <center>
                                                                {!! $expense->is_recurring ? '<i class="fa-solid fa-check iclass"></i>' : '<i class="fa-regular fa-circle-xmark iclass"></i>' !!}
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <center>{!! $expense->is_tax_deductible
                                                                ? '<i class="fa-solid fa-check iclass"></i>'
                                                                : '<i class="fa-regular fa-circle-xmark iclass"></i>' !!}
                                                            </center>
                                                        </td>
                                                        {{-- <td>{{ \Carbon\Carbon::parse($expense->remainder_date)->format('jS M') ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($expense->remainder_time)->format('h:i A') ?? 'N/A' }}
                                                        </td> --}}
                                                        <td class="text-end">
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-light btn-active-light-primary"
                                                                data-kt-menu-trigger="click"
                                                                data-kt-menu-placement="bottom-end">Actions</a>
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                data-kt-menu="true">
                                                                <div class="menu-item px-3">
                                                                    <a class="menu-link px-3 editDetails"
                                                                        data-value="{{ json_encode($expense) }}"
                                                                        href="Javascript:void(0)" data-bs-toggle="modal"
                                                                        data-bs-target="#addExpenseMdl">Edit</a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:void(0)" data-table="user_expenses"
                                                                        data-uuid="{{ $expense->uuid }}"
                                                                        class="menu-link px-3 custom-data-table deleteData"
                                                                        data-kt-customer-table-filter="delete_row">Delete</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="13">No Data Found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    {{-- {!! $users->withQueryString()->links('pagination::bootstrap-5') !!} --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $(document).on("click", ".editDetails", function() {
                const dataValue = JSON.parse($(this).attr('data-value'));
                console.log(dataValue);
                $('#id').val(dataValue.id);
                $('#category_id').val(dataValue.category_id);
                $('#service_frequency_id').val(dataValue.service_frequency_id);
                $('#name').val(dataValue.name);
                $('#price').val(dataValue.price);
                $('#date').val(dataValue.date);
                $('#billPreview').attr('src', dataValue.bill_path);
                $('#billPreview').show();
                $('#e_script_url').val(dataValue.e_script_url);
                $('#remainder_date').val(dataValue.remainder_date);
                $('#remainder_time').val(dataValue.remainder_time);
                if (dataValue.is_recurring) {
                    $('#recurringYes').prop('checked', true);
                } else {
                    $('#recurringNo').prop('checked', true);
                }
                if (dataValue.is_tax_deductible) {
                    $('#taxDeductibleYes').prop('checked', true);
                } else {
                    $('#taxDeductibleNo').prop('checked', true);
                }
            });
            $(document).on("click", ".addDetail", function() {
                $('#id').val('');
                $('#category_id').val('');
                $('#service_frequency_id').val('');
                $('#name').val('');
                $('#price').val('');
                $('#date').val('');
                $('#bill').val('');
                $('#billPreview').attr('src', "{{ asset('assets/media/avatars/blank.png') }}");
                $('#billPreview').hide();
                $('#e_script_url').val('');
                $('#remainder_date').val('');
                $('#remainder_time').val('');
                $('#recurringNo').prop('checked', true);
                $('#taxDeductibleNo').prop('checked', true);
            });

            function previewImage(event) {
                var reader = new FileReader();
                reader.onload = function() {
                    var output = document.getElementById('billPreview');
                    output.src = reader.result;
                    output.style.display = 'block';
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        </script>
    @endpush
@endsection

@section('pageModal')
    <div class="modal fade modal-xl" id="addExpenseMdl" tabindex="-1" aria-labelledby="addExpenseMdlLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addExpenseMdlLabel">Add Expense</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="userExpense" action="{{ route('admin.user.expense') }}" method="POST"
                    class="formSubmit fileUpload">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="user_id" value="{{ $detail->id }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Select Category</option>
                                    @foreach (getCategories() ?? [] as $category)
                                        <option value="{{ $category->id }}"
                                            {{ !empty($details) && $details->category_id == $category->id ? 'selected' : '' }}>
                                            {{ $category->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="service_frequency_id" class="form-label">Service Frequency <span
                                        class="text-danger">*</span></label>
                                <select class="form-control" id="service_frequency_id" name="service_frequency_id">
                                    <option value="">Select Service Frequency</option>
                                    @foreach (getServiceFrequencies() ?? [] as $frequency)
                                        <option value="{{ $frequency->id }}"
                                            {{ !empty($details->serviceDetail) && $details->serviceDetail?->service_frequency_id == $frequency->id ? 'selected' : '' }}>
                                            {{ $frequency->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control number-only" id="price"
                                    name="price">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date" name="date">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="bill" class="form-label">Bill <span class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="bill" name="bill"
                                    onchange="previewImage(event)">
                            </div>
                            <div class="col-md-3 mb-3">
                                <img id="billPreview" src="{{ asset('assets/media/avatars/blank.png') }}"
                                    alt="Bill Image" style="display:none; margin-top:10px; max-width:100px;">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="e_script_url" class="form-label">E-Script URL <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="e_script_url" name="e_script_url">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Recurring <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_recurring"
                                            id="recurringYes" value="1">
                                        <label class="form-check-label" for="recurringYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_recurring"
                                            id="recurringNo" value="0" checked>
                                        <label class="form-check-label" for="recurringNo">No</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tax Deductible <span class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_tax_deductible"
                                            id="taxDeductibleYes" value="1">
                                        <label class="form-check-label" for="taxDeductibleYes">Yes</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="is_tax_deductible"
                                            id="taxDeductibleNo" value="0" checked>
                                        <label class="form-check-label" for="taxDeductibleNo">No</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="remainder_date" class="form-label">Remainder Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="remainder_date" name="remainder_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="remainder_time" class="form-label">Remainder Time <span
                                        class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="remainder_time" name="remainder_time">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitBtn" class="btn btn-dark">
                            <span class="indicator-label">Add Expense</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
