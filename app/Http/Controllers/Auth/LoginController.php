<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
            'status' => session('status'),
            'tenant' => app('currentTenant'),
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
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Handle login attempts with email and password.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Check if the current tenant is active
        $currentTenant = app('currentTenant');
        if (!$currentTenant || !$currentTenant->is_active) {
            throw ValidationException::withMessages([
                'email' => __('This restaurant account is currently inactive. Please contact support.'),
            ]);
        }

        // Check if tenant's subscription is active
        if ($currentTenant->status !== 'approved') {
            throw ValidationException::withMessages([
                'email' => __('This restaurant account is pending approval. Please wait or contact support.'),
            ]);
        }

        // Attempt authentication with tenant guard
        if (Auth::guard('tenant')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Get the authenticated user
            $user = Auth::guard('tenant')->user();

            // Log successful login
            logger()->info('User login successful', [
                'tenant_id' => $currentTenant->id,
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended(route('dashboard'));
        }

        // Log failed login attempt
        logger()->warning('Login attempt failed', [
            'tenant_id' => $currentTenant->id,
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        throw ValidationException::withMessages([
            'email' => __('These credentials do not match our records.'),
        ]);
    }

    /**
     * Show language selection for login.
     */
    public function showLanguageSelection(): Response
    {
        return Inertia::render('Auth/LanguageSelection', [
            'tenant' => app('currentTenant'),
            'availableLocales' => config('app.available_locales'),
        ]);
    }

    /**
     * Set language preference and redirect to login.
     */
    public function setLanguage(Request $request): RedirectResponse
    {
        $request->validate([
            'language' => ['required', 'string', 'in:' . implode(',', array_keys(config('app.available_locales')))],
        ]);

        // Set locale in session
        session(['locale' => $request->language]);
        app()->setLocale($request->language);

        return redirect()->route('login');
    }
}
