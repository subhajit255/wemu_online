<?php

namespace App\Http\Controllers\Api;

use \App\Http\Resources\Api\PaginateSongCollection;
use \App\Models\Genre;
use \App\Models\SongLike;
use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Http;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Str;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\ArtistResource;
use App\Http\Resources\Api\PaginatePlayListResource;
use App\Http\Resources\Api\PlayListResource;
use App\Http\Resources\Api\SongResource;
use App\Models\ArtistFollower;
use App\Models\PlayHistory;
use App\Models\PlayList;
use App\Models\Song;
use App\Models\StreamLog;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/dashboard",
     *     summary="Get user dashboard",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Dashboard fetched successfully")
     * )
     */
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();

            // 1. Recents (Songs)
            $recentSongsList = PlayHistory::with(['song.artist', 'song.album', 'song.genre'])
                ->where('user_id', $userId)
                ->orderByDesc('last_played_at')
                ->take(5)
                ->get()
                ->pluck('song')
                ->filter();

            // Format for Recent Grid (mixed top 6: 3 songs, 3 playlists)
            $recentGrid = collect();
            foreach ($recentSongsList->take(3) as $song) {
                $recentGrid->push([
                    'id' => $song->id,
                    'title' => $song->title,
                    'image' => $song->cover_image_path,
                    'type' => 'song'
                ]);
            }
            $randomPlaylists = PlayList::where('is_public', 1)->inRandomOrder()->take(3)->get();
            foreach ($randomPlaylists as $playlist) {
                $recentGrid->push([
                    'id' => $playlist->id,
                    'title' => $playlist->title,
                    'image' => $playlist->cover_image_path,
                    'type' => 'playlist'
                ]);
            }
            $recentGrid = $recentGrid->shuffle()->values();

            // 2. Artists you like
            $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
            $preferredArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();
            $artistIds = array_unique(array_merge($followedArtistIds, $preferredArtistIds));

            $artistsYouLike = collect();
            if (!empty($artistIds)) {
                $artistsYouLike = User::whereIn('id', $artistIds)->whereHas('profile')->take(5)->get();
            } else {
                $artistsYouLike = User::whereHas('profile')->inRandomOrder()->take(5)->get(); // Fallback
            }

            // 3. Recommended for today
            $recommendedSongs = Song::where('status', 1);
            if (!empty($artistIds)) {
                $recommendedSongs = $recommendedSongs->whereIn('user_id', $artistIds)->orderByDesc('play_count')->orderByDesc('published_at')->take(5)->get();
            } else {
                $recommendedSongs = $recommendedSongs->orderByDesc('play_count')->orderByDesc('published_at')->take(5)->get();
            }
            $newRelease = Song::orderBy('published_at', 'desc')->take(5)->get();

            // 4. Popular radio (Taking top artists based on followers or just random top artists)
            $popularRadio = User::whereHas('profile')->withCount('followers')->orderByDesc('followers_count')->take(5)->get();

            // 5. Your top mixes (Fetching some public playlists)
            $topMixes = PlayList::where('is_public', 1)->inRandomOrder()->take(5)->get();

            // 6. Dynamic Spotify-like Genre Mixes (or Fallback to Sad Songs)
            $topGenres = collect();
            if ($recentSongsList->isNotEmpty()) {
                $topGenres = $recentSongsList->map(function ($song) {
                    return $song->genre;
                })->filter()->groupBy('id')->map(function ($group) {
                    return [
                        'genre' => $group->first(),
                        'count' => $group->count()
                    ];
                })->sortByDesc('count')->take(3)->pluck('genre');
            }
            if ($topGenres->isEmpty()) {
                $topGenres = Genre::where('is_active', 1)
                    ->whereHas('songs', function ($q) {
                        $q->where('status', 1);
                    })
                    ->withCount(['songs' => function ($q) {
                        $q->where('status', 1);
                    }])
                    ->orderByDesc('songs_count')
                    ->take(2)
                    ->get();
            }

            $dynamicGenreSections = [];
            foreach ($topGenres as $genre) {
                if (!$genre) continue;
                $genreSongs = $this->getGenreForYouSongsQuery($genre->id, $userId ?? 0, $artistIds)
                    ->take(5)
                    ->get();
                if ($genreSongs->isNotEmpty()) {
                    $dynamicGenreSections[] = [
                        'title_in_english' => $genre->title . ' for you',
                        'title_in_spanish' => $genre->title . ' para ti',
                        'type' => 'song',
                        'items' => SongResource::collection($genreSongs)
                    ];
                }
            }

            // Fallback if no dynamic genres were found (e.g. no playlists matching genres)
            if (empty($dynamicGenreSections)) {
                $sadSongs = PlayList::where('is_public', 1)
                    ->where(function ($q) {
                        $q->where('title', 'like', '%Sad%')
                            ->orWhere('description', 'like', '%Sad%');
                    })->take(5)->get();
                $dynamicGenreSections[] = [
                    'title_in_english' => 'Sad songs',
                    'title_in_spanish' => 'Canciones tristes',
                    'type' => 'playlist',
                    'items' => PlayListResource::collection($sadSongs)
                ];
            }

            // 7. Location's Best
            $ip = $request->header('x-forwarded-for') ?? $request->header('ip') ?? $request->ip();
            if (is_string($ip) && str_contains($ip, ',')) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip ?? '');

            $country = 'California';

            if ($ip && filter_var($ip, FILTER_VALIDATE_IP) && !in_array($ip, ['127.0.0.1', '::1'])) {
                try {
                    $country = Cache::remember("user_country_{$ip}", 86400, function () use ($ip) {
                        try {
                            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                            if ($response->successful()) {
                                $data = $response->json();
                                if (isset($data['status']) && $data['status'] === 'success' && !empty($data['country'])) {
                                    return $data['country'];
                                }
                            }
                        } catch (\Exception $e) {
                            // Proceed to default
                        }
                        return 'California';
                    });
                } catch (\Exception $e) {
                    // Default to California
                }
            }

            $countryBestPlaylists = PlayList::where('is_public', 1)
                ->where(function ($q) use ($country) {
                    $q->where('title', 'like', '%' . $country . '%');
                    if ($country === 'India') {
                        $q->orWhere('title', 'like', '%Hindi%')
                            ->orWhere('title', 'like', '%Bollywood%');
                    }
                })->take(5)->get();

            // 8. More of what you like / Features songs
            $moreLike = Song::where('status', 1)->orderByDesc('play_count')->orderByDesc('published_at')->take(5)->get();

            $sections = [
                [
                    'title_in_english' => 'Made for you',
                    'title_in_spanish' => 'Hecho para ti',
                    'type' => 'song',
                    'items' => SongResource::collection($recommendedSongs)
                ],
                [
                    'title_in_english' => 'New Release',
                    'title_in_spanish' => 'Lanzamiento',
                    'type' => 'song',
                    'items' => SongResource::collection($newRelease)
                ],
                [
                    'title_in_english' => 'Recents',
                    'title_in_spanish' => 'Recientes',
                    'type' => 'song',
                    'items' => SongResource::collection($recentSongsList)
                ],
                [
                    'title_in_english' => 'Artists you like',
                    'title_in_spanish' => 'Artistas que te gustan',
                    'type' => 'artist',
                    'items' => ArtistResource::collection($artistsYouLike)
                ],
                [
                    'title_in_english' => 'Features songs',
                    'title_in_spanish' => 'Canciones destacadas',
                    'type' => 'song',
                    'items' => SongResource::collection($moreLike)
                ],
                [
                    'title_in_english' => 'Popular radio',
                    'title_in_spanish' => 'Radio popular',
                    'type' => 'radio',
                    'items' => ArtistResource::collection($popularRadio)
                ],
                [
                    'title_in_english' => 'Your top mixes',
                    'title_in_spanish' => 'Tus mezclas principales',
                    'type' => 'playlist',
                    'items' => PlayListResource::collection($topMixes)
                ]
            ];

            foreach ($dynamicGenreSections as $dynamicSection) {
                $sections[] = $dynamicSection;
            }

            $sections[] = [
                'title_in_english' => $country . '\'s Best',
                'title_in_spanish' => $country . '\'s Mejores',
                'type' => 'playlist',
                'items' => PlayListResource::collection($countryBestPlaylists)
            ];

            // Filter out empty sections for new users and clean up display
            $sections = array_values(array_filter($sections, function ($section) {
                return isset($section['items']) && count($section['items']) > 0;
            }));

            foreach ($sections as &$section) {
                $section['type_id'] = Str::slug($section['title_in_english']);
                $section['is_seeall'] = isset($section['items']) && count($section['items']) >= 5;
            }
            unset($section); // break the reference with the last element
            $data = [
                'sections' => $sections
            ];

            return $this->responseJson(true, 200, 'Dashboard data fetched successfully', $data);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/dashboard/section-details",
     *     summary="Get section details",
     *     tags={"Dashboard"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="type_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Section details fetched successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function sectionDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type_id' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
            }

            $typeId = $request->type_id;
            $userId = auth()->id() ?? 0;
            $perPage = $request->per_page ?? 15;

            $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
            $preferredArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();
            $artistIds = array_unique(array_merge($followedArtistIds, $preferredArtistIds));

            $ip = $request->header('x-forwarded-for') ?? $request->header('ip') ?? $request->ip();
            if (is_string($ip) && str_contains($ip, ',')) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip ?? '');
            $country = 'California';
            if ($ip && filter_var($ip, FILTER_VALIDATE_IP) && !in_array($ip, ['127.0.0.1', '::1'])) {
                try {
                    $country = Cache::remember("user_country_{$ip}", 86400, function () use ($ip) {
                        try {
                            $response = Http::timeout(2)->get("http://ip-api.com/json/{$ip}");
                            if ($response->successful()) {
                                $data = $response->json();
                                if (isset($data['status']) && $data['status'] === 'success' && !empty($data['country'])) {
                                    return $data['country'];
                                }
                            }
                        } catch (\Exception $e) {
                        }
                        return 'California';
                    });
                } catch (\Exception $e) {
                }
            }

            switch ($typeId) {
                case 'made-for-you':
                    $query = Song::where('status', 1);
                    if (!empty($artistIds)) {
                        $query->whereIn('user_id', $artistIds)->orderByDesc('play_count')->orderByDesc('published_at');
                    } else {
                        $query->orderByDesc('play_count')->orderByDesc('published_at');
                    }
                    $paginator = $query->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginateSongCollection($paginator));

                case 'new-release':
                    $paginator = Song::where("status", 1)->orderBy('published_at', 'desc')->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginateSongCollection($paginator));

                case 'recents':
                    $paginator = PlayHistory::with(['song.artist', 'song.album', 'song.genre'])
                        ->where('user_id', $userId)
                        ->orderByDesc('last_played_at')
                        ->paginate($perPage);

                    $paginator->getCollection()->transform(function ($item) {
                        return $item->song;
                    });
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginateSongCollection($paginator));

                case 'artists-you-like':
                    if (!empty($artistIds)) {
                        $paginator = User::whereIn('id', $artistIds)->whereHas('profile')->paginate($perPage);
                    } else {
                        $paginator = User::whereHas('profile')->inRandomOrder()->paginate($perPage);
                    }
                    return $this->responseJson(true, 200, 'Data fetched successfully', [
                        'result' => ArtistResource::collection($paginator),
                        'pagination' => [
                            'total' => $paginator->total(),
                            'count' => $paginator->count(),
                            'per_page' => $paginator->perPage(),
                            'current_page' => $paginator->currentPage(),
                            'total_pages' => $paginator->lastPage()
                        ]
                    ]);

                case 'features-songs':
                    $paginator = Song::where('status', 1)->orderByDesc('play_count')->orderByDesc('published_at')->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginateSongCollection($paginator));

                case 'popular-radio':
                    $paginator = User::whereHas('profile')->withCount('followers')->orderByDesc('followers_count')->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', [
                        'result' => ArtistResource::collection($paginator),
                        'pagination' => [
                            'total' => $paginator->total(),
                            'count' => $paginator->count(),
                            'per_page' => $paginator->perPage(),
                            'current_page' => $paginator->currentPage(),
                            'total_pages' => $paginator->lastPage()
                        ]
                    ]);

                case 'your-top-mixes':
                    $paginator = PlayList::where('is_public', 1)->inRandomOrder()->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginatePlayListResource($paginator));

                case Str::slug($country . '\'s Best'):
                    $paginator = PlayList::where('is_public', 1)
                        ->where(function ($q) use ($country) {
                            $q->where('title', 'like', '%' . $country . '%');
                            if ($country === 'India') {
                                $q->orWhere('title', 'like', '%Hindi%')
                                    ->orWhere('title', 'like', '%Bollywood%');
                            }
                        })->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginatePlayListResource($paginator));

                case 'sad-songs':
                    $paginator = PlayList::where('is_public', 1)
                        ->where(function ($q) {
                            $q->where('title', 'like', '%Sad%')
                                ->orWhere('description', 'like', '%Sad%');
                        })->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new PaginatePlayListResource($paginator));

                default:
                    if (Str::endsWith($typeId, '-for-you')) {
                        $genreSlug = Str::replaceLast('-for-you', '', $typeId);
                        $genre = Genre::where('is_active', 1)->get()->first(function ($g) use ($genreSlug) {
                            return Str::slug($g->title) === $genreSlug;
                        });

                        if ($genre) {
                            // Uses exact same query and ranking as Dashboard, with pagination
                            $paginator = $this->getGenreForYouSongsQuery($genre->id, $userId, $artistIds)
                                ->paginate($perPage);

                            return $this->responseJson(true, 200, 'Data fetched successfully', new PaginateSongCollection($paginator));
                        }
                    }
                    break;
            }

            return $this->responseJson(false, 404, 'Section not found', (object)[]);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }

    /**
     * Get Spotify-like personalized query for dynamic genre 'For You' section.
     * Handles both active users (with history/likes) and cold-start new users.
     *
     * @param int $genreId
     * @param int $userId
     * @param array $artistIds
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getGenreForYouSongsQuery(int $genreId, int $userId = 0, array $artistIds = [])
    {
        $query = Song::with(['artist', 'album', 'genre'])
            ->where('status', 1)
            ->where('genre_id', $genreId);

        if ($userId > 0) {
            // 1. Songs liked by this user in this genre
            $likedSongIds = SongLike::where('user_id', $userId)
                ->whereHas('song', fn($q) => $q->where('genre_id', $genreId))
                ->pluck('song_id')
                ->toArray();

            // 2. Songs played by this user in this genre
            $playedSongIds = PlayHistory::where('user_id', $userId)
                ->whereHas('song', fn($q) => $q->where('genre_id', $genreId))
                ->pluck('song_id')
                ->toArray();

            // 3. Songs by artists followed/preferred by this user in this genre
            $genreArtistIds = !empty($artistIds)
                ? Song::where('genre_id', $genreId)->whereIn('user_id', $artistIds)->pluck('user_id')->unique()->toArray()
                : [];

            $hasLiked = !empty($likedSongIds);
            $hasArtists = !empty($genreArtistIds);
            $hasPlayed = !empty($playedSongIds);

            // Active user branch: If the user has any interaction or preferred artists in this genre
            if ($hasLiked || $hasArtists || $hasPlayed) {
                $likedList = $hasLiked ? implode(',', array_map('intval', $likedSongIds)) : '0';
                $artistList = $hasArtists ? implode(',', array_map('intval', $genreArtistIds)) : '0';
                $playedList = $hasPlayed ? implode(',', array_map('intval', $playedSongIds)) : '0';

                // Spotify scoring:
                // +50 for user-liked tracks
                // +30 for tracks from followed/preferred artists
                // +20 for previously played tracks
                $scoreRaw = "(
                    (CASE WHEN id IN ({$likedList}) THEN 50 ELSE 0 END) +
                    (CASE WHEN user_id IN ({$artistList}) THEN 30 ELSE 0 END) +
                    (CASE WHEN id IN ({$playedList}) THEN 20 ELSE 0 END)
                )";

                return $query->orderByRaw("{$scoreRaw} DESC")
                    ->orderByDesc('play_count')
                    ->orderByDesc('likes_count')
                    ->orderByDesc('published_at')
                    ->orderByDesc('id');
            }
        }

        // Cold-start branch (new users with 0 likes/history/artists or guest users):
        // Ranks by genre's top hits, highest likes, and newest releases
        return $query->orderByDesc('play_count')
            ->orderByDesc('likes_count')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }
}
