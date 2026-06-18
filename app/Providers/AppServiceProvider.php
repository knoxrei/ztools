<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        $this->configureDefaults();

        $host = !app()->runningInConsole() ? request()->getHost() : '';
        $isOnion = str_ends_with(strtolower($host), '.onion');

        if ($isOnion) {
            \Illuminate\Support\Facades\URL::forceScheme('http');
            config(['app.asset_url' => '/']);
        } else {
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            } elseif (str_starts_with(config('app.url'), 'https://')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            } elseif (!app()->runningInConsole()) {
                $isLocal = in_array($host, ['localhost', '127.0.0.1', '::1']) || filter_var($host, FILTER_VALIDATE_IP) !== false;

                if (!$isLocal) {
                    \Illuminate\Support\Facades\URL::forceScheme('https');
                } else {
                    config(['app.asset_url' => '/']);
                }
            }
        }
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}
