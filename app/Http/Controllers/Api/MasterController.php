<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\PaginateSongCollection;
use App\Models\Album;
use App\Models\Genre;
use App\Models\Song;
use App\Models\StreamLog;
use App\Models\PlayHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\MasterResource;


class MasterController extends BaseController
{
    public function albums(Request $request)
    {
        try {
            $query = Album::where('status', 1);
            // Filter by genre_id if provided
            if ($request->filled('genre_id')) $query->where('genre_id', $request->genre_id);
            $albums = $query->latest()->get();
            return $this->responseJson(true, 200, 'Albums fetched successfully', MasterResource::collection($albums));
        } catch (\Throwable $th) {
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function genres()
    {
        try {
            $genres = Genre::where('is_active', 1)->latest()->get();
            return $this->responseJson(true, 200, 'Genres fetched successfully', MasterResource::collection($genres));
        } catch (\Throwable $th) {
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function songsCountIncrease($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            [
                'id' => 'required|exists:songs,id',
            ]
        );

        if ($validator->fails()) {

            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        increaseSongPlayCount($id, auth()->id());
        return response()->json(['success' => true]);
    }
    public function songsPlayed($id)
    {
        $validator = Validator::make(
            ['id' => $id],
            [
                'id' => 'required|exists:songs,id',
            ]
        );

        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $song = Song::find($id);
            if ($song) {
                logAudience($song->user_id, $song->album_id, $song->id);
            }

            // Update or create play history
            $history = PlayHistory::where(['user_id' => auth()->user()->id, 'song_id' => $id])->first();
            if ($history) {
                $history->increment('play_count');
                $history->update([
                    'last_played_at' => now(),
                ]);
            } else {
                PlayHistory::create([
                    'user_id' => auth()->user()->id,
                    'song_id' => $id,
                    'play_count' => 1,
                    'last_played_at' => now(),
                ]);
                StreamLog::create([
                    'user_id' => auth()->user()->id,
                    'artist_id' => $song->user_id,
                    'song_id' => $song->id,
                ]);
            }
            return $this->responseJson(true, 200, 'Song played', []);
        } catch (\Throwable $th) {
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    public function songsByAlbum($albumId)
    {
        $validator = Validator::make(
            ['album_id' => $albumId],
            [
                'album_id' => 'required|exists:albums,id',
            ]
        );
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        try {
            $songs = Song::where('album_id', $albumId)->latest()->paginate(10);
            return $this->responseJson(true, 200, 'Songs fetched successfully', new PaginateSongCollection($songs));
        } catch (\Throwable $th) {
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
}
