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
            $artistId = auth()->id(); // Assume the logged-in user is the artist
            $filter = $request->query('filter', 'this_month'); // e.g., 'this_month', 'this_year'

            $query = StreamLog::where('artist_id', $artistId);

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
                    $monthName = Carbon::createFromFormat('m', $log->period)->format('M');
                    $labels[] = $monthName;
                    $dataPoints[] = $log->total_streams;
                    $totalCount += $log->total_streams;
                }

            } else {
                // Default: this_month
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
