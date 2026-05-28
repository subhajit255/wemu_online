@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 mb-0 border-0">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('artist.albums.index') }}" class="text-muted text-hover-primary">Albums</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-dark">After Hours</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('artist.albums.storeOrUpdate', $albumDetails->id) }}" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;">Edit Album</a>
            <a href="{{ route('artist.songs.storeOrUpdate', ['album_id' => $albumDetails->id]) }}" class="btn btn-sm btn-dark fw-bold">Add Song</a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Album Hero Section -->
        <div class="card clean-metric-card mb-5 mb-xl-10 overflow-hidden">
            <div class="card-body p-8 d-flex flex-column flex-md-row align-items-md-center">
                <div class="album-hero-img rounded bg-light d-flex align-items-center justify-content-center me-md-8 mb-5 mb-md-0 flex-shrink-0" style="width: 200px; height: 200px; overflow: hidden;">
                    <img src="{{ $albumDetails->image_path }}" alt="Album Cover" class="w-100 h-100" style="object-fit: cover;">
                </div>
                <div class="d-flex flex-column flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge badge-light-{{ $albumDetails->status == 1 ? 'success' : 'warning' }} fw-bold px-3 py-1 me-3">{{ $albumDetails->status == 1 ? 'Published' : 'Draft' }}</span>
                        <span class="text-muted fs-6 fw-semibold">Album • {{ $albumDetails->created_at->format('Y') }}</span>
                    </div>
                    <h1 class="text-dark fw-bolder mb-4" style="font-size: 2.5rem; letter-spacing: -1px;">{{ $albumDetails->title }}</h1>
                    <p class="text-muted fs-5 mb-5" style="max-width: 600px;">
                        {{ $albumDetails->description }}
                    </p>
                    <div class="d-flex align-items-center">
                        <span class="text-dark fw-bold fs-5 me-5">{{ $albumDetails->songs->count() ?? 0 }} Songs</span>
                        <span class="text-muted fs-6">
                            @php
                            $totalSeconds = $albumDetails->songs->sum('duration');
                            $hours = floor($totalSeconds / 3600);
                            $minutes = floor(($totalSeconds % 3600) / 60);
                            $durationString = '';
                            if ($hours > 0) {
                            $durationString .= $hours . ' hr ';
                            }
                            $durationString .= $minutes . ' min';
                            @endphp
                            {{ trim($durationString) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracklist Section -->
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bold fs-4 text-dark">Tracklist</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 song-table mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-transparent">
                                <th class="ps-8 min-w-50px w-50px">#</th>
                                <th class="min-w-200px">Title</th>
                                <th class="min-w-100px text-end">Streams</th>
                                <th class="min-w-150px text-end">Duration</th>
                                <th class="min-w-120px text-end">Status</th>
                                <th class="min-w-80px text-end pe-8 rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($albumDetails->songs as $index => $song)
                            <tr>
                                <td class="ps-8 text-muted fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="symbol symbol-40px me-3">
                                            <img src="{{ $song->cover_image_path }}" alt="Song Cover" class="rounded w-40px h-40px" style="object-fit: cover;">
                                        </div>
                                        <div class="d-flex justify-content-start flex-column">
                                            <span class="text-dark fw-bold text-hover-primary mb-1 fs-6">{{ $song->title }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted fw-semibold d-block fs-7">{{ $song->play_count }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="text-muted fw-semibold d-block fs-7">
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
                                    <span class="badge badge-light-{{ $song->status == 1 ? 'success' : 'warning' }} fw-bold px-4 py-2">
                                        {{ $song->status == 1 ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-end pe-8">
                                    <a href="{{ route('artist.songs.show', $song->id) }}" class="btn btn-icon btn-light btn-sm w-30px h-30px">
                                        <i class="fa-solid fa-play fs-7 text-dark"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center empty-data py-10">No songs found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection