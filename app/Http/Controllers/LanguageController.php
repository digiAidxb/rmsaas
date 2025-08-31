<?php

namespace App\Http\Controllers;

use App\Services\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function __construct(
        private TranslationService $translationService
    ) {}

    /**
     * Switch the application language
     */
    public function switch(Request $request): JsonResponse
    {
        $request->validate([
            'language' => 'required|string|size:2',
        ]);

        $locale = $request->input('language');
        $availableLocales = array_keys(config('app.available_locales', []));

        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported language',
            ], 400);
        }

        // Update user preference if authenticated
        if (auth()->check()) {
            auth()->user()->update([
                'preferred_language' => $locale,
            ]);
        }

        // Set session locale
        Session::put('locale', $locale);
        App::setLocale($locale);

        return response()->json([
            'success' => true,
            'message' => __('common.saved_successfully'),
            'locale' => $locale,
        ]);
    }

    /**
     * Get current locale information
     */
    public function current(): JsonResponse
    {
        $currentLocale = App::getLocale();
        $availableLocales = config('app.available_locales', []);

        return response()->json([
            'current' => $currentLocale,
            'available' => $availableLocales,
            'is_rtl' => $availableLocales[$currentLocale]['rtl'] ?? false,
        ]);
    }

    /**
     * Get translations for current locale
     */
    public function translations(): JsonResponse
    {
        $locale = App::getLocale();
        $translations = $this->translationService->getTranslations($locale);

        return response()->json([
            'locale' => $locale,
            'translations' => $translations,
        ]);
    }

    /**
     * Get translation statistics
     */
    public function stats(): JsonResponse
    {
        $availableLocales = array_keys(config('app.available_locales', []));
        $stats = [];

        foreach ($availableLocales as $locale) {
            $stats[$locale] = $this->translationService->getTranslationStats($locale);
        }

        return response()->json($stats);
    }

    /**
     * Export translations for a locale
     */
    public function export(string $locale): JsonResponse
    {
        $availableLocales = array_keys(config('app.available_locales', []));
        
        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported language',
            ], 400);
        }

        $translations = $this->translationService->exportTranslations($locale);
        
        return response()->json([
            'locale' => $locale,
            'translations' => $translations,
            'export_date' => now()->toISOString(),
        ]);
    }

    /**
     * Import translations for a locale
     */
    public function import(Request $request, string $locale): JsonResponse
    {
        $request->validate([
            'translations' => 'required|array',
        ]);

        $availableLocales = array_keys(config('app.available_locales', []));
        
        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported language',
            ], 400);
        }

        try {
            $this->translationService->importTranslations(
                $locale, 
                $request->input('translations')
            );

            return response()->json([
                'success' => true,
                'message' => __('common.saved_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a single translation key
     */
    public function updateTranslation(Request $request): JsonResponse
    {
        $request->validate([
            'locale' => 'required|string|size:2',
            'file' => 'required|string|max:50',
            'key' => 'required|string|max:255',
            'value' => 'required|string|max:1000',
        ]);

        $locale = $request->input('locale');
        $availableLocales = array_keys(config('app.available_locales', []));
        
        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported language',
            ], 400);
        }

        try {
            $this->translationService->updateTranslation(
                $locale,
                $request->input('file'),
                $request->input('key'),
                $request->input('value')
            );

            return response()->json([
                'success' => true,
                'message' => __('common.saved_successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get missing translations for a locale
     */
    public function missing(string $locale): JsonResponse
    {
        $availableLocales = array_keys(config('app.available_locales', []));
        
        if (!in_array($locale, $availableLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Unsupported language',
            ], 400);
        }

        $missingKeys = $this->translationService->getMissingTranslations($locale);

        return response()->json([
            'locale' => $locale,
            'missing_keys' => $missingKeys,
            'count' => count($missingKeys),
        ]);
    }
}