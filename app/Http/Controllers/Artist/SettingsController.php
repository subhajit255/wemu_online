<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\ArtistPreference;

class SettingsController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $genres = Genre::where('is_active', 1)->get();
        $preferences = ArtistPreference::firstOrCreate(['user_id' => $user->id]);
        
        return view('artist.settings.index', compact('user', 'genres', 'preferences'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'favorite_genres' => 'nullable|array',
            'release_frequency' => 'nullable|integer',
            'artist_type' => 'nullable|in:INDEPENDENT,SIGNED',
        ]);

        $preferences = ArtistPreference::firstOrCreate(['user_id' => $user->id]);
        
        $preferences->update([
            'favorite_genres' => $request->favorite_genres ?? [],
            'release_frequency' => $request->release_frequency,
            'artist_type' => $request->artist_type ?? 'INDEPENDENT',
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }
}
