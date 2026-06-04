<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class AnalyticsController extends BaseController
{
    public function index()
    {
        // 1. Subscription Statistics
        $userSubscriptionCount = UserSubscription::whereHas('subscription', function($q) {
            $q->where('available_for', 1);
        })->where('status', 1)->count();

        $artistSubscriptionCount = UserSubscription::whereHas('subscription', function($q) {
            $q->where('available_for', 2);
        })->where('status', 1)->count();

        $totalActiveSubscriptions = $userSubscriptionCount + $artistSubscriptionCount;

        // 2. Revenue Statistics
        $totalRevenue = Transaction::where('payment_status', 'completed')->sum('amount');
        
        $currentMonthRevenue = Transaction::where('payment_status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        // 3. Graph Data - Current Month Daily Revenue
        $daysInMonth = Carbon::now()->daysInMonth;
        $dailyRevenueData = array_fill(1, $daysInMonth, 0); // Initialize all days with 0

        $currentMonthData = Transaction::where('payment_status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('DAY(created_at) as day'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('day')
            ->get();

        foreach ($currentMonthData as $data) {
            $dailyRevenueData[$data->day] = (float) $data->total;
        }

        // 4. Graph Data - Current Year Monthly Revenue
        $monthlyRevenueData = array_fill(1, 12, 0); // Initialize 12 months with 0

        $currentYearData = Transaction::where('payment_status', 'completed')
            ->whereYear('created_at', Carbon::now()->year)
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->get();

        foreach ($currentYearData as $data) {
            $monthlyRevenueData[$data->month] = (float) $data->total;
        }

        // Prepare labels for graphs
        $monthLabels = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $monthLabels[] = $i;
        }
        
        $yearLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        return view('admin.analytics.index', compact(
            'userSubscriptionCount',
            'artistSubscriptionCount',
            'totalActiveSubscriptions',
            'totalRevenue',
            'currentMonthRevenue',
            'dailyRevenueData',
            'monthlyRevenueData',
            'monthLabels',
            'yearLabels'
        ));
    }
}
