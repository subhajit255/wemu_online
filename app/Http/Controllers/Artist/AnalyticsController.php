<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use App\Models\Artist;
use App\Models\StreamLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->added_by) {
                $perms = $user->permissions ? json_decode($user->permissions, true) : [];
                if (!is_array($perms)) $perms = [];
                if (!in_array('analytics', $perms)) {
                    abort(403, 'Unauthorized access.');
                }
            }
            return $next($request);
        });
    }

    public function index()
    {
        return view('artist.analytics.index');
    }

    public function streamsChart(Request $request)
    {
        try {
            $user = auth()->user();
            $userId = $user ? $user->id : 0;
            $mainArtistId = $user && $user->added_by ? $user->added_by : $userId;
            $teamIds = \App\Models\User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

            $filter = $request->query('filter', '1'); // '1' or 'this_month', '3' or 'this_year', 'last_7_days'

            $query = StreamLog::whereIn('artist_id', $teamIds);

            if ($filter === '3' || $filter === 'this_year') { // This Year
                $query->whereYear('created_at', Carbon::now()->year);

                $logs = $query->select(
                    DB::raw('MONTH(created_at) as period'),
                    DB::raw('COUNT(*) as total_streams')
                )->groupBy('period')->orderBy('period')->get();

                // Format data points
                $labels = [];
                $dataPoints = [];
                $totalCount = 0;
                foreach ($logs as $log) {
                    $monthName = Carbon::createFromFormat('m', str_pad($log->period, 2, '0', STR_PAD_LEFT))->format('M');
                    $labels[] = $monthName;
                    $dataPoints[] = $log->total_streams;
                    $totalCount += $log->total_streams;
                }
            } elseif ($filter === '1' || $filter === 'this_month') {
                // This month
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);

                $logs = $query->select(
                    DB::raw('DATE(created_at) as period'),
                    DB::raw('COUNT(*) as total_streams')
                )->groupBy('period')->orderBy('period')->get();

                // Format data points
                $labels = [];
                $dataPoints = [];
                $totalCount = 0;
                foreach ($logs as $log) {
                    $dateName = Carbon::parse($log->period)->format('M j');
                    $labels[] = $dateName;
                    $dataPoints[] = $log->total_streams;
                    $totalCount += $log->total_streams;
                }
            } else {
                // last_7_days
                $query->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay());
                
                $logs = $query->select(
                    DB::raw('DATE(created_at) as period'),
                    DB::raw('COUNT(*) as total_streams')
                )->groupBy('period')->orderBy('period')->get()->pluck('total_streams', 'period');

                $labels = [];
                $dataPoints = [];
                $totalCount = 0;
                for ($i = 6; $i >= 0; $i--) {
                    $dateStr = Carbon::now()->subDays($i)->format('Y-m-d');
                    $labels[] = Carbon::now()->subDays($i)->format('M d');
                    $val = $logs->get($dateStr, 0);
                    $dataPoints[] = $val;
                    $totalCount += $val;
                }
            }

            // Calculate growth percentage
            if ($filter === '3' || $filter === 'this_year') { // This Year vs Last Year
                $previousPeriodCount = StreamLog::whereIn('artist_id', $teamIds)
                    ->whereYear('created_at', Carbon::now()->subYear()->year)
                    ->count();
            } elseif ($filter === '1' || $filter === 'this_month') { // This Month vs Last Month
                $previousPeriodCount = StreamLog::whereIn('artist_id', $teamIds)
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', \Illuminate\Support\Carbon::now()->subMonth()->year)
                    ->count();
            } else { // Last 7 days vs previous 7 days
                $previousPeriodCount = StreamLog::whereIn('artist_id', $teamIds)
                    ->where('created_at', '>=', Carbon::now()->subDays(13)->startOfDay())
                    ->where('created_at', '<', Carbon::now()->subDays(6)->startOfDay())
                    ->count();
            }

            $growth = 0;
            if ($previousPeriodCount > 0) {
                $growth = (($totalCount - $previousPeriodCount) / $previousPeriodCount) * 100;
            } elseif ($totalCount > 0) {
                $growth = 100; // From 0 to something is a 100% increase conceptually
            }

            $growthFormatted = number_format($growth, 1);
            $isPositive = $growth >= 0;
            $growthPercentage = ($isPositive ? '+' : '') . $growthFormatted . '%';

            $data = [
                'total_streams' => $totalCount,
                'growth_percentage' => $growthPercentage,
                'is_positive' => $isPositive,
                'chart' => [
                    'labels' => $labels,
                    'data' => $dataPoints,
                ]
            ];

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
