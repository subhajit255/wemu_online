<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AlbumResource extends JsonResource
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
            'description' => $this->description,
            'cover_image_path' => $this->image_path, // Uses getImagePathAttribute() from Album model
            'release_date' => $this->release_date,
            'created_at' => $this->created_at,
            'artist' => new ArtistResource($this->whenLoaded('user')),
        ];
    }
}
