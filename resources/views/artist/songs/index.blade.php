@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Songs
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Manage your songs</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('artist.songs.storeOrUpdate') }}" class="btn btn-sm btn-dark fw-bold">Upload Song</a>
            <a href="#" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                <i class="fa-solid fa-filter fs-7 me-1"></i>Filters
            </a>
            <!--begin::Menu 1-->
            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_artist_song_filter">
                <div class="px-7 py-5">
                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                </div>
                <div class="separator border-gray-200"></div>
                <form action="{{ route('artist.songs.index') }}" method="GET" class="px-7 py-5">
                    <div class="mb-10">
                        <label class="form-label fw-semibold">Song Title:</label>
                        <div>
                            <input class="form-control form-control-solid" type="text" name="title" value="{{ request('title') }}" placeholder="Search by title..." />
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('artist.songs.index') }}" class="btn btn-sm btn-light btn-active-light-primary me-2">Reset</a>
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

        <!-- Songs List Card -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 song-table mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-transparent">
                                <th class="ps-6 min-w-200px rounded-start">Track</th>
                                <th class="min-w-150px">Album</th>
                                <th class="min-w-100px">Status</th>
                                <th class="min-w-100px text-end">Streams</th>
                                <th class="min-w-100px text-end">Duration</th>
                                <th class="min-w-150px text-end">Date</th>
                                <th class="min-w-80px text-end pe-6 rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($songs as $song)
                            <tr>
                                <td class="ps-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <a href="{{ route('artist.songs.show', $song->id) }}">
                                                <img src="{{ $song->cover_image_path }}" alt="song Cover" class="w-40px h-40px" style="object-fit: cover;">
                                            </a>
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <a href="{{ route('artist.songs.show', $song->id) }}" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $song->title }}</a>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($song->album)
                                    <a href="{{ route('artist.albums.show', $song->album->id) }}" class="text-dark fw-semibold text-hover-primary d-block fs-7">{{ $song->album->title }}</a>
                                    @else
                                    <span class="text-muted fw-semibold d-block fs-7">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light-{{ $song->status == 1 ? 'success' : 'warning' }} fw-bold px-4 py-2">{{ $song->status == 1 ? 'Published' : 'Draft' }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $song->play_count }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-dark fw-semibold d-block fs-7">
                                        @php
                                        $minutes = floor($song->duration / 60);
                                        $seconds = $song->duration % 60;
                                        $durationStr = '';
                                        if ($minutes > 0) {
                                        $durationStr .= $minutes . ' min ';
                                        }
                                        $durationStr .= $seconds . ' sec';
                                        @endphp
                                        {{ trim($durationStr) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $song->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="text-end pe-6">
                                    <a href="{{ route('artist.songs.show', $song->id) }}" class="btn btn-icon btn-light btn-sm w-30px h-30px" title="Play / View Details">
                                        <i class="fa-solid fa-play fs-7 text-dark"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fa-solid fa-music text-muted mb-4" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <h4 class="fw-bold text-dark mb-1">No Songs Found</h4>
                                        <p class="text-muted fs-7 mb-4">You haven't uploaded any songs yet.</p>
                                        <a href="{{ route('artist.songs.storeOrUpdate') }}" class="btn btn-sm btn-dark fw-bold px-5 py-2">
                                            <i class="fa-solid fa-plus me-1"></i>Upload Song
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end py-6 pe-6">
                    <div class="custom-pagination-wrapper">
                        {!! $songs->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Post Releases Card -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-dark">Post Releases</span>
                </h3>
                <div class="card-toolbar">
                    <a href="{{ route('artist.releases.index') }}" class="text-muted fs-7 text-hover-primary">See all</a>
                </div>
            </div>
            <div class="card-body p-6 pt-3">
                @forelse ($postReleases as $release)
                <div class="release-item upcoming-release-item d-flex align-items-center justify-content-between pb-4 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-40px me-3">
                            <a href="{{ route('artist.songs.show', $release->id) }}">
                                <img src="{{ $release->cover_image_path }}" alt="song Cover" class="w-40px h-40px" style="object-fit: cover;">
                            </a>
                        </div>
                        <div class="release-item-info">
                            <a href="{{ route('artist.songs.show', $release->id) }}" class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $release->title }}</a>
                            <p class="text-muted fs-7 m-0">{{ $release->album->title ?? 'Single' }} • Scheduled for {{ $release->published_at ? \Carbon\Carbon::parse($release->published_at)->format('M d, Y') : 'Not Set' }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary fw-bold px-4 py-2">Scheduled</span>
                    </div>
                </div>
                @empty
                <div class="release-item upcoming-release-item d-flex align-items-center justify-content-between pb-4 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="release-item-img me-4">

                        </div>
                        <div class="release-item-info">
                            <h4 class="fw-bold text-dark fs-5 mb-1">No post releases</h4>
                            <p class="text-muted fs-7 m-0">You have no upcoming post releases.</p>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary fw-bold px-4 py-2">N/A</span>
                    </div>
                </div>
                @endforelse
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