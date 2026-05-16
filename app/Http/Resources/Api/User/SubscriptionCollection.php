<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionCollection extends JsonResource
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
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
            'title' => $this->title,
            'mrp' => $this->mrp,
            'discount' => $this->discount,
            'price' => $this->price,
            'type' => $this->type,
            'activity_count' => $this->activity_count,
            'description' => $this->description,
        ];
    }
}
