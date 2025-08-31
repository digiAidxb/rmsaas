<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TenantRegistrationController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'canTenantRegister' => Route::has('tenant.register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('home');

// Tenant Registration Routes (main domain only)
Route::middleware(['web'])->group(function () {
    Route::get('/tenant/register', [TenantRegistrationController::class, 'create'])
        ->name('tenant.register');
    Route::post('/tenant/register', [TenantRegistrationController::class, 'store'])
        ->name('tenant.register.store');
    Route::get('/tenant/registration/success/{tenant}', [TenantRegistrationController::class, 'success'])
        ->name('tenant.registration.success');
        
    // Auto-login route for testing
    Route::get('/tenant-auto-login/{token}', [TenantRegistrationController::class, 'autoLogin'])
        ->name('tenant.auto.login');
});

// Language routes
Route::prefix('api/language')->group(function () {
    Route::post('switch', [LanguageController::class, 'switch']);
    Route::get('current', [LanguageController::class, 'current']);
    Route::get('translations', [LanguageController::class, 'translations']);
    Route::get('stats', [LanguageController::class, 'stats']);
    Route::get('{locale}/export', [LanguageController::class, 'export']);
    Route::post('{locale}/import', [LanguageController::class, 'import']);
    Route::post('update', [LanguageController::class, 'updateTranslation']);
    Route::get('{locale}/missing', [LanguageController::class, 'missing']);
});

require __DIR__.'/auth.php';

// Include test routes in development
if (app()->environment(['local', 'testing'])) {
    require __DIR__.'/test.php';
}
