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
            $artistId = auth()->id(); // Assume the logged-in user is the artist
            $filter = $request->query('filter', '1'); // '1' = This Month, '3' = This Year

            $query = StreamLog::where('artist_id', $artistId);

            if ($filter == '3') { // This Year
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
                    $monthName = Carbon::createFromFormat('m', $log->period)->format('M');
                    $labels[] = $monthName;
                    $dataPoints[] = $log->total_streams;
                    $totalCount += $log->total_streams;
                }
            } else {
                // Default: this month
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
            }

            // Calculate growth percentage
            if ($filter == '3') { // This Year vs Last Year
                $previousPeriodCount = StreamLog::where('artist_id', $artistId)
                    ->whereYear('created_at', Carbon::now()->subYear()->year)
                    ->count();
            } else { // This Month vs Last Month
                $previousPeriodCount = StreamLog::where('artist_id', $artistId)
                    ->whereMonth('created_at', Carbon::now()->subMonth()->month)
                    ->whereYear('created_at', \Illuminate\Support\Carbon::now()->subMonth()->year)
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
