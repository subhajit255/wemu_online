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
                                    <!--begin::Title-->
                                    <h1
                                        class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                                        Customer List</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Customer</li>
                                    </ul>
                                </div>
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                        <form action="{{ route('admin.user.list') }}" method="GET" id="searchFrm">
                                            <div class="input-group">
                                                <select name="type" id="type" class="form-control actInActCls"
                                                    style="width:100px">
                                                    <option value="3">All Type</option>
                                                    <option value="1"
                                                        {{ !empty(Request::get('type')) && Request::get('type') == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="2"
                                                        {{ !empty(Request::get('type')) && Request::get('type') == 2 ? 'selected' : '' }}>
                                                        In-Active</option>
                                                </select>
                                                &nbsp;&nbsp;
                                                <input style="width:200px" type="text" class="form-control"
                                                    placeholder="Search User" aria-label="Search Student"
                                                    aria-describedby="button-search" id="search" name="search"
                                                    value="{{ Request::get('search') ?? '' }}">
                                                <button class="btn btn-primary searchBtn" type="button"
                                                    id="button-search">Search</button>
                                            </div>
                                        </form>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-info goTo"
                                            data-action="{{ route('admin.user.list') }}">Reset</button>
                                        &nbsp;&nbsp;
                                        @can('add-user')
                                            <button type="button" class="btn btn-dark goTo"
                                                data-action="{{ route('admin.user.add') }}">Add
                                                Customer</button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed fs-6 gy-5" id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th class="min-w-50px">Sl No</th>
                                            <th class="min-w-125px">Customer Name</th>
                                            <th class="min-w-70px">Contact</th>
                                            @can('user-income')
                                                <th class="min-w-70px">Income</th>
                                            @endcan
                                            @can('user-expense')
                                                <th class="min-w-70px">Expense</th>
                                            @endcan
                                            @can('user-item')
                                                <th class="min-w-70px">Items</th>
                                            @endcan
                                            <th class="min-w-70px">Since</th>
                                            <th class="min-w-70px">Status</th>
                                            <th class="text-end min-w-70px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <img src="{{ $detail->image_path }}" class="img-fluid"
                                                        style="max-width: 20%; height: auto;">
                                                    <a href="{{ route('admin.user.view', $detail->uuid) }}">
                                                        {{ $detail->name ?? 'N/A' }}
                                                    </a>
                                                    @if ($detail->hasActiveSubscription())
                                                        <img class="blueTick"
                                                            src="{{ asset('assets/media/logos/blue_tick.png') }}"
                                                            alt="">
                                                    @endif
                                                </td>
                                                <td>
                                                    <i class="fa-solid fa-envelope"></i> {{ $detail->email ?? 'N/A' }}
                                                    @if ($detail->email_verified == 1)
                                                        <i class="fa-solid fa-check"></i>
                                                    @endif
                                                    <br>
                                                    <i class="fa-solid fa-phone"></i> {{ getPhoneCode($detail->id) }}
                                                    {{ $detail->mobile_number ?? 'N/A' }}
                                                </td>
                                                @can('user-income')
                                                    <td>
                                                        <a href="{{ route('admin.user.income', $detail->uuid) }}">Income -></a>
                                                    </td>
                                                @endcan
                                                @can('user-expense')
                                                    <td>
                                                        <a href="{{ route('admin.user.expense', $detail->uuid) }}">Expense
                                                            -></a>
                                                    </td>
                                                @endcan
                                                @can('user-item')
                                                    <td>
                                                        <a href="{{ route('admin.user.item', $detail->uuid) }}">Item -></a>
                                                    </td>
                                                @endcan
                                                <td>{{ \Carbon\Carbon::parse($detail->created_at)->format('jS F Y') }}</td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" data-uuid="{{ $detail->uuid }}"
                                                            data-table="users" class="form-check-input isVerified"
                                                            id="status_{{ $detail->id }}"
                                                            value="{{ $detail->is_active ?? 0 }}"
                                                            {{ $detail->is_active == 1 ? 'checked' : '' }}>
                                                        <label class="custom-control-label"
                                                            for="status_{{ $detail->id }}">{{ $detail->is_active == 1 ? 'Active' : 'In-Active' }}</label>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <a href="javascript:void(0)"
                                                        class="btn btn-sm btn-light btn-active-light-primary"
                                                        data-kt-menu-trigger="click"
                                                        data-kt-menu-placement="bottom-end">Actions</a>
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                        data-kt-menu="true">
                                                        @can('edit-user')
                                                            <div class="menu-item px-3">
                                                                <a class="menu-link px-3"
                                                                    href="{{ route('admin.user.edit', $detail->uuid) }}">Edit</a>
                                                            </div>
                                                        @endcan
                                                        @can('delete-user')
                                                            <div class="menu-item px-3">
                                                                <a href="javascript:void(0)" data-table="users"
                                                                    data-uuid="{{ $detail->uuid }}"
                                                                    class="menu-link px-3 custom-data-table deleteData"
                                                                    data-kt-customer-table-filter="delete_row">Delete</a>
                                                            </div>
                                                        @endcan
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
                                {!! $details->withQueryString()->links('pagination::bootstrap-5') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).on("click", ".searchBtn", function() {
                if ($('#search').val() == '' && $('#type').val() == '') {
                    toastr.info("Please give some value");
                } else {
                    $('#searchFrm').submit();
                }
            });
        </script>
    @endpush
@endsection
