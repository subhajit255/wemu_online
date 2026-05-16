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
            <a href="#" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;">Filters</a>
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
                                <th class="min-w-150px text-end pe-6 rounded-end">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($songs as $song)
                            <tr>
                                <td class="ps-6">
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <img src="{{ $song->cover_image_path }}" alt="song Cover" class="w-40px h-40px" style="object-fit: cover;">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $song->title }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $song->album->title ?? 'N/A' }}</span>
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
                                <td class="text-end pe-6">
                                    <span class="text-dark fw-semibold d-block fs-7">{{ $song->created_at->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No songs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center py-6">
                    <ul class="pagination custom-pagination m-0">
                        <li class="page-item previous disabled"><a href="#" class="page-link"><i class="fa-solid fa-angle-left"></i></a></li>
                        <li class="page-item active"><a href="#" class="page-link">1</a></li>
                        <li class="page-item"><a href="#" class="page-link">2</a></li>
                        <li class="page-item"><a href="#" class="page-link">3</a></li>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        <li class="page-item"><a href="#" class="page-link">10</a></li>
                        <li class="page-item next"><a href="#" class="page-link"><i class="fa-solid fa-angle-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Upcoming Releases Card -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-dark">Upcoming Releases</span>
                </h3>
                <div class="card-toolbar">
                    <a href="#" class="text-muted fs-7 text-hover-primary">See all</a>
                </div>
            </div>
            <div class="card-body p-6 pt-3">
                <div class="release-item upcoming-release-item d-flex align-items-center justify-content-between pb-4 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="release-item-img me-4"></div>
                        <div class="release-item-info">
                            <h4 class="fw-bold text-dark fs-5 mb-1">My Dear Melancholy,</h4>
                            <p class="text-muted fs-7 m-0">EP • Scheduled for Jun 15, 2024</p>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary fw-bold px-4 py-2">Scheduled</span>
                    </div>
                </div>

                <div class="release-item upcoming-release-item d-flex align-items-center justify-content-between pb-4 mb-4">
                    <div class="d-flex align-items-center">
                        <div class="release-item-img me-4"></div>
                        <div class="release-item-info">
                            <h4 class="fw-bold text-dark fs-5 mb-1">Hurry Up Tomorrow</h4>
                            <p class="text-muted fs-7 m-0">Album • Scheduled for Aug 10, 2024</p>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary fw-bold px-4 py-2">Scheduled</span>
                    </div>
                </div>

                <div class="release-item upcoming-release-item d-flex align-items-center justify-content-between border-0 pb-0 mb-0">
                    <div class="d-flex align-items-center">
                        <div class="release-item-img me-4"></div>
                        <div class="release-item-info">
                            <h4 class="fw-bold text-dark fs-5 mb-1">Live at SoFi Stadium</h4>
                            <p class="text-muted fs-7 m-0">Album • Scheduled for Oct 5, 2024</p>
                        </div>
                    </div>
                    <div>
                        <span class="badge badge-light-secondary fw-bold px-4 py-2">Scheduled</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection