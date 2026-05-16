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
                                        Item List</h1>
                                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                                        <li class="breadcrumb-item text-muted">
                                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item">
                                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                                        </li>
                                        <li class="breadcrumb-item text-muted">Item</li>
                                    </ul>
                                </div>
                                <div class="d-flex align-items-center gap-2 gap-lg-3">
                                    <div class="d-flex justify-content-end" data-kt-customer-table-toolbar="base">
                                        <form action="{{ route('admin.item.list') }}" method="GET" id="searchFrm">
                                            <div class="input-group">
                                                <input style="width:200px" type="text" class="form-control"
                                                    placeholder="Search" aria-label="Search Student"
                                                    aria-describedby="button-search" id="search" name="search"
                                                    value="{{ Request::get('search') ?? '' }}">
                                                <button class="btn btn-primary searchBtn" type="button"
                                                    id="button-search">Search</button>
                                            </div>
                                        </form>
                                        &nbsp;&nbsp;
                                        <button type="button" class="btn btn-info goTo"
                                            data-action="{{ route('admin.item.list') }}">Reset</button>
                                        &nbsp;&nbsp;
                                        @can('add-item')
                                            <button type="button" class="btn btn-dark goTo"
                                                data-action="{{ route('admin.item.add') }}">Add
                                                Item</button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="kt_app_content" class="app-content flex-column-fluid">
                            <div id="kt_app_content_container" class="app-container container-xxl">
                                <div class="card">
                                    <div class="card-body pt-0">
                                        <table class="table align-middle table-row-dashed fs-6 gy-5"
                                            id="kt_customers_table">
                                            <thead>
                                                <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                    <th>Sl No</th>
                                                    <th style="width: 25%">Customer</th>
                                                    <th>Item Name</th>
                                                    <th>Category</th>
                                                    <th>Price</th>
                                                    <th>Purchase Date</th>
                                                    <th>Brand Name</th>
                                                    <th>Model No</th>
                                                    {{-- <th>Serial No</th> --}}
                                                    <th>Is Expense</th>
                                                    <th class="text-end min-w-70px">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="fw-semibold text-gray-600">
                                                @forelse ($details as $detail)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {{ $detail->user?->name ?? 'N/A' }}<br>
                                                            <i class="fa-solid fa-envelope"></i>
                                                            {{ $detail->user?->email ?? 'N/A' }}<br>
                                                            <i class="fa-solid fa-phone"></i>
                                                            {{ getPhoneCode($detail->user?->id) }} {{ $detail->user?->mobile_number ?? 'N/A' }}
                                                        </td>
                                                        <td>{{ $detail->name ?? 'N/A' }}</td>
                                                        <td>{{ $detail->category->title ?? 'N/A' }}</td>
                                                        <td>{{ getCurrency() }}{{ $detail->price ?? 'N/A' }}</td>
                                                        <td>{{ $detail->date ?? 'N/A' }}</td>
                                                        <td>{{ $detail->brand_name ?? 'N/A' }}</td>
                                                        <td>{{ $detail->model_no ?? 'N/A' }}</td>
                                                        {{-- <td>{{ $detail->serial_no ?? 'N/A' }}</td> --}}
                                                        <td>{{ $detail->is_expense == 1 ? 'Yes' : 'No' }}</td>
                                                        <td class="text-end">
                                                            <a href="javascript:void(0)"
                                                                class="btn btn-sm btn-light btn-active-light-primary"
                                                                data-kt-menu-trigger="click"
                                                                data-kt-menu-placement="bottom-end">Actions</a>
                                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4"
                                                                data-kt-menu="true">
                                                                @can('view-item')
                                                                    <div class="menu-item px-3">
                                                                        <a class="menu-link px-3"
                                                                            href="{{ route('admin.item.view', $detail->uuid) }}">View</a>
                                                                    </div>
                                                                @endcan
                                                                @can('edit-item')
                                                                    <div class="menu-item px-3">
                                                                        <a class="menu-link px-3"
                                                                            href="{{ route('admin.item.edit', $detail->uuid) }}">Edit</a>
                                                                    </div>
                                                                @endcan
                                                                @can('delete-item')
                                                                    <div class="menu-item px-3">
                                                                        <a href="javascript:void(0)" data-table="items"
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
                                                        <td colspan="10">No Data Found</td>
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
        </div>
    </div>

    @push('script')
        <script>
            const searchPlaceholderTexts = ["Search by user name . . .", "Search by user email . . .",
                "Search by user phone . . .",
                "Search by item name . . .", "Search by brand name . . .", "Search by model no . . .",
                "Search by category name . . ."
            ];
            let currentIndex = 0;
            const searchInput = document.getElementById('search');

            function animatePlaceholder(text, callback) {
                let i = 0;
                searchInput.placeholder = "";
                const interval = setInterval(() => {
                    if (i < text.length) {
                        searchInput.placeholder += text.charAt(i);
                        i++;
                    } else {
                        clearInterval(interval);
                        if (callback) callback();
                    }
                }, 50);
            }

            function changePlaceholder() {
                animatePlaceholder(searchPlaceholderTexts[currentIndex], () => {
                    setTimeout(() => {
                        currentIndex = (currentIndex + 1) % searchPlaceholderTexts.length;
                        changePlaceholder();
                    }, 1000);
                });
            }

            changePlaceholder();

            $(document).on("click", ".searchBtn", function() {
                if ($('#search').val() == '') {
                    toastr.info("Please give some value for search . . .");
                } else {
                    $('#searchFrm').submit();
                }
            });
        </script>
    @endpush
@endsection
