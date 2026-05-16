<?php

namespace App\Http\Resources\Api\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceFrequencyCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'day_count' => $this->day_count,
            'added_time' => Carbon::parse($this->created_at)->format('F j, Y, g:i a')
        ];
    }
}
