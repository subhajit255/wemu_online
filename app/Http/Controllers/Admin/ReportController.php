<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Song;
use App\Models\Album;
use App\Models\StreamLog;
use App\Models\AudienceLog;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ReportController extends BaseController
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        $dateFilter = null;
        
        switch ($period) {
            case 'today':
                $dateFilter = [now()->startOfDay(), now()->endOfDay()];
                break;
            case 'this_week':
                $dateFilter = [now()->startOfWeek(), now()->endOfWeek()];
                break;
            case 'this_month':
                $dateFilter = [now()->startOfMonth(), now()->endOfMonth()];
                break;
            case 'this_year':
                $dateFilter = [now()->startOfYear(), now()->endOfYear()];
                break;
        }

        // 1. User Stats
        $artistQuery = User::where('user_type', 3);
        $listenerQuery = User::where('user_type', '!=', 1)->where('user_type', '!=', 3);
        
        if ($dateFilter) {
            $artistQuery->whereBetween('created_at', $dateFilter);
            $listenerQuery->whereBetween('created_at', $dateFilter);
        }
        
        $totalArtists = $artistQuery->count();
        $totalListeners = $listenerQuery->count(); 

        // 2. Financials
        $totalRevenue = 0;
        $thisMonthRevenue = 0;
        if (class_exists(Transaction::class)) {
            try {
                $revenueQuery = Transaction::where('payment_status', 'completed');
                if ($dateFilter) {
                    $revenueQuery->whereBetween('created_at', $dateFilter);
                }
                $totalRevenue = $revenueQuery->sum('amount');
                
                $thisMonthRevenue = Transaction::where('payment_status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
            } catch (\Exception $e) {
                // If column doesn't exist, ignore
            }
        }

        // 3. Most Liked Songs
        $likedQuery = \App\Models\SongLike::select('song_id', DB::raw('count(*) as total_likes'));
        if ($dateFilter) {
            $likedQuery->whereBetween('created_at', $dateFilter);
        }
        $mostLikedSongs = $likedQuery->groupBy('song_id')
            ->orderBy('total_likes', 'desc')
            ->with('song.artist')
            ->take(10)
            ->get();

        // 4. Most Followed Artists
        $followerQuery = \App\Models\ArtistFollower::select('artist_id', DB::raw('count(*) as total_followers'));
        if ($dateFilter) {
            $followerQuery->whereBetween('created_at', $dateFilter);
        }
        $mostFollowedArtists = $followerQuery->groupBy('artist_id')
            ->orderBy('total_followers', 'desc')
            ->with('artist')
            ->take(10)
            ->get();

        // 5. Top Searches
        $searchQuery = \App\Models\SearchHistory::select('keyword', DB::raw('count(*) as search_count'))
            ->whereNotNull('keyword')
            ->where('keyword', '!=', '');
        if ($dateFilter) {
            $searchQuery->whereBetween('created_at', $dateFilter);
        }
        $topSearches = $searchQuery->groupBy('keyword')
            ->orderBy('search_count', 'desc')
            ->take(10)
            ->get();

        // 6. Graph Data - Current Month Daily Revenue
        $daysInMonth = \Carbon\Carbon::now()->daysInMonth;
        $dailyRevenueData = array_fill(1, $daysInMonth, 0); 
        $monthLabels = range(1, $daysInMonth);

        if (class_exists(Transaction::class)) {
            try {
                $currentMonthData = Transaction::where('payment_status', 'completed')
                    ->whereMonth('created_at', \Carbon\Carbon::now()->month)
                    ->whereYear('created_at', \Carbon\Carbon::now()->year)
                    ->select(
                        DB::raw('DAY(created_at) as day'),
                        DB::raw('SUM(amount) as total')
                    )
                    ->groupBy('day')
                    ->get();

                foreach ($currentMonthData as $data) {
                    $dailyRevenueData[$data->day] = (float) $data->total;
                }
            } catch (\Exception $e) {}
        }

        // 7. Graph Data - Current Year Monthly Revenue
        $monthlyRevenueData = array_fill(1, 12, 0); 
        $yearLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        if (class_exists(Transaction::class)) {
            try {
                $currentYearData = Transaction::where('payment_status', 'completed')
                    ->whereYear('created_at', \Carbon\Carbon::now()->year)
                    ->select(
                        DB::raw('MONTH(created_at) as month'),
                        DB::raw('SUM(amount) as total')
                    )
                    ->groupBy('month')
                    ->get();

                foreach ($currentYearData as $data) {
                    $monthlyRevenueData[$data->month] = (float) $data->total;
                }
            } catch (\Exception $e) {}
        }

        return view('admin.report.index', compact(
            'totalArtists', 'totalListeners', 
            'totalRevenue', 'thisMonthRevenue',
            'mostLikedSongs', 'mostFollowedArtists', 'topSearches',
            'period', 'dailyRevenueData', 'monthLabels', 'monthlyRevenueData', 'yearLabels'
        ));
    }
}
