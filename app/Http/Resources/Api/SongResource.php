<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SongResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'cover_image_path' => $this->cover_image_path,
            'featured_artists' => $this->featured_artists ?? null,
            'other_artists' => $this->artist_name ?? null,
            'total_duration' => $this->totalSongsDuration($this->duration),
            'description' => $this->description ?? null,
            'lyrics' => $this->lyrics ?? null,
            'duration' => $this->totalSongsDuration($this->duration),
            'is_explicit' => $this->is_explicit ?? 0,
            'play_count' => $this->play_count,
            'likes_count' => $this->likes_count ?? 0,
            'download_count' => $this->download_count ?? 0,
            'audio_file_path' => $this->audio_file_path ?? null,
            'background_file_path' => $this->background_file_path ?? null,
            'status' => $this->status ?? 0,
            'published_at' => $this->published_at ?? null,
            'artist' => new ArtistResource($this->whenLoaded('artist')),
            'album' => new AlbumResource($this->whenLoaded('album')),
            'genre' => $this->whenLoaded('genre'),
        ];
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
}
