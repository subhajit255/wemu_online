<?php

use App\Models\Song;
use App\Models\Item;
use App\Models\Role;
use App\Models\User;
use Twilio\Rest\Client;
use App\Models\Category;
use App\Models\StreamLog;
use App\Models\UserBudget;
use App\Models\UserIncome;
use App\Models\UserExpense;
use Illuminate\Support\Str;
use App\Models\Notification;
use App\Models\Subscription;
use Illuminate\Support\Carbon;
use App\Models\ServiceFrequency;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

if (!function_exists('isSluggable')) {
    function isSluggable($value)
    {
        return Str::slug($value);
    }
}

if (!function_exists('pageLimit')) {
    function pageLimit($limit = NULL)
    {
        if (is_null($limit)) {
            $limitCount = 10;
        } else {
            $limitCount = $limit;
        }
        return $limitCount;
    }
}

if (!function_exists('sidebarOpen')) {
    function sidebarOpen($routes = [])
    {
        $currRoute = Route::currentRouteName();
        $open = false;
        foreach ($routes as $route) {
            if (str_contains($route, '*')) {
                if (str_contains($currRoute, substr($route, 0, strpos($route, '*')))) {
                    $open = true;
                    break;
                }
            } else {
                if ($currRoute === $route) {
                    $open = true;
                    break;
                }
            }
        }
        return $open ? 'show' : '';
    }
}

if (!function_exists('sidebarActive')) {
    function sidebarActive($routes = [])
    {
        $currRoute = Route::currentRouteName();
        $open = false;
        foreach ($routes as $route) {
            if (str_contains($route, '*')) {
                if (str_contains($currRoute, substr($route, 0, strpos($route, '*')))) {
                    $open = true;
                    break;
                }
            } else {
                if ($currRoute === $route) {
                    $open = true;
                    break;
                }
            }
        }
        return $open ? 'active' : '';
    }
}

if (!function_exists('get_content')) {
    function get_content($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        ob_start();
        curl_exec($ch);
        curl_close($ch);
        $string = ob_get_contents();
        ob_end_clean();
        return $string;
    }
}

if (!function_exists('isMobileDevice')) {
    function isMobileDevice()
    {
        return preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cricket|docomo
                            |fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i",
            $_SERVER["HTTP_USER_AGENT"]
        );
    }
}

if (!function_exists('getStatusName')) {
    function getStatusName($status)
    {
        $returnData = "In Active";
        if ($status == 1) {
            $returnData = "Active";
        } else if ($status == 3) {
            $returnData = "Deleted";
        } else if ($status == 4) {
            $returnData = "Drafted";
        }
        return $returnData;
    }
}

if (!function_exists('getDayNumber')) {
    function getDayNumber($dayName)
    {
        $days = ["sunday" => 1, "monday" => 2, "tuesday" => 3, "wednesday" => 4, "thursday" => 5, "friday" => 6, "saturday" => 7];
        $currentDay = $days[$dayName];
        return $currentDay;
    }
}

if (!function_exists('uuidtoid')) {
    function uuidtoid($uuid, $table)
    {
        $dbDetails = DB::table($table)
            ->select('id')
            ->where('uuid', $uuid)->first();

        if ($dbDetails) {
            return $dbDetails->id;
        } else {
            return NULL;
        }
    }
}
if (!function_exists('idtouuid')) {
    function idtouuid($id, $table)
    {
        $dbDetails = DB::table($table)
            ->select('uuid')
            ->where('id', $id)->first();

        if ($dbDetails) {
            return $dbDetails->uuid;
        } else {
            return NULL;
        }
    }
}

if (!function_exists('safe_b64encode')) {
    function safe_b64encode($string)
    {
        $pretoken = "";
        $posttoken = "";

        $codealphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codealphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codealphabet .= "0123456789";
        $max = strlen($codealphabet); // edited

        for ($i = 0; $i < 3; $i++) {
            $pretoken .= $codealphabet[rand(0, $max - 1)];
        }

        for ($i = 0; $i < 3; $i++) {
            $posttoken .= $codealphabet[rand(0, $max - 1)];
        }

        $string = $pretoken . $string . $posttoken;
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }
}

