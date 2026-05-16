<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGoalCollection extends JsonResource
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
            'image' => $this->image_path,
            'link' => $this->link,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'comments' => $this->comments,
            'url' => $this->url,
            'is_completed' => $this->is_completed,
            'service_frequency_id' => $this->service_frequency_id,
            'service_frequency' => $this->serviceFrequency?->name ?? null,
            'task' => UserGoalTaskCollection::collection($this->tasks),
        ];
    }
}
