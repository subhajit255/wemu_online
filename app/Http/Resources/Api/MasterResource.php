<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\AuthResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MasterResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $dataArray = [];
        if (str_contains(request()->route()->getName(), 'albums') || str_contains(request()->route()->getName(), 'made.for.you') || str_contains(request()->route()->getName(), 'recently.played')) {
            $dataArray =  [
                'id' => $this->id ?? '',
                'uuid' => $this->uuid ?? '',
                'title' => $this->title ?? '',
                'slug' => $this->slug ?? '',
                'description' => $this->description ?? '',
                'image' => $this->image_path ?? '',
                'status' => $this->status ?? '',
                'created_by' => new AuthResource($this->user ?? null),
            ];
            return $dataArray;
        } else if (str_contains(request()->route()->getName(), 'genres')) {
            $dataArray =  [
                'id' => $this->id ?? '',
                'title' => $this->title ?? '',
                'slug' => $this->slug ?? '',
                'description' => $this->description ?? '',
                'status' => $this->is_active ?? '',
                'parent' => new MasterResource($this->parent ?? null)
            ];
            return $dataArray;
        } else {
            $dataArray =  parent::toArray($request);
        }
        return $dataArray;
    }
}
