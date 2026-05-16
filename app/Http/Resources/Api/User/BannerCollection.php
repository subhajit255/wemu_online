<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerCollection extends JsonResource
{
    use CommonFunction;
    use NotificationTrait;
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'file' => $this->image_path,
            'url' => $this->url,
            'start_time' => $this->start_time ?? null,
            'end_time' => $this->end_time ?? null,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
