@extends('layout.app')

@section('content')
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Artist Settings
            </h1>
            <span class="text-muted fs-7 my-1 pt-1">Manage your release preferences and default settings</span>
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

        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0 cursor-pointer" role="button" data-bs-toggle="collapse" data-bs-target="#kt_account_settings">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Default Release Settings & Preferences</h3>
                </div>
            </div>

            <div id="kt_account_settings" class="collapse show">
                <form class="form" method="POST" action="{{ route('artist.settings.update') }}">
                    @csrf
                    <div class="card-body border-top p-9">

                        <!-- Artist Type -->
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Artist Type</div>
                            </div>
                            <div class="col-xl-9">
                                <div class="d-flex gap-5">
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="artist_type" value="INDEPENDENT" {{ (old('artist_type', $preferences->artist_type) == 'INDEPENDENT') ? 'checked' : '' }} />
                                        <span class="form-check-label fw-semibold text-gray-700">Independent</span>
                                    </label>
                                    <label class="form-check form-check-custom form-check-solid">
                                        <input class="form-check-input" type="radio" name="artist_type" value="SIGNED" {{ (old('artist_type', $preferences->artist_type) == 'SIGNED') ? 'checked' : '' }} />
                                        <span class="form-check-label fw-semibold text-gray-700">Signed / Label</span>
                                    </label>
                                </div>
                                @error('artist_type')
                                <div class="text-danger mt-2 fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Release Scheduling Preferences -->
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Release Scheduling Preferences</div>
                            </div>
                            <div class="col-xl-9">
                                <select name="release_frequency" class="form-select form-select-solid" data-control="select2" data-hide-search="true">
                                    <option value="">Select Release Frequency</option>
                                    <option value="1" {{ (old('release_frequency', $preferences->release_frequency) == 1) ? 'selected' : '' }}>Weekly</option>
                                    <option value="2" {{ (old('release_frequency', $preferences->release_frequency) == 2) ? 'selected' : '' }}>Monthly</option>
                                    <option value="3" {{ (old('release_frequency', $preferences->release_frequency) == 3) ? 'selected' : '' }}>Occasionally</option>
                                </select>
                                <div class="form-text mt-2">How often do you plan to drop new releases? We use this to auto-fill scheduling hints.</div>
                                @error('release_frequency')
                                <div class="text-danger mt-2 fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Preferred Genre Selection -->
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Preferred Genre Selection</div>
                            </div>
                            <div class="col-xl-9">
                                @php
                                $selectedGenres = old('favorite_genres', $preferences->favorite_genres ?? []);
                                @endphp
                                <select name="favorite_genres[]" class="form-select form-select-solid" data-control="select2" data-placeholder="Select your preferred genres" multiple="multiple">
                                    @foreach($genres as $genre)
                                    <option value="{{ $genre->id }}" {{ in_array($genre->id, $selectedGenres) ? 'selected' : '' }}>{{ $genre->title }}</option>
                                    @endforeach
                                </select>
                                <div class="form-text mt-2">These genres will be auto-filled or suggested when you upload new songs and albums.</div>
                                @error('favorite_genres')
                                <div class="text-danger mt-2 fs-7">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Auto-fill metadata (Visual toggle for future/currently implicit in preference) -->
                        <div class="row mb-8">
                            <div class="col-xl-3">
                                <div class="fs-6 fw-semibold mt-2 mb-3">Auto-fill Metadata</div>
                            </div>
                            <div class="col-xl-9">
                                <label class="form-check form-switch form-check-custom form-check-solid">
                                    <input class="form-check-input" type="checkbox" checked disabled />
                                    <span class="form-check-label fw-semibold text-gray-700">
                                        Enable Smart Auto-Fill
                                    </span>
                                </label>
                                <div class="form-text mt-2">Automatically fill metadata (like genres) in your uploads based on the preferences set above. (Always active)</div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer d-flex justify-content-end py-6 px-9">
                        <button type="submit" class="btn btn-dark" id="kt_account_profile_details_submit">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection