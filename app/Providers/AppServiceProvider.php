<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // 60 richieste al secondo per IP sul lookup DNS.
        RateLimiter::for('dns-lookup', function (Request $request): Limit {
            return Limit::perSecond(60)->by($request->ip());
        });

        // 60 richieste al minuto per IP sul port checker.
        RateLimiter::for('port-checker', function (Request $request): Limit {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
