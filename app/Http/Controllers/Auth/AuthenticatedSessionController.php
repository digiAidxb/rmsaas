<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        $tenant = null;
        $tenantStatus = null;
        
        // Check if we're in tenant context
        if (app()->bound('currentTenant')) {
            $tenant = app('currentTenant');
            $tenantStatus = $tenant->status;
        }
        
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'tenant' => $tenant ? [
                'name' => $tenant->name,
                'status' => $tenant->status,
                'domain' => $tenant->domain
            ] : null,
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Use tenant guard if we're in tenant context, otherwise use default web guard
        $guard = app()->bound('currentTenant') ? 'tenant' : 'web';
        Auth::guard($guard)->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
