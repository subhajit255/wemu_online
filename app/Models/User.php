<?php

namespace App\Models;

use Webpatser\Uuid\Uuid;
use Laravel\Passport\HasApiTokens;
use App\Traits\HasRolesAndPermissions;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRolesAndPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = (string) Uuid::generate(4);
        });
    }
    protected $appends = ['image_path'];
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getImagePathAttribute()
    {
        $filePath = 'storage/profile/' . $this->profile_image;
        if (!$this->profile_image || !file_exists(public_path($filePath))) {
            return asset('assets/media/avatars/blank.png');
        }
        return asset($filePath);
    }

    public function userIncome(): HasMany
    {
        return $this->hasMany(UserIncome::class);
    }
    public function userIncomeCurrentMonth(): HasMany
    {
        return $this->hasMany(UserIncome::class)->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }
    public function userIncomePreviousMonth(): HasMany
    {
        return $this->hasMany(UserIncome::class)->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    }

    public function userExpense(): HasMany
    {
        return $this->hasMany(UserExpense::class);
    }
    public function userExpenseCurrentMonth(): HasMany
    {
        return $this->hasMany(UserExpense::class)->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }
    public function userExpensePreviousMonth(): HasMany
    {
        return $this->hasMany(UserExpense::class)->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    }

    public function userItem(): HasMany
    {
        return $this->hasMany(Item::class);
    }
    public function userItemCurrentMonth(): HasMany
    {
        return $this->hasMany(Item::class)->whereMonth('date', now()->month)->whereYear('date', now()->year);
    }
    public function userItemPreviousMonth(): HasMany
    {
        return $this->hasMany(Item::class)->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year);
    }
    public function userItemExpense(): HasMany
    {
        return $this->hasMany(Item::class)->where('is_expense', 1);
    }
    public function userItemCurrentMonthExpense(): HasMany
    {
        return $this->hasMany(Item::class)->whereMonth('date', now()->month)->whereYear('date', now()->year)->where('is_expense', 1);
    }
    public function userItemPreviousMonthExpense(): HasMany
    {
        return $this->hasMany(Item::class)->whereMonth('date', now()->subMonth()->month)->whereYear('date', now()->subMonth()->year)->where('is_expense', 1);
    }
    public function userGoals(): HasMany
    {
        return $this->hasMany(UserGoal::class);
    }

    public function hasActiveSubscription(): bool
    {
        return UserSubscription::where(['user_id' => $this->id, 'is_active' => 1])
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where('remaining_activity_count', '>', 0)
            ->exists();
    }
}
