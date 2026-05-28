<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AudienceLog;
use Symfony\Component\HttpFoundation\Response;

class CaptureClientInfo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->header('X-Forwarded-For') ?? $request->ip();

        // If multiple IPs exist
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Store globally in request
        $request->merge([
            'client_ip' => $ip
        ]);

        // Log Audience Info (Once per IP per day to avoid DB bloat)
        if ($ip !== '127.0.0.1' && $ip !== '::1') {
            $lockKey = 'logged_ip_' . $ip . '_' . date('Y-m-d');

            if (!\Illuminate\Support\Facades\Cache::has($lockKey)) {
                $locationData = \Illuminate\Support\Facades\Cache::remember('ip_location_' . $ip, 86400, function () use ($ip) {
                    $response = \Illuminate\Support\Facades\Http::get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");
                    if ($response->successful() && $response->json('status') === 'success') {
                        return $response->json();
                    }
                    return null;
                });

                AudienceLog::create([
                    'ip_address' => $ip,
                    'country' => $locationData['country'] ?? null,
                    'city' => $locationData['city'] ?? null,
                    'region' => $locationData['regionName'] ?? null,
                    'latitude' => $locationData['lat'] ?? null,
                    'longitude' => $locationData['lon'] ?? null,
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]);

                \Illuminate\Support\Facades\Cache::put($lockKey, true, 86400);
            }
        }

        // Optional logging
        // logger('Client IP: ' . $ip);

        return $next($request);
    }
}
