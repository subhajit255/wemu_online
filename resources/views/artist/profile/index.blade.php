@extends('layout.app')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                My Profile
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Update your personal and artist information here.</span>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        @if(session('success'))
        <div class="alert alert-success d-flex align-items-center p-5 mb-10">
            <i class="fa-solid fa-check-circle fs-2hx text-success me-4"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-success">Success</h4>
                <span>{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center p-5 mb-10">
            <i class="fa-solid fa-exclamation-triangle fs-2hx text-danger me-4"></i>
            <div class="d-flex flex-column">
                <h4 class="mb-1 text-danger">Error</h4>
                <span>{{ session('error') }}</span>
            </div>
        </div>
        @endif

        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_profile_details">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Profile Details</h3>
                </div>
            </div>

            <div id="kt_account_profile_details" class="collapse show">
                <form class="form" method="POST" action="{{ route('artist.profile.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body border-top p-9">

                        <div class="row mb-8">
                            <div class="col-md-6">
                                <label class="d-block fw-semibold fs-6 mb-5">
                                    <span class="label_title">Profile Image</span>
                                </label>
                                <div class="fv-row">
                                    <style>
                                        .image-input-placeholder-admin {
                                            background-image: url("{{ auth()->user()->image_path }}");
                                            background-size: cover;
                                        }
                                    </style>
                                    <div class="image-input image-input-empty image-input-outline image-input-placeholder-admin" data-kt-image-input="true">
                                        <div class="image-input-wrapper w-125px h-125px"></div>
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change avatar">
                                            <i class="fa-solid fa-pen fs-7"></i>
                                            <input type="file" name="profile_image" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="avatar_remove" />
                                        </label>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel avatar">
                                            <i class="fa-solid fa-xmark fs-3"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">Allowed file types: png, jpg, jpeg.</div>
                                    @error('profile_image')
                                    <div class="text-danger mt-2 fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="d-block fw-semibold fs-6 mb-5">
                                    <span class="label_title">Cover Banner</span>
                                </label>
                                <div class="fv-row">
                                    <style>
                                        .image-input-placeholder-cover {
                                            background-image: url("{{ $profile->cover_image_path ?? asset('assets/media/books/11.png') }}");
                                            background-size: cover;
                                            background-position: center;
                                        }
                                    </style>
                                    <div class="image-input image-input-empty image-input-outline image-input-placeholder-cover w-100" data-kt-image-input="true" style="border-radius: 0.475rem;">
                                        <div class="image-input-wrapper w-100 h-125px" style="border-radius: 0.475rem;"></div>
                                        <label class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="change" data-bs-toggle="tooltip" title="Change cover">
                                            <i class="fa-solid fa-pen fs-7"></i>
                                            <input type="file" name="cover_banner" accept=".png, .jpg, .jpeg" />
                                            <input type="hidden" name="cover_remove" />
                                        </label>
                                        <span class="btn btn-icon btn-circle btn-active-color-primary w-25px h-25px bg-body shadow" data-kt-image-input-action="cancel" data-bs-toggle="tooltip" title="Cancel cover">
                                            <i class="fa-solid fa-xmark fs-3"></i>
                                        </span>
                                    </div>
                                    <div class="form-text">Allowed file types: png, jpg, jpeg. Optimal size: 1920x1080.</div>
                                    @error('cover_banner')
                                    <div class="text-danger mt-2 fs-7">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-lg form-control-solid" placeholder="Full Name" value="{{ old('name', $user->name) }}" />
                                @error('name')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Stage Name</label>
                                <input type="text" name="stage_name" class="form-control form-control-lg form-control-solid" placeholder="Stage Name" value="{{ old('stage_name', $profile->display_name) }}" />
                                @error('stage_name')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-lg form-control-solid" placeholder="Email" value="{{ old('email', $user->email) }}" />
                                @error('email')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Phone Number</label>
                                <input type="text" name="mobile" class="form-control form-control-lg form-control-solid" placeholder="Phone Number" value="{{ old('mobile', $user->mobile_number) }}" />
                                @error('mobile')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Country</label>
                                <input type="text" name="country" class="form-control form-control-lg form-control-solid" placeholder="Country" value="{{ old('country', $profile->country) }}" />
                                @error('country')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Years of Active</label>
                                <input type="number" name="years_of_active" class="form-control form-control-lg form-control-solid" placeholder="Years" value="{{ old('years_of_active', $profile->years_of_active) }}" />
                                @error('years_of_active')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label required fw-semibold fs-6">Primary Genre</label>
                                <select name="primary_genre_id" class="form-select form-select-solid" data-control="select2" data-hide-search="false">
                                    <option value="">Select Genre</option>
                                    @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ old('primary_genre_id', $profile->primary_genre_id) == $genre->id ? 'selected' : '' }}>{{ $genre->title }}</option>
                                    @endforeach
                                </select>
                                @error('primary_genre_id')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Sub Genre</label>
                                <select name="sub_genre_id" class="form-select form-select-solid" data-control="select2" data-hide-search="false">
                                    <option value="">Select Sub Genre (Optional)</option>
                                    @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ old('sub_genre_id', $profile->sub_genre_id) == $genre->id ? 'selected' : '' }}>{{ $genre->title }}</option>
                                    @endforeach
                                </select>
                                @error('sub_genre_id')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Label / Company</label>
                                <input type="text" name="label" class="form-control form-control-lg form-control-solid" placeholder="Label Name" value="{{ old('label', $profile->label) }}" />
                                @error('label')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Website URL</label>
                                <input type="url" name="website" class="form-control form-control-lg form-control-solid" placeholder="https://..." value="{{ old('website', $profile->website) }}" />
                                @error('website')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="row mb-6">
                            <div class="col-lg-12 fv-row">
                                <label class="col-form-label required fw-semibold fs-6">Bio</label>
                                <textarea name="bio" class="form-control form-control-lg form-control-solid" rows="4" placeholder="Tell us about yourself...">{{ old('bio', $profile->bio) }}</textarea>
                                @error('bio')<div class="text-danger mt-1 fs-7">{{ $message }}</div>@enderror
                            </div>
                        </div>

                    </div>
                    
                    <div class="card-header border-0 mt-5">
                        <div class="card-title m-0">
                            <h3 class="fw-bold m-0">Social Links</h3>
                        </div>
                    </div>
                    <div class="card-body border-top p-9">
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Instagram</label>
                                <input type="url" name="instagram_url" class="form-control form-control-lg form-control-solid" placeholder="https://instagram.com/..." value="{{ old('instagram_url', $socials->instagram_url) }}" />
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">YouTube</label>
                                <input type="url" name="youtube_url" class="form-control form-control-lg form-control-solid" placeholder="https://youtube.com/..." value="{{ old('youtube_url', $socials->youtube_url) }}" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">TikTok</label>
                                <input type="url" name="tiktok_url" class="form-control form-control-lg form-control-solid" placeholder="https://tiktok.com/..." value="{{ old('tiktok_url', $socials->tiktok_url) }}" />
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Facebook</label>
                                <input type="url" name="facebook_url" class="form-control form-control-lg form-control-solid" placeholder="https://facebook.com/..." value="{{ old('facebook_url', $socials->facebook_url) }}" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Twitter</label>
                                <input type="url" name="twitter_url" class="form-control form-control-lg form-control-solid" placeholder="https://twitter.com/..." value="{{ old('twitter_url', $socials->twitter_url) }}" />
                            </div>
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Spotify</label>
                                <input type="url" name="spotify_url" class="form-control form-control-lg form-control-solid" placeholder="https://spotify.com/..." value="{{ old('spotify_url', $socials->spotify_url) }}" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-lg-6 fv-row mb-6">
                                <label class="col-form-label fw-semibold fs-6">Apple Music</label>
                                <input type="url" name="apple_music_url" class="form-control form-control-lg form-control-solid" placeholder="https://music.apple.com/..." value="{{ old('apple_music_url', $socials->apple_music_url) }}" />
                            </div>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-dark" id="kt_account_profile_details_submit">Save Changes</button>
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
        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif
    });
</script>
@endpush
