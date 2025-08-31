<?php

namespace App\Guards;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Auth\SessionGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class TenantGuard extends SessionGuard
{
    /**
     * Create a new authentication guard.
     *
     * @param  string  $name
     * @param  \Illuminate\Auth\EloquentUserProvider  $provider
     * @param  \Illuminate\Contracts\Session\Session  $session
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct($name, EloquentUserProvider $provider, Session $session, Request $request = null)
    {
        parent::__construct($name, $provider, $session, $request);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool  $remember
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false)
    {
        $this->fireAttemptEvent($credentials, $remember);

        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        // Check if user belongs to current tenant
        if ($this->hasValidCredentials($user, $credentials) && $this->belongsToCurrentTenant($user)) {
            $this->login($user, $remember);

            // Update last login information
            $this->updateLastLoginInfo($user);

            // Set user's language preference
            $this->setUserLanguage($user);

            return true;
        }

        $this->fireFailedEvent($user, $credentials);

        return false;
    }

    /**
     * Check if the user belongs to the current tenant.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    protected function belongsToCurrentTenant($user)
    {
        if (! $user) {
            return false;
        }

        $currentTenant = app('currentTenant');
        
        if (! $currentTenant) {
            return false;
        }

        return $user->tenant_id === $currentTenant->id;
    }

    /**
     * Update the user's last login information.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function updateLastLoginInfo($user)
    {
        if ($user instanceof User) {
            // Ensure we're updating on the landlord connection since users are stored there
            $user->setConnection('landlord');
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ]);
        }
    }

    /**
     * Set the application locale based on user's language preference.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    protected function setUserLanguage($user)
    {
        if ($user instanceof User && $user->preferred_language) {
            app()->setLocale($user->preferred_language);
            session(['locale' => $user->preferred_language]);
        }
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout()
    {
        $user = $this->user();

        $this->clearUserDataFromStorage();

        if (isset($this->events)) {
            $this->events->dispatch(new \Illuminate\Auth\Events\Logout($this->name, $user));
        }

        // Clear language preference
        session()->forget(['locale']);

        $this->user = null;
        $this->loggedOut = true;
    }
}