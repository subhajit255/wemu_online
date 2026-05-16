<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGoalTaskCollection extends JsonResource
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
            'service_frequency_id' => $this->service_frequency_id,
            'service_frequency' => $this->serviceFrequency?->name ?? null,
            'service_frequency_name' => $this->serviceFrequency?->name ?? null,
            'task_type' => $this->task_type,
            'task_type_name' => $this->task_type == 1 ? 'One Time' : ($this->task_type == 2 ? 'Repetitive' : null),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'comments' => $this->comments ?? '',
            'url' => $this->url ?? '',
            'is_completed' => $this->is_completed,
        ];
    }
}
