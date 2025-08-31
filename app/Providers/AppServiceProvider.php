<?php

namespace App\Providers;

use App\Guards\TenantGuard;
use App\Providers\TenantUserProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);
        
        // Register custom tenant authentication components
        $this->registerTenantAuthentication();
    }

    /**
     * Register tenant-specific authentication components.
     */
    protected function registerTenantAuthentication(): void
    {
        // Register the tenant user provider
        Auth::provider('tenant-eloquent', function ($app, array $config) {
            return new TenantUserProvider($app['hash'], $config['model']);
        });

        // Register the tenant guard
        Auth::extend('tenant-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            $guard = new TenantGuard(
                $name,
                $provider,
                $app['session.store'],
                $app['request']
            );

            // Add various methods to the guard
            $guard->setCookieJar($app['cookie']);
            $guard->setDispatcher($app['events']);
            $guard->setRequest($app['request']);

            return $guard;
        });
    }
}
