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
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        // Apply rate limiters for API routes
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Register routes
        $this->routes(function () {
            // Register API routes under 'api' middleware
            Route::middleware('api')
                ->prefix('api')   // This ensures all API routes are prefixed with 'api'
                ->group(base_path('routes/api.php'));

            // Register Web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
