<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $guarded  = [];

    protected $appends = ['income_icon_path','expense_icon_path','budget_icon_path','item_icon_path'];

    public function getIncomeIconPathAttribute()
    {
        $filePath = "storage/icon/{$this->income_icon}";
        if (!$this->income_icon || !file_exists(public_path($filePath))) {
            return null;
        }
        return asset($filePath);
    }
    public function getExpenseIconPathAttribute()
    {
        $filePath = "storage/icon/{$this->expense_icon}";
        if (!$this->expense_icon || !file_exists(public_path($filePath))) {
            return null;
        }
        return asset($filePath);
    }

    public function getBudgetIconPathAttribute()
    {
        $filePath = "storage/icon/{$this->budget_icon}";
        if (!$this->budget_icon || !file_exists(public_path($filePath))) {
            return null;
        }
        return asset($filePath);
    }

    public function getItemIconPathAttribute()
    {
        $filePath = "storage/icon/{$this->item_icon}";
        if (!$this->item_icon || !file_exists(public_path($filePath))) {
            return null;
        }
        return asset($filePath);
    }
    public function getGoalIconPathAttribute()
    {
        $filePath = "storage/icon/{$this->goal_icon}";
        if (!$this->goal_icon || !file_exists(public_path($filePath))) {
            return null;
        }
        return asset($filePath);
    }
}
