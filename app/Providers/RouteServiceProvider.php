<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */


    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?: $request->ip()
            );
        });

        $this->routes(function () {
            // API routes
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // General web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            if (config('app.env') === 'production') {
                // Production: admin subdomain
                Route::domain('admin.wemu.online')
                    ->middleware('web')
                    ->namespace($this->namespace . '\Admin')
                    ->group(base_path('routes/admin.php'));

                // Production: artist subdomain
                Route::domain('artist.wemu.online')
                    ->middleware('web')
                    ->namespace($this->namespace . '\Artist')
                    ->group(base_path('routes/artist.php'));
            } else {
                // Local development: /admin/...
                Route::middleware('web')
                    ->prefix('admin')
                    ->namespace($this->namespace . '\Admin')
                    ->group(base_path('routes/admin.php'));

                // Local development: /artist/...
                Route::middleware('web')
                    ->prefix('artist')
                    ->namespace($this->namespace . '\Artist')
                    ->group(base_path('routes/artist.php'));
            }

            // AJAX routes
            Route::middleware('web')
                ->prefix('ajax')
                ->namespace($this->namespace . '\Ajax')
                ->group(base_path('routes/ajax.php'));
        });
    }
}
