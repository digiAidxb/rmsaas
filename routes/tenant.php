<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Tenant home page - redirect to login if not authenticated
Route::get('/', function () {
    $tenant = app('currentTenant');
    
    // Check if tenant is approved/active
    if ($tenant && $tenant->status !== 'approved') {
        return Inertia::render('TenantPending', [
            'tenant' => [
                'name' => $tenant->name,
                'status' => $tenant->status,
                'domain' => $tenant->domain
            ]
        ]);
    }
    
    if (auth('tenant')->check()) {
        // Check if user needs onboarding
        // Don't show onboarding if data was just cleared
        $skipOnboarding = $tenant && ($tenant->onboarding_completed_at || $tenant->skip_onboarding);
        
        if ($tenant && !$skipOnboarding) {
            return redirect()->route('onboarding.index');
        }
        return redirect()->route('dashboard');
    }
    
    return redirect()->route('login');
})->name('tenant.home');

// Auto-login endpoint for tenant
Route::get('/auto-login/{token}', function($token) {
    try {
        $data = json_decode(base64_decode($token), true);
        
        if (!$data || !isset($data['user_id'], $data['tenant_id'], $data['expires'], $data['signature'])) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid login token']);
        }
        
        // Check if token has expired
        if (time() > $data['expires']) {
            return redirect()->route('login')->withErrors(['error' => 'Login token has expired']);
        }
        
        // Get current tenant
        $tenant = \Spatie\Multitenancy\Models\Tenant::current();
        
        // Verify this token is for this tenant
        if ($tenant->id != $data['tenant_id']) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid tenant token']);
        }
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $data['user_id'] . $data['tenant_id'], config('app.key'));
        if (!hash_equals($expectedSignature, $data['signature'])) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid token signature']);
        }
        
        // Get user
        $user = \App\Models\User::find($data['user_id']);
        
        if (!$user || $user->tenant_id != $tenant->id) {
            return redirect()->route('login')->withErrors(['error' => 'Invalid user']);
        }
        
        // Login the user
        auth('tenant')->login($user);
        
        // Check if user needs onboarding
        if (!$tenant->onboarding_completed_at && !$tenant->skip_onboarding) {
            return redirect()->route('onboarding.index');
        }
        
        // Redirect to dashboard
        return redirect()->route('dashboard');
        
    } catch (\Exception $e) {
        return redirect()->route('login')->withErrors(['error' => 'Auto-login failed: ' . $e->getMessage()]);
    }
})->name('tenant.auto.login');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth:tenant', 'verified'])->name('dashboard');

// Onboarding routes
Route::middleware('auth:tenant')->prefix('onboarding')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/step/{step}', [OnboardingController::class, 'step'])->name('onboarding.step');
    Route::post('/step/{step}/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
    
    // Revolutionary Import Integration
    Route::get('/import', [OnboardingController::class, 'importStep'])->name('onboarding.import');
    Route::post('/import/quick-setup', [OnboardingController::class, 'quickImportSetup'])->name('onboarding.import.quick');
});

Route::middleware('auth:tenant')->group(function () {
    // Import System Routes - Revolutionary AI-Powered Import Center
    Route::prefix('imports')->name('imports.')->group(function () {
        Route::get('/', [App\Http\Controllers\Tenant\ImportController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Tenant\ImportController::class, 'create'])->name('create');
        Route::post('/upload', [App\Http\Controllers\Tenant\ImportController::class, 'store'])->name('store');
        Route::post('/preview', [App\Http\Controllers\Tenant\ImportController::class, 'preview'])->name('preview');
        Route::post('/clear-data', [App\Http\Controllers\Tenant\ImportController::class, 'clearData'])->name('clear-data');
        Route::get('/mapping', [App\Http\Controllers\Tenant\ImportController::class, 'mapping'])->name('mapping');
        Route::get('/validation', [App\Http\Controllers\Tenant\ImportController::class, 'validation'])->name('validation');
        Route::get('/progress', [App\Http\Controllers\Tenant\ImportController::class, 'progressView'])->name('progress');
        Route::get('/summary', [App\Http\Controllers\Tenant\ImportController::class, 'summary'])->name('summary');
        Route::get('/{id}', [App\Http\Controllers\Tenant\ImportController::class, 'show'])->name('show');
        Route::get('/{id}/progress', [App\Http\Controllers\Tenant\ImportController::class, 'getProgress'])->name('progress.show');
    });
    
    // Analytics Routes - AI Loss Analysis & Profitability
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/losses', [App\Http\Controllers\Tenant\AnalyticsController::class, 'losses'])->name('losses');
        Route::get('/profits', [App\Http\Controllers\Tenant\AnalyticsController::class, 'profits'])->name('profits');
        Route::get('/insights', [App\Http\Controllers\Tenant\AnalyticsController::class, 'insights'])->name('insights');
        Route::get('/profitability', [App\Http\Controllers\Tenant\AnalyticsController::class, 'profitability'])->name('profitability');
        Route::get('/api/data', [App\Http\Controllers\Tenant\AnalyticsController::class, 'apiData'])->name('api.data');
        Route::post('/reports', [App\Http\Controllers\Tenant\AnalyticsController::class, 'generateReport'])->name('reports.generate');
    });
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';