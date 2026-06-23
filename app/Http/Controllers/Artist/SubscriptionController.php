<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = UserSubscription::with('subscription')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('artist.subscription.index', compact('subscriptions'));
    }
}