if (!function_exists('safe_b64decode')) {
    function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }

        $data = base64_decode($data);
        $data = substr($data, 3);
        $data = substr($data, 0, -3);

        return $data;
    }
}

if (!function_exists('human_date')) {
    function human_date($date)
    {
        $now = Carbon::now();
        $date = Carbon::parse($date);
        return $date->diffForHumans($now);
    }
}

if (!function_exists('getCurrency')) {
    function getCurrency($userId = null)
    {
        return '$ ';
    }
}
if (!function_exists('getPhoneCode')) {
    function getPhoneCode($userId = null)
    {
        $returnData = '+ 61 ';
        if ($userId) {
            $user = User::find($userId);
            $returnData = '+ ' . $user->phone_code;
        } else {
            $returnData = '+ ' . auth()->user()->phone_code;
        }
        return $returnData;
    }
}

if (!function_exists('generateOtp')) {
    function generateOtp($digit = 4)
    {
        $generator = "1357902468";
        $result = "";
        for ($i = 1; $i <= $digit; $i++) {
            $result .= substr($generator, (rand() % (strlen($generator))), 1);
        }
        return $result;
    }
}

if (!function_exists('getRoles')) {
    function getRoles()
    {
        $data = Role::where('is_active', 1)->whereNotIn('slug', ['super-admin'])->get();
        return $data;
    }
}

// if (!function_exists('countUnReadNotificationSuperAdmin')) {
//     function countUnReadNotificationSuperAdmin()
//     {
//         $allNotificationCount = Notification::where(['is_read' => 0, 'for' => 1])->count();
//         if ($allNotificationCount) {
//             return $allNotificationCount;
//         } else {
//             return 0;
//         }
//     }
// }
// if (!function_exists('listUnReadNotificationSuperAdmin')) {
//     function listUnReadNotificationSuperAdmin()
//     {
//         $allNotification = Notification::where(['for' => 1, 'is_read' => 0])->latest()->get();
//         if ($allNotification) {
//             return $allNotification;
//         } else {
//             return array();
//         }
//     }
// }

if (!function_exists('getCategories')) {
    function getCategories()
    {
        $categories = Category::where('is_active', 1)->get();
        return $categories ?? null;
    }
}

if (!function_exists('getServiceFrequencies')) {
    function getServiceFrequencies()
    {
        $serviceFrequency = ServiceFrequency::where('is_active', 1)->get();
        return $serviceFrequency ?? null;
    }
}

if (!function_exists('increaseSongPlayCount')) {

    function increaseSongPlayCount($songId, $userId = null)
    {
        // Unique cache key
        $identifier = $userId ?? request()->ip();

        $cacheKey = "song_play_{$songId}_{$identifier}";

        // Prevent multiple increments within 30 seconds
        if (Cache::has($cacheKey)) {
            return false;
        }

        // Store cache
        Cache::put($cacheKey, true, now()->addSeconds(5));

        // Atomic DB increment
        Song::where('id', $songId)
            ->increment('play_count');

        $song = Song::find($songId);
        if ($song) {
            StreamLog::create([
                'user_id' => $userId,
                'artist_id' => $song->user_id, // assuming artist is user_id of the song
                'song_id' => $songId,
            ]);
        }

        return true;
    }
}
// if (!function_exists('userExpenseCategoryWise')) {
//     function userExpenseCategoryWise($userId = null, $month = null)
//     {
//         $month ??= Carbon::now()->month;
//         $userId ??= auth()->user()->id;
//         $categoryWiseExpense = UserExpense::where('user_id', $userId)
//             ->whereMonth('date', $month)
//             ->select('category_id', DB::raw('SUM(price) as total_expense'))
//             ->groupBy('category_id')
//             ->with('category:id,uuid,title,color_code', 'expenseRecurring')
//             ->get()
//             ->map(fn($expense) => [
//                 'category_id' => $expense->category_id,
//                 'category_uuid' => $expense->category->uuid,
//                 'title' => $expense->category->title,
//                 'total_expense' => $expense->total_expense,
//                 'color_code' => $expense->category->color_code,
//                 'expense_recurring' => $expense->expenseRecurring ?? null,
//                 'item_count' => itemCountCategoryWise($expense->category_id, $userId, $month),
//                 'item_price_count' => itemPriceCountCategoryWise($expense->category_id, $userId, $month),
//             ]);
//         return $categoryWiseExpense->toArray() ?? null;
//     }
// }

