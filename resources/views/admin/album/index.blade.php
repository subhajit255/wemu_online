@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Albums
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Manage your albums and EPs</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.albums.storeOrUpdate') }}" class="btn btn-sm btn-dark fw-bold">Create Album</a>
            <a href="#" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                <i class="fa-solid fa-filter fs-7 me-1"></i>Filters
            </a>
            <!--begin::Menu 1-->
            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_62cfa2f3_album">
                <div class="px-7 py-5">
                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                </div>
                <div class="separator border-gray-200"></div>
                <form action="{{ route('admin.albums.index') }}" method="GET" class="px-7 py-5">
                    <div class="mb-10">
                        <label class="form-label fw-semibold">Album Title:</label>
                        <div>
                            <input class="form-control form-control-solid" type="text" name="title" value="{{ request('title') }}" placeholder="Search by title..." />
                        </div>
                    </div>
                    <div class="mb-10">
                        <label class="form-label fw-semibold">Artist Name:</label>
                        <div>
                            <input class="form-control form-control-solid" type="text" name="artist" value="{{ request('artist') }}" placeholder="Search by artist..." />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('admin.albums.index') }}" class="btn btn-sm btn-light btn-active-light-primary me-2">Reset</a>
                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                    </div>
                </form>
            </div>
            <!--end::Menu 1-->
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <div class="row g-5 g-xl-8">
            <!-- Album Card 1 -->
            @forelse($albums as $album)
            <div class="col-sm-6 col-md-4 col-xl-3">
                <div class="card album-card clean-metric-card h-100">
                    <div class="card-body p-4">
                        <a href="{{ route('admin.albums.show', $album->id) }}" class="text-decoration-none">
                            <div class="album-cover mb-4 rounded bg-light position-relative" style="background-image: url('{{ $album->image_path }}'); background-size: cover; background-position: center; aspect-ratio: 1 / 1;">
                                <div class="position-absolute bottom-0 end-0 m-3">
                                    <span class="badge badge-light-{{ $album->status == 1 ? 'success' : 'dark' }} fw-bold px-3 py-1">{{ $album->status == 1 ? 'Published' : 'Draft' }}</span>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="fw-bold text-dark fs-5 mb-0 text-hover-primary">{{ $album->title }}</h4>
                            </div>
                        </a>
                        <p class="text-muted fs-7 mb-4 album-desc">
                            {{ $album->description }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <span class="badge badge-light-secondary fw-bold px-3 py-1">{{ $album->songs?->count() ?? 0 }} Tracks</span>
                            <a href="#" class="btn btn-sm btn-icon btn-light-primary w-30px h-30px" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end" title="Manage Album">
                                <i class="fa-solid fa-gear fs-7"></i>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg-light-primary fw-semibold w-200px py-3" data-kt-menu="true">
                                <div class="menu-item px-3">
                                    <a href="{{ route('admin.albums.storeOrUpdate', $album->id) }}" class="menu-link px-3"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Album</a>
                                </div>
                                <div class="menu-item px-3">
                                    <a href="{{ route('admin.albums.show', $album->id) }}" class="menu-link px-3"><i class="fa-solid fa-music me-2"></i> Manage Songs</a>
                                </div>
                                <div class="separator my-2"></div>
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3 text-danger deleteData" data-uuid="{{ $album->uuid }}" data-table="albums"><i class="fa-solid fa-trash me-2 text-danger"></i> Delete Album</a>
                                </div>
                            </div>
                            <!--end::Menu-->
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card w-100 border-0 shadow-sm bg-body h-400px d-flex justify-content-center align-items-center">
                    <div class="card-body d-flex flex-column flex-center justify-content-center text-center">
                        <i class="fa-solid fa-compact-disc text-muted mb-5" style="font-size: 5rem; opacity: 0.2;"></i>
                        <h2 class="fw-bold text-dark mb-2">No Albums Found</h2>
                        <p class="text-muted fs-6 mb-6">You haven't created any albums or EPs yet.</p>
                        <a href="{{ route('admin.albums.storeOrUpdate') }}" class="btn btn-dark fw-bold px-6 py-3">
                            <i class="fa-solid fa-plus me-2"></i>Create Your First Album
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-end py-10 mt-5">
            <div class="custom-pagination-wrapper">
                {!! $albums->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .custom-pagination-wrapper .pagination {
        display: flex;
        gap: 8px;
        border: none;
    }

    .custom-pagination-wrapper .page-item {
        margin: 0;
    }

    .custom-pagination-wrapper .page-item .page-link {
        border: 1px solid #e5e7eb;
        border-radius: 12px !important;
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #ffffff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }

    .custom-pagination-wrapper .page-item.active .page-link {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        border-color: transparent;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.35);
        transform: translateY(-2px);
    }

    .custom-pagination-wrapper .page-item .page-link:hover:not(.active) {
        background-color: #f5f3ff;
        border-color: #c4b5fd;
        color: #7c3aed;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .custom-pagination-wrapper .page-item.disabled .page-link {
        background-color: #f9fafb;
        border-color: #f3f4f6;
        color: #d1d5db;
        opacity: 0.7;
        pointer-events: none;
        box-shadow: none;
    }

    .custom-pagination-wrapper .page-link:focus {
        box-shadow: 0 0 0 0.25rem rgba(139, 92, 246, 0.25) !important;
        outline: 0;
    }
</style>
@endpush
@endsection