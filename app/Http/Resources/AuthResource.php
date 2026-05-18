<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\SongResource;

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
                'songs' => SongResource::collection($this->songs),
                'total_streams' => 0,
                'total_duration' => $this->totalSongsDuration($this->songs->sum('duration'))
            ];
        } else {
            return [
                'id' => $this->id,
                'uuid' => $this->uuid,
                'name' => $this->name,
                'email' => $this->email,
                'mobile' => $this->mobile_number,
                'phone_code' => $this->phone_code,
                'profile_image' => $this->image_path
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
}
