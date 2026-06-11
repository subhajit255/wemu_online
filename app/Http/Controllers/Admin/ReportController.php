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
        // 1. User Stats
        $totalArtists = User::where('user_type', 3)->count();
        $totalListeners = User::where('user_type', '!=', 1)->where('user_type', '!=', 3)->count(); 

        // 2. Financials
        $totalRevenue = 0;
        $thisMonthRevenue = 0;
        if (class_exists(Transaction::class)) {
            try {
                $totalRevenue = Transaction::where('payment_status', 2)->sum('amount');
                $thisMonthRevenue = Transaction::where('payment_status', 2)
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('amount');
            } catch (\Exception $e) {
                // If column doesn't exist, ignore
            }
        }

        // 3. Most Liked Songs
        $mostLikedSongs = \App\Models\SongLike::select('song_id', DB::raw('count(*) as total_likes'))
            ->groupBy('song_id')
            ->orderBy('total_likes', 'desc')
            ->with('song.artist')
            ->take(10)
            ->get();

        // 4. Most Followed Artists
        $mostFollowedArtists = \App\Models\ArtistFollower::select('artist_id', DB::raw('count(*) as total_followers'))
            ->groupBy('artist_id')
            ->orderBy('total_followers', 'desc')
            ->with('artist')
            ->take(10)
            ->get();

        // 5. Top Searches
        $topSearches = \App\Models\SearchHistory::select('keyword', DB::raw('count(*) as search_count'))
            ->whereNotNull('keyword')
            ->where('keyword', '!=', '')
            ->groupBy('keyword')
            ->orderBy('search_count', 'desc')
            ->take(10)
            ->get();

        return view('admin.report.index', compact(
            'totalArtists', 'totalListeners', 
            'totalRevenue', 'thisMonthRevenue',
            'mostLikedSongs', 'mostFollowedArtists', 'topSearches'
        ));
    }
}
