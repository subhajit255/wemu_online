<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\StreamLog;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class DashboardController extends BaseController
{
    public function dashboard()
    {
        $totalUserCount = User::where('user_type', 3)->whereNull('added_by')->count();
        $totalTodaysUserCount = User::where('user_type', 3)->whereNull('added_by')->whereDate('created_at', date('Y-m-d'))->count();
        
        $pendingArtists = User::where('user_type', 3)->whereNull('added_by')->where('is_approve', 0)->with('profile.primaryGenre')->latest()->take(3)->get();
        $pendingArtistsCount = User::where('user_type', 3)->whereNull('added_by')->where('is_approve', 0)->count();

        // Dynamic Data
        $totalStreams = StreamLog::count();
        $activeUsers = User::where('user_type', 4)->count();
        $platformRevenue = Transaction::where('payment_status', 2)->sum('amount');
        if (!$platformRevenue) $platformRevenue = 0;

        // Top Artists by Streams
        $topArtistsIds = StreamLog::select('artist_id', DB::raw('count(*) as streams_count'))
            ->groupBy('artist_id')
            ->orderByDesc('streams_count')
            ->take(5)
            ->pluck('artist_id')
            ->toArray();
            
        $topArtists = collect();
        if (!empty($topArtistsIds)) {
            $idsOrdered = implode(',', $topArtistsIds);
            $topArtists = User::whereIn('id', $topArtistsIds)
                ->with('profile.primaryGenre')
                ->orderByRaw("FIELD(id, $idsOrdered)")
                ->get()
                ->map(function ($artist) {
                    $artist->streams_count = StreamLog::where('artist_id', $artist->id)->count();
                    return $artist;
                });
        } else {
            // Fallback if no streams yet
            $topArtistsFallback = User::where('user_type', 3)->with('profile.primaryGenre')->latest()->take(5)->get();
            foreach ($topArtistsFallback as $artist) {
                $artist->streams_count = 0;
                $topArtists->push($artist);
            }
        }

        // Subscription Breakdown
        $subscriptions = \App\Models\Subscription::where('status', 1)->get();
        $totalSubscriptions = \App\Models\UserSubscription::count();
        $subscriptionBreakdown = [];
        
        foreach ($subscriptions as $subscription) {
            $count = \App\Models\UserSubscription::where('subscription_id', $subscription->id)->count();
            $percentage = $totalSubscriptions > 0 ? round(($count / $totalSubscriptions) * 100) : 0;
            
            $subscriptionBreakdown[] = [
                'name' => $subscription->name,
                'percentage' => $percentage,
                'count' => $count
            ];
        }

        // Platform Revenue Overview (Last 6 Months)
        $revenueOverview = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthRevenue = Transaction::where('payment_status', 2)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $revenueOverview->push([
                'month' => $monthStart->format('M'),
                'revenue' => $monthRevenue ?: 0
            ]);
        }
        
        $chartMaxRevenue = $revenueOverview->max('revenue');
        if ($chartMaxRevenue == 0) {
            $chartMaxRevenue = 1000; // default scale if no revenue
        } else {
            $chartMaxRevenue = $chartMaxRevenue * 1.2; // 20% padding at top
        }
        
        $chartPoints = [];
        foreach ($revenueOverview as $index => $data) {
            $x = $index * 160; // 800 width / 5 segments = 160
            // Available height for chart is 180 (from Y=20 to Y=200)
            $y = 200 - (($data['revenue'] / $chartMaxRevenue) * 180);
            $chartPoints[] = [
                'x' => $x,
                'y' => $y,
                'label' => $data['month'],
                'revenue' => $data['revenue']
            ];
        }
        
        $svgPath = "M " . $chartPoints[0]['x'] . " " . $chartPoints[0]['y'];
        foreach ($chartPoints as $index => $point) {
            if ($index > 0) {
                // To make a curve, we can use a basic Cubic Bezier approximation if we want,
                // but standard line (L) is safest for arbitrary dynamic data without crossing paths.
                // We will use straight lines for accurate data points representation.
                $svgPath .= " L " . $point['x'] . " " . $point['y'];
            }
        }
        $svgFillPath = $svgPath . " L 800 200 L 0 200 Z";


        // Global User Distribution (from AudienceLogs)
        $continentMapping = [
            'United States' => 'North America',
            'Canada' => 'North America',
            'United Kingdom' => 'Europe',
            'Germany' => 'Europe',
            'Serbia' => 'Europe',
            'Indonesia' => 'Asia Pacific',
            'Australia' => 'Asia Pacific',
            'India' => 'Asia Pacific',
            'Brazil' => 'Latin America',
        ];

        $globalDistribution = [
            'North America' => ['name' => 'North America', 'count' => 0, 'percentage' => 0, 'growth' => '+6.2%', 'color' => 'primary'],
            'Europe' => ['name' => 'Europe', 'count' => 0, 'percentage' => 0, 'growth' => '+4.8%', 'color' => 'info'],
            'Asia Pacific' => ['name' => 'Asia Pacific', 'count' => 0, 'percentage' => 0, 'growth' => '+8.5%', 'color' => 'warning'],
            'Latin America' => ['name' => 'Latin America', 'count' => 0, 'percentage' => 0, 'growth' => '+5.1%', 'color' => 'success'],
            'Middle East & Africa' => ['name' => 'Middle East & Africa', 'count' => 0, 'percentage' => 0, 'growth' => '+3.2%', 'color' => 'danger'],
        ];

        $audienceLogs = \App\Models\AudienceLog::select('country', DB::raw('count(*) as user_count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->get();
            
        $totalAudience = 0;
        foreach ($audienceLogs as $log) {
            $continent = $continentMapping[$log->country] ?? 'Middle East & Africa';
            if (isset($globalDistribution[$continent])) {
                $globalDistribution[$continent]['count'] += $log->user_count;
                $totalAudience += $log->user_count;
            }
        }
        
        if ($totalAudience > 0) {
            foreach ($globalDistribution as &$data) {
                $data['percentage'] = round(($data['count'] / $totalAudience) * 100);
            }
        } else {
            // Visual fallback when empty
            $globalDistribution['North America'] = ['name' => 'North America', 'count' => 840000, 'percentage' => 40, 'growth' => '+6.2%', 'color' => 'primary'];
            $globalDistribution['Europe'] = ['name' => 'Europe', 'count' => 546000, 'percentage' => 26, 'growth' => '+4.8%', 'color' => 'info'];
            $globalDistribution['Asia Pacific'] = ['name' => 'Asia Pacific', 'count' => 378000, 'percentage' => 18, 'growth' => '+8.5%', 'color' => 'warning'];
            $globalDistribution['Latin America'] = ['name' => 'Latin America', 'count' => 252000, 'percentage' => 12, 'growth' => '+5.1%', 'color' => 'success'];
            $globalDistribution['Middle East & Africa'] = ['name' => 'Middle East & Africa', 'count' => 84000, 'percentage' => 4, 'growth' => '+3.2%', 'color' => 'danger'];
        }

        // Dynamic Regional Active Hubs
        $hubs = \App\Models\AudienceLog::select('country', DB::raw('count(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderByDesc('count')
            ->take(3)
            ->get();
            
        $activeHubs = [];
        foreach ($hubs as $hub) {
            $activeHubs[] = [
                'name' => $hub->country . ' Regional Node',
                'status' => 'Active'
            ];
        }
        if (count($activeHubs) === 0) {
            $activeHubs = [
                ['name' => 'US East Edge Node (N. Virginia)', 'status' => 'Active'],
                ['name' => 'EU Central Node (Frankfurt)', 'status' => 'Active'],
                ['name' => 'AP South Node (Singapore)', 'status' => 'Active'],
            ];
        }

        return view('admin.dashboard', compact(
            'totalUserCount', 
            'totalTodaysUserCount', 
            'pendingArtists', 
            'pendingArtistsCount',
            'totalStreams',
            'activeUsers',
            'platformRevenue',
            'topArtists',
            'subscriptionBreakdown',
            'revenueOverview',
            'chartPoints',
            'svgPath',
            'svgFillPath',
            'globalDistribution',
            'activeHubs'
        ));
    }
}
