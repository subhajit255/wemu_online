<?php

namespace App\Http\Resources\Api;

use App\Models\ArtistFollower;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArtistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'username' => $this->username,
            'image_path' => $this->profile?->image_path,
            'cover_image_path' => $this->profile?->cover_image_path,
            'bio' => $this->profile?->bio ?? null,
            'total_followers' => self::totalFollowers($this->id) ?? 0,
            'is_followed' => self::isFollowedArtist($this->id) ?? false
        ];
    }

    public static function totalFollowers($artistId): int
    {
        return ArtistFollower::where('artist_id', $artistId)->count();
    }
    public static function isFollowedArtist($artistId): bool
    {
        return ArtistFollower::where(['user_id' => auth()->id(), 'artist_id' => $artistId])->exists();
    }
}
