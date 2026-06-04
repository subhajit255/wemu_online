@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Releases
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Manage your released songs and albums</span>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Post Released Songs Card -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-dark">Post Releases Songs</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Your scheduled upcoming songs</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 song-table mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-transparent">
                                <th class="ps-6 min-w-200px rounded-start">Track</th>
                                <th class="min-w-150px">Album</th>
                                <th class="min-w-100px text-end">Streams</th>
                                <th class="min-w-100px text-end">Duration</th>
                                <th class="min-w-150px text-end pe-6 rounded-end">Release Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($postReleaseSongs as $song)
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
                                    <span class="text-muted fw-semibold d-block fs-7">Single</span>
                                    @endif
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
                                    <span class="badge badge-light-secondary fw-bold px-4 py-2">Scheduled: {{ \Carbon\Carbon::parse($song->published_at)->format('M d, Y') }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-10">
                                    <div class="d-flex flex-column align-items-center justify-content-center py-5">
                                        <i class="fa-solid fa-music text-muted mb-4" style="font-size: 3rem; opacity: 0.2;"></i>
                                        <h4 class="fw-bold text-dark mb-1">No Post Releases</h4>
                                        <p class="text-muted fs-7 mb-4">You don't have any upcoming scheduled songs.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($postReleaseSongs->hasPages())
                <div class="d-flex justify-content-end py-6 pe-6">
                    <div class="custom-pagination-wrapper">
                        {!! $postReleaseSongs->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Post Released Albums Card -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-dark">Post Releases Albums</span>
                    <span class="text-muted mt-1 fw-semibold fs-7">Your scheduled upcoming albums</span>
                </h3>
            </div>
            <div class="card-body p-6 pt-3">
                <div class="row g-6">
                    @forelse($postReleaseAlbums as $album)
                    <div class="col-md-4 col-lg-3 col-xxl-3">
                        <div class="card card-flush h-100 border-0 premium-card-hover premium-album-card" style="border-radius: 16px;">
                            <div class="card-body p-4 text-center">
                                <a href="{{ route('artist.albums.show', $album->id) }}" class="d-block mb-4 premium-image-wrapper">
                                    <img src="{{ $album->image_path }}" class="w-100 rounded-3" alt="{{ $album->title }}" style="object-fit: cover; aspect-ratio: 1/1;" />
                                </a>
                                <a href="{{ route('artist.albums.show', $album->id) }}" class="text-dark fw-bold text-hover-primary fs-5">{{ $album->title }}</a>
                                <div class="mt-3">
                                    <span class="badge premium-badge-date px-3 py-2 fs-8"><i class="fa-solid fa-calendar text-gray-500 me-2"></i>Scheduled: {{ \Carbon\Carbon::parse($album->release_date)->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-10">
                        <div class="d-flex flex-column align-items-center justify-content-center py-5">
                            <i class="fa-solid fa-compact-disc text-muted mb-4" style="font-size: 3rem; opacity: 0.2;"></i>
                            <h4 class="fw-bold text-dark mb-1">No Post Releases</h4>
                            <p class="text-muted fs-7 mb-4">You don't have any upcoming scheduled albums.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                @if($postReleaseAlbums->hasPages())
                <div class="mt-8 d-flex justify-content-end">
                    <div class="custom-pagination-wrapper">
                        {!! $postReleaseAlbums->withQueryString()->links('pagination::bootstrap-5') !!}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
