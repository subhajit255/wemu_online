<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Auth\CategoryCollection;
use App\Http\Resources\Api\User\ItemImageCollection;
use App\Http\Resources\Api\Auth\ServiceFrequencyCollection;

class ItemCollection extends JsonResource
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
            'user_id' => $this->user_id,
            'category' => new CategoryCollection($this->category),
            'price' => $this->price,
            'date' => $this->date,
            'brand_name' => $this->brand_name,
            'model_no' => $this->model_no,
            'serial_no' => $this->serial_no,
            'is_expense' => $this->is_expense,
            'bill_path' => $this->bill_path,
            'supplier_name' => $this->supplier_name,
            'supplier_contact_number' => $this->supplier_contact_number,
            'service_frequency' => new ServiceFrequencyCollection($this->serviceDetail->serviceFrequency),
            'last_service_date' => $this->serviceDetail->last_service_date ?? null,
            'set_remainder_service' => $this->serviceDetail->set_remainder ?? null,
            'comment_service' => $this->serviceDetail->comments ?? null,
            'expiry_date' => $this->warrantyDetail->expiry_date ?? null,
            'include' => $this->warrantyDetail->include ?? null,
            'customer_care_number' => $this->warrantyDetail->customer_care_number ?? null,
            'set_remainder_warranty' => $this->warrantyDetail->set_remainder ?? null,
            'comment_warranty' => $this->warrantyDetail->comments ?? null,
            'images' => ItemImageCollection::collection($this->images),
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
