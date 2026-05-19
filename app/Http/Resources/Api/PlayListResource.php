<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataArray = [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title ?? '',
            'description' => $this->description ?? '',
            'cover_image_path' => $this->cover_image_path,
            'is_public' => $this->is_public ?? 0,
            'songs_count' => $this->songs_count ?? 0,
            'followers_count' => $this->followers_count ?? 0,
            'play_count' => $this->play_count ?? 0,
            'created_at' => $this->created_at,
        ];
        if (str_contains(request()->route()->getName(), 'playlist.details')) {
            $dataArray['songs'] = $this->when(isset($this->songs_paginated), $this->songs_paginated);
        }
        return $dataArray;
    }
}
