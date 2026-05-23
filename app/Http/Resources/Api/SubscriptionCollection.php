<?php

namespace App\Http\Resources\Api;

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
            'id' => $this->id,
            'uuid' => $this->uuid ?? null,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
            'name' => $this->subscription?->name,
            'description' => $this->subscription?->description,
            'features' => $this->subscription?->features,
            'price' => $this->price,
            'interval' => $this->subscription?->interval,
            'started_on' => Carbon::parse($this->started_on)->diffForHumans(),
            'ended_at' => Carbon::parse($this->ended_at)->diffForHumans(),
        ];
    }
}
