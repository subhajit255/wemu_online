<?php

namespace App\Http\Resources\Api\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryCollection extends JsonResource
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
            'title' => $this->title,
            'short_desc' => Str::limit($this->description, 50, $end = '...'),
            'description' => $this->description,
            'image' => $this->image_path,
            'alias' => $this->alias,
            'color_code' => $this->color_code,
            'added_time' => Carbon::parse($this->created_at)->format('F j, Y, g:i a')
        ];
    }
}
