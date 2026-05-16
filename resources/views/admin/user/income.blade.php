@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar whitee-bg  " class="app-toolbar py-3 py-lg-6" style="background-color:#fff">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Income Details</h1>
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
                                Income Details
                            </li>
                        </ul>
                    </div>

                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-dark addDetail" data-bs-toggle="modal"
                                data-bs-target="#addIncomeMdl">Add
                                Income</button>
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
                                <label for="">Total Income Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userIncome->sum('amount') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Income : </label>
                                <b>{{ getCurrency() }}{{ $detail->userIncomePreviousMonth->sum('amount') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Income : </label>
                                <b>{{ getCurrency() }}{{ $detail->userIncomeCurrentMonth->sum('amount') ?? 'N/A' }}</b>
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
                                                    <th>Name</th>
                                                    <th>Amount</th>
                                                    <th>Date</th>
                                                    <th>Recurring</th>
                                                    <th class="text-end min-w-70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @forelse ($detail->userIncome ?? [] as $income)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {{ $income->name ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ getCurrency() }}{{ $income->amount ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($income->date)->format('jS M') ?? 'N/A' }}</td>
                                                        <td>{{ $income->is_recurring ? 'Yes' : 'No' }}</td>
                                                        <td class="text-end">
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-light btn-active-light-primary"
                                                                data-kt-menu-trigger="click"
                                                                data-kt-menu-placement="bottom-end">Actions</a>
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                data-kt-menu="true">
                                                                <div class="menu-item px-3">
                                                                    <a class="menu-link px-3 editDetails"
                                                                        data-value="{{ json_encode($income) }}"
                                                                        href="Javascript:void(0)" data-bs-toggle="modal"
                                                                        data-bs-target="#addIncomeMdl">Edit</a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:void(0)" data-table="user_incomes"
                                                                        data-uuid="{{ $income->uuid }}"
                                                                        class="menu-link px-3 custom-data-table deleteData"
                                                                        data-kt-customer-table-filter="delete_row">Delete</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">No Data Found</td>
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
                $('#name').val(dataValue.name);
                $('#amount').val(dataValue.amount);
                $('#date').val(dataValue.date);
                if (dataValue.is_recurring) {
                    $('#recurringYes').prop('checked', true);
                } else {
                    $('#recurringNo').prop('checked', true);
                }
            });
            $(document).on("click", ".addDetail", function() {
                $('#id').val('');
                $('#name').val('');
                $('#amount').val('');
                $('#date').val('');
                $('#recurringNo').prop('checked', true);
            });
        </script>
    @endpush
@endsection

@section('pageModal')
    <div class="modal fade" id="addIncomeMdl" tabindex="-1" aria-labelledby="addIncomeMdlLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addIncomeMdlLabel">Add Income</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.user.income') }}" method="POST" class="formSubmit">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" id="id" value="">
                        <input type="hidden" name="user_id" value="{{ $detail->id }}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="mb-3">
                            <label for="amount" class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control number-only" id="amount"
                                name="amount">
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Recurring</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_recurring" id="recurringYes"
                                        value="1">
                                    <label class="form-check-label" for="recurringYes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_recurring" id="recurringNo"
                                        value="0" checked>
                                    <label class="form-check-label" for="recurringNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="submitBtn" class="btn btn-dark">
                            <span class="indicator-label">Add Income</span>
                            <span class="indicator-progress">Please wait...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
