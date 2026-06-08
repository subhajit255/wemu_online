<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\StreamLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends BaseController
{
    public function artistStreamsChart(Request $request)
    {
        try {
            $user = auth()->user();
            $userId = $user ? $user->id : 0;
            $mainArtistId = $user && $user->added_by ? $user->added_by : $userId;
            $teamIds = \App\Models\User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

            $filter = $request->query('filter', 'this_month'); // e.g., 'last_7_days', 'this_month', 'this_year'

            $query = StreamLog::whereIn('artist_id', $teamIds);

            if ($filter === 'this_year') {
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

            } elseif ($filter === 'this_month') {
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
                // Default: last 7 days
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

            $data = [
                'total_streams' => $totalCount,
                'growth_percentage' => '+0.0%', // Placeholder for now
                'chart' => [
                    'labels' => $labels,
                    'data' => $dataPoints,
                ]
            ];

            return $this->responseJson(true, 200, 'Streams chart data fetched successfully', $data);

        } catch (\Exception $e) {
            logger($e->getMessage() . '--' . $e->getLine() . '--' . $e->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', (object)[]);
        }
    }
}
