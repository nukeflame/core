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
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // $this->configureRateLimiting();

        // RateLimiter::for('api', function (Request $request) {
        //     return Limit::perMinute(100)->by($request->user()?->id ?: $request->ip());
        // });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Bootstrap services
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('webhook', function ($request) {
            return Limit::perMinute(100)->by($request->ip());
        });

        RateLimiter::for('email-list', function ($request) {
            return Limit::perMinute(30)->by($request->user()->id);
        });

        RateLimiter::for('email-sync', function ($request) {
            return [
                Limit::perMinute(2)->by($request->user()->id),
                Limit::perHour(10)->by($request->user()->id)
            ];
        });
    }
}
