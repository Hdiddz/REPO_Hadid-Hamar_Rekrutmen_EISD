<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());

        if (
            request()->header('x-forwarded-proto') === 'https'
            || str_contains((string) request()->header('host', ''), 'trycloudflare.com')
            || str_contains((string) request()->header('host', ''), 'azurewebsites.net')
        ) {
            URL::forceScheme('https');
        }

        if (config('database.default') === 'sqlite') {
            $sqlitePath = config('database.connections.sqlite.database');
            if ($sqlitePath && ! file_exists($sqlitePath) && ! str_contains($sqlitePath, ':memory:')) {
                @mkdir(dirname($sqlitePath), 0775, true);
                @touch($sqlitePath);
                try {
                    Artisan::call('migrate', ['--force' => true]);
                    Artisan::call('db:seed', ['--force' => true]);
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        RateLimiter::for('login', function (Request $request): Limit {
            $key = Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });
    }
}
