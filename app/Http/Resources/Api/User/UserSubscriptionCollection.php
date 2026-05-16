<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionCollection extends JsonResource
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
            'subscription_uuid' => $this->subscription->uuid,
            'title' => $this->subscription?->title,
            'mrp' => $this->subscription?->mrp,
            'discount' => $this->subscription?->discount,
            'price' => $this->subscription?->price,
            'activity_count' => $this->subscription?->activity_count,
            'description' => $this->subscription?->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'total_activity_count' => $this->total_activity_count,
            'used_activity_count' => $this->used_activity_count,
            'remaining_activity_count' => $this->remaining_activity_count,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
