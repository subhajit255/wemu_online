<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponCollection extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'code' => $this->code,
            'type' => $this->type,
            'coupon_discount' => $this->coupon_discount,
            'for_all_user' => $this->for_all_user,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
