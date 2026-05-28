@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                {{ !empty($details->id) ? 'Edit Song' : 'Upload Song' }}
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">{{ !empty($details->id) ? 'Update the details of your track' : 'Add a new track to your catalog' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.songs.index') }}" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;">Cancel</a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <form id="songForm" class="formSubmit fileUpload" action="{{ !empty($details->id) ? route('admin.songs.storeOrUpdate', $details->id) : route('admin.songs.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(!empty($details->id))
            <input type="hidden" name="id" value="{{ $details->id }}">
            @endif

            <div class="row g-5 g-xl-8">
                <!-- Left Column: Media & Primary Info -->
                <div class="col-xl-8">
                    <div class="card shadow-sm border-0 clean-metric-card mb-8">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <h3 class="fw-bolder mb-0">Track Details</h3>
                            </div>
                        </div>
                        <div class="card-body p-9">
                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-heading text-muted me-2"></i> Song Title <span class="text-danger">*</span></div>
                                </div>
                                <div class="col-xl-9">
                                    <input type="text" class="form-control form-control-solid form-control-lg" name="title" id="title" value="{{ !empty($details->title) ? $details->title : '' }}" placeholder="Enter song title" required />
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-file-audio text-muted me-2"></i> Audio File <span class="text-danger">*</span></div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="dropzone-custom" id="audioDropzone">
                                        <div class="dropzone-icon">
                                            <i class="fa-solid fa-cloud-arrow-up fs-3x"></i>
                                        </div>
                                        <div class="dropzone-text mt-3">
                                            <h4 class="fw-bold text-dark mb-1 fs-5">Click to upload or drag and drop</h4>
                                            <span class="text-muted fs-7">High-quality audio (MP3, WAV) up to 50MB</span>
                                        </div>
                                        <div id="audioFileName" class="mt-4 fw-bold text-primary p-3 rounded" style="display: none; background: rgba(99, 102, 241, 0.1);"></div>
                                    </div>
                                    <input type="file" class="d-none" name="audio_file" id="audio_file" accept=".mp3,.wav,.ogg" {{ empty($details->id) ? 'required' : '' }} onchange="handleDropzoneSelect(this, 'audioDropzone', 'audioFileName', 'fa-solid fa-music')" />

                                    @if(!empty($details->audio_file))
                                    <div class="mt-4 p-4 rounded border" style="border-color: #e2e8f0; background-color: #f8fafc;">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="fa-solid fa-compact-disc fs-2 text-primary me-3"></i>
                                            <div>
                                                <div class="fw-bold text-dark fs-6">Current Track</div>
                                                <div class="text-muted fs-8">{{ $details->audio_file }}</div>
                                            </div>
                                        </div>
                                        <audio controls class="w-100" style="height: 40px;">
                                            <source src="{{ $details->audio_file_path }}" type="audio/mpeg">
                                            Your browser does not support the audio element.
                                        </audio>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-user text-muted me-2"></i> Select Artist <span class="text-danger">*</span></div>
                                </div>
                                <div class="col-xl-9">
                                    <select class="form-select form-select-solid form-select-lg" name="user_id" id="user_id" data-control="select2" data-placeholder="Select an artist" required>
                                        <option></option>
                                        @if(isset($artists))
                                        @foreach($artists as $artist)
                                        <option value="{{ $artist->id }}" {{ (!empty($details->user_id) && $details->user_id == $artist->id) ? 'selected' : '' }}>{{ $artist->name ?? 'Artist '.$artist->id }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-compact-disc text-muted me-2"></i> Album</div>
                                </div>
                                <div class="col-xl-9">
                                    <select class="form-select form-select-solid form-select-lg" name="album_id" id="album_id" data-control="select2" data-placeholder="Select an album">
                                        <option></option>
                                        @if(isset($albums))
                                        @foreach($albums as $album)
                                        <option value="{{ $album->id }}" {{ (!empty($details->album_id) && $details->album_id == $album->id) ? 'selected' : '' }}>{{ $album->title }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-hashtag text-muted me-2"></i> Track Number</div>
                                </div>
                                <div class="col-xl-9">
                                    <input type="number" class="form-control form-control-solid" name="track_number" id="track_number" value="{{ !empty($details->track_number) ? $details->track_number : '' }}" placeholder="e.g. 1" min="1" />
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-users text-muted me-2"></i> Artists</div>
                                </div>
                                <div class="col-xl-9">
                                    <div class="mb-4">
                                        <label class="form-label fs-7 fw-semibold text-muted">Primary Artist Name</label>
                                        <input type="text" class="form-control form-control-solid" name="artist_name" id="artist_name" value="{{ !empty($details->artist_name) ? $details->artist_name : auth()->user()->name ?? '' }}" placeholder="Artist Name" />
                                    </div>
                                    <div>
                                        <label class="form-label fs-7 fw-semibold text-muted">Featured Artists</label>
                                        <input type="text" class="form-control form-control-solid" name="featured_artists" id="featured_artists" value="{{ !empty($details->featured_artists) ? $details->featured_artists : '' }}" placeholder="e.g. The Weeknd, Daft Punk" />
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-align-left text-muted me-2"></i> Description</div>
                                </div>
                                <div class="col-xl-9">
                                    <textarea name="description" id="description" class="form-control form-control-solid" rows="4" placeholder="Write a short description about this track">{{ !empty($details->description) ? $details->description : '' }}</textarea>
                                </div>
                            </div>

                            <div class="row mb-8">
                                <div class="col-xl-3">
                                    <div class="fs-6 fw-bold mt-2 mb-3"><i class="fa-solid fa-microphone-lines text-muted me-2"></i> Lyrics</div>
                                </div>
                                <div class="col-xl-9">
                                    <textarea name="lyrics" id="lyrics" class="form-control form-control-solid" rows="6" placeholder="Add lyrics for your listeners...">{{ !empty($details->lyrics) ? $details->lyrics : '' }}</textarea>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Right Column: Meta & Media -->
                <div class="col-xl-4">
                    <div class="card shadow-sm border-0 clean-metric-card mb-8">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <h3 class="fw-bolder mb-0">Cover & Media</h3>
                            </div>
                        </div>
                        <div class="card-body p-9 text-center">
                            <div class="image-input image-input-outline {{ empty($details->cover_image_path) ? 'image-input-empty' : '' }} mb-4" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}')">
                                <div class="image-input-wrapper w-200px h-200px shadow-sm" style="background-image: url('{{ !empty($details->cover_image_path) ? $details->cover_image_path : 'none' }}'); border-radius: 1rem;"></div>

                                <label class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change cover image">
                                    <i class="fa-solid fa-pencil fs-7"></i>
                                    <input type="file" name="cover_image" id="cover_image" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="cover_image_remove" />
                                </label>

                                <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel cover image">
                                    <i class="fa-solid fa-xmark fs-7"></i>
                                </span>
                                <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove cover image">
                                    <i class="fa-solid fa-xmark fs-7"></i>
                                </span>
                            </div>
                            <div class="text-muted fs-7">Set the track cover image. If empty, the album cover is typically used.</div>

                            <div class="separator my-8"></div>

                            <div class="text-start mb-3">
                                <label class="fs-6 fw-bold mb-4"><i class="fa-solid fa-film text-muted me-2"></i> Canvas / Background Video</label>

                                <div class="dropzone-custom py-4 px-3" id="bgDropzone">
                                    <div class="dropzone-icon">
                                        <i class="fa-solid fa-video fs-2x"></i>
                                    </div>
                                    <div class="dropzone-text mt-2">
                                        <h4 class="fw-bold text-dark mb-1 fs-6">Upload Video Canvas</h4>
                                        <span class="text-muted fs-8">Looping MP4 up to 20MB</span>
                                    </div>
                                    <div id="bgFileName" class="mt-3 fw-bold text-primary p-2 rounded fs-8" style="display: none; background: rgba(99, 102, 241, 0.1);"></div>
                                </div>

                                <input type="file" class="d-none" name="background" id="background" accept=".mp4,.png,.jpg,.jpeg" onchange="handleDropzoneSelect(this, 'bgDropzone', 'bgFileName', 'fa-solid fa-file-video')" />
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 clean-metric-card">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <h3 class="fw-bolder mb-0">Metadata</h3>
                            </div>
                        </div>
                        <div class="card-body p-9">
                            <div class="mb-8">
                                <label class="fs-6 fw-bold mb-3">Genre <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" name="genre_id" id="genre_id" data-control="select2" data-placeholder="Select a genre" required onchange="document.getElementById('genre').value = this.options[this.selectedIndex].text;">
                                    <option></option>
                                    @if(isset($genres))
                                    @foreach($genres as $genreOption)
                                    <option value="{{ $genreOption->id }}" {{ (!empty($details->genre_id) && $details->genre_id == $genreOption->id) ? 'selected' : '' }}>{{ $genreOption->title }}</option>
                                    @endforeach
                                    @endif
                                </select>
                                <input type="hidden" name="genre" id="genre" value="{{ !empty($details->genre_id) && isset($genres) ? $genres->firstWhere('id', $details->genre_id)?->title : '' }}">
                            </div>

                            <div class="mb-8">
                                <label class="fs-6 fw-bold mb-3">Language <span class="text-danger">*</span></label>
                                <select class="form-select form-select-solid" name="language_id" id="language_id" data-control="select2" data-placeholder="Select a language" required>
                                    <option></option>
                                    @if(isset($languages))
                                    @foreach($languages as $language)
                                    <option value="{{ $language->id }}" {{ (!empty($details->language_id) && $details->language_id == $language->id) ? 'selected' : '' }}>{{ $language->name ?? $language->title }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="mb-8">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="fs-6 fw-bold text-gray-700">Explicit Content</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="is_explicit" value="0">
                                        <input class="form-check-input h-20px w-40px" type="checkbox" value="1" id="is_explicit" name="is_explicit"
                                            {{ (isset($details->is_explicit) && (int)$details->is_explicit === 1) ? 'checked' : '' }} />
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8">
                                <div class="d-flex align-items-center justify-content-between">
                                    <label class="fs-6 fw-bold text-gray-700">Publish Track</label>
                                    <div class="form-check form-switch form-check-custom form-check-solid">
                                        <input type="hidden" name="status" value="0">
                                        <input class="form-check-input h-20px w-40px" type="checkbox" value="1" id="song_status" name="status" {{ (isset($details->status) && $details->status == 1) ? 'checked' : '' }} />
                                    </div>
                                </div>
                            </div>

                            {{-- Scheduled Release Date & Time (shown when toggle is OFF) --}}
                            <div class="mb-8" id="songReleaseDateRow" style="{{ (isset($details->status) && $details->status == 1) || (!isset($details->status)) ? 'display:none;' : '' }}">
                                <label class="fs-6 fw-bold text-gray-700 mb-2">
                                    Scheduled Release Date & Time <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    class="form-control form-control-solid"
                                    name="release_at"
                                    id="song_release_at"
                                    placeholder="Select date & time"
                                    value="{{ !empty($details->published_at) ? \Carbon\Carbon::parse($details->published_at)->format('Y-m-d H:i') : '' }}"
                                    readonly />
                                <div class="form-text mt-2">Only future date & time are allowed.</div>
                            </div>

                            <div class="separator my-6"></div>

                            <button type="submit" class="btn btn-gradient-primary w-100 fs-5 py-4 fw-bolder" id="kt_submit_button">
                                <span class="indicator-label"><i class="fa-solid fa-rocket me-2"></i> Save & Upload Track</span>
                                <span class="indicator-progress">Processing...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>
    function handleDropzoneSelect(input, dropzoneId, fileNameId, iconClass) {
        if (input.files[0]) {
            document.getElementById(fileNameId).style.display = 'inline-block';
            document.getElementById(fileNameId).innerHTML = `
                <div class="d-flex align-items-center justify-content-between">
                    <span><i class="${iconClass} me-2"></i> ${input.files[0].name}</span>
                    <button type="button" class="btn btn-sm btn-icon btn-light-danger ms-4 w-25px h-25px" 
                        onclick="event.stopPropagation(); removeDropzoneFile('${input.id}', '${dropzoneId}', '${fileNameId}')">
                        <i class="fa-solid fa-xmark fs-8"></i>
                    </button>
                </div>
            `;
            document.getElementById(dropzoneId).style.borderColor = '#6366f1';
            document.getElementById(dropzoneId).style.backgroundColor = 'rgba(99, 102, 241, 0.05)';
        }
    }

    function removeDropzoneFile(inputId, dropzoneId, fileNameId) {
        document.getElementById(inputId).value = "";
        document.getElementById(fileNameId).style.display = "none";
        document.getElementById(dropzoneId).style.borderColor = "";
        document.getElementById(dropzoneId).style.backgroundColor = "";
    }

    function setupDropzone(dropzoneId, inputId, fileNameId, iconClass) {
        const dropzone = document.getElementById(dropzoneId);
        const input = document.getElementById(inputId);

        dropzone.addEventListener('click', () => input.click());

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, () => dropzone.classList.remove('dragover'), false);
        });

        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                handleDropzoneSelect(input, dropzoneId, fileNameId, iconClass);
            }
        });
    }

    $(document).ready(function() {
        setupDropzone('audioDropzone', 'audio_file', 'audioFileName', 'fa-solid fa-music');
        setupDropzone('bgDropzone', 'background', 'bgFileName', 'fa-solid fa-file-video');

        $('#genre_id').on('change', function() {
            var selectedText = $(this).find("option:selected").text();
            $('#genre').val(selectedText);
        });
    });
    $(document).ready(function() {
        var releasePicker = null;

        if ($('#song_release_at').length) {
            releasePicker = flatpickr('#song_release_at', {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                altInput: true,
                altFormat: 'D, d M Y - h:i K',
                time_24hr: false,
                minuteIncrement: 15,
                minDate: 'today',
                defaultDate: $('#song_release_at').val() || null,
            });
        }

        // Toggle function
        function toggleSongReleaseDate() {
            var isPublished = $('#song_status').is(':checked');

            if (isPublished) {
                // Hide and clear
                $('#songReleaseDateRow').hide();
                $('#song_release_at').removeAttr('required');
                if (releasePicker) {
                    releasePicker.clear();
                }
            } else {
                // Show and require
                $('#songReleaseDateRow').show();
                $('#song_release_at').attr('required', 'required');
            }
        }

        toggleSongReleaseDate();

        $('#song_status').on('change', function() {
            toggleSongReleaseDate();
        });

    });
</script>
@endpush