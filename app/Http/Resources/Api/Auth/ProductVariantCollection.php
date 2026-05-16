<?php

namespace App\Http\Resources\Api\Auth;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantCollection extends JsonResource
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
            'price' => getCurrency() . $this->price,
            'qty' => $this->qty,
            'attribute_values' => $this->attribute_values,
            'attribute_values_str' => $this->attribute_values_str,
            'added_time' => Carbon::parse($this->created_at)->format('F j, Y, g:i a')
        ];
    }
}