// if (!function_exists('userExpenseCategoryWiseFilter')) {
//     function userExpenseCategoryWiseFilter($userId = null, $startDate = null, $endDate = null, $isTaxable = 0, $isRecurring = 0)
//     {
//         $month = Carbon::now()->month;
//         $endDate ??= Carbon::now();
//         $startDate ??= Carbon::now()->subMonth();
//         $userId ??= auth()->user()->id;
//         $categoryWiseExpense = UserExpense::select('category_id', DB::raw('SUM(price) as total_expense'))
//             ->where('user_id', $userId)
//             ->where(function ($q) use ($startDate, $endDate, $isRecurring) {
//                 $q->where(function ($q1) use ($startDate, $endDate) {
//                     // For non-recurring expenses: validate with both start_date and end_date
//                     $q1->where('is_recurring', 0)
//                         ->whereBetween('date', [$startDate, $endDate]);
//                 })->orWhere(function ($q2) use ($endDate) {
//                     // For recurring expenses: validate only with end_date
//                     $q2->where('is_recurring', 1)
//                         ->where('date', '<=', $endDate);
//                 });
//             })
//             ->where('is_tax_deductible', $isTaxable)
//             ->groupBy('category_id')
//             ->with('category:id,uuid,title,color_code', 'expenseRecurring')
//             ->get()
//             ->map(fn($expense) => [
//                 'category_id' => $expense->category_id,
//                 'category_uuid' => $expense->category->uuid,
//                 'title' => $expense->category->title,
//                 'total_expense' => $expense->total_expense,
//                 'color_code' => $expense->category->color_code,
//                 'expense_recurring' => $expense->expenseRecurring ?? null,
//                 'item_count' => itemCountCategoryWise($expense->category_id, $userId, $month),
//                 'item_price_count' => itemPriceCountCategoryWise($expense->category_id, $userId, $month),
//             ]);
//         return $categoryWiseExpense->toArray() ?? null;
//     }
// }
// if (!function_exists('userBudgetCategoryWise')) {
//     function userBudgetCategoryWise($userId = null, $month = null, $year = null)
//     {
//         $month ??= Carbon::now()->month;
//         $year ??= Carbon::now()->year;
//         $categoryWiseBudget = UserBudget::where('user_id', $userId ?? auth()->user()->id)
//             ->where('month', $month)
//             ->where('year', $year)
//             ->groupBy('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring')
//             ->select('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring', DB::raw('SUM(budget) as total_budget'))
//             ->with(['category:id,uuid,title,color_code', 'budgetRecurring'])
//             ->get()
//             ->map(fn($budget) => [
//                 'id' => $budget->id,
//                 'uuid' => $budget->uuid,
//                 'category_id' => $budget->category_id,
//                 'category_uuid' => $budget->category->uuid,
//                 'title' => $budget->category->title,
//                 'budget' => $budget->budget,
//                 'total_budget' => $budget->total_budget,
//                 'month' => $budget->month,
//                 'year' => $budget->year,
//                 'color_code' => $budget->category->color_code,
//                 'is_recurring' => $budget->is_recurring ?? 0,
//                 'budget_recurring' => $budget->budgetRecurring ? [
//                     'id' => $budget->budgetRecurring->id,
//                     'uuid' => $budget->budgetRecurring->uuid,
//                     'start_date' => $budget->budgetRecurring?->starting_date,
//                     'end_date' => $budget->budgetRecurring?->ending_date,
//                 ] : null,
//                 'item_count' => itemCountCategoryWise($budget->category_id, $userId, $month),
//                 'item_price_count' => itemPriceCountCategoryWise($budget->category_id, $userId, $month),
//             ]);
//         return $categoryWiseBudget->toArray() ?? null;
//     }
// }
// if (!function_exists('userBudgetCategoryWise')) {
//     function userBudgetCategoryWise($userId = null, $month = null, $year = null)
//     {
//         $month ??= Carbon::now()->month;
//         $year ??= Carbon::now()->year;
//         $categoryWiseBudget = UserBudget::where('user_id', $userId ?? auth()->user()->id)
//             ->where('month', $month)
//             ->where('year', $year)
//             ->groupBy('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring')
//             ->select('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring', DB::raw('SUM(budget) as total_budget'))
//             ->with('category:id,uuid,title,color_code')
//             ->get()
//             ->map(fn($budget) => [
//                 'id' => $budget->id,
//                 'uuid' => $budget->uuid,
//                 'category_id' => $budget->category_id,
//                 'category_uuid' => $budget->category->uuid,
//                 'title' => $budget->category->title,
//                 'total_budget' => $budget->total_budget,
//                 'month' => $budget->month,
//                 'year' => $budget->year,
//                 'color_code' => $budget->category->color_code,
//                 'is_recurring' => $budget->is_recurring ?? 0,
//                 'item_count' => itemCountCategoryWise($budget->category_id, $userId, $month),
//                 'item_price_count' => itemPriceCountCategoryWise($budget->category_id, $userId, $month),
//             ]);
//         return $categoryWiseBudget->toArray() ?? null;
//     }
// }

