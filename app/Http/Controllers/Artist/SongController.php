<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Song;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Album;
use App\Traits\UploadAble;

class SongController extends BaseController
{
    use UploadAble;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->added_by) {
                $perms = $user->permissions ? json_decode($user->permissions, true) : [];
                if (!is_array($perms)) $perms = [];
                if (!in_array('songs', $perms)) {
                    abort(403, 'Unauthorized access.');
                }
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $mainArtistId = auth()->user()->added_by ?: auth()->user()->id;
        $teamIds = \App\Models\User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

        $query = Song::whereIn('user_id', $teamIds);

        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        $songs = $query->latest()->paginate(10);
        $postReleases = Song::whereIn('user_id', $teamIds)
            ->where('status', 0)
            ->where('published_at', '>', $currentDateTime)
            ->orderBy('published_at', 'asc')
            ->take(3)
            ->get();
        return view('artist.songs.index', compact('songs', 'postReleases'));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        if ($request->post()) {
            $id = $request->id ?? null;

            if (!empty($id)) {
                $request->validate([
                    'title'       => 'required|string',
                    'genre_id'    => 'required|exists:genres,id',
                    'album_id'    => 'required|exists:albums,id',
                    'language_id' => 'required|exists:languages,id',
                    'artist_name' => 'required|string',
                    'lyrics'      => 'nullable|string',
                    'status'      => 'required|in:0,1',
                    'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                    // 'audio_file'  => 'nullable|file|mimes:mp3,wav',
                    'background'  => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4',
                    'is_explicit' => 'required|in:0,1',
                ]);
                $message = "Song Updated Successfully";
            } else {
                $request->validate([
                    'title'       => 'required|string',
                    'genre_id'    => 'required|exists:genres,id',
                    'album_id'    => 'required|exists:albums,id',
                    'language_id' => 'required|exists:languages,id',
                    'artist_name' => 'required|string',
                    'lyrics'      => 'nullable|string',
                    'status'      => 'required|in:0,1',
                    'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                    // 'audio_file'  => 'nullable|file|mimes:mp3,wav',
                    'background'  => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,mp4',
                    'is_explicit' => 'required|in:0,1',
                ]);
                $message = "Song Created Successfully";
            }

            DB::beginTransaction();
            try {
                $postData = [
                    "title"            => $request->title,
                    "slug"             => Str::slug($request->title),
                    "user_id"          => auth()->user()->id,
                    "album_id"         => $request->album_id,
                    "genre_id"         => $request->genre_id,
                    "language_id"      => $request->language_id,
                    "artist_name"      => $request->artist_name,
                    "featured_artists" => $request->featured_artists,
                    "track_number"     => $request->track_number,
                    "duration"         => $request->duration,
                    "description"      => $request->description,
                    "lyrics"           => $request->lyrics,
                    "status"           => $request->status,
                    "is_explicit"      => $request->is_explicit,
                    'published_at'     => $request->release_at ?? date('Y-m-d H:i:s'),
                ];

                // Handle cover image
                if ($request->hasFile('cover_image')) {
                    $image    = $request->file('cover_image');
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $uploaded = $this->uploadOne($image, config('constants.SITE_SONG_COVER_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                    if ($uploaded) {
                        // Delete old cover image if updating
                        if (!empty($id)) {
                            $old = Song::find($id);
                            if ($old && $old->cover_image) {
                                $this->deleteOne(config('constants.SITE_SONG_COVER_IMAGE_UPLOAD_PATH') . '/' . $old->cover_image);
                            }
                        }
                        $postData['cover_image'] = $uploaded;
                    }
                }

                // Handle audio file
                if ($request->hasFile('audio_file')) {
                    $audio    = $request->file('audio_file');
                    $fileName = uniqid() . '.' . $audio->getClientOriginalExtension();
                    $duration = $this->getAudioDuration($audio);
                    if ($duration) {
                        $postData['duration'] = $duration; // saves as seconds e.g. 213.45
                    }
                    $uploaded = $this->uploadOne($audio, config('constants.SITE_SONG_UPLOAD_PATH'), $fileName, 'public');
                    if ($uploaded) {
                        // Delete old audio file if updating
                        if (!empty($id)) {
                            $old = $old ?? Song::find($id);
                            if ($old && $old->audio_file) {
                                $this->deleteOne(config('constants.SITE_SONG_UPLOAD_PATH') . '/' . $old->audio_file);
                            }
                        }
                        $postData['audio_file'] = $uploaded;
                    }
                }

                // Handle background
                if ($request->hasFile('background')) {
                    $bg       = $request->file('background');
                    $fileName = uniqid() . '.' . $bg->getClientOriginalExtension();
                    $uploaded = $this->uploadOne($bg, config('constants.SITE_SONG_COVER_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                    if ($uploaded) {
                        // Delete old background if updating
                        if (!empty($id)) {
                            $old = $old ?? Song::find($id);
                            if ($old && $old->background) {
                                $this->deleteOne(config('constants.SITE_SONG_COVER_IMAGE_UPLOAD_PATH') . '/' . $old->background);
                            }
                        }
                        $postData['background'] = $uploaded;
                    }
                }
                $song = Song::updateOrCreate(['id' => $id], $postData);
                DB::commit();
            } catch (\Throwable $th) {
                DB::rollback();
                return $this->responseJson(false, 500, [
                    'Message'     => $th->getMessage(),
                    'File Path'   => $th->getFile(),
                    'Line Number' => $th->getLine()
                ]);
            }

            return response([
                'status'  => true,
                'message' => $message,
                'data'    => $song,
                'url'     => route('artist.songs.index')
            ]);
        }

        $details   = $id ? Song::findOrFail($id) : new Song();
        if (!$id && $request->has('album_id')) {
            $details->album_id = $request->album_id;
        }
        $genres    = Genre::where('is_active', 1)->get();
        $languages = Language::get();
        $albums    = Album::where('user_id', auth()->id())->latest()->get();

        return view('artist.songs.form', compact('details', 'genres', 'languages', 'albums'));
    }

    public function show($id)
    {
        $song = Song::with(['album', 'genre', 'language'])->findOrFail($id);
        return view('artist.songs.details', compact('song'));
    }

    public function play($id)
    {
        $song = Song::with(['album', 'genre', 'language'])->findOrFail($id);
        return view('artist.songs.play', compact('song'));
    }
}
