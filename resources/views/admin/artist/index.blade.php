@extends('layout.app')
@section('content')

<div class="app-main flex-column flex-row-fluid" id="kt_app_main">
    <div class="d-flex flex-column flex-column-fluid">

        {{-- ===== Standard Toolbar ===== --}}
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            <div id="kt_app_toolbar_container" class="app-container container-xxl d-flex flex-stack">
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Artist Directory
                    </h1>
                    <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                        <li class="breadcrumb-item text-muted">
                            <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <span class="bullet bg-gray-400 w-5px h-2px"></span>
                        </li>
                        <li class="breadcrumb-item text-muted">Artists</li>
                    </ul>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <button type="button" class="btn wemu-btn-add-user goTo d-flex align-items-center gap-2"
                        data-action="{{ route('admin.artist.add') }}">
                        <i class="fa-solid fa-guitar fs-6"></i> Add Artist
                    </button>
                </div>
            </div>
        </div>

        <div id="kt_app_content" class="app-content flex-column-fluid pt-0">
            <div id="kt_app_content_container" class="app-container container-xxl">

                {{-- ===== Filter Bar ===== --}}
                <div class="wemu-glass-card mb-6">
                    <form action="{{ route('admin.artist.list') }}" method="GET" id="searchFrm">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-5">
                                <div class="wemu-search-bar">
                                    <i class="fa-solid fa-magnifying-glass text-muted"></i>
                                    <input type="text" class="wemu-search-input"
                                        placeholder="Search artists by name, email or mobile…" id="search" name="search"
                                        value="{{ Request::get('search') ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="type" id="type" class="form-select form-select-solid wemu-select actInActCls">
                                    <option value="3">All Statuses</option>
                                    <option value="1" {{ Request::get('type') == 1 ? 'selected' : '' }}>Active Artists</option>
                                    <option value="2" {{ Request::get('type') == 2 ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex justify-content-md-end gap-2">
                                <button class="btn wemu-btn-filter searchBtn" type="button" id="button-search">
                                    <i class="fa-solid fa-sliders me-2"></i> Apply
                                </button>
                                <button type="button" class="btn btn-light goTo px-5" data-action="{{ route('admin.artist.list') }}">
                                    Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- ===== Artist Table ===== --}}
                <div class="wemu-glass-card p-0">
                    {{-- Table header bar --}}
                    <div class="d-flex align-items-center justify-content-between px-7 py-5" style="border-bottom: 1px solid rgba(0,0,0,0.04);">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-6 fw-bold text-gray-800">Artist Directory</span>
                            <span class="badge badge-light-primary fw-semibold">{{ $details->total() }} artists</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 text-muted fs-8" style="cursor: pointer;" onclick="window.location.reload()">
                            <i class="fa-solid fa-arrow-rotate-right"></i> Live data
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table wemu-listener-table m-0">
                            <thead>
                                <tr>
                                    <th class="ps-7">#</th>
                                    <th>Artist</th>
                                    <th>Contact</th>
                                    <th class="text-center">Songs</th>
                                    <th class="text-center">Albums</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-end pe-7">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($details as $detail)
                                @php
                                $rowNum = $loop->iteration + ($details->currentPage() - 1) * $details->perPage();
                                @endphp
                                <tr class="wemu-listener-row">

                                    {{-- Row Number --}}
                                    <td class="ps-7 text-muted fw-bold fs-8">{{ str_pad($rowNum, 2, '0', STR_PAD_LEFT) }}</td>

                                    {{-- Profile --}}
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="wemu-avatar">
                                                <img src="{{ $detail->image_path }}" alt="{{ $detail->name }}"
                                                    class="onerror-image" data-onerror-image="{{ $detail->image_path }}">
                                                <span class="wemu-avatar-dot {{ $detail->is_active == 1 ? 'wemu-dot-online' : 'wemu-dot-offline' }}"></span>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.artist.view', $detail->uuid) }}"
                                                    class="fw-bold text-gray-900 text-hover-primary fs-6 d-block">
                                                    {{ $detail->name ?? 'Unnamed Artist' }}
                                                </a>
                                                <span class="text-muted fs-8">Joined {{ \Carbon\Carbon::parse($detail->created_at)->format('M Y') }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Contact --}}
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="d-flex align-items-center gap-2 fs-7 text-gray-800">
                                                <i class="fa-solid fa-envelope text-gray-400 fs-9"></i>
                                                {{ $detail->email ?? '—' }}
                                                @if ($detail->email_verified == 1 || $detail->is_verified == 1)
                                                <span class="wemu-verified-badge" style="background: #3b82f6;">✓</span>
                                                @endif
                                            </span>
                                            <span class="d-flex align-items-center gap-2 fs-8 text-muted">
                                                <i class="fa-solid fa-phone text-gray-400 fs-9"></i>
                                                {{ getPhoneCode($detail->id) }} {{ $detail->mobile_number ?? '—' }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Songs Count --}}
                                    <td class="text-center">
                                        <span class="badge badge-light-success fw-bold px-3 py-2 fs-7">
                                            <i class="fa-solid fa-music me-1"></i> {{ $detail->songs_count ?? 0 }}
                                        </span>
                                    </td>

                                    {{-- Albums Count --}}
                                    <td class="text-center">
                                        <span class="badge badge-light-primary fw-bold px-3 py-2 fs-7">
                                            📀 {{ $detail->albums_count ?? 0 }}
                                        </span>
                                    </td>

                                    {{-- Status Toggle --}}
                                    <td class="text-center">
                                        <div class="form-check form-switch form-check-custom form-check-solid justify-content-center">
                                            <input type="checkbox" data-uuid="{{ $detail->uuid }}"
                                                data-table="users" class="form-check-input isVerified"
                                                id="status_{{ $detail->id }}"
                                                value="{{ $detail->is_active ?? 0 }}"
                                                {{ $detail->is_active == 1 ? 'checked' : '' }}>
                                        </div>
                                        <label class="wemu-status-label {{ $detail->is_active == 1 ? 'wemu-status-active' : 'wemu-status-suspended' }}"
                                            id="lbl_{{ $detail->id }}"
                                            for="status_{{ $detail->id }}">
                                            {{ $detail->is_active == 1 ? 'Active' : 'Suspended' }}
                                        </label>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="text-end pe-7">
                                        <a href="javascript:void(0)"
                                            class="btn btn-sm btn-light btn-active-light-primary fw-bold wemu-action-btn"
                                            data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </a>
                                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                            data-kt-menu="true">
                                            <div class="menu-item px-3">
                                                <a class="menu-link px-3" href="{{ route('admin.artist.view', $detail->uuid) }}">
                                                    <i class="fa-solid fa-eye me-2 text-primary"></i> View Profile
                                                </a>
                                            </div>
                                            <div class="menu-item px-3">
                                                <a class="menu-link px-3" href="{{ route('admin.artist.edit', $detail->uuid) }}">
                                                    <i class="fa-solid fa-pen me-2 text-warning"></i> Edit Details
                                                </a>
                                            </div>
                                            <div class="separator my-1"></div>
                                            <div class="menu-item px-3">
                                                <a href="javascript:void(0)" data-table="users"
                                                    data-uuid="{{ $detail->uuid }}"
                                                    class="menu-link px-3 custom-data-table deleteData text-danger"
                                                    data-kt-customer-table-filter="delete_row">
                                                    <i class="fa-solid fa-trash me-2"></i> Remove
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-16">
                                        <div class="d-flex flex-column align-items-center gap-4">
                                            <div class="wemu-empty-icon" style="background: rgba(59, 130, 246, 0.05);">
                                                <i class="fa-solid fa-guitar text-primary fs-1"></i>
                                            </div>
                                            <p class="text-gray-500 fw-bold fs-5 m-0">No artists found</p>
                                            <p class="text-muted fs-7 m-0">Try adjusting your search or filter</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($details->hasPages())
                    <div class="d-flex align-items-center justify-content-between px-7 py-5" style="border-top: 1px solid rgba(0,0,0,0.04);">
                        <span class="fs-7 text-muted fw-semibold">
                            Showing {{ $details->firstItem() }}–{{ $details->lastItem() }} of {{ $details->total() }} artists
                        </span>
                        <div>
                            {!! $details->withQueryString()->links('pagination::bootstrap-5') !!}
                        </div>
                    </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>

@push('script')
<script>
    $(document).on("click", ".searchBtn", function() {
        if ($('#search').val() == '' && $('#type').val() == '3') {
            toastr.info("Please enter a search term or select a filter.");
        } else {
            $('#searchFrm').submit();
        }
    });

    $(document).on("change", ".isVerified", function() {
        const id = $(this).attr('id').replace('status_', '');
        const lbl = $('#lbl_' + id);
        if ($(this).is(':checked')) {
            lbl.text('Active').removeClass('wemu-status-suspended').addClass('wemu-status-active');
        } else {
            lbl.text('Suspended').removeClass('wemu-status-active').addClass('wemu-status-suspended');
        }
    });
</script>
@endpush
@endsection