// if (!function_exists('userBudgetCategoryWiseRange')) {
//     function userBudgetCategoryWiseRange($userId = null, $monthStart = null, $yearStart = null, $monthEnd = null, $yearEnd = null, $isRecurring = 0)
//     {
//         // dd($monthStart, $yearStart, $monthEnd, $yearEnd);
//         $monthCount = (($yearEnd - $yearStart) * 12) + ($monthEnd - $monthStart) + 1;

//         $categoryWiseBudget = UserBudget::where('user_id', $userId ?? auth()->user()->id)
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                     $q->where(function ($subQuery) use ($monthStart, $yearStart) {
//                         $subQuery->where('year', $yearStart)
//                             ->where('month', '>=', $monthStart);
//                     })->where(function ($subQuery) use ($monthEnd, $yearEnd) {
//                         $subQuery->where('year', $yearEnd)
//                             ->where('month', '<=', $monthEnd);
//                     });
//                 });
//             })
//             ->groupBy('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring')
//             ->select('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring', DB::raw('SUM(budget) as total_budget'))
//             ->with('category:id,uuid,title,color_code', 'budgetRecurring')
//             ->get()
//             ->map(fn($budget) => [
//                 'id' => $budget->id,
//                 'uuid' => $budget->uuid,
//                 'category_id' => $budget->category_id,
//                 'category_uuid' => $budget->category->uuid,
//                 'title' => $budget->category->title,
//                 'total_budget' => ($budget->is_recurring == 1) ? $budget->total_budget * $monthCount : $budget->total_budget,
//                 'monthly_budget' => $budget->total_budget,
//                 'budget' => $budget->budget,
//                 'month' => $budget->month,
//                 'year' => $budget->year,
//                 'color_code' => $budget->category->color_code,
//                 'is_recurring' => $budget->is_recurring ?? 0,
//                 'item_count' => itemCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//                 'item_price_count' => itemPriceCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//                 'budget_recurring' => $budget->budgetRecurring ? [
//                     'id' => $budget->budgetRecurring->id,
//                     'uuid' => $budget->budgetRecurring->uuid,
//                     'start_date' => $budget->budgetRecurring?->starting_date,
//                     'end_date' => $budget->budgetRecurring?->ending_date,
//                 ] : null,
//             ]);
//         return $categoryWiseBudget->toArray() ?? null;
//     }
// }

// if (!function_exists('userBudgetCategoryWiseRange')) {
//     function userBudgetCategoryWiseRange($userId = null, $monthStart = null, $yearStart = null, $monthEnd = null, $yearEnd = null, $isRecurring = 0)
//     {
//         $monthStart ??= date('m');
//         $yearStart ??= date('Y');
//         $monthEnd ??= $monthStart;
//         $yearEnd ??= $yearStart;

//         // dd($monthStart, $yearStart, $monthEnd, $yearEnd);
//         $monthCount = (($yearEnd - $yearStart) * 12) + ($monthEnd - $monthStart) + 1;

//         $categoryWiseBudget = UserBudget::where('user_id', $userId ?? auth()->user()->id)
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd, $isRecurring) {
//                 $nonRecurringLogic = function ($q) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                     $q->where(function ($q2) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                         if ($yearStart == $yearEnd) {
//                             $q2->where('year', $yearStart)
//                                 ->whereBetween('month', [$monthStart, $monthEnd]);
//                         } else {
//                             $q2->where(function ($sq1) use ($monthStart, $yearStart) {
//                                 $sq1->where('year', $yearStart)
//                                     ->where('month', '>=', $monthStart);
//                             })->orWhere(function ($sq2) use ($monthEnd, $yearEnd) {
//                                 $sq2->where('year', $yearEnd)
//                                     ->where('month', '<=', $monthEnd);
//                             })->orWhere(function ($sq3) use ($yearStart, $yearEnd) {
//                                 $sq3->whereBetween('year', [$yearStart + 1, $yearEnd - 1]);
//                             });
//                         }
//                     });
//                 };

//                 $recurringLogic = function ($q) use ($monthEnd, $yearEnd) {
//                     $q->where('is_recurring', 1)
//                         ->where(function ($d) use ($monthEnd, $yearEnd) {
//                             $d->where('year', '<', $yearEnd)
//                                 ->orWhere(function ($sd) use ($monthEnd, $yearEnd) {
//                                     $sd->where('year', $yearEnd)
//                                         ->where('month', '<=', $monthEnd);
//                                 });
//                         });
//                 };

//                 if ($isRecurring == 1) {
//                     $recurringLogic($query);
//                 } elseif ($isRecurring == 2) {
//                     $query->where(function ($q) use ($nonRecurringLogic, $recurringLogic) {
//                         $q->where(function ($sq) use ($nonRecurringLogic) {
//                             $nonRecurringLogic($sq);
//                         })->orWhere(function ($sq) use ($recurringLogic) {
//                             $recurringLogic($sq);
//                         });
//                     });
//                 } else {
//                     // Default to non-recurring only
//                     $nonRecurringLogic($query);
//                 }
//             })
//             ->groupBy('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring')
//             ->select('id', 'uuid', 'category_id', 'month', 'year', 'is_recurring', DB::raw('SUM(budget) as total_budget'))
//             ->with('category:id,uuid,title,color_code', 'budgetRecurring')
//             ->get()
//             ->map(fn($budget) => [
//                 'id' => $budget->id,
//                 'uuid' => $budget->uuid,
//                 'category_id' => $budget->category_id,
//                 'category_uuid' => $budget->category->uuid,
//                 'title' => $budget->category->title,
//                 'total_budget' => ($budget->is_recurring == 1) ? $budget->total_budget * $monthCount : $budget->total_budget,
//                 'monthly_budget' => $budget->total_budget,
//                 'budget' => $budget->budget,
//                 'month' => $budget->month,
//                 'year' => $budget->year,
//                 'color_code' => $budget->category->color_code,
//                 'is_recurring' => $budget->is_recurring ?? 0,
//                 'item_count' => itemCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//                 'item_price_count' => itemPriceCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//                 'budget_recurring' => $budget->budgetRecurring ? [
//                     'id' => $budget->budgetRecurring->id,
//                     'uuid' => $budget->budgetRecurring->uuid,
//                     'start_date' => $budget->budgetRecurring?->starting_date,
//                     'end_date' => $budget->budgetRecurring?->ending_date,
//                 ] : null,
//             ]);
//         return $categoryWiseBudget->toArray() ?? null;
//     }
// }



// if (!function_exists('userBudgetCategoryWiseRange')) {
//     function userBudgetCategoryWiseRange($userId = null, $monthStart = null, $yearStart = null, $monthEnd = null, $yearEnd = null)
//     {
//         // dd($monthStart, $yearStart, $monthEnd, $yearEnd);
//         $categoryWiseBudget = UserBudget::where('user_id', $userId ?? auth()->user()->id)
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                     $q->where(function ($subQuery) use ($monthStart, $yearStart) {
//                         $subQuery->where('year', $yearStart)
//                             ->where('month', '>=', $monthStart);
//                     })->where(function ($subQuery) use ($monthEnd, $yearEnd) {
//                         $subQuery->where('year', $yearEnd)
//                             ->where('month', '<=', $monthEnd);
//                     });
//                 });
//             })
//             ->groupBy('uuid', 'category_id', 'month', 'year', 'is_recurring')
//             ->select('uuid', 'category_id', 'month', 'year', 'is_recurring', DB::raw('SUM(budget) as total_budget'))
//             ->with('category:id,uuid,title,color_code')
//             ->get()
//             ->map(fn($budget) => [
//                 'uuid' => $budget->uuid,
//                 'category_id' => $budget->category_id,
//                 'category_uuid' => $budget->category->uuid,
//                 'title' => $budget->category->title,
//                 'total_budget' => $budget->total_budget,
//                 'month' => $budget->month,
//                 'year' => $budget->year,
//                 'color_code' => $budget->category->color_code,
//                 'is_recurring' => $budget->is_recurring ?? 0,
//                 'item_count' => itemCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//                 'item_price_count' => itemPriceCountCategoryWiseRange($budget->category_id, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd),
//             ]);
//         return $categoryWiseBudget->toArray() ?? null;
//     }
// }

// if (!function_exists('itemCountCategoryWiseRange')) {
//     function itemCountCategoryWiseRange($categoryId, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd)
//     {
//         $expanseCount = UserExpense::where('user_id', $userId)
//             ->where('category_id', $categoryId)
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart) {
//                     $q->whereYear('date', $yearStart)
//                         ->whereMonth('date', '>=', $monthStart);
//                 })->orWhere(function ($q) use ($monthEnd, $yearEnd) {
//                     $q->whereYear('date', $yearEnd)
//                         ->whereMonth('date', '<=', $monthEnd);
//                 })->orWhere(function ($q) use ($yearStart, $yearEnd) {
//                     $q->whereBetween(DB::raw('YEAR(date)'), [$yearStart + 1, $yearEnd - 1]);
//                 });
//             })
//             ->count();

//         $itemCount = Item::where(['user_id' => $userId, 'category_id' => $categoryId, 'is_expense' => 1])
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart) {
//                     $q->whereYear('date', $yearStart)
//                         ->whereMonth('date', '>=', $monthStart);
//                 })->orWhere(function ($q) use ($monthEnd, $yearEnd) {
//                     $q->whereYear('date', $yearEnd)
//                         ->whereMonth('date', '<=', $monthEnd);
//                 })->orWhere(function ($q) use ($yearStart, $yearEnd) {
//                     $q->whereBetween(DB::raw('YEAR(date)'), [$yearStart + 1, $yearEnd - 1]);
//                 });
//             })
//             ->count();

//         return ((int) $expanseCount + (int) $itemCount) ?? 0;
//     }
// }

// if (!function_exists('itemPriceCountCategoryWiseRange')) {
//     function itemPriceCountCategoryWiseRange($categoryId, $userId, $monthStart, $yearStart, $monthEnd, $yearEnd)
//     {
//         $expanseTotalPrice = UserExpense::where('user_id', $userId)
//             ->where('category_id', $categoryId)
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart) {
//                     $q->whereYear('date', $yearStart)
//                         ->whereMonth('date', '>=', $monthStart);
//                 })->orWhere(function ($q) use ($monthEnd, $yearEnd) {
//                     $q->whereYear('date', $yearEnd)
//                         ->whereMonth('date', '<=', $monthEnd);
//                 })->orWhere(function ($q) use ($yearStart, $yearEnd) {
//                     $q->whereBetween(DB::raw('YEAR(date)'), [$yearStart + 1, $yearEnd - 1]);
//                 });
//             })
//             ->sum('price');

//         $itemTotalPrice = Item::where(['user_id' => $userId, 'category_id' => $categoryId, 'is_expense' => 1])
//             ->where(function ($query) use ($monthStart, $yearStart, $monthEnd, $yearEnd) {
//                 $query->where(function ($q) use ($monthStart, $yearStart) {
//                     $q->whereYear('date', $yearStart)
//                         ->whereMonth('date', '>=', $monthStart);
//                 })->orWhere(function ($q) use ($monthEnd, $yearEnd) {
//                     $q->whereYear('date', $yearEnd)
//                         ->whereMonth('date', '<=', $monthEnd);
//                 })->orWhere(function ($q) use ($yearStart, $yearEnd) {
//                     $q->whereBetween(DB::raw('YEAR(date)'), [$yearStart + 1, $yearEnd - 1]);
//                 });
//             })
//             ->sum('price');

//         return ((float) $expanseTotalPrice + (float) $itemTotalPrice) ?? 0;
//     }
// }

// if (!function_exists('itemCountCategoryWise')) {
//     function itemCountCategoryWise($categoryId, $userId, $month)
//     {
//         $expanseCount = UserExpense::where('user_id', $userId)
//             ->where('category_id', $categoryId)
//             ->whereMonth('date', $month)
//             ->count();

//         $itemCount = Item::where(['user_id' => $userId, 'category_id' => $categoryId, 'is_expense' => 1])->whereMonth('date', $month)->count();

//         return ((int) $expanseCount + (int) $itemCount) ?? 0;
//     }
// }
// if (!function_exists('itemPriceCountCategoryWise')) {
//     function itemPriceCountCategoryWise($categoryId, $userId, $month)
//     {
//         $expanseTotalPrice = UserExpense::where('user_id', $userId)
//             ->where('category_id', $categoryId)
//             ->whereMonth('date', $month)
//             ->sum('price');

//         $itemTotalPrice = Item::where(['user_id' => $userId, 'category_id' => $categoryId, 'is_expense' => 1])->whereMonth('date', $month)->sum('price');

//         return ((float) $expanseTotalPrice + (float) $itemTotalPrice) ?? 0;
//     }
// }

// if (!function_exists('userSubscription')) {
//     function userSubscription($userId = null)
//     {
//         $userId ??= auth()->user()->id;
//         $userSubscription = UserSubscription::where(['user_id' => $userId, 'is_active' => 1])
//             ->where('start_date', '<=', Carbon::now())
//             ->where('end_date', '>=', Carbon::now())
//             ->first();
//         return $userSubscription ?? null;
//     }
// }

// if (!function_exists('userSubscriptionIsActive')) {
//     function userSubscriptionIsActive($userId = null)
//     {
//         $userId ??= auth()->user()->id;
//         $userSubscription = UserSubscription::where(['user_id' => $userId, 'is_active' => 1])
//             ->where('start_date', '<=', Carbon::now())
//             ->where('end_date', '>=', Carbon::now())
//             ->where('remaining_activity_count', '>', 0)
//             ->exists();
//         return $userSubscription ?? false;
//     }
// }
// if (!function_exists('userSubscriptionActivity')) {
//     function userSubscriptionActivity($userId = null)
//     {
//         $userSubscription = userSubscription($userId ?? null);
//         $userSubscription->update([
//             'used_activity_count' => $userSubscription->used_activity_count + 1,
//             'remaining_activity_count' => $userSubscription->remaining_activity_count - 1
//         ]);
//     }
// }
if (!function_exists('isActiveSubscription')) {
    function isActiveSubscription($subscriptionId)
    {
        $returnData = true;
        $userActiveSubscription = UserSubscription::where('subscription_id', $subscriptionId)->where('started_on', '<=', Carbon::now()->format('Y-m-d'))->where('ended_at', '>=', Carbon::now()->format('Y-m-d'))->first();
        if ($userActiveSubscription) {
            $returnData = false;
        }
        return $returnData;
    }
}

// if (!function_exists('addFreeSubscription')) {
//     function addFreeSubscription($userId)
//     {
//         $subscription = Subscription::latest()->where(['price' => 0, 'is_active' => 1])->first();
//         $startDate = Carbon::now();
//         if ($subscription) {
//             switch ($subscription->type) {
//                 case 1:
//                     $endDate = $startDate->copy()->addMonth();
//                     break;
//                 case 2:
//                     $endDate = $startDate->copy()->addMonths(3);
//                     break;
//                 case 3:
//                     $endDate = $startDate->copy()->addMonths(6);
//                     break;
//                 case 4:
//                     $endDate = $startDate->copy()->addYear();
//                     break;
//                 default:
//                     throw new \Exception('Invalid subscription type');
//             }

//             $subscriptionCreated = UserSubscription::create([
//                 'user_id' => $userId,
//                 'transaction_id' => null,
//                 'subscription_id' => $subscription->id,
//                 'start_date' => $startDate->format('Y-m-d'),
//                 'end_date' => $endDate->format('Y-m-d'),
//                 'total_activity_count' => $subscription->activity_count,
//                 'remaining_activity_count' => $subscription->activity_count,
//                 'used_activity_count' => 0,
//             ]);

//             return $subscriptionCreated ? true : false;
//         }
//     }
// }

// if (!function_exists('getUserIncomeMonthWise')) {
//     function getUserIncomeMonthWise($month = 12)
//     {
//         $meditation = UserIncome::where(['user_id' => auth()->user()->id])
//             ->where('date', '>=', Carbon::now()->subMonths((int) $month))
//             ->latest()->get();

//         // dd($meditation->toArray());

//         $combinedData = [];
//         foreach ($meditation as $data) {
//             $monthYear = Carbon::parse($data['date'])->format('M y');
//             if (isset($combinedData[$monthYear])) {
//                 $combinedData[$monthYear] += ($data['amount']);
//             } else {
//                 $combinedData[$monthYear] = ($data['amount']);
//             }
//         }

//         $dates = range(0, - ($month - 1));
//         $formattedData = array_combine(array_map(fn($d) => Carbon::now()->addMonths($d)->format('M y'), $dates), array_fill(0, $month, 0));


//         foreach ($formattedData as $date => $value) {
//             if (isset($combinedData[$date])) {
//                 $formattedData[$date] = $combinedData[$date];
//             }
//         }

//         return array_map(fn($date, $count) => ['value' => (int) $count, 'label' => $date], array_keys($formattedData), $formattedData);
//     }
// }

// if (!function_exists('sendSms')) {
//     function sendSms($mobile, $otp)
//     {
//         try {
//             $accountSid = env('TWILIO_ACCOUNT_SID');
//             $authToken = env('TWILIO_AUTH_TOKEN');
//             $client = new Client($accountSid, $authToken);

//             $client->messages->create(
//                 $mobile,
//                 ['from' => env('TWILIO_FROM_NUMBER'), 'body' => "Your OTP is $otp"]
//             );
//         } catch (Exception $e) {
//             Log::error($e->getMessage());
//         }
//     }
// }

if (!function_exists('logAudience')) {
    function logAudience($artistId = null, $albumId = null, $songId = null)
    {
        $request = request();
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();

        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }
        
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            $locationData = \Illuminate\Support\Facades\Cache::remember('ip_location_' . $ip, 86400, function () use ($ip) {
                $response = \Illuminate\Support\Facades\Http::get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");
                if ($response->successful() && $response->json('status') === 'success') {
                    return $response->json();
                }
                return null;
            });

            \App\Models\AudienceLog::create([
                'ip_address' => $ip,
                'country' => $locationData['country'] ?? null,
                'city' => $locationData['city'] ?? null,
                'region' => $locationData['regionName'] ?? null,
                'latitude' => $locationData['lat'] ?? null,
                'longitude' => $locationData['lon'] ?? null,
                'user_id' => auth()->check() ? auth()->id() : null,
                'artist_id' => $artistId,
                'album_id' => $albumId,
                'song_id' => $songId,
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
            ]);
        }
    }
}
