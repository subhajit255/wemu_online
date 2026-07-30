<?php

namespace App\Http\Controllers\Api;

use App\Models\PlayHistory;
use App\Models\User;
use App\Models\Song;
use App\Models\PlayList;
use App\Models\ArtistFollower;
use App\Models\UserPreference;
use App\Models\StreamLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\SongResource;
use App\Http\Resources\Api\ArtistResource;
use App\Http\Resources\Api\PlayListResource;

class DashboardController extends BaseController
{
    public function index(Request $request)
    {
        try {
            $userId = auth()->id();
            
            // 1. Recents (Songs)
            $recentSongsList = PlayHistory::with(['song.artist', 'song.album'])
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
                $recommendedSongs->whereIn('user_id', $artistIds);
            }
            $recommendedSongs = $recommendedSongs->inRandomOrder()->take(5)->get();

            // 4. Popular radio (Taking top artists based on followers or just random top artists)
            $popularRadio = User::whereHas('profile')->withCount('followers')->orderByDesc('followers_count')->take(5)->get();

            // 5. Your top mixes (Fetching some public playlists)
            $topMixes = PlayList::where('is_public', 1)->inRandomOrder()->take(5)->get();

            // 6. Sad Songs
            $sadSongs = PlayList::where('is_public', 1)
                ->where(function ($q) {
                    $q->where('title', 'like', '%Sad%')
                      ->orWhere('description', 'like', '%Sad%');
                })->take(5)->get();

            // 7. India's Best
            $indiasBest = PlayList::where('is_public', 1)
                ->where(function ($q) {
                    $q->where('title', 'like', '%India%')
                      ->orWhere('title', 'like', '%Hindi%')
                      ->orWhere('title', 'like', '%Bollywood%');
                })->take(5)->get();

            // 8. More of what you like
            $moreLike = PlayList::where('is_public', 1)->inRandomOrder()->take(5)->get();

            // Building the JSON Structure
            $data = [
                'filters' => [
                    ['id' => 'all', 'name' => 'All'],
                    ['id' => 'music', 'name' => 'Music']
                ],
                'recent_grid' => $recentGrid,
                'sections' => [
                    [
                        'title' => 'Recents',
                        'type' => 'song',
                        'items' => SongResource::collection($recentSongsList)
                    ],
                    [
                        'title' => 'Artists you like',
                        'type' => 'artist',
                        'items' => ArtistResource::collection($artistsYouLike)
                    ],
                    [
                        'title' => 'Recommended for today',
                        'type' => 'song',
                        'items' => SongResource::collection($recommendedSongs)
                    ],
                    [
                        'title' => 'More of what you like',
                        'type' => 'playlist',
                        'items' => PlayListResource::collection($moreLike)
                    ],
                    [
                        'title' => 'Popular radio',
                        'type' => 'radio',
                        'items' => ArtistResource::collection($popularRadio)
                    ],
                    [
                        'title' => 'Your top mixes',
                        'type' => 'playlist',
                        'items' => PlayListResource::collection($topMixes)
                    ],
                    [
                        'title' => 'Sad songs',
                        'type' => 'playlist',
                        'items' => PlayListResource::collection($sadSongs)
                    ],
                    [
                        'title' => 'India\'s Best',
                        'type' => 'playlist',
                        'items' => PlayListResource::collection($indiasBest)
                    ]
                ]
            ];

            return $this->responseJson(true, 200, 'Dashboard data fetched successfully', $data);

        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
}
