<?php

namespace App\Http\Controllers\Api;

use \App\Http\Resources\Api\SongResource;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\PaginateSongCollection;
use App\Models\Album;
use App\Models\ArtistFollower;
use App\Models\PlayHistory;
use App\Models\PlayList;
use App\Models\Song;
use App\Models\SongLike;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PlayerController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/player/queue",
     *     summary="Get player queue with autoplay support",
     *     tags={"Player"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(name="source_type", in="query", required=true, description="Type of source. Examples: album, artist, playlist, made-for-you, artists-you-like, new-release, search, recents, liked-songs, or dynamic genres like rock-for-you", @OA\Schema(type="string")),
     *     @OA\Parameter(name="source_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="keyword", in="query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="per_page", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="last_played_song_id", in="query", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="direction", in="query", required=false, description="Direction for local queue generation: next or prev", @OA\Schema(type="string", enum={"next", "prev"})),
     *     @OA\Response(response=200, description="Player queue fetched successfully")
     * )
     */
    public function playerQueue(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'source_type' => 'required|string', // Loosened because of dynamic '*-for-you' types from dashboard
            'source_id' => 'required_if:source_type,album,artist,playlist|integer|nullable',
            'keyword' => 'required_if:source_type,search|string|nullable',
            'direction' => 'nullable|in:next,prev',
        ]);

        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
        }

        try {
            $sourceType = $request->source_type;
            $sourceId = $request->source_id;
            $perPage = $request->per_page ?? 15;
            $page = $request->page ?? 1;

            // 1. Get Base Query
            $baseQuery = Song::where('status', 1)->with(['artist', 'album', 'genre']);

            $isAutoplaySupported = true; // Most sources support fallback to autoplay

            switch ($sourceType) {
                case 'album':
                    $baseQuery->where('album_id', $sourceId);
                    break;
                case 'artist':
                case 'popular-radio':
                case 'popular_radio':
                    $baseQuery->where('user_id', $sourceId)->orderBy('published_at', 'desc');
                    break;
                case 'playlist':
                case 'your-top-mixes':
                case 'your_top_mixes':
                case 'sad-songs':
                case 'sad_songs':
                    $baseQuery->whereIn('id', function ($q) use ($sourceId) {
                        $q->select('song_id')->from('play_list_songs')->where('play_list_id', $sourceId);
                    });
                    break;
                case 'new-release':
                case 'new_release':
                    $baseQuery->orderBy('published_at', 'desc');
                    $isAutoplaySupported = false; // New releases are just the newest songs
                    break;
                case 'search':
                    $keywords = $request->keyword;
                    $baseQuery->where(function ($q) use ($keywords) {
                        $q->where('title', 'like', "%{$keywords}%")
                            ->orWhere('artist_name', 'like', "%{$keywords}%");
                    });
                    $isAutoplaySupported = false; // Usually don't autoplay after a search query ends
                    break;
                case 'recents':
                case 'recently_played':
                    if (auth('api')->check()) {
                        $songIds = PlayHistory::where('user_id', auth('api')->id())
                            ->orderByDesc('last_played_at')
                            ->pluck('song_id');
                        if ($songIds->isNotEmpty()) {
                            $idsStr = implode(',', $songIds->toArray());
                            $baseQuery->whereIn('id', $songIds)
                                ->orderByRaw("FIELD(id, {$idsStr})");
                        } else {
                            $baseQuery->whereRaw('1 = 0');
                        }
                    } else {
                        $baseQuery->whereRaw('1 = 0');
                    }
                    $isAutoplaySupported = false;
                    break;
                case 'liked_songs':
                case 'liked-songs':
                    if (auth('api')->check()) {
                        $songIds = SongLike::where('user_id', auth('api')->id())
                            ->orderByDesc('created_at')
                            ->pluck('song_id');
                        if ($songIds->isNotEmpty()) {
                            $idsStr = implode(',', $songIds->toArray());
                            $baseQuery->whereIn('id', $songIds)
                                ->orderByRaw("FIELD(id, {$idsStr})");
                        } else {
                            $baseQuery->whereRaw('1 = 0');
                        }
                    } else {
                        $baseQuery->whereRaw('1 = 0');
                    }
                    break;
                case 'made-for-you':
                case 'made_for_you':
                    if (auth('api')->check()) {
                        $userId = auth('api')->id();
                        $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
                        $preferredArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();
                        $artistIds = array_unique(array_merge($followedArtistIds, $preferredArtistIds));

                        if (!empty($artistIds)) {
                            $baseQuery->whereIn('user_id', $artistIds)->inRandomOrder();
                        } else {
                            $baseQuery->orderByDesc('play_count');
                        }
                    } else {
                        $baseQuery->orderByDesc('play_count');
                    }
                    break;
                case 'artists-you-like':
                case 'artists_you_like':
                    if ($sourceId) {
                        $baseQuery->where('user_id', $sourceId)->orderBy('published_at', 'desc');
                    } else {
                        if (auth('api')->check()) {
                            $userId = auth('api')->id();
                            $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
                            $preferredArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();
                            $artistIds = array_unique(array_merge($followedArtistIds, $preferredArtistIds));

                            if (!empty($artistIds)) {
                                $baseQuery->whereIn('user_id', $artistIds)->inRandomOrder();
                            } else {
                                $baseQuery->orderByDesc('play_count');
                            }
                        } else {
                            $baseQuery->orderByDesc('play_count');
                        }
                    }
                    break;
                case 'features-songs':
                case 'features_songs':
                    $baseQuery->inRandomOrder();
                    break;
                default:
                    // Support for dynamic Dashboard genres (e.g. 'rock-for-you')
                    if (\Illuminate\Support\Str::endsWith($sourceType, '-for-you')) {
                        $genreSlug = \Illuminate\Support\Str::replaceLast('-for-you', '', $sourceType);
                        $genre = \App\Models\Genre::where('is_active', 1)->get()->first(function ($g) use ($genreSlug) {
                            return \Illuminate\Support\Str::slug($g->title_in_english) === $genreSlug;
                        });

                        if ($genre) {
                            $baseQuery->where('genre_id', $genre->id);
                        } else {
                            $baseQuery->whereRaw('1 = 0'); // Empty if genre not found
                        }
                    } else {
                        // Fallback to empty if unsupported
                        $baseQuery->whereRaw('1 = 0');
                    }
                    break;
            }

            // Pagination setup
            $totalBaseRecords = $baseQuery->count();

            // Standard Pagination
            $songs = $baseQuery->paginate($perPage, ['*'], 'page', $page);
            $items = $songs->items();
            $fetchedCount = count($items);

            // Add 'is_autoplay' flag to base songs
            foreach ($items as $item) {
                $item->is_autoplay = false;
            }

            // Check if we need to append recommended songs (Autoplay)
            if ($isAutoplaySupported && $fetchedCount < $perPage) {
                $needed = $perPage - $fetchedCount;

                // 2. Recommendation Logic (Autoplay)
                $recommendQuery = Song::where('status', 1)->with(['artist', 'album', 'genre']);

                // Exclude songs we already fetched in base query to avoid duplicates
                $excludedSongIds = collect($items)->pluck('id')->toArray();

                // Context-based recommendations
                if ($sourceType === 'album') {
                    $album = Album::find($sourceId);
                    if ($album) {
                        $recommendQuery->where(function ($q) use ($album) {
                            $q->where('genre_id', $album->genre_id)
                                ->orWhere('user_id', $album->user_id);
                        });
                    }
                } elseif ($sourceType === 'artist') {
                    $recommendQuery->where('user_id', $sourceId);
                } elseif ($request->last_played_song_id) {
                    $lastSong = Song::find($request->last_played_song_id);
                    if ($lastSong) {
                        $recommendQuery->where(function ($q) use ($lastSong) {
                            $q->where('genre_id', $lastSong->genre_id)
                                ->orWhere('user_id', $lastSong->user_id);
                        });
                    }
                } else {
                    // General recommendation based on user taste
                    if (auth('api')->check()) {
                        $userId = auth('api')->id();
                        $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
                        $chosenArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();

                        $likedSongIds = DB::table('song_likes')->where('user_id', $userId)->pluck('song_id');
                        $likedSongArtistIds = Song::whereIn('id', $likedSongIds)->pluck('user_id')->toArray();

                        $artistIds = array_unique(array_merge($followedArtistIds, $chosenArtistIds, $likedSongArtistIds));
                        if (!empty($artistIds)) {
                            $artistIdsStr = implode(',', $artistIds);
                            $recommendQuery->orderByRaw("user_id IN ($artistIdsStr) DESC");
                        }
                    }
                }

                if (!empty($excludedSongIds)) {
                    $recommendQuery->whereNotIn('id', $excludedSongIds);
                }

                $recommendQuery->inRandomOrder(); // Add randomness to Autoplay

                // We use limit offset for the recommendation part
                $recommendOffset = max(0, ($page - 1) * $perPage - $totalBaseRecords + $fetchedCount);
                $recommendedSongs = $recommendQuery->offset($recommendOffset)->limit($needed)->get();

                foreach ($recommendedSongs as $rSong) {
                    $rSong->is_autoplay = true;
                    $items[] = $rSong; // Append to items
                }

                // Adjust paginator
                $songs->setCollection(collect($items));
            }

            $responseArray = (new PaginateSongCollection($songs))->toArray($request);
            $localQueue = $items;

            if ($request->direction && $request->last_played_song_id) {
                $foundIndex = -1;
                foreach ($items as $index => $item) {
                    if ($item->id == $request->last_played_song_id) {
                        $foundIndex = $index;
                        break;
                    }
                }

                if ($foundIndex !== -1) {
                    $before = array_slice($items, 0, $foundIndex);
                    $after = array_slice($items, $foundIndex + 1);
                    $current = [$items[$foundIndex]];

                    if ($request->direction == 'next') {
                        $localQueue = array_merge($after, $before, $current);
                    } elseif ($request->direction == 'prev') {
                        $localQueue = array_merge(array_reverse($before), array_reverse($after), $current);
                    }
                }
            }

            $responseArray['local_queue'] = SongResource::collection(collect($localQueue));

            return $this->responseJson(true, 200, 'Player queue fetched successfully', $responseArray);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
}
