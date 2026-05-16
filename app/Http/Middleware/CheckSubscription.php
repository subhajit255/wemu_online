<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!userSubscriptionIsActive()) {
            return response()->json([
                'status' => false,
                'code' => 403,
                'message' => 'Your subscription has been expired or out of limit. Please upgrade your subscription.to continue using our services.',
                'response' => null
            ], 403);
        }
        return $next($request);
    }
}
