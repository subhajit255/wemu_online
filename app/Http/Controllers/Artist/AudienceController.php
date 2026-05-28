<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class AudienceController extends BaseController
{
    public function index(Request $request)
    {
        $artistId = auth()->id() ?? 1; // Fallback to 1 for testing if not logged in
        
        $filter = $request->get('filter', 'this_month');
        $query = \App\Models\AudienceLog::where('artist_id', $artistId);
        
        if ($filter === 'this_month') {
            $query->whereMonth('created_at', now()->month)
                  ->whereYear('created_at', now()->year);
        } elseif ($filter === 'last_month') {
            $query->whereMonth('created_at', now()->subMonth()->month)
                  ->whereYear('created_at', now()->subMonth()->year);
        } elseif ($filter === 'this_year') {
            $query->whereYear('created_at', now()->year);
        }
        
        $totalListeners = (clone $query)->count();
        
        // Simulating 'new listeners' for the period (e.g., last 20% of the period or just an arbitrary metric)
        $newListeners = (clone $query)->whereDay('created_at', '>=', 20)->count();
            
        $countriesCount = (clone $query)
            ->whereNotNull('country')
            ->distinct('country')
            ->count('country');
            
        $citiesCount = (clone $query)
            ->whereNotNull('city')
            ->distinct('city')
            ->count('city');

        $topCountries = (clone $query)
            ->whereNotNull('country')
            ->select('country', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('artist.audience.index', compact('totalListeners', 'newListeners', 'countriesCount', 'citiesCount', 'topCountries', 'filter'));
    }
}
