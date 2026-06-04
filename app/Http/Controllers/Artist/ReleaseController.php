<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;

class ReleaseController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->added_by) {
                $perms = $user->permissions ? json_decode($user->permissions, true) : [];
                if (!is_array($perms)) $perms = [];
                if (!in_array('releases', $perms)) {
                    abort(403, 'Unauthorized access.');
                }
            }
            return $next($request);
        });
    }
    public function index()
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        
        $mainArtistId = auth()->user()->added_by ?: auth()->user()->id;
        $teamIds = \App\Models\User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

        // Fetch post releases songs
        $postReleaseSongs = Song::whereIn('user_id', $teamIds)
            ->where('status', 0)
            ->where('published_at', '>', $currentDateTime)
            ->orderBy('published_at', 'asc')
            ->paginate(10, ['*'], 'songs_page');

        // Fetch post releases albums
        $postReleaseAlbums = Album::whereIn('user_id', $teamIds)
            ->where('status', 0)
            ->where('release_date', '>', $currentDate)
            ->orderBy('release_date', 'asc')
            ->paginate(10, ['*'], 'albums_page');

        return view('artist.release.index', compact('postReleaseSongs', 'postReleaseAlbums'));
    }
}
