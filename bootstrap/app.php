<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Controllers\TenantRegistrationController;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Only load tenant routes when we have a valid tenant domain
            // Skip tenant routes for main domain (localhost, 127.0.0.1, or main app domain)
            $host = request()->getHost();
            $isMainDomain = in_array($host, ['localhost', '127.0.0.1']) || 
                           $host === parse_url(config('app.url'), PHP_URL_HOST);
            
            if (!$isMainDomain) {
                Route::middleware(['web', 'tenant'])
                    ->group(base_path('routes/tenant.php'));
            }
            
            // Test routes without CSRF protection for API testing
            Route::middleware(['web'])
                ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
                ->group(base_path('routes/test.php'));
            
            // Allow tenant registration POST without CSRF for API testing
            Route::post('/tenant/register', [\App\Http\Controllers\TenantRegistrationController::class, 'store'])
                ->name('tenant.register.api')
                ->middleware(['web'])
                ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\LoggingMiddleware::class,
        ]);

        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantMiddleware::class,
            'language' => \App\Http\Middleware\LanguageMiddleware::class,
            'logging' => \App\Http\Middleware\LoggingMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
