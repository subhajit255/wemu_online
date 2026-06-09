<?php

namespace App\Http\Controllers\Api;

use App\Models\PlayList;
use App\Models\Album;
use App\Models\Song;
use App\Models\User;
use App\Models\SearchHistory;
use App\Models\ArtistFollower;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Traits\UploadAble;
use App\Http\Resources\Api\PaginatePlayListResource;
use App\Http\Resources\Api\PlayListResource;
use App\Http\Resources\Api\SongResource;
use App\Http\Resources\Api\AlbumResource;
use App\Http\Resources\Api\ArtistResource;
use App\Http\Resources\Api\PaginateSongCollection;
use App\Models\StreamLog;

class SongController extends BaseController
{
    use UploadAble;

    public function createUpdatePlaylist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'playlist_id' => 'nullable|integer|exists:play_lists,id',
            'title' => 'required_without:playlist_id|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        $playlistId = $request->playlist_id;

        DB::beginTransaction();
        try {
            $playlist = null;
            $message = 'Playlist created successfully';
            $postData = [
                'user_id' => auth()->user()->id,
            ];

            if (!empty($playlistId)) {
                $playlist = PlayList::where('id', $playlistId)
                    ->where('user_id', auth()->user()->id)
                    ->first();

                if (!$playlist) {
                    return $this->responseJson(false, 403, 'Unauthorized or Playlist not found', []);
                }
                $message = 'Playlist updated successfully';
            }

            if ($request->has('title')) {
                $postData['title'] = $request->title;
            }
            if ($request->has('description')) {
                $postData['description'] = $request->description;
            }
            if ($request->has('is_public')) {
                $postData['is_public'] = (bool)$request->is_public;
            } else if (empty($playlistId)) {
                $postData['is_public'] = 0;
            }

            // Handle cover image
            if ($request->hasFile('cover_image')) {
                $image = $request->file('cover_image');
                $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                $uploaded = $this->uploadOne($image, config('constants.SITE_PLAYLIST_COVER_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                if ($uploaded) {
                    // Delete old cover image if updating
                    if ($playlist && $playlist->cover_image) {
                        $this->deleteOne(config('constants.SITE_PLAYLIST_COVER_IMAGE_UPLOAD_PATH') . '/' . $playlist->cover_image);
                    }
                    $postData['cover_image'] = $uploaded;
                }
            }

            if (!empty($playlistId)) {
                $playlist->update($postData);
            } else {
                $playlist = PlayList::create($postData);
            }

            DB::commit();
            return $this->responseJson(true, 200, $message, $playlist);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function myPlayLists(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 15;
            $playlists = PlayList::where('user_id', auth()->user()->id)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            return $this->responseJson(true, 200, 'Playlists fetched successfully', new PaginatePlayListResource($playlists));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function songAddRemovePlayList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'playlist_id' => 'required|integer|exists:play_lists,id',
            'song_id' => 'required|integer|exists:songs,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }

        DB::beginTransaction();
        try {
            $playlist = PlayList::where('id', $request->playlist_id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if (!$playlist) {
                return $this->responseJson(false, 403, 'Unauthorized or Playlist not found', []);
            }

            $exists = $playlist->songs()->where('song_id', $request->song_id)->exists();

            if ($exists) {
                $playlist->songs()->detach($request->song_id);
                $playlist->decrement('songs_count');
                $message = 'Song removed from playlist successfully';
                $action = 'removed';
            } else {
                $playlist->songs()->attach($request->song_id);
                $playlist->increment('songs_count');
                $message = 'Song added to playlist successfully';
                $action = 'added';
            }

            DB::commit();
            return $this->responseJson(true, 200, $message, ['action' => $action]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function playListDetails($playlistId, Request $request)
    {
        $validator = Validator::make(
            ['playlist_id' => $playlistId],
            [
                'playlist_id' => 'required|integer|exists:play_lists,id',
            ]
        );
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
        }
        try {
            $perPage = $request->per_page ?? 15;
            $playlist = PlayList::find($playlistId);
            if (!$playlist) {
                return $this->responseJson(false, 404, 'Playlist not found', (object)[]);
            }

            // Paginate songs relationship
            $songs = $playlist->songs()->paginate($perPage);

            // Set songs_paginated attribute on model so resource can conditionally load it
            $playlist->songs_paginated = new PaginateSongCollection($songs);

            return $this->responseJson(true, 200, 'Playlist fetched successfully', new PlayListResource($playlist));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
    public function deletePlaylist($playlistId)
    {
        $validator = Validator::make(
            ['playlist_id' => $playlistId],
            [
                'playlist_id' => 'required|integer|exists:play_lists,id',
            ]
        );
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
        }

        DB::beginTransaction();
        try {
            $playlist = PlayList::where('id', $playlistId)
                ->where('user_id', auth()->user()->id)
                ->first();

            if (!$playlist) {
                return $this->responseJson(false, 403, 'Unauthorized or Playlist not found', (object)[]);
            }

            // Delete cover image if it exists
            if ($playlist->cover_image) {
                $this->deleteOne(config('constants.SITE_PLAYLIST_COVER_IMAGE_UPLOAD_PATH') . '/' . $playlist->cover_image);
            }

            // Detach pivot records (although ON DELETE CASCADE is set, this is safe/clean)
            $playlist->songs()->detach();

            // Delete playlist itself
            $playlist->delete();

            DB::commit();
            return $this->responseJson(true, 200, 'Playlist deleted successfully', (object)[]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
    public function bulkSongAddRemovePlayList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'playlist_id' => 'required|integer|exists:play_lists,id',
            'song_ids' => 'required|array',
            'song_ids.*' => 'integer|exists:songs,id',
            'action' => 'required|string|in:add,remove',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
        }

        DB::beginTransaction();
        try {
            $playlist = PlayList::where('id', $request->playlist_id)
                ->where('user_id', auth()->user()->id)
                ->first();

            if (!$playlist) {
                return $this->responseJson(false, 403, 'Unauthorized or Playlist not found', (object)[]);
            }

            $songIds = array_unique($request->song_ids);

            if ($request->action === 'add') {
                // Find songs already in the playlist to avoid unique constraint violations
                $existingSongIds = $playlist->songs()->whereIn('song_id', $songIds)->pluck('songs.id')->toArray();
                $newSongIds = array_diff($songIds, $existingSongIds);

                if (!empty($newSongIds)) {
                    $playlist->songs()->attach($newSongIds);
                    $playlist->increment('songs_count', count($newSongIds));
                }
                $message = 'Songs added to playlist successfully';
            } else {
                // Find songs actually in the playlist before detaching to decrement songs_count accurately
                $existingSongIds = $playlist->songs()->whereIn('song_id', $songIds)->pluck('songs.id')->toArray();
                $songsToDetach = array_intersect($songIds, $existingSongIds);

                if (!empty($songsToDetach)) {
                    $playlist->songs()->detach($songsToDetach);
                    $playlist->decrement('songs_count', count($songsToDetach));
                }
                $message = 'Songs removed from playlist successfully';
            }

            DB::commit();
            return $this->responseJson(true, 200, $message, (object)[]);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
    public function searchSongs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keywords' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), (object)[]);
        }

        try {
            $keywords = $request->keywords;

            // Log search history
            SearchHistory::create([
                'user_id' => auth()->check() ? auth()->id() : null,
                'keyword' => trim($keywords),
            ]);

            // Search Songs
            $songs = Song::where(function ($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                    ->orWhere('artist_name', 'like', "%{$keywords}%")
                    ->orWhere('description', 'like', "%{$keywords}%");
            })
                ->where('status', 1)
                ->with(['artist', 'album', 'genre'])
                ->get();

            // Search Albums
            $albums = Album::where(function ($q) use ($keywords) {
                $q->where('title', 'like', "%{$keywords}%")
                    ->orWhereHas('songs', function ($sq) use ($keywords) {
                        $sq->where('title', 'like', "%{$keywords}%")
                            ->orWhere('artist_name', 'like', "%{$keywords}%");
                    });
            })
                ->where('status', 1)
                ->with('user')
                ->get();

            // Search Artists
            $artists = User::where(function ($q) use ($keywords) {
                $q->where('name', 'like', "%{$keywords}%");
                // ->orWhereHas('songs', function ($sq) use ($keywords) {
                //     $sq->where('title', 'like', "%{$keywords}%");
                // });
            })
                ->whereHas('profile') // Profile usually means artist in this app
                ->get();

            // Search Public Playlists
            $playlists = PlayList::where('is_public', 1)
                ->where(function ($q) use ($keywords) {
                    $q->where('title', 'like', "%{$keywords}%")
                        ->orWhereHas('songs', function ($sq) use ($keywords) {
                            $sq->where('title', 'like', "%{$keywords}%")
                                ->orWhere('artist_name', 'like', "%{$keywords}%");
                        });
                })
                ->with('user')
                ->get();

            $data = [
                'songs' => SongResource::collection($songs),
                'albums' => AlbumResource::collection($albums),
                'artists' => ArtistResource::collection($artists),
                'playlists' => PlayListResource::collection($playlists),
            ];

            return $this->responseJson(true, 200, 'Search results fetched successfully', $data);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
    public function trendingSearches()
    {
        try {
            // Get trending keywords from actual search history
            $trendingKeywords = SearchHistory::select('keyword', DB::raw('COUNT(*) as total_searches'))
                ->groupBy('keyword')
                ->orderByDesc('total_searches')
                ->limit(5)
                ->pluck('keyword');

            // Fallback if no searches exist yet
            if ($trendingKeywords->isEmpty()) {
                $trendingKeywords = collect(['The Weeknd', 'Coldplay', 'Arijit Singh', 'Imagine Dragons', 'Diljit Dosanjh']);
            }

            return $this->responseJson(true, 200, 'Trending searches fetched successfully', $trendingKeywords);
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function biggestHits(Request $request)
    {
        try {
            $query = StreamLog::select('song_id', DB::raw('count(*) as stream_count'))
                ->groupBy('song_id')
                ->orderByDesc('stream_count');

            $isEmpty = false;

            if ($request->has('dashboard')) {
                $biggestHitsSongs = $query->take(3)->get()->pluck('song')->filter();
                $isEmpty = $biggestHitsSongs->isEmpty();
            } else {
                /** @var \Illuminate\Pagination\LengthAwarePaginator $biggestHitsSongs */
                $biggestHitsSongs = $query->paginate(10);
                $isEmpty = $biggestHitsSongs->isEmpty();
                if (!$isEmpty) {
                    $biggestHitsSongs->through(function ($history) {
                        return $history->song;
                    });
                }
            }

            if ($isEmpty) {
                $artistIds = [];
                if (auth('sanctum')->check() || auth('api')->check() || auth()->check()) {
                    $userId = auth()->id();
                    $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
                    $chosenArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();
                    $artistIds = array_unique(array_merge($followedArtistIds, $chosenArtistIds));
                }

                $fallbackQuery = Song::where('status', 1);
                if (!empty($artistIds)) {
                    $fallbackQuery->whereIn('user_id', $artistIds);
                }

                $fallbackQuery->inRandomOrder();

                if ($request->has('dashboard')) {
                    $biggestHitsSongs = $fallbackQuery->take(3)->get();
                } else {
                    $biggestHitsSongs = $fallbackQuery->paginate(10);
                }
            }

            return $this->responseJson(true, 200, 'Biggest hits fetched successfully', $request->has('dashboard')
                ? SongResource::collection($biggestHitsSongs)
                : new PaginateSongCollection($biggestHitsSongs));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function recomendateSongs(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'playlist_id' => 'nullable|integer|exists:play_lists,id',
            ]);
            if ($validator->fails()) {
                return $this->responseJson(false, 422, $validator->errors()->first(), []);
            }
            $playlistId = $request->playlist_id;
            $excludedSongIds = [];

            if ($playlistId) {
                $playlist = PlayList::find($playlistId);
                if ($playlist) {
                    $excludedSongIds = $playlist->songs()->pluck('songs.id')->toArray();
                }
            }

            $query = Song::where('status', 1);

            if (auth('api')->check()) {
                $userId = auth('api')->id();
                $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
                $chosenArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();

                // Get artists of the songs the user has liked
                $likedSongIds = DB::table('song_likes')->where('user_id', $userId)->pluck('song_id');
                $likedSongArtistIds = Song::whereIn('id', $likedSongIds)->pluck('user_id')->toArray();

                $artistIds = array_unique(array_merge($followedArtistIds, $chosenArtistIds, $likedSongArtistIds));

                if (!empty($artistIds)) {
                    $query->whereIn('user_id', $artistIds);
                } else {
                    // Return no results if the user has no preferences or likes
                    $query->whereRaw('1 = 0');
                }
            } else {
                // Return no results if unauthenticated
                $query->whereRaw('1 = 0');
            }

            if (!empty($excludedSongIds)) {
                $query->whereNotIn('id', $excludedSongIds);
            }

            $recommendedSongs = $query->inRandomOrder()->paginate($request->per_page ?? 15);

            return $this->responseJson(true, 200, 'Recommended songs fetched successfully', new PaginateSongCollection($recommendedSongs));
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
}
