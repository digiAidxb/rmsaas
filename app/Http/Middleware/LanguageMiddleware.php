<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->determineLocale($request);
        
        // Set the application locale
        App::setLocale($locale);
        
        // Store in session for consistency
        Session::put('locale', $locale);
        
        // Set locale information in view composer
        view()->share([
            'currentLocale' => $locale,
            'availableLocales' => config('app.available_locales'),
            'isRTL' => config("app.available_locales.{$locale}.rtl", false),
        ]);

        return $next($request);
    }

    /**
     * Determine the locale to use for the current request
     */
    private function determineLocale(Request $request): string
    {
        // Priority order:
        // 1. URL parameter 'lang'
        // 2. User's preferred language (if authenticated)
        // 3. Session stored locale
        // 4. Browser Accept-Language header
        // 5. Application default

        // Check URL parameter
        if ($request->has('lang')) {
            $requestedLocale = $request->get('lang');
            if ($this->isValidLocale($requestedLocale)) {
                // Update user preference if authenticated
                if (auth()->check()) {
                    auth()->user()->update(['preferred_language' => $requestedLocale]);
                }
                return $requestedLocale;
            }
        }

        // Check authenticated user preference
        if (auth()->check() && auth()->user()->preferred_language) {
            $userLocale = auth()->user()->preferred_language;
            if ($this->isValidLocale($userLocale)) {
                return $userLocale;
            }
        }

        // Check session
        $sessionLocale = Session::get('locale');
        if ($sessionLocale && $this->isValidLocale($sessionLocale)) {
            return $sessionLocale;
        }

        // Check browser preference
        $browserLocale = $this->getBrowserLocale($request);
        if ($browserLocale && $this->isValidLocale($browserLocale)) {
            return $browserLocale;
        }

        // Fall back to application default
        return config('app.locale', 'en');
    }

    /**
     * Check if the given locale is valid/supported
     */
    private function isValidLocale(string $locale): bool
    {
        return array_key_exists($locale, config('app.available_locales', []));
    }

    /**
     * Get preferred locale from browser Accept-Language header
     */
    private function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        // Parse Accept-Language header
        $languages = [];
        if (preg_match_all('/([a-z]{1,8}(?:-[a-z]{1,8})?)(?:;q=([0-9.]+))?/i', $acceptLanguage, $matches)) {
            $languages = array_combine($matches[1], $matches[2] ?: array_fill(0, count($matches[1]), 1.0));
            arsort($languages); // Sort by preference
        }

        // Find first supported language
        foreach (array_keys($languages) as $browserLang) {
            $locale = substr($browserLang, 0, 2); // Get language code without region
            if ($this->isValidLocale($locale)) {
                return $locale;
            }
        }

        return null;
    }
}