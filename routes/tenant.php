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
        
        // Redirect to dashboard
        return redirect()->route('dashboard');
        
    } catch (\Exception $e) {
        return redirect()->route('login')->withErrors(['error' => 'Auto-login failed: ' . $e->getMessage()]);
    }
})->name('tenant.auto.login');

Route::get('/dashboard', function () {
    $tenant = \Spatie\Multitenancy\Models\Tenant::current();
    
    // Check if onboarding is needed
    if (!$tenant->onboarding_completed_at && !$tenant->skip_onboarding) {
        return redirect()->route('onboarding.index');
    }
    
    return Inertia::render('Dashboard', [
        'tenant' => $tenant,
        'onboarding_progress' => $tenant->getOnboardingProgress()
    ]);
})->middleware(['auth:tenant', 'verified'])->name('dashboard');

// Onboarding routes
Route::middleware('auth:tenant')->prefix('onboarding')->group(function () {
    Route::get('/', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::get('/step/{step}', [OnboardingController::class, 'step'])->name('onboarding.step');
    Route::post('/step/{step}/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
    Route::post('/skip', [OnboardingController::class, 'skip'])->name('onboarding.skip');
});

Route::middleware('auth:tenant')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';