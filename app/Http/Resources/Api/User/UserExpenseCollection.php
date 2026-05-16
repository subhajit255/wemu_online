<?php

namespace App\Http\Resources\Api\User;

use App\Http\Resources\Api\Auth\CategoryCollection;
use App\Http\Resources\Api\Auth\ServiceFrequencyCollection;
use App\Models\ServiceFrequency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\User\ItemImageCollection;

class UserExpenseCollection extends JsonResource
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
            'supplier' => $this->supplier,
            'category_id' => $this->category_id,
            'category' => new CategoryCollection($this->category),
            'service_frequency_id' => $this->service_frequency_id,
            'service_frequency' => new ServiceFrequencyCollection($this->serviceFrequency),
            'expense_recurring' => $this->expenseRecurring ?? null,
            'price' => $this->price,
            'date' => $this->date,
            'bill' => $this->bill_path,
            'e_script_url' => $this->e_script_url,
            'is_recurring' => $this->is_recurring,
            'is_tax_deductible' => $this->is_tax_deductible,
            'remainder_date' => $this->remainder_date,
            'remainder_time' => $this->remainder_time,
            'since' => Carbon::parse($this->created_at)->diffForHumans(),
        ];
    }
}
