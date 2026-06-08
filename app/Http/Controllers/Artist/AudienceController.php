<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\AudienceLog;
use Illuminate\Support\Facades\DB;

class AudienceController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user && $user->added_by) {
                $perms = $user->permissions ? json_decode($user->permissions, true) : [];
                if (!is_array($perms)) $perms = [];
                if (!in_array('audience', $perms)) {
                    abort(403, 'Unauthorized access.');
                }
            }
            return $next($request);
        });
    }
    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user ? $user->id : 0;
        $mainArtistId = $user && $user->added_by ? $user->added_by : $userId;
        $teamIds = \App\Models\User::where('id', $mainArtistId)->orWhere('added_by', $mainArtistId)->pluck('id')->toArray();

        $filter = $request->get('filter', 'this_month');
        $query = AudienceLog::whereIn('artist_id', $teamIds);

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
            ->select('country', DB::raw('count(*) as total'))
            ->groupBy('country')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('artist.audience.index', compact('totalListeners', 'newListeners', 'countriesCount', 'citiesCount', 'topCountries', 'filter'));
    }
}
