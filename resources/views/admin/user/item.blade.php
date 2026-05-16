@extends('layout.app')
@section('content')
    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar whitee-bg  " class="app-toolbar py-3 py-lg-6" style="background-color:#fff">
                <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <!--begin::Title-->
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                            Item Details</h1>
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
                                Item Details
                            </li>
                        </ul>
                    </div>

                    {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                            <button type="button" class="btn btn-dark addDetail" data-bs-toggle="modal"
                                data-bs-target="#addIncomeMdl">Add
                                Income</button>
                        </div>
                    </div> --}}

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
                                <label for="">Total Item Expense Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemPreviousMonthExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item Expense : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemCurrentMonthExpense->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Item Till Date : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItem->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemPreviousMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item : </label>
                                <b>{{ getCurrency() }}{{ $detail->userItemCurrentMonth->sum('price') ?? 'N/A' }}</b>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="">Total Item Count Till Date : </label>
                                <b>{{ $detail->userItem->count() ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">Previous Month's Item Count : </label>
                                <b>{{ $detail->userItemPreviousMonth->count() ?? 'N/A' }}</b>
                            </div>
                            <div class="col-md-4">
                                <label for="">This Month's Item Count : </label>
                                <b>{{ $detail->userItemCurrentMonth->count() ?? 'N/A' }}</b>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card mt-4 p-4">
                                <h5 class="margin-left">{{ $detail->name ?? 'N/A' }}'s Item Details -></h5>
                                <hr>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                            id="kt_customers_table">
                                            <thead>
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Sl No</th>
                                                    <th>Item Name</th>
                                                    <th>Price</th>
                                                    <th>Date</th>
                                                    <th>Brand Name</th>
                                                    <th>Model No</th>
                                                    <th>Serial No</th>
                                                    <th>Is Expense</th>
                                                    <th class="text-end min-w-70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @forelse ($detail->userItem ?? [] as $item)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {{ $item->name ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ getCurrency() }}{{ $item->price ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($item->date)->format('jS M') ?? 'N/A' }}</td>
                                                        <td>{{ $item->brand_name ?? 'N/A' }}</td>
                                                        <td>{{ $item->model_no ?? 'N/A' }}</td>
                                                        <td>{{ $item->serial_no ?? 'N/A' }}</td>
                                                        <td>{{ $item->is_expense ? 'Yes' : 'No' }}</td>
                                                        <td class="text-end">
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-light btn-active-light-primary"
                                                                data-kt-menu-trigger="click"
                                                                data-kt-menu-placement="bottom-end">Actions</a>
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                data-kt-menu="true">
                                                                <div class="menu-item px-3">
                                                                    <a target="_blank" class="menu-link px-3"
                                                                        href="{{ route('admin.item.view', $item->uuid) }}">View</a>
                                                                </div>
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:void(0)" data-table="items"
                                                                        data-uuid="{{ $item->uuid }}"
                                                                        class="menu-link px-3 custom-data-table deleteData"
                                                                        data-kt-customer-table-filter="delete_row">Delete</a>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9">No Data Found</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        {{-- {!! $users->withQueryString()->links('pagination::bootstrap-5') !!} --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
