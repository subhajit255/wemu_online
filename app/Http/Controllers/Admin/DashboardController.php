<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function dashboard()
    {
        $totalUserCount = User::where('user_type', 3)->whereNull('added_by')->count();
        $totalTodaysUserCount = User::where('user_type', 3)->whereNull('added_by')->whereDate('created_at', date('Y-m-d'))->count();
        // $totalItemsCount = Item::count();
        // $totalTodayItemsCount = Item::whereDate('created_at', date('Y-m-d'))->count();
        // $totalCategoriesCount = Category::count();
        // $totalTodayCategoriesCount = Category::whereDate('created_at', date('Y-m-d'))->count();
        // $totalUserGoalCount = UserGoal::count();
        // $totalUserGoalTaskCount = UserGoalTask::count();
        // $totalIncome = Transaction::where('payment_status', 2)->sum('amount');
        // $totalIncomeThisMonth = Transaction::where('payment_status', 2)
        //     ->whereBetween('created_at', [
        //         now()->startOfMonth()->toDateTimeString(),
        //         now()->endOfMonth()->toDateTimeString()
        //     ])
        //     ->sum('amount');
        // $transactions = Transaction::where('payment_status', 2)->latest()->paginate(10);
        // return view('admin.dashboard', compact('totalUserCount', 'totalTodaysUserCount', 'totalItemsCount', 'totalTodayItemsCount', 'totalCategoriesCount', 'totalTodayCategoriesCount', 'totalUserGoalCount', 'totalUserGoalTaskCount', 'transactions', 'totalIncome', 'totalIncomeThisMonth'));
        
        $pendingArtists = User::where('user_type', 3)->whereNull('added_by')->where('is_approve', 0)->with('profile.primaryGenre')->latest()->take(3)->get();
        $pendingArtistsCount = User::where('user_type', 3)->whereNull('added_by')->where('is_approve', 0)->count();

        return view('admin.dashboard', compact('totalUserCount', 'totalTodaysUserCount', 'pendingArtists', 'pendingArtistsCount'));
    }
}
