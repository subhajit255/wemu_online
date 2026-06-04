@extends('layout.app')
@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        <!-- Page Header -->
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        CMS Directory
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">CMS</li>
                    </ul>
                </div>
                @can('add-cms')
                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                        <button type="button" class="btn wemu-btn-add-user goTo d-flex align-items-center gap-2"
                            data-action="{{ route('admin.cms.add') }}">
                            <i class="fa-solid fa-plus fs-6"></i> Add Page
                        </button>
                    </div>
                @endcan
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid pt-0">
            <div id="kt_app_content_container" class="app-container container-xxl">
                
                <!-- Filter Section -->
                <div class="wemu-glass-card mb-6">
                    <form action="{{ route('admin.cms.list') }}" method="GET" id="searchFrm">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <div class="wemu-search-bar">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                    <input type="text" class="wemu-search-input"
                                        placeholder="Search pages by title or alias..." id="search" name="search"
                                        value="{{ Request::get('search') ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" id="type" class="form-select form-select-solid wemu-select">
                                    <option value="">All Statuses</option>
                                    <option value="1" {{ Request::get('type') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ Request::get('type') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex justify-content-md-end gap-2">
                                <button class="btn wemu-btn-filter searchBtn" type="button" id="button-search">
                                    <i class="fa-solid fa-sliders me-2"></i> Apply
                                </button>
                                <button type="button" class="btn btn-light goTo px-5" data-action="{{ route('admin.cms.list') }}">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Main Table Card -->
                <div class="wemu-glass-card p-0">
                    <div class="d-flex align-items-center justify-content-between px-7 py-5" style="border-bottom: 1px solid rgba(0,0,0,0.04);">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-6 fw-bold text-gray-800">CMS Directory</span>
                            <span class="badge badge-light-primary fw-semibold">{{ count($details) }} pages</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted fs-8" style="cursor: pointer;" onclick="window.location.reload()">
                            <i class="fa-solid fa-arrow-rotate-right"></i> Live data
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table wemu-listener-table m-0" id="kt_customers_table">
                            <thead>
                                <tr>
                                    <th class="ps-7">#</th>
                                    <th>Page Information</th>
                                    <th class="text-center">Visibility</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($details as $detail)
                                    <tr class="wemu-listener-row">
                                        <td class="ps-7 text-muted fw-bold fs-8">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="wemu-avatar">
                                                    @if($detail->file)
                                                        <img src="{{ $detail->image_path }}" class="onerror-image" data-onerror-image="{{ $detail->image_path }}" alt="">
                                                    @else
                                                        <div class="symbol-label bg-light-primary text-primary fw-bolder fs-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 8px;">
                                                            {{ strtoupper(substr($detail->title, 0, 1)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="fw-bold text-gray-900 fs-6 d-block">{{ $detail->title ?? 'N/A' }}</span>
                                                    <span class="text-muted fs-8">Alias: {{ $detail->alias ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if($detail->for_home)
                                                <span class="wemu-tier-pill wemu-tier-duo">
                                                    <i class="fa-solid fa-house me-1"></i> Home Page
                                                </span>
                                            @else
                                                <span class="text-muted fs-8">Standard</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
                                                <input class="form-check-input isVerified" type="checkbox" data-uuid="{{ $detail->uuid }}" data-table="cms" id="status_{{ $detail->id }}" value="{{ $detail->is_active ?? 0 }}" {{ $detail->is_active == 1 ? 'checked' : '' }}>
                                            </div>
                                            <label class="wemu-status-label {{ $detail->is_active == 1 ? 'wemu-status-active' : 'wemu-status-suspended' }}" id="lbl_{{ $detail->id }}" for="status_{{ $detail->id }}">
                                                {{ $detail->is_active == 1 ? 'Active' : 'Inactive' }}
                                            </label>
                                        </td>
                                        <td class="text-end pe-7">
                                            <a href="javascript:void(0)" class="btn btn-sm btn-light btn-active-light-primary fw-bold wemu-action-btn" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-125px py-4 shadow-sm" data-kt-menu="true">
                                                @can('edit-cms')
                                                    <div class="menu-item px-3">
                                                        <a href="{{ route('admin.cms.edit', $detail->uuid) }}" class="menu-link px-3">
                                                            <i class="fa-solid fa-pen me-2 text-warning"></i> Edit
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete-cms')
                                                    <div class="menu-item px-3">
                                                        <a href="javascript:void(0)" data-table="cms" data-uuid="{{ $detail->uuid }}" class="menu-link px-3 text-danger custom-data-table deleteData" data-kt-customer-table-filter="delete_row">
                                                            <i class="fa-solid fa-trash me-2 text-danger"></i> Remove
                                                        </a>
                                                    </div>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-16">
                                            <div class="d-flex flex-column align-items-center gap-4">
                                                <div class="wemu-empty-icon">
                                                    <i class="fa-solid fa-file-lines text-gray-300 fs-1"></i>
                                                </div>
                                                <p class="text-gray-500 fw-bold fs-5 m-0">No CMS pages found.</p>
                                                <p class="text-muted fs-7 m-0">Try adjusting your search or add a new page.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    $(document).on("change", ".isVerified", function() {
        const id = $(this).attr('id').replace('status_', '');
        const lbl = $('#lbl_' + id);
        if ($(this).is(':checked')) {
            lbl.text('Active').removeClass('wemu-status-suspended').addClass('wemu-status-active');
        } else {
            lbl.text('Inactive').removeClass('wemu-status-active').addClass('wemu-status-suspended');
        }
    });
</script>
@endpush
@endsection
