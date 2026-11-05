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
                <li class="breadcrumb-item text-dark">{{ $albumDetails->title }}</li>
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
        <style>
            .cursor-move {
                cursor: grab !important;
            }
            .cursor-move:active {
                cursor: grabbing !important;
            }
            .sortable-ghost {
                opacity: 0.35;
                background-color: #f1faff !important;
            }
            .sortable-chosen {
                background-color: #f8f9fa !important;
            }
            .drag-handle {
                cursor: grab;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 20px;
                border-radius: 4px;
                transition: background-color 0.15s, color 0.15s;
            }
            .drag-handle:hover {
                background-color: #eff2f5;
                color: #009ef7 !important;
            }
            .drag-handle:active {
                cursor: grabbing;
            }
        </style>
        <div class="card clean-metric-card mb-5 mb-xl-10">
            <div class="card-header border-0 pt-6 d-flex justify-content-between align-items-center">
                <div class="card-title align-items-center d-flex">
                    <span class="card-label fw-bold fs-4 text-dark">Tracklist</span>
                    <span class="badge badge-light-primary fw-semibold fs-8 ms-3" title="Drag and drop rows using the grip handle to change track order">
                        <i class="fa-solid fa-arrows-up-down fs-8 me-1 text-primary"></i> Drag to reorder
                    </span>
                </div>
                <div class="card-toolbar">
                    <span id="reorder-saving-indicator" style="display: none;" class="text-primary fw-bold fs-7 align-items-center">
                        <span class="spinner-border spinner-border-sm me-2 text-primary" role="status"></span>
                        Saving order...
                    </span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle gs-0 gy-4 song-table mb-0">
                        <thead>
                            <tr class="fw-bold text-muted bg-transparent">
                                <th class="ps-8 min-w-60px w-60px">#</th>
                                <th class="min-w-200px">Title</th>
                                <th class="min-w-100px text-end">Streams</th>
                                <th class="min-w-150px text-end">Duration</th>
                                <th class="min-w-120px text-end">Status</th>
                                <th class="min-w-80px text-end pe-8 rounded-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tracklist-tbody">
                            @forelse ($albumDetails->songs as $index => $song)
                            <tr class="sortable-row" data-id="{{ $song->id }}">
                                <td class="ps-8 text-muted fw-bold">
                                    <div class="d-flex align-items-center">
                                        <span class="drag-handle text-gray-400 text-hover-primary cursor-move me-2" title="Drag to reorder">
                                            <i class="fa-solid fa-grip-vertical fs-6"></i>
                                        </span>
                                        <span class="row-index">{{ $index + 1 }}</span>
                                    </div>
                                </td>
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
                                    <div class="d-flex justify-content-end align-items-center gap-1">
                                        <a href="{{ route('artist.songs.show', $song->id) }}" class="btn btn-icon btn-light btn-sm w-30px h-30px" title="Play / View Details">
                                            <i class="fa-solid fa-play fs-7 text-dark"></i>
                                        </a>
                                        <a href="{{ route('artist.songs.storeOrUpdate', $song->id) }}" class="btn btn-icon btn-light-primary btn-sm w-30px h-30px" title="Edit">
                                            <i class="fa-solid fa-pen-to-square fs-7"></i>
                                        </a>
                                        <form action="{{ route('artist.songs.destroy', $song->id) }}" method="POST" class="d-inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-icon btn-light-danger btn-sm w-30px h-30px btn-delete" title="Delete">
                                                <i class="fa-solid fa-trash fs-7"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr class="no-songs-row">
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
@push('script')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
    $(document).ready(function() {
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            let form = $(this).closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });

        const tracklistTbody = document.getElementById('tracklist-tbody');
        if (tracklistTbody && tracklistTbody.querySelectorAll('.sortable-row').length > 1) {
            new Sortable(tracklistTbody, {
                handle: '.drag-handle',
                draggable: '.sortable-row',
                animation: 180,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                onEnd: function(evt) {
                    // Update index numbers visually immediately
                    const rows = tracklistTbody.querySelectorAll('.sortable-row');
                    const songIds = [];
                    rows.forEach((row, idx) => {
                        const indexEl = row.querySelector('.row-index');
                        if (indexEl) {
                            indexEl.textContent = idx + 1;
                        }
                        const id = row.getAttribute('data-id');
                        if (id) {
                            songIds.push(id);
                        }
                    });

                    // If position didn't change, do nothing
                    if (evt.oldIndex === evt.newIndex) {
                        return;
                    }

                    const indicator = $('#reorder-saving-indicator');
                    indicator.css('display', 'inline-flex');

                    $.ajax({
                        url: "{{ route('artist.albums.reorderSongs', $albumDetails->id) }}",
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            _token: '{{ csrf_token() }}',
                            song_ids: songIds
                        },
                        success: function(response) {
                            indicator.hide();
                            if (response.status) {
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(response.message || 'Track order updated successfully!');
                                }
                            } else {
                                if (typeof toastr !== 'undefined') {
                                    toastr.error(response.message || 'Failed to update track order.');
                                }
                            }
                        },
                        error: function(xhr) {
                            indicator.hide();
                            let msg = 'Failed to update track order. Please try again.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            if (typeof toastr !== 'undefined') {
                                toastr.error(msg);
                            }
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
@endsection