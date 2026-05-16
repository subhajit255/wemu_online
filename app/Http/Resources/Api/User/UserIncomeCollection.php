<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class UserIncomeCollection extends JsonResource
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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'amount' => $this->amount,
            'service_frequency_id' => $this->service_frequency_id,
            'service_frequency' => $this->serviceFrequency,
            'is_recurring' => $this->is_recurring,
            'date' => $this->date,
            'income_recurring' => $this->incomeRecurring ?? null,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
