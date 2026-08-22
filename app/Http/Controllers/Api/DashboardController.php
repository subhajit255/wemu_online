<?php

namespace App\Http\Controllers\Api;

use \Illuminate\Support\Facades\Cache;
use \Illuminate\Support\Facades\Http;
use \Illuminate\Support\Facades\Validator;
use \Illuminate\Support\Str;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\ArtistResource;
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
                $recommendedSongs = $recommendedSongs->whereIn('user_id', $artistIds)->inRandomOrder()->take(5)->get();
            } else {
                $recommendedSongs = $recommendedSongs->orderByDesc('play_count')->take(5)->get();
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
                $topGenres = \App\Models\Genre::where('is_active', 1)->inRandomOrder()->take(2)->get();
            }

            $dynamicGenreSections = [];
            foreach ($topGenres as $genre) {
                if (!$genre) continue;
                $genreSongs = Song::where('status', 1)
                    ->where('genre_id', $genre->id)
                    ->take(5)->get();

                if ($genreSongs->isNotEmpty()) {
                    $dynamicGenreSections[] = [
                        'title' => $genre->title . ' for you',
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
                    'title' => 'Sad songs',
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

            // 8. More of what you like
            $moreLike = PlayList::where('is_public', 1)->inRandomOrder()->take(5)->get();

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
                    'title_in_english' => 'More of what you like',
                    'title_in_spanish' => 'Más de lo que te gusta',
                    'type' => 'block',
                    'items' => [
                        [
                            'title_in_english' => 'Top songs Spanish',
                            'title_in_spanish' => 'Canciones populares en español',
                            'description' => 'Spanish songs collections'
                        ],
                        [
                            'title_in_english' => 'Global Songs',
                            'title_in_spanish' => 'Canciones globales',
                            'description' => 'Global songs collections'
                        ]
                    ]
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
                        $query->whereIn('user_id', $artistIds)->inRandomOrder();
                    } else {
                        $query->orderByDesc('play_count');
                    }
                    $paginator = $query->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginateSongCollection($paginator));

                case 'new-release':
                    $paginator = Song::orderBy('published_at', 'desc')->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginateSongCollection($paginator));

                case 'recents':
                    $paginator = PlayHistory::with(['song.artist', 'song.album', 'song.genre'])
                        ->where('user_id', $userId)
                        ->orderByDesc('last_played_at')
                        ->paginate($perPage);

                    $paginator->getCollection()->transform(function ($item) {
                        return $item->song;
                    });
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginateSongCollection($paginator));

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
                    $paginator = PlayList::where('is_public', 1)->inRandomOrder()->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginateSongCollection($paginator));

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
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginatePlayListResource($paginator));

                case Str::slug($country . '\'s Best'):
                    $paginator = PlayList::where('is_public', 1)
                        ->where(function ($q) use ($country) {
                            $q->where('title', 'like', '%' . $country . '%');
                            if ($country === 'India') {
                                $q->orWhere('title', 'like', '%Hindi%')
                                    ->orWhere('title', 'like', '%Bollywood%');
                            }
                        })->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginatePlayListResource($paginator));

                case 'sad-songs':
                    $paginator = PlayList::where('is_public', 1)
                        ->where(function ($q) {
                            $q->where('title', 'like', '%Sad%')
                                ->orWhere('description', 'like', '%Sad%');
                        })->paginate($perPage);
                    return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginatePlayListResource($paginator));

                default:
                    if (Str::endsWith($typeId, '-for-you')) {
                        $genreSlug = Str::replaceLast('-for-you', '', $typeId);
                        $genre = \App\Models\Genre::where('is_active', 1)->get()->first(function ($g) use ($genreSlug) {
                            return Str::slug($g->title_in_english) === $genreSlug;
                        });

                        if ($genre) {
                            $paginator = Song::where('status', 1)
                                ->where('genre_id', $genre->id)
                                ->paginate($perPage);
                            return $this->responseJson(true, 200, 'Data fetched successfully', new \App\Http\Resources\Api\PaginateSongCollection($paginator));
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
}
