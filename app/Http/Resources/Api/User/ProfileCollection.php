<?php

namespace App\Http\Resources\Api\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Traits\NotificationTrait;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileCollection extends JsonResource
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
            'email' => $this->email,
            'phone_code' => $this->phone_code ?? 61,
            'mobile_number' => $this->mobile_number,
            'pin' => $this->pin,
            'user_type' => $this->user_type == 1 ? 'Admin' : 'Customer',
            'is_verified' => $this->is_verified,
            'profile_image' => $this->image_path,
            'total_income_till_date' => $this->userIncome->sum('amount') ?? 0,
            'total_income_this_month' => $this->userIncomeCurrentMonth->sum('amount') ?? 0,
            'total_income_previous_month' => $this->userIncomePreviousMonth->sum('amount') ?? 0,
            'total_expense_till_date' => $this->userExpense->sum('price') ?? 0,
            'total_expense_this_month' => $this->userExpenseCurrentMonth->sum('price') ?? 0,
            'total_expense_previous_month' => $this->userExpensePreviousMonth->sum('price') ?? 0,
            'total_item_count_till_date' => $this->userItem->count() ?? 0,
            'total_item_count_this_month' => $this->userItemCurrentMonth->count() ?? 0,
            'total_item_count_previous_month' => $this->userItemPreviousMonth->count() ?? 0,
            'total_item_amount_till_date' => $this->userItem->sum('price') ?? 0,
            'total_item_amount_this_month' => $this->userItemCurrentMonth->sum('price') ?? 0,
            'total_item_amount_previous_month' => $this->userItemPreviousMonth->sum('price') ?? 0,
            'total_item_expense_till_date' => $this->userItemExpense->sum('price') ?? 0,
            'total_item_expense_this_month' => $this->userItemCurrentMonthExpense->sum('price') ?? 0,
            'total_item_expense_previous_month' => $this->userItemPreviousMonthExpense->sum('price') ?? 0,
            'is_subscribed' => userSubscriptionIsActive(),
            'subscription' => new UserSubscriptionCollection(userSubscription()),
            'since' => Carbon::parse($this->created_at)->diffForhumans()
        ];
    }
}
