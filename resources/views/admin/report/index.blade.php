@extends('layout.app')
@section('content')

<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                System Reports
            </h1>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 pt-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-muted">Reports</li>
            </ul>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Row 1: Top Metrics -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-primary h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Revenue</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($totalRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-success h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">This Month Revenue</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">${{ number_format($thisMonthRevenue, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-info h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Artists</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalArtists) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card clean-metric-card accent-border-warning h-100">
                    <div class="card-body p-6">
                        <span class="fs-6 fw-semibold text-muted mb-4 d-block">Total Listeners</span>
                        <div class="d-flex flex-column">
                            <span class="fs-1 fw-bold text-dark lh-1 ls-n2 mb-2">{{ number_format($totalListeners) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 2: Most Liked Songs & Most Followed Artists -->
        <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
            <!-- Most Liked Songs -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Most Liked Songs</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        @forelse($mostLikedSongs as $index => $item)
                        <div class="song-list-item mb-3 d-flex align-items-center">
                            <div class="song-list-item-num fw-bold text-muted me-3" style="width: 20px;">{{ $index + 1 }}</div>
                            <div class="song-list-item-img gradient-avatar d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%); font-size: 1.2rem;">
                                ❤️
                            </div>
                            <div class="song-list-item-title ms-3 flex-grow-1">
                                <div class="fw-semibold text-dark">{{ $item->song ? $item->song->title : 'Unknown' }}</div>
                                <div class="text-muted fs-8">{{ $item->song && $item->song->artist ? $item->song->artist->name : 'Unknown Artist' }}</div>
                            </div>
                            <div class="song-list-item-plays fw-bold text-dark">{{ number_format($item->total_likes) }} likes</div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-5">No likes recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Most Followed Artists -->
            <div class="col-lg-6">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Most Followed Artists</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        @forelse($mostFollowedArtists as $index => $item)
                        <div class="song-list-item mb-3 d-flex align-items-center">
                            <div class="song-list-item-num fw-bold text-muted me-3" style="width: 20px;">{{ $index + 1 }}</div>
                            <div class="song-list-item-img gradient-avatar d-flex justify-content-center align-items-center rounded-circle" style="width: 40px; height: 40px; background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); font-size: 1.2rem;">
                                🧑‍🎤
                            </div>
                            <div class="song-list-item-title ms-3 flex-grow-1">
                                <div class="fw-semibold text-dark">{{ $item->artist ? $item->artist->name : 'Unknown Artist' }}</div>
                            </div>
                            <div class="song-list-item-plays fw-bold text-dark">{{ number_format($item->total_followers) }} followers</div>
                        </div>
                        @empty
                        <div class="text-muted text-center py-5">No followers recorded yet</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Row 3: Top Searches -->
        <div class="row g-5 g-xl-10">
            <div class="col-lg-12">
                <div class="card clean-metric-card h-100">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold fs-4 text-dark">Top Search Keywords</span>
                        </h3>
                    </div>
                    <div class="card-body p-6 pt-3">
                        <div class="row g-4">
                            @forelse($topSearches as $item)
                            <div class="col-md-4">
                                <div class="region-card border rounded p-4" style="background-color: #f9f9f9; transition: all 0.3s ease;">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="fw-bold text-dark fs-6">"{{ $item->keyword }}"</div>
                                    </div>
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div>
                                            <span class="fs-4 fw-bold text-gray-800">{{ number_format($item->search_count) }}</span>
                                            <span class="text-muted fs-8 ms-1">searches</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12 text-muted text-center py-5">No search keywords recorded yet</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
