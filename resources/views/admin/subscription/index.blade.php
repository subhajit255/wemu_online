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
                                        Subscription List</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Subscription</li>
                                    </ul>
                                </div>
                                @can('add-subscription')
                                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                                        <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                            <button type="button" class="btn btn-dark goTo"
                                                data-action="{{ route('admin.subscription.add') }}">Add
                                                Subscription</button>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div class="card-body pt-0">
                                <table class="table align-middle table-row-dashed fs-6 gy-5 dataTable"
                                    id="kt_customers_table">
                                    <thead>
                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                            <th>Sl No</th>
                                            <th>Title</th>
                                            <th>Mrp</th>
                                            <th>Discount</th>
                                            <th>Price</th>
                                            <th>Activity Count</th>
                                            <th>Status</th>
                                            <th class="text-end min-w-70px">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fw-semibold text-gray-600">
                                        @forelse ($details as $detail)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $detail->title ?? 'N/A' }}</td>
                                                <td>{{ getCurrency() }}{{ $detail->mrp ?? 'N/A' }}</td>
                                                <td>{{ $detail->discount ?? 'N/A' }}%</td>
                                                <td>{{ getCurrency() }}{{ $detail->price ?? 'N/A' }}</td>
                                                <td>{{ $detail->activity_count ?? 'N/A' }}</td>
                                                <td>
                                                    <div class="form-check form-switch">
                                                        <input type="checkbox" data-uuid="{{ $detail->uuid }}"
                                                            data-table="subscriptions" class="form-check-input isVerified"
                                                            id="status_{{ $detail->id }}"
                                                            value="{{ $detail->is_active ?? 0 }}"{{ $detail->is_active == 1 ? 'checked' : '' }}>
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
                                                        @can('edit-subscription')
                                                            <div class="menu-item px-3">
                                                                <a class="menu-link px-3"
                                                                    href="{{ route('admin.subscription.edit', $detail->uuid) }}">Edit</a>
                                                            </div>
                                                        @endcan
                                                        @can('delete-subscription')
                                                            @if (isActiveSubscription($detail->id))
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:void(0)" data-table="subscriptions"
                                                                        data-uuid="{{ $detail->uuid }}"
                                                                        class="menu-link px-3 custom-data-table deleteData"
                                                                        data-kt-customer-table-filter="delete_row">Delete</a>
                                                                </div>
                                                            @else
                                                                <div class="menu-item px-3">
                                                                    <a href="javascript:void(0)"
                                                                        title="Since this subscription is active in some customer profiles, it cannot be deleted !"
                                                                        class="menu-link px-3 deleteNotActive custom-data-table"
                                                                        data-kt-customer-table-filter="delete_row">Delete</a>
                                                                </div>
                                                            @endif
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).on("click", ".deleteNotActive", function() {
                Swal.fire({
                    text: "Since this subscription is active in some customer profiles, it cannot be deleted !",
                    icon: "error",
                    showCancelButton: true,
                    showConfirmButton: false,
                    buttonsStyling: false,
                    cancelButtonText: "Ok got it !!",
                    customClass: {
                        cancelButton: "btn fw-bold btn-danger",
                    },
                })
            });
        </script>
    @endpush
@endsection


