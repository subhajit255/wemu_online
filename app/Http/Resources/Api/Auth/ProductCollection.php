<?php

namespace App\Http\Resources\Api\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Resources\Api\Auth\UomCollection;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCollection extends JsonResource
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
            'sku' => $this->sku,
            'code' => $this->code,
            'short_desc' => $this->short_desc,
            'additional_desc' => $this->additional_desc,
            'description' => $this->description,
            'slug' => $this->slug,
            'mrp' => $this->mrp,
            'added_time' => Carbon::parse($this->created_at)->format('F j, Y, g:i a'),
            'images' => ProductImageCollection::collection($this->images)
        ];
    }
}
