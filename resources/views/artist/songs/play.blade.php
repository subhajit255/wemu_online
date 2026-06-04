<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $song->title }} - Play Screen</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ asset('assets/css/cdn/bootstrap.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            background-color: #121212;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            overflow: hidden;
            position: relative;
        }
        
        .bg-blur {
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background-size: cover;
            background-position: center;
            filter: blur(80px) brightness(0.2);
            z-index: -1;
            opacity: 0.8;
        }

        /* Top Back Button */
        .top-bar {
            position: absolute;
            top: 20px;
            left: 30px;
            z-index: 100;
        }

        .back-btn {
            color: #b3b3b3;
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: color 0.2s;
        }

        .back-btn:hover {
            color: #ffffff;
        }

        /* Main Container */
        .play-container {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 40px;
            gap: 60px;
        }

        /* Left Side: Song Info */
        .song-info {
            flex: 1;
            max-width: 500px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .album-title {
            font-size: 14px;
            font-weight: 600;
            color: #b3b3b3;
            margin-bottom: 20px;
        }

        .song-title {
            font-size: 48px;
            font-weight: 800;
            margin: 0 0 10px 0;
            letter-spacing: -1px;
            line-height: 1.1;
        }

        .artist-name {
            font-size: 16px;
            color: #b3b3b3;
            margin: 0 0 30px 0;
        }

        .playback-ui {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-play-pause {
            background: #1ed760;
            color: #000000;
            border: none;
            border-radius: 50%;
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s, box-shadow 0.2s;
            box-shadow: 0 8px 24px rgba(30,215,96,0.3);
        }

        .btn-play-pause:hover {
            transform: scale(1.05);
            background: #1fdf64;
            box-shadow: 0 12px 30px rgba(30,215,96,0.5);
        }

        .thumbnail {
            width: 50px;
            height: 50px;
            border-radius: 4px;
            object-fit: cover;
        }

        .progress-container {
            flex: 1;
            height: 6px;
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
            position: relative;
            cursor: pointer;
            transition: height 0.2s;
        }
        
        .progress-container:hover {
            height: 8px;
        }

        .progress-bar-fill {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: #ffffff;
            border-radius: 3px;
            width: 0%; 
            transition: width 0.1s linear;
        }
        
        .progress-container:hover .progress-bar-fill {
            background: #1ed760;
        }

        /* Artist Profile Area */
        .artist-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .artist-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .artist-details {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .artist-name-sm {
            font-size: 14px;
            font-weight: 600;
        }

        .listener-count {
            font-size: 13px;
            color: #b3b3b3;
        }

        .btn-follow {
            background: transparent;
            border: 1px solid #727272;
            color: #ffffff;
            border-radius: 20px;
            padding: 4px 15px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-follow:hover {
            border-color: #ffffff;
            transform: scale(1.05);
        }
        
        .btn-follow.active {
            background: #ffffff;
            color: #000000;
            border-color: #ffffff;
        }

        /* Tags */
        .tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .genre-tag {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 500;
        }

        /* Lyrics Scroll Box */
        .lyrics-scroll-box {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 20px;
            height: 300px;
            overflow-y: auto;
            font-size: 22px;
            font-weight: 700;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.4);
            position: relative;
            scroll-behavior: smooth;
        }

        .lyrics-scroll-box::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }

        .lyric-line {
            transition: color 0.3s ease, font-size 0.3s ease;
            margin-bottom: 12px;
            cursor: pointer;
        }

        .lyric-line:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .lyric-line.active {
            color: #ffffff;
            font-size: 24px;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }

        .static-lyrics {
            font-size: 14px;
            font-weight: normal;
            color: #d1d1d1;
        }

        /* Right Side: Canvas Media */
        .canvas-area {
            position: relative;
            height: 80vh;
            width: 45vh; /* Aspect ratio roughly 9:16 */
            max-width: 400px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            background: #282828;
        }

        .canvas-media {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Far Right Sidebar Icons */
        .action-sidebar {
            display: flex;
            flex-direction: column;
            gap: 25px;
            margin-left: 20px;
            color: #b3b3b3;
        }

        .action-icon {
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s, transform 0.2s;
        }

        .action-icon:hover {
            color: #ffffff;
            transform: scale(1.1);
        }
        
        .action-icon.active.fa-heart {
            color: #1ed760;
        }

        .icon-success {
            color: #1ed760;
        }
        
        /* Custom Toast Notification */
        .custom-toast {
            position: fixed;
            bottom: -60px;
            left: 50%;
            transform: translateX(-50%);
            background: #1ed760;
            color: #000;
            padding: 12px 24px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            transition: bottom 0.3s ease;
            z-index: 1000;
        }
        .custom-toast.show {
            bottom: 40px;
        }

        /* Audio element hidden */
        audio {
            display: none;
        }

    </style>
</head>
<body>
    <div class="bg-blur" style="background-image: url('{{ $song->cover_image_path }}');"></div>
    <div id="toastMessage" class="custom-toast">Action successful!</div>

    <div class="top-bar">
        <a href="{{ route('artist.songs.show', $song->id) }}" class="back-btn">
            <i class="fa-solid fa-chevron-left"></i> Back to Details
        </a>
    </div>

    @php
        $hasRealAudio = !empty($song->audio_file) && $song->audio_file_path !== '#';
        $audioSrc = $hasRealAudio ? $song->audio_file_path : 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3';
    @endphp
    
    <audio id="audioPlayer" src="{{ $audioSrc }}" autoplay loop></audio>

    <div class="play-container">
        
        <!-- Left Side: Info -->
        <div class="song-info">
            <div class="album-title">{{ $song->album ? $song->album->title : 'Single' }}</div>
            <h1 class="song-title">{{ $song->title }}</h1>
            <h2 class="artist-name">
                {{ $song->artist_name ?? 'Unknown Artist' }}
                @if($song->featured_artists)
                , {{ $song->featured_artists }}
                @endif
            </h2>

            <!-- Mock Playback UI -->
            <div class="playback-ui">
                <button id="togglePlayBtn" class="btn-play-pause">
                    <i class="fa-solid fa-pause" id="playIcon"></i>
                </button>
                <img src="{{ $song->cover_image_path }}" class="thumbnail" alt="Thumbnail">
                <div class="progress-container" id="progressContainer">
                    <div class="progress-bar-fill" id="progressBar"></div>
                </div>
            </div>

            <!-- Artist Profile -->
            <div class="artist-profile">
                @php
                    $artistAvatar = $song->user && $song->user->image_path ? $song->user->image_path : asset('assets/media/avatars/blank.png');
                @endphp
                <img src="{{ $artistAvatar }}" class="artist-avatar" alt="Artist">
                <div class="artist-details">
                    <span class="artist-name-sm">{{ $song->artist_name ?? 'Unknown Artist' }}</span>
                    <span class="listener-count">{{ number_format($song->play_count ?? 0) }} Streams</span>
                    <button class="btn-follow" id="btnFollow">Follow</button>
                </div>
            </div>

            <!-- Tags -->
            <div class="tags-container" style="margin-bottom: 25px;">
                @if($song->genre)
                <span class="genre-tag">#{{ strtolower(str_replace(' ', '', $song->genre->title)) }}</span>
                @endif
                <span class="genre-tag">#music</span>
                <span class="genre-tag">#wemu</span>
            </div>

            <!-- Lyrics Section -->
            <div id="lyricsContainer" class="lyrics-scroll-box">
                @if($song->lyrics)
                    <div class="static-lyrics">
                        <h4 style="margin-top: 0; color: #fff; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">Lyrics</h4>
                        {!! nl2br(e($song->lyrics)) !!}
                    </div>
                @else
                    <div class="static-lyrics">Fetching synced lyrics...</div>
                @endif
            </div>
        </div>

        <!-- Right Side: Canvas Media -->
        <div class="canvas-area">
            @php
                $bgFile = $song->background_file_path;
                $isVideo = false;
                if ($song->background) {
                    $ext = strtolower(pathinfo($song->background, PATHINFO_EXTENSION));
                    if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
                        $isVideo = true;
                    }
                }
            @endphp
            @if($isVideo)
                <video src="{{ $bgFile }}" class="canvas-media" autoplay loop muted playsinline id="bgVideo"></video>
            @else
                <img src="{{ $bgFile }}" class="canvas-media" alt="Canvas Fallback">
            @endif
        </div>

        <!-- Far Right Actions -->
        <div class="action-sidebar">
            <i class="fa-solid fa-heart action-icon" id="btnLike" title="Like"></i>
            <i class="fa-solid fa-share-from-square action-icon" id="btnShare" title="Share"></i>
            <i class="fa-solid fa-list action-icon" id="btnPlaylist" title="Add to Playlist"></i>
            <i class="fa-solid fa-circle-check action-icon icon-success" title="Verified"></i>
        </div>

    </div>

    <script src="{{ asset('assets/js/custom_js/cdn/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const audio = document.getElementById('audioPlayer');
            const bgVideo = document.getElementById('bgVideo');
            const progressBar = document.getElementById('progressBar');
            const progressContainer = document.getElementById('progressContainer');
            const togglePlayBtn = document.getElementById('togglePlayBtn');
            const playIcon = document.getElementById('playIcon');
            const lyricsContainer = document.getElementById('lyricsContainer');

            let isPlaying = true;
            let syncedLyricsData = [];

            // Toggle Play/Pause
            togglePlayBtn.addEventListener('click', function() {
                if (audio.paused) {
                    audio.play();
                } else {
                    audio.pause();
                }
            });

            // Ensure UI syncs with audio state
            audio.addEventListener('play', () => {
                playIcon.classList.remove('fa-play');
                playIcon.classList.add('fa-pause');
                if(bgVideo) bgVideo.play();
            });

            audio.addEventListener('pause', () => {
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
                if(bgVideo) bgVideo.pause();
            });

            // Play audio on load if allowed
            audio.play().catch(e => {
                console.log("Autoplay prevented:", e);
                playIcon.classList.remove('fa-pause');
                playIcon.classList.add('fa-play');
            });

            // Update Progress Bar & Lyrics
            audio.addEventListener('timeupdate', function() {
                if(audio.duration) {
                    const percentage = (audio.currentTime / audio.duration) * 100;
                    progressBar.style.width = percentage + '%';
                }

                // Sync Lyrics
                if (syncedLyricsData.length > 0) {
                    let activeIndex = -1;
                    const currentTime = audio.currentTime;

                    for (let i = 0; i < syncedLyricsData.length; i++) {
                        if (currentTime >= syncedLyricsData[i].time) {
                            activeIndex = i;
                        } else {
                            break;
                        }
                    }

                    if (activeIndex !== -1) {
                        const lines = lyricsContainer.querySelectorAll('.lyric-line');
                        lines.forEach((line, index) => {
                            if (index === activeIndex) {
                                if (!line.classList.contains('active')) {
                                    line.classList.add('active');
                                    // Scroll to active line
                                    const containerHalfHeight = lyricsContainer.clientHeight / 2;
                                    const lineOffsetTop = line.offsetTop - lyricsContainer.offsetTop;
                                    lyricsContainer.scrollTo({
                                        top: lineOffsetTop - containerHalfHeight + 20, // adjust slightly
                                        behavior: 'smooth'
                                    });
                                }
                            } else {
                                line.classList.remove('active');
                            }
                        });
                    }
                }
            });

            progressContainer.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const pos = (e.clientX - rect.left) / rect.width;
                audio.currentTime = pos * audio.duration;
                if(bgVideo) bgVideo.currentTime = audio.currentTime % bgVideo.duration;
            });

            // Fetch Synced Lyrics
            const artistName = "{{ addslashes($song->artist_name) }}";
            const trackName = "{{ addslashes($song->title) }}";
            
            if (artistName && trackName) {
                fetch(`https://lrclib.net/api/get?artist_name=${encodeURIComponent(artistName)}&track_name=${encodeURIComponent(trackName)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.syncedLyrics) {
                            parseSyncedLyrics(data.syncedLyrics);
                            renderSyncedLyrics();
                        } else {
                            console.log("No synced lyrics found on LRCLIB. Using static.");
                            // If no static lyrics existed, update the message
                            if(!lyricsContainer.querySelector('.static-lyrics').innerHTML.includes('Lyrics')) {
                                lyricsContainer.innerHTML = '<div class="static-lyrics">No synced lyrics found.</div>';
                            }
                        }
                    })
                    .catch(err => {
                        console.error("Failed to fetch lyrics:", err);
                        if(!lyricsContainer.querySelector('.static-lyrics').innerHTML.includes('Lyrics')) {
                            lyricsContainer.innerHTML = '<div class="static-lyrics">Failed to fetch lyrics.</div>';
                        }
                    });
            } else {
                if(!lyricsContainer.querySelector('.static-lyrics').innerHTML.includes('Lyrics')) {
                    lyricsContainer.innerHTML = '<div class="static-lyrics">Artist or track name missing.</div>';
                }
            }

            function parseSyncedLyrics(lrcString) {
                const lines = lrcString.split('\n');
                const regex = /\[(\d+):(\d+\.\d+)\](.*)/;

                lines.forEach(line => {
                    const match = line.match(regex);
                    if (match) {
                        const minutes = parseInt(match[1]);
                        const seconds = parseFloat(match[2]);
                        const text = match[3].trim();
                        const time = minutes * 60 + seconds;
                        if (text !== '') {
                            syncedLyricsData.push({ time, text });
                        }
                    }
                });
            }

            function renderSyncedLyrics() {
                lyricsContainer.innerHTML = '';
                // Add padding to top
                const topPadding = document.createElement('div');
                topPadding.style.height = '100px';
                lyricsContainer.appendChild(topPadding);

                syncedLyricsData.forEach((lyric, index) => {
                    const lineDiv = document.createElement('div');
                    lineDiv.className = 'lyric-line';
                    lineDiv.textContent = lyric.text;
                    lineDiv.dataset.time = lyric.time;
                    lineDiv.addEventListener('click', () => {
                        audio.currentTime = lyric.time;
                        if(bgVideo) bgVideo.currentTime = audio.currentTime % bgVideo.duration;
                        if(audio.paused) audio.play();
                    });
                    lyricsContainer.appendChild(lineDiv);
                });
                
                // Add padding to bottom so last lyric can scroll up
                const paddingDiv = document.createElement('div');
                paddingDiv.style.height = '150px';
                lyricsContainer.appendChild(paddingDiv);
            }

            // Interactive Buttons Mock Functionality
            const toastMessage = document.getElementById('toastMessage');
            let toastTimeout;
            
            function showToast(message) {
                toastMessage.textContent = message;
                toastMessage.classList.add('show');
                clearTimeout(toastTimeout);
                toastTimeout = setTimeout(() => {
                    toastMessage.classList.remove('show');
                }, 3000);
            }

            const btnFollow = document.getElementById('btnFollow');
            if(btnFollow) {
                btnFollow.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if(this.classList.contains('active')) {
                        this.textContent = 'Following';
                        showToast('Following ' + "{{ addslashes($song->artist_name ?? 'Artist') }}");
                    } else {
                        this.textContent = 'Follow';
                        showToast('Unfollowed ' + "{{ addslashes($song->artist_name ?? 'Artist') }}");
                    }
                });
            }

            const btnLike = document.getElementById('btnLike');
            if(btnLike) {
                btnLike.addEventListener('click', function() {
                    this.classList.toggle('active');
                    if(this.classList.contains('active')) {
                        showToast('Added to your Liked Songs');
                    } else {
                        showToast('Removed from your Liked Songs');
                    }
                });
            }

            const btnShare = document.getElementById('btnShare');
            if(btnShare) {
                btnShare.addEventListener('click', function() {
                    showToast('Link copied to clipboard');
                });
            }

            const btnPlaylist = document.getElementById('btnPlaylist');
            if(btnPlaylist) {
                btnPlaylist.addEventListener('click', function() {
                    showToast('Added to Playlist');
                });
            }
        });
    </script>
</body>
</html>
