<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AudienceLog;
use App\Models\Song;
use App\Models\Album;
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
            $songId = null;
            $albumId = null;
            $artistId = null;

            // Identify IDs based on the Route Name
            if ($route = $request->route()) {
                $routeName = $route->getName();
                // dd($routeName);
                switch ($routeName) {
                    case 'song.increase-play-count':
                    case 'song.add-play-history':
                        $songId = $route->parameter('id');
                        break;
                    case 'artist.details':
                        $artistId = $route->parameter('id');
                        break;
                    case 'toggle.song.like':
                        $songId = $route->parameter('songId');
                        break;
                    case 'toggle.artist.follow':
                        $artistId = $route->parameter('artistId');
                        break;
                    case 'songs.by.album':
                        $albumId = $route->parameter('albumId');
                        break;
                }
            }

            // Update the lock key to include the request path 
            // so it logs once per item per day, rather than once per IP entirely.
            $lockKey = 'logged_ip_' . $ip . '_' . md5($request->path()) . '_' . date('Y-m-d');

            if (!\Illuminate\Support\Facades\Cache::has($lockKey)) {
                $locationData = \Illuminate\Support\Facades\Cache::remember('ip_location_' . $ip, 86400, function () use ($ip) {
                    $response = \Illuminate\Support\Facades\Http::get("http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon");
                    if ($response->successful() && $response->json('status') === 'success') {
                        return $response->json();
                    }
                    return null;
                });
                
                // Added null-checks to prevent server crashes if the ID doesn't exist
                if ($songId != null) {
                    $song = Song::find($songId);
                    if ($song) {
                        $artistId = $song->user_id;
                        $albumId = $song->album_id;
                    }
                }
                
                if ($albumId != null) {
                    $album = Album::find($albumId);
                    if ($album) {
                        $artistId = $album->user_id;
                    }
                }
                // dd($songId, $artistId, $albumId);
                
                AudienceLog::create([
                    'ip_address' => $ip,
                    'country' => $locationData['country'] ?? null,
                    'city' => $locationData['city'] ?? null,
                    'region' => $locationData['regionName'] ?? null,
                    'latitude' => $locationData['lat'] ?? null,
                    'longitude' => $locationData['lon'] ?? null,
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'artist_id' => $artistId,
                    'album_id' => $albumId,
                    'song_id' => $songId,
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                ]);

                \Illuminate\Support\Facades\Cache::put($lockKey, true, 86400);
            }
            // dd($lockKey);
        }

        // Optional logging
        // logger('Client IP: ' . $ip);

        return $next($request);
    }
}
