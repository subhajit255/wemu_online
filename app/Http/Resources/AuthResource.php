<?php

namespace App\Http\Resources;

use \App\Models\UserSkip;
use \App\Models\UserSubscription;
use App\Http\Resources\Api\SongResource;
use App\Models\ArtistFollower;
use App\Models\PlayList;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $dataArray = [];
        if (str_contains(request()->route()->getName(), 'artists')) {
            $dataArray =  [
                'id' => $this->id,
                'uuid' => $this->uuid,
                'name' => 'The Best of ' . $this->name . '. The essential tracks, all in one playlist.',
                'profile_image' => $this->image_path
            ];
        } else if (str_contains(request()->route()->getName(), 'artist.details')) {
            $dataArray =  [
                'id' => $this->id,
                'uuid' => $this->uuid,
                'name' => $this->name,
                'profile_image' => $this->image_path,
                'songs' => SongResource::collection($this->songs->sortByDesc('published_at')->values()),
                'total_streams' => $this->stream_count,
                'total_duration' => $this->totalSongsDuration($this->songs->sum('duration')),
                'is_followed' => $this->is_followed($this->id)
            ];
        } else {
            return [
                'id' => $this->id,
                'uuid' => $this->uuid,
                'name' => $this->name,
                'email' => $this->email,
                'mobile' => $this->mobile_number,
                'phone_code' => $this->phone_code,
                'profile_image' => $this->image_path,
                'playlists_count' => $this->totalPlaylists($this->id),
                'following_count' => $this->totalFollowing($this->id),
                'followers_count' => 0,
                'is_skipped' => $this->isSkippedAllowed($this->id)
            ];
        }

        return $dataArray;
    }

    public function totalSongsDuration($totalSongDuration)
    {
        $hours = floor($totalSongDuration / 3600);
        $minutes = floor(($totalSongDuration % 3600) / 60);
        $durationString = '';
        if ($hours > 0) {
            $durationString .= $hours . ' hr ';
        }
        $durationString .= $minutes . ' min';
        return $durationString;
    }
    public function is_followed($artist_id): bool
    {
        return auth()->check() ? ArtistFollower::where(['artist_id' => $artist_id, 'user_id' => auth()->id()])->exists() : false;
    }
    public function totalPlaylists($userId): int
    {
        return PlayList::where('user_id', $userId)->count();
    }
    public function totalFollowing($userId): int
    {
        return ArtistFollower::where('user_id', $userId)->count();
    }
    public function isSkippedAllowed($userId): bool
    {
        $userSubscription = UserSubscription::with('subscription')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->latest()
            ->first();

        $activeSubscription = $userSubscription ? $userSubscription->subscription : null;

        if (!$activeSubscription) {
            $activeSubscription = \App\Models\Subscription::where('is_default', 1)
                ->where('available_for', 1)
                ->first();
        }

        $maxSkips = $activeSubscription ? $activeSubscription->max_song_skips : null;

        if (!is_null($maxSkips)) {
            $skipsToday = UserSkip::where('user_id', $userId)
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->count();

            if ($skipsToday >= $maxSkips) {
                return false;
            }
        }

        return true;
    }
}
