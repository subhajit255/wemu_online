<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Song;
use App\Models\Album;
use App\Models\SongLike;
use App\Models\ArtistFollower;
use App\Models\PlayHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\SongResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\MasterResource;
use App\Http\Resources\Api\PaginateSongCollection;
use App\Http\Resources\Api\PaginateMasterCollection;

class UserController extends BaseController
{
    public function recentlyPlayedSongsV1(Request $request)
    {
        try {
            $query = PlayHistory::with(['song'])->where('user_id', auth()->id())->orderByDesc('last_played_at');

            if ($request->has('dashboard')) {
                $recentlyPlayed = $query->take(3)->get()->pluck('song')->filter();
            } else {
                /** @var \Illuminate\Pagination\LengthAwarePaginator $recentlyPlayed */
                $recentlyPlayed = $query->paginate(10);
                $recentlyPlayed->through(function ($history) {
                    return $history->song;
                });
            }

            return $this->responseJson(
                true,
                200,
                'Recently played songs fetched successfully',
                $request->has('dashboard')
                    ? SongResource::collection($recentlyPlayed)
                    : new PaginateSongCollection($recentlyPlayed)
            );
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function recentlyPlayedSongs(Request $request)
    {
        try {
            $query = PlayHistory::with(['song.album'])->where('user_id', auth()->id())->orderByDesc('last_played_at');
            if ($request->has('dashboard')) {
                $recentlyPlayed = $query->take(10)->get()->pluck('song.album')->filter()->unique('id')->values();
            } else {
                $recentlyPlayed = $query->paginate(10);
                $recentlyPlayed->through(function ($history) {
                    return $history->song?->album;
                });
                $recentlyPlayed->setCollection($recentlyPlayed->getCollection()->filter()->unique('id')->values());
            }
            return $this->responseJson(
                true,
                200,
                'Recently played albums fetched successfully',
                $request->has('dashboard')
                    ? MasterResource::collection($recentlyPlayed)
                    : new PaginateMasterCollection($recentlyPlayed)
            );
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function toggleSongLike($songId)
    {
        $validator = Validator::make(
            ['song_id' => $songId],
            [
                'song_id' => 'required|exists:songs,id',
            ]
        );
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), false);
        }
        DB::beginTransaction();
        try {
            $song = Song::find($songId);
            $alreadyLiked = SongLike::where(['user_id' => auth()->user()->id, 'song_id' => $songId])->exists();
            if ($alreadyLiked) {
                // Unlike song
                SongLike::where(['user_id' => auth()->user()->id, 'song_id' => $songId])->delete();
                // Decrease global likes count
                Song::where('id', $songId)->decrement('likes_count');
                $message = 'Song unliked successfully';
                $isLiked = false;
            } else {
                // Like song
                SongLike::create(['user_id' => auth()->user()->id, 'song_id' => $songId]);
                // Increase global likes count
                Song::where('id', $songId)->increment('likes_count');
                $message = 'Song liked successfully';
                $isLiked = true;
            }
            DB::commit();
            return $this->responseJson(true, 200, $message, $isLiked);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', false);
        }
    }
    public function likedSong(Request $request)
    {
        try {
            $query = SongLike::with(['song'])->where('user_id', auth()->id())->latest();

            if ($request->has('dashboard')) {
                $likedSongs = $query->take(3)->get()->pluck('song')->filter();
            } else {
                $likedSongs = $query->paginate(10);
                $likedSongs->through(function ($like) {
                    return $like->song;
                });
            }

            return $this->responseJson(
                true,
                200,
                'liked songs fetched successfully',
                $request->has('dashboard')
                    ? SongResource::collection($likedSongs)
                    : new PaginateSongCollection($likedSongs)
            );
        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function madeForYouSongs(Request $request)
    {
        try {

            $playedSongIds = PlayHistory::where('user_id', auth()->id())
                ->pluck('song_id');
            $genreIds = Song::whereIn('id', $playedSongIds)
                ->pluck('genre_id')
                ->unique();
            $playedAlbumIds = Song::whereIn('id', $playedSongIds)
                ->pluck('album_id')
                ->filter();
            $query = Album::whereIn('genre_id', $genreIds)
                ->whereNotIn('id', $playedAlbumIds)
                ->where('status', 1)
                ->latest();
            if ($request->has('dashboard')) {
                $albums = $query->take(6)->get();
            } else {
                $albums = $query->paginate(10);
            }
            return $this->responseJson(
                true,
                200,
                'Made for you fetched successfully',
                $request->has('dashboard')
                    ? MasterResource::collection($albums)
                    : new PaginateMasterCollection($albums)
            );
        } catch (\Throwable $th) {
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function toggleArtistFollow($artistId)
    {
        $validator = Validator::make(
            ['artist_id' => $artistId],
            [
                'artist_id' => 'required|exists:users,id',
            ]
        );
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        DB::beginTransaction();
        try {
            $artist = User::find($artistId);
            $alreadyFollowed = ArtistFollower::where(['user_id' => auth()->user()->id, 'artist_id' => $artistId])->exists();
            if ($alreadyFollowed) {
                // Unfollow artist
                ArtistFollower::where(['user_id' => auth()->user()->id, 'artist_id' => $artistId])->delete();
                $message = 'Artist unfollowed successfully';
            } else {
                // Follow artist
                ArtistFollower::create(['user_id' => auth()->user()->id, 'artist_id' => $artistId]);
                $message = 'Artist followed successfully';
            }
            DB::commit();
            return $this->responseJson(true, 200, $message, []);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
}
