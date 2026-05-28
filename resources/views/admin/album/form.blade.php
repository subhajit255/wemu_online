@extends('layout.app')
@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                {{ !empty($details->id) ? 'Edit Album' : 'Create Album' }}
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">{{ !empty($details->id) ? 'Update the details of your album' : 'Add a new album to your catalog' }}</span>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('admin.albums.index') }}" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;">Cancel</a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <div class="card shadow-sm border-0 clean-metric-card">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <h3 class="fw-bolder mb-0">Album Details</h3>
                </div>
            </div>
            <div class="card-body p-9">
                <form id="albumForm" class="formSubmit fileUpload" action="{{ !empty($details->id) ? route('admin.albums.storeOrUpdate', $details->id) : route('admin.albums.storeOrUpdate') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(!empty($details->id))
                    <input type="hidden" name="id" value="{{ $details->id }}">
                    @endif

                    <div class="row mb-8">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">Cover Image {!! empty($details->id) ? '<span class="text-danger">*</span>' : '' !!}</div>
                            <div class="text-muted fs-7">Set the album cover image. Must be a square image.</div>
                        </div>
                        <div class="col-xl-9">
                            <div class="image-input image-input-outline {{ empty($details->cover_image) ? 'image-input-empty' : '' }}" data-kt-image-input="true" style="background-image: url('{{ asset('assets/media/svg/files/blank-image.svg') }}')">
                                <div class="image-input-wrapper w-200px h-200px shadow-sm" style="background-image: url('{{ !empty($details->cover_image) ? asset('storage/album/' . $details->cover_image) : 'none' }}'); border-radius: 1rem;"></div>

                                <label class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change cover image">
                                    <i class="fa-solid fa-pencil fs-7"></i>
                                    <input type="file" name="file" id="file" accept=".png, .jpg, .jpeg" />
                                    <input type="hidden" name="cover_image_remove" />
                                </label>

                                <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel cover image">
                                    <i class="fa-solid fa-xmark fs-7"></i>
                                </span>

                                <span class="btn btn-icon btn-circle btn-active-color-primary w-35px h-35px bg-body shadow" data-kt-image-input-action="remove" data-bs-toggle="tooltip" title="Remove cover image">
                                    <i class="fa-solid fa-xmark fs-7"></i>
                                </span>
                            </div>
                            <div class="form-text mt-3">Allowed file types: png, jpg, jpeg. Maximum size 10MB.</div>
                        </div>
                    </div>

                    <div class="row mb-8">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">Album Title <span class="text-danger">*</span></div>
                        </div>
                        <div class="col-xl-9">
                            <input type="text" class="form-control form-control-solid form-control-lg" name="title" id="title" value="{{ !empty($details->title) ? $details->title : '' }}" placeholder="Enter album title" required />
                        </div>
                    </div>

                    <div class="row mb-8">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">Select Artist <span class="text-danger">*</span></div>
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
                            <div class="fs-6 fw-bold mt-2 mb-3">Genre <span class="text-danger">*</span></div>
                        </div>
                        <div class="col-xl-9">
                            <select class="form-select form-select-solid form-select-lg" name="genre_id" id="genre_id" data-control="select2" data-placeholder="Select a genre" required onchange="document.getElementById('genre').value = this.options[this.selectedIndex].text;">
                                <option></option>
                                @foreach($genres as $genreOption)
                                <option value="{{ $genreOption->id }}" {{ (!empty($details->genre_id) && $details->genre_id == $genreOption->id) ? 'selected' : '' }}>{{ $genreOption->title }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="genre" id="genre" value="{{ !empty($details->genre_id) ? $genres->firstWhere('id', $details->genre_id)?->title : '' }}">
                        </div>
                    </div>

                    <div class="row mb-8">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">Description</div>
                        </div>
                        <div class="col-xl-9">
                            <textarea name="description" id="description" class="form-control form-control-solid" rows="5" placeholder="Write a short description about this album">{{ !empty($details->description) ? $details->description : '' }}</textarea>
                        </div>
                    </div>

                    <div class="row mb-8">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">Status</div>
                        </div>
                        <div class="col-xl-9">
                            <div class="d-flex align-items-center mt-3">
                                <input type="hidden" name="status" value="0">
                                <div class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input h-30px w-50px" type="checkbox" value="1" id="status" name="status" {{ (isset($details->status) && $details->status == 1) ? 'checked' : '' }} />
                                    <label class="form-check-label ms-3 fs-6 text-gray-700" for="status">
                                        Publish Album
                                    </label>
                                </div>
                            </div>
                            <div class="form-text mt-3">Published albums will be visible to your audience. Draft albums remain hidden.</div>
                        </div>
                    </div>
                    <div class="row mb-8" id="releaseDateRow" style="{{ (isset($details->status) && $details->status == 1) ? 'display:none;' : '' }}">
                        <div class="col-xl-3">
                            <div class="fs-6 fw-bold mt-2 mb-3">
                                Scheduled Release Date <span class="text-danger">*</span>
                            </div>
                            <div class="text-muted fs-7">Pick a future date when this album should go live.</div>
                        </div>
                        <div class="col-md-6">
                            <input
                                type="date"
                                class="form-control form-control-solid datepicker"
                                name="release_date"
                                id="release_date"
                                min="{{ now()->addDay()->format('Y-m-d') }}"
                                value="{{ !empty($details->release_date) ? \Carbon\Carbon::parse($details->release_date)->format('Y-m-d') : '' }}" />
                            <div class="form-text mt-3">Only future dates are allowed.</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-3"></div>
                        <div class="col-xl-9">
                            <button type="submit" class="btn btn-primary btn-lg me-3" id="kt_submit_button">
                                <span class="indicator-label"><i class="fa-solid fa-check me-2"></i> Save Changes</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <a href="{{ route('admin.albums.index') }}" class="btn btn-light btn-lg btn-active-light-primary">Discard</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#genre_id').on('change', function() {
            var selectedText = $(this).find("option:selected").text();
            $('#genre').val(selectedText);
        });
    });
    $(document).ready(function() {

        // Genre hidden field sync
        $('#genre_id').on('change', function() {
            $('#genre').val($(this).find("option:selected").text());
        });

        // Toggle release date field
        function toggleReleaseDate() {
            const isPublished = $('#status').is(':checked');
            if (isPublished) {
                $('#releaseDateRow').hide();
                $('#release_date').removeAttr('required').val('');
            } else {
                $('#releaseDateRow').show();
                $('#release_date').attr('required', 'required');
            }
        }

        // Run on page load (handles edit mode)
        toggleReleaseDate();

        // Run on toggle change
        $('#status').on('change', function() {
            toggleReleaseDate();
        });

    });
</script>
@endpush