@extends('layout.app')

@section('content')
@php
$hasRealAudio = !empty($song->audio_file) && $song->audio_file_path !== '#';
$audioSrc = $hasRealAudio ? $song->audio_file_path : 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
@endphp
<!-- Page Toolbar -->
<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6 mb-0 border-0">
    <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex flex-stack">
        <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('artist.songs.index') }}" class="text-muted text-hover-primary">Songs</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-400 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-dark">Song Details</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <a href="{{ route('artist.songs.index') }}" class="btn btn-sm btn-light fw-bold" style="border: 1px solid #e5e7eb;">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Songs
            </a>
            <a href="{{ route('artist.songs.storeOrUpdate', $song->id) }}" class="btn btn-sm btn-dark fw-bold">
                <i class="fa-solid fa-pen me-1"></i> Edit Song
            </a>
        </div>
    </div>
</div>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">

        <!-- Custom Warning banner for Fallback Audio -->
        @if(!$hasRealAudio)
        <div class="alert alert-dismissible bg-light-warning d-flex flex-column flex-sm-row p-5 mb-8 border border-warning">
            <i class="fa-solid fa-triangle-exclamation fs-2hx text-warning me-4 mb-5 mb-sm-0"></i>
            <div class="d-flex flex-column pe-0 pe-sm-10">
                <h5 class="mb-1 text-dark fw-bold">{{ empty($song->audio_file) ? 'No Audio File Uploaded' : 'Audio File Missing from Server' }}</h5>
                <span>{{ empty($song->audio_file) ? 'This song does not currently have an active audio file uploaded.' : 'The audio file referenced in the database was not found in the physical storage directory.' }} We have loaded a beautiful high-fidelity royalty-free fallback preview track so you can fully interact with and verify the audio playback animations and volume controls.</span>
            </div>
            <button type="button" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-icon ms-sm-auto" data-bs-dismiss="alert">
                <i class="fa-solid fa-xmark text-warning fs-4"></i>
            </button>
        </div>
        @endif

        <!-- Hero Section & Custom Audio Player Layout -->
        <div class="card glass-card mb-8 overflow-hidden position-relative">
            <!-- Ambient blurred cover art backdrop -->
            <div class="player-hero-backdrop" style="background-image: url('{{ $song->cover_image_path }}');"></div>
            
            <div class="card-body p-8 p-lg-12 position-relative" style="z-index: 1;">
                <div class="row align-items-center g-8">

                    <!-- Vinyl spinning visualizer -->
                    <div class="col-12 col-md-4 text-center">
                        <div class="vinyl-container">
                            <div class="vinyl-disk" id="vinylDisk">
                                <div class="vinyl-center-hole"></div>
                                <img src="{{ $song->cover_image_path }}" alt="Song Cover" class="vinyl-cover" id="vinylCover">
                            </div>
                            <!-- Tonearm / Needle -->
                            <svg class="vinyl-tonearm" id="vinylTonearm" viewBox="0 0 100 200">
                                <!-- Base/Pivot -->
                                <circle cx="70" cy="30" r="14" fill="#333" stroke="#444" stroke-width="2" />
                                <circle cx="70" cy="30" r="6" fill="#111" />
                                <!-- Arm Shaft -->
                                <path d="M 70 30 Q 50 80 40 140" fill="none" stroke="#888" stroke-width="3" stroke-linecap="round" />
                                <!-- Head Shell / Cartridge -->
                                <rect x="32" y="140" width="16" height="24" rx="2" transform="rotate(-15 40 152)" fill="#222" />
                                <rect x="35" y="164" width="10" height="4" fill="#1b84ff" />
                            </svg>
                        </div>
                    </div>

                    <!-- Song details and high-fidelity Player Controls -->
                    <div class="col-12 col-md-8">
                        <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                            @if($song->is_explicit)
                            <span class="badge badge-light-danger fw-bold px-3 py-1 text-uppercase fs-8" style="letter-spacing: 0.5px;">Explicit</span>
                            @endif
                            <span class="badge badge-light-{{ $song->status == 1 ? 'success' : 'warning' }} fw-bold px-3 py-1">
                                {{ $song->status == 1 ? 'Published' : 'Draft' }}
                            </span>
                            @if($song->album)
                            <span class="text-muted fs-7 fw-semibold">
                                Album: <a href="{{ route('artist.albums.show', $song->album->id) }}" class="text-hover-primary fw-bold">{{ $song->album->title }}</a>
                            </span>
                            @endif
                        </div>

                        <h1 class="text-dark fw-bolder mb-1" style="font-size: 2.2rem; letter-spacing: -0.5px;">{{ $song->title }}</h1>
                        <p class="fs-5 text-muted mb-6">
                            By <span class="fw-bold text-dark">{{ $song->artist_name ?? 'Unknown Artist' }}</span>
                            @if($song->featured_artists)
                            <span class="fs-6 text-muted ms-1">ft. {{ $song->featured_artists }}</span>
                            @endif
                        </p>

                        <!-- High-Fidelity Audio Controls -->
                        <div class="p-6 rounded-4 bg-light bg-opacity-25 border border-dashed border-gray-300">
                            <!-- Hidden Audio Player -->
                            <audio id="audioElement" src="{{ $audioSrc }}" preload="auto"></audio>

                            <!-- Playback Control Buttons Row -->
                            <div class="d-flex align-items-center justify-content-between mb-4">
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Play/Pause Button -->
                                    <button class="btn btn-icon btn-primary w-50px h-50px rounded-circle" id="playBtn" title="Play">
                                        <i class="fa-solid fa-play fs-4 text-white" id="playIcon"></i>
                                    </button>
                                    <!-- Skip Backward 10s -->
                                    <button class="btn btn-icon btn-light btn-active-light-primary w-35px h-35px rounded-circle border" id="skipBackBtn" title="Back 10s">
                                        <i class="fa-solid fa-backward fs-7"></i>
                                    </button>
                                    <!-- Skip Forward 10s -->
                                    <button class="btn btn-icon btn-light btn-active-light-primary w-35px h-35px rounded-circle border" id="skipForwardBtn" title="Forward 10s">
                                        <i class="fa-solid fa-forward fs-7"></i>
                                    </button>
                                </div>

                                <!-- Dynamic Volume Section -->
                                <div class="d-flex align-items-center gap-3">
                                    <!-- Volume Mute/Unmute Toggle -->
                                    <button class="btn btn-icon btn-clean btn-active-color-primary btn-sm" id="volumeMuteBtn" title="Mute/Unmute">
                                        <i class="fa-solid fa-volume-high fs-5 text-dark" id="volumeIcon"></i>
                                    </button>

                                    <!-- Volume Decrease (-) Button -->
                                    <button class="btn btn-icon btn-light btn-sm w-30px h-30px border rounded-circle" id="volumeDecBtn" title="Decrease Volume (-10%)">
                                        <i class="fa-solid fa-minus fs-8"></i>
                                    </button>

                                    <!-- Volume Slider -->
                                    <div style="width: 100px;">
                                        <input type="range" class="premium-range" id="volumeSlider" min="0" max="1" step="0.05" value="0.8" title="Volume Slider">
                                    </div>

                                    <!-- Volume Increase (+) Button -->
                                    <button class="btn btn-icon btn-light btn-sm w-30px h-30px border rounded-circle" id="volumeIncBtn" title="Increase Volume (+10%)">
                                        <i class="fa-solid fa-plus fs-8"></i>
                                    </button>

                                    <!-- Volume Percentage Badge -->
                                    <span class="badge badge-light-primary fw-bold fs-7 min-w-45px text-center" id="volumePercentage">80%</span>
                                </div>
                            </div>

                            <!-- Progress Seek Bar Row -->
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-8 fw-semibold text-muted min-w-40px" id="currentTime">0:00</span>
                                <div class="flex-grow-1">
                                    <input type="range" class="premium-range" id="progressSlider" min="0" value="0" step="1" title="Playback Progress">
                                </div>
                                <span class="fs-8 fw-semibold text-muted min-w-40px text-end" id="totalDuration">
                                    @php
                                    $durationVal = $song->duration ?? 213; // default fallback seconds
                                    $min = floor($durationVal / 60);
                                    $sec = floor($durationVal % 60);
                                    echo sprintf('%d:%02d', $min, $sec);
                                    @endphp
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Stats row matching migration analytics -->
        <div class="row g-5 mb-8">
            <div class="col-6 col-lg-3">
                <div class="card metric-card-premium card-streams shadow-sm">
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-primary">
                                <i class="fa-solid fa-play fs-4 text-primary"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-4">{{ number_format($song->play_count ?? 0) }}</span>
                            <span class="text-muted fw-semibold fs-7">Total Streams</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card metric-card-premium card-favorites shadow-sm">
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-danger">
                                <i class="fa-solid fa-heart fs-4 text-danger"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-4">{{ number_format($song->likes_count ?? 0) }}</span>
                            <span class="text-muted fw-semibold fs-7">Favorites</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card metric-card-premium card-downloads shadow-sm">
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-success">
                                <i class="fa-solid fa-download fs-4 text-success"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-4">{{ number_format($song->download_count ?? 0) }}</span>
                            <span class="text-muted fw-semibold fs-7">Downloads</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="card metric-card-premium card-shares shadow-sm">
                    <div class="card-body p-6 d-flex align-items-center">
                        <div class="symbol symbol-45px me-4">
                            <span class="symbol-label bg-light-warning">
                                <i class="fa-solid fa-share-nodes fs-4 text-warning"></i>
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <span class="text-dark fw-bolder fs-4">{{ number_format($song->shares_count ?? 0) }}</span>
                            <span class="text-muted fw-semibold fs-7">Shares</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Details Grid & Lyrics Section -->
        <div class="row g-8">

            <!-- Metadata details card -->
            <div class="col-12 col-lg-5">
                <div class="card glass-card shadow-sm h-100">
                    <div class="card-header border-0 pt-6">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark fs-4">Track Information</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Technical and publishing details</span>
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle table-row-dashed fs-6 gy-4 mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted min-w-100px">Genre</td>
                                        <td class="text-dark fw-bold text-end">{{ $song->genre->title ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted min-w-100px">Language</td>
                                        <td class="text-dark fw-bold text-end">{{ $song->language->name ?? 'None' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted min-w-100px">Track No.</td>
                                        <td class="text-dark fw-bold text-end">#{{ $song->track_number ?? '1' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted min-w-100px">Release Date</td>
                                        <td class="text-dark fw-bold text-end">{{ $song->published_at ? \Carbon\Carbon::parse($song->published_at)->format('M d, Y') : 'Not Set' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        @if($song->description)
                        <div class="mt-8 border-top pt-6 border-dashed">
                            <h4 class="text-dark fw-bold mb-4 fs-6 d-flex align-items-center">
                                <i class="fa-solid fa-quote-left text-primary me-2 fs-5"></i>
                                Story Behind the Song
                            </h4>
                            <div class="story-container p-5 rounded-3 border-start position-relative overflow-hidden">
                                <div class="position-absolute opacity-5 end-0 bottom-0 mb-n4 me-n4">
                                    <i class="fa-solid fa-music text-primary" style="font-size: 7rem;"></i>
                                </div>
                                <p class="text-dark fs-7 leading-relaxed m-0 fst-italic position-relative" style="z-index: 1;">{{ $song->description }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lyrics card with Copy to Clipboard feature -->
            <div class="col-12 col-lg-7">
                <div class="card glass-card shadow-sm h-100">
                    <div class="card-header border-0 pt-6 d-flex align-items-center justify-content-between">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label fw-bold text-dark fs-4">Lyrics</span>
                            <span class="text-muted mt-1 fw-semibold fs-7">Interactive scrollable lyrics</span>
                        </h3>
                        <button class="btn btn-sm btn-light-primary fw-bold" id="copyLyricsBtn">
                            <i class="fa-solid fa-copy me-1"></i> Copy Lyrics
                        </button>
                    </div>
                    <div class="card-body">
                        @if($song->lyrics)
                        <div class="lyrics-container text-dark fs-6" id="lyricsText">
                            {{ $song->lyrics }}
                        </div>
                        @else
                        <div class="d-flex flex-column align-items-center justify-content-center py-12 text-center">
                            <i class="fa-solid fa-music fs-3hx text-muted mb-4 opacity-50"></i>
                            <h5 class="text-dark fw-bold mb-1">No lyrics available</h5>
                            <span class="text-muted fs-7">You can add lyrics by editing this song.</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        const audio = document.getElementById('audioElement');
        const playBtn = document.getElementById('playBtn');
        const playIcon = document.getElementById('playIcon');
        const progressSlider = document.getElementById('progressSlider');
        const currentTimeEl = document.getElementById('currentTime');
        const totalDurationEl = document.getElementById('totalDuration');
        const vinylDisk = document.getElementById('vinylDisk');
        const vinylTonearm = document.getElementById('vinylTonearm');

        const volumeSlider = document.getElementById('volumeSlider');
        const volumeMuteBtn = document.getElementById('volumeMuteBtn');
        const volumeIcon = document.getElementById('volumeIcon');
        const volumePercentage = document.getElementById('volumePercentage');

        const volumeIncBtn = document.getElementById('volumeIncBtn');
        const volumeDecBtn = document.getElementById('volumeDecBtn');

        let isPlaying = false;
        let isMuted = false;
        let previousVolume = 0.8;

        // Format duration seconds to mm:ss
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';
            const m = Math.floor(seconds / 60);
            const s = Math.floor(seconds % 60);
            return `${m}:${s < 10 ? '0' : ''}${s}`;
        }

        // Update Volume UI components (using robust vanilla classList)
        function updateVolumeUI(value) {
            volumeSlider.value = value;
            const percentage = Math.round(value * 100);
            volumePercentage.textContent = `${percentage}%`;

            // Re-apply standard Classes cleanly
            volumeIcon.className = 'fa-solid fs-5 text-dark';
            if (value === 0 || isMuted) {
                volumeIcon.classList.add('fa-volume-xmark');
            } else if (value < 0.5) {
                volumeIcon.classList.add('fa-volume-low');
            } else {
                volumeIcon.classList.add('fa-volume-high');
            }
        }

        // Sync volume on load
        audio.volume = 0.8;
        updateVolumeUI(0.8);

        // Toggle Playback
        function togglePlay() {
            if (isPlaying) {
                audio.pause();
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
                vinylDisk.classList.remove('spinning');
                vinylTonearm.classList.remove('playing');
                isPlaying = false;
            } else {
                audio.play().then(() => {
                    playIcon.classList.remove('fa-play');
                    playIcon.classList.add('fa-pause');
                    vinylDisk.classList.add('spinning');
                    vinylTonearm.classList.add('playing');
                    isPlaying = true;
                }).catch(err => {
                    console.error("Audio playback error: ", err);
                    toastr.error("Could not play the audio file. Please try again.");
                });
            }
        }

        playBtn.addEventListener('click', togglePlay);

        // Skip 10s backward
        $('#skipBackBtn').on('click', function() {
            audio.currentTime = Math.max(0, audio.currentTime - 10);
        });

        // Skip 10s forward
        $('#skipForwardBtn').on('click', function() {
            audio.currentTime = Math.min(audio.duration || 0, audio.currentTime + 10);
        });

        // Audio metadata loaded
        audio.addEventListener('loadedmetadata', function() {
            progressSlider.max = Math.floor(audio.duration);
            totalDurationEl.textContent = formatTime(audio.duration);
        });

        // Update progress in slider & text time
        audio.addEventListener('timeupdate', function() {
            if (!isNaN(audio.duration)) {
                progressSlider.value = Math.floor(audio.currentTime);
                currentTimeEl.textContent = formatTime(audio.currentTime);
            }
        });

        // Scrub progress
        progressSlider.addEventListener('input', function() {
            audio.currentTime = progressSlider.value;
        });

        // When song ends, reset UI
        audio.addEventListener('ended', function() {
            playIcon.classList.remove('fa-pause');
            playIcon.classList.add('fa-play');
            vinylDisk.classList.remove('spinning');
            vinylTonearm.classList.remove('playing');
            isPlaying = false;
            audio.currentTime = 0;
            progressSlider.value = 0;
            currentTimeEl.textContent = '0:00';
        });

        // Change volume via slider
        volumeSlider.addEventListener('input', function() {
            audio.volume = this.value;
            isMuted = this.value == 0;
            updateVolumeUI(this.value);
        });

        // Toggle mute button
        volumeMuteBtn.addEventListener('click', function() {
            if (isMuted) {
                audio.volume = previousVolume;
                isMuted = false;
                updateVolumeUI(previousVolume);
                toastr.success("Unmuted");
            } else {
                previousVolume = audio.volume;
                audio.volume = 0;
                isMuted = true;
                updateVolumeUI(0);
                toastr.info("Muted");
            }
        });

        // Increase Volume (+) by 10%
        volumeIncBtn.addEventListener('click', function() {
            let newVolume = Math.min(1.0, audio.volume + 0.10);
            audio.volume = newVolume;
            isMuted = false;
            updateVolumeUI(newVolume);
            toastr.success(`Volume: ${Math.round(newVolume * 100)}%`);
        });

        // Decrease Volume (-) by 10%
        volumeDecBtn.addEventListener('click', function() {
            let newVolume = Math.max(0.0, audio.volume - 0.10);
            audio.volume = newVolume;
            if (newVolume === 0) {
                isMuted = true;
            }
            updateVolumeUI(newVolume);
            toastr.info(`Volume: ${Math.round(newVolume * 100)}%`);
        });

        // Copy Lyrics feature
        $('#copyLyricsBtn').on('click', function() {
            const lyricsText = document.getElementById('lyricsText');
            if (lyricsText) {
                const text = lyricsText.innerText;
                navigator.clipboard.writeText(text).then(function() {
                    toastr.success("Lyrics copied to clipboard!");
                }).catch(function(err) {
                    toastr.error("Could not copy lyrics: " + err);
                });
            } else {
                toastr.warning("No lyrics to copy.");
            }
        });

        // In case audio source load fails, ensure slider max is set from backend if metadata fails to load
        setTimeout(() => {
            if (isNaN(audio.duration) || audio.duration === Infinity) {
                const backendDuration = parseInt("{{ $song->duration ?? 0 }}");
                if (backendDuration > 0) {
                    progressSlider.max = backendDuration;
                    totalDurationEl.textContent = formatTime(backendDuration);
                }
            }
        }, 1500);
    });
</script>
@endpush