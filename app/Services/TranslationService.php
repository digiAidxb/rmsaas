<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TranslationService
{
    /**
     * Get all translation keys for a given locale
     */
    public function getTranslations(string $locale = 'en'): array
    {
        $cacheKey = "translations.{$locale}";
        
        return Cache::remember($cacheKey, 3600, function () use ($locale) {
            $translations = [];
            $langPath = resource_path("lang/{$locale}");
            
            if (!File::exists($langPath)) {
                return [];
            }

            $files = File::allFiles($langPath);
            
            foreach ($files as $file) {
                $filename = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                $fileTranslations = include $file->getPathname();
                
                if (is_array($fileTranslations)) {
                    $translations[$filename] = $fileTranslations;
                }
            }

            return $translations;
        });
    }

    /**
     * Get missing translation keys compared to base locale
     */
    public function getMissingTranslations(string $targetLocale, string $baseLocale = 'en'): array
    {
        $baseTranslations = $this->getTranslations($baseLocale);
        $targetTranslations = $this->getTranslations($targetLocale);
        
        return $this->findMissingKeys($baseTranslations, $targetTranslations);
    }

    /**
     * Update a translation key
     */
    public function updateTranslation(string $locale, string $file, string $key, string $value): bool
    {
        $langPath = resource_path("lang/{$locale}/{$file}.php");
        
        if (!File::exists($langPath)) {
            $this->createTranslationFile($locale, $file);
        }

        $translations = include $langPath;
        
        // Support nested keys using dot notation
        $keys = explode('.', $key);
        $current = &$translations;
        
        foreach ($keys as $k) {
            if (!isset($current[$k])) {
                $current[$k] = [];
            }
            $current = &$current[$k];
        }
        
        $current = $value;

        // Write back to file
        $content = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
        File::put($langPath, $content);

        // Clear cache
        Cache::forget("translations.{$locale}");

        return true;
    }

    /**
     * Create a new translation file
     */
    public function createTranslationFile(string $locale, string $file): void
    {
        $langDir = resource_path("lang/{$locale}");
        
        if (!File::exists($langDir)) {
            File::makeDirectory($langDir, 0755, true);
        }

        $filePath = "{$langDir}/{$file}.php";
        
        if (!File::exists($filePath)) {
            File::put($filePath, "<?php\n\nreturn [];\n");
        }
    }

    /**
     * Export translations to JSON format
     */
    public function exportTranslations(string $locale): array
    {
        return $this->flattenArray($this->getTranslations($locale));
    }

    /**
     * Import translations from JSON format
     */
    public function importTranslations(string $locale, array $translations): void
    {
        foreach ($translations as $key => $value) {
            $parts = explode('.', $key, 2);
            $file = $parts[0];
            $translationKey = $parts[1] ?? $key;
            
            $this->updateTranslation($locale, $file, $translationKey, $value);
        }
    }

    /**
     * Get translation statistics for a locale
     */
    public function getTranslationStats(string $locale): array
    {
        $baseTranslations = $this->flattenArray($this->getTranslations('en'));
        $localeTranslations = $this->flattenArray($this->getTranslations($locale));
        
        $totalKeys = count($baseTranslations);
        $translatedKeys = count(array_intersect_key($localeTranslations, $baseTranslations));
        $missingKeys = $totalKeys - $translatedKeys;
        
        return [
            'total' => $totalKeys,
            'translated' => $translatedKeys,
            'missing' => $missingKeys,
            'percentage' => $totalKeys > 0 ? round(($translatedKeys / $totalKeys) * 100, 2) : 0,
        ];
    }

    /**
     * Scan PHP files for translation keys
     */
    public function scanForTranslationKeys(): array
    {
        $keys = [];
        $directories = [
            app_path(),
            resource_path('views'),
            resource_path('js'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);
            
            foreach ($files as $file) {
                $content = File::get($file->getPathname());
                
                // Match various translation patterns
                $patterns = [
                    '/\b__\([\'"]([^\'"\)]+)[\'"]\)/',           // __('key')
                    '/\btrans\([\'"]([^\'"\)]+)[\'"]\)/',        // trans('key')
                    '/\b@lang\([\'"]([^\'"\)]+)[\'"]\)/',        // @lang('key')
                    '/\$t\([\'"]([^\'"\)]+)[\'"]\)/',           // $t('key') for Vue
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match_all($pattern, $content, $matches)) {
                        $keys = array_merge($keys, $matches[1]);
                    }
                }
            }
        }

        return array_unique($keys);
    }

    /**
     * Generate missing translation files for all locales
     */
    public function generateMissingFiles(): void
    {
        $scannedKeys = $this->scanForTranslationKeys();
        $groupedKeys = $this->groupTranslationKeys($scannedKeys);
        
        $locales = array_keys(config('app.available_locales', []));
        
        foreach ($locales as $locale) {
            foreach ($groupedKeys as $file => $keys) {
                foreach ($keys as $key) {
                    $this->updateTranslation($locale, $file, $key, $key); // Use key as default value
                }
            }
        }
    }

    /**
     * Find missing keys recursively
     */
    private function findMissingKeys(array $base, array $target, string $prefix = ''): array
    {
        $missing = [];
        
        foreach ($base as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (!array_key_exists($key, $target)) {
                $missing[] = $fullKey;
            } elseif (is_array($value) && is_array($target[$key])) {
                $missing = array_merge($missing, $this->findMissingKeys($value, $target[$key], $fullKey));
            }
        }

        return $missing;
    }

    /**
     * Flatten nested array with dot notation
     */
    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;
            
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Group translation keys by file
     */
    private function groupTranslationKeys(array $keys): array
    {
        $grouped = [];
        
        foreach ($keys as $key) {
            if (Str::contains($key, '.')) {
                $parts = explode('.', $key, 2);
                $file = $parts[0];
                $translationKey = $parts[1];
            } else {
                $file = 'common';
                $translationKey = $key;
            }
            
            $grouped[$file][] = $translationKey;
        }

        return $grouped;
    }
}