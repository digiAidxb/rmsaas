<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;

class LanguageHelper
{
    /**
     * Get the current locale
     */
    public static function getCurrentLocale(): string
    {
        return App::getLocale();
    }

    /**
     * Get all available locales
     */
    public static function getAvailableLocales(): array
    {
        return config('app.available_locales', []);
    }

    /**
     * Check if current locale is RTL
     */
    public static function isRTL(): bool
    {
        $currentLocale = self::getCurrentLocale();
        $availableLocales = self::getAvailableLocales();
        
        return $availableLocales[$currentLocale]['rtl'] ?? false;
    }

    /**
     * Get locale direction (ltr or rtl)
     */
    public static function getDirection(): string
    {
        return self::isRTL() ? 'rtl' : 'ltr';
    }

    /**
     * Get locale information
     */
    public static function getLocaleInfo(?string $locale = null): array
    {
        $locale = $locale ?: self::getCurrentLocale();
        $availableLocales = self::getAvailableLocales();
        
        return $availableLocales[$locale] ?? [
            'name' => 'Unknown',
            'native' => 'Unknown',
            'flag' => '🌍',
            'rtl' => false,
        ];
    }

    /**
     * Get formatted currency based on locale
     */
    public static function formatCurrency(float $amount, string $currency = 'USD'): string
    {
        $locale = self::getCurrentLocale();
        
        // Map locales to their standard formats
        $localeMap = [
            'en' => 'en_US',
            'ar' => 'ar_SA',
            'zh' => 'zh_CN',
            'hi' => 'hi_IN',
            'es' => 'es_ES',
            'fr' => 'fr_FR',
            'de' => 'de_DE',
            'pt' => 'pt_BR',
            'ru' => 'ru_RU',
            'ja' => 'ja_JP',
        ];

        $formatterLocale = $localeMap[$locale] ?? 'en_US';
        
        if (class_exists('NumberFormatter')) {
            $formatter = new \NumberFormatter($formatterLocale, \NumberFormatter::CURRENCY);
            return $formatter->formatCurrency($amount, $currency);
        }
        
        // Fallback formatting
        return $currency . ' ' . number_format($amount, 2);
    }

    /**
     * Get formatted number based on locale
     */
    public static function formatNumber(float $number, int $decimals = 2): string
    {
        $locale = self::getCurrentLocale();
        
        // Different number formatting based on locale
        switch ($locale) {
            case 'ar':
                // Arabic uses Arabic-Indic numerals in some contexts
                return number_format($number, $decimals, '٫', '٬');
            case 'hi':
                // Hindi uses Indian numbering system
                return self::formatIndianNumber($number, $decimals);
            default:
                return number_format($number, $decimals);
        }
    }

    /**
     * Format number in Indian numbering system
     */
    private static function formatIndianNumber(float $number, int $decimals): string
    {
        $formatted = number_format($number, $decimals);
        
        // Convert to Indian numbering (lakh, crore)
        if ($number >= 10000000) { // 1 crore
            return number_format($number / 10000000, $decimals) . ' crore';
        } elseif ($number >= 100000) { // 1 lakh
            return number_format($number / 100000, $decimals) . ' lakh';
        }
        
        return $formatted;
    }

    /**
     * Get date format based on locale
     */
    public static function getDateFormat(): string
    {
        $locale = self::getCurrentLocale();
        
        $formats = [
            'en' => 'M d, Y',
            'ar' => 'd/m/Y',
            'zh' => 'Y年m月d日',
            'hi' => 'd/m/Y',
            'es' => 'd/m/Y',
            'fr' => 'd/m/Y',
            'de' => 'd.m.Y',
            'pt' => 'd/m/Y',
            'ru' => 'd.m.Y',
            'ja' => 'Y年m月d日',
        ];

        return $formats[$locale] ?? 'M d, Y';
    }

    /**
     * Get time format based on locale
     */
    public static function getTimeFormat(): string
    {
        $locale = self::getCurrentLocale();
        
        $formats = [
            'en' => 'h:i A',
            'ar' => 'H:i',
            'zh' => 'H:i',
            'hi' => 'H:i',
            'es' => 'H:i',
            'fr' => 'H:i',
            'de' => 'H:i',
            'pt' => 'H:i',
            'ru' => 'H:i',
            'ja' => 'H:i',
        ];

        return $formats[$locale] ?? 'h:i A';
    }

    /**
     * Translate and format a message with pluralization
     */
    public static function transChoice(string $key, int $number, array $replace = []): string
    {
        return trans_choice($key, $number, $replace);
    }

    /**
     * Get appropriate font family for current locale
     */
    public static function getFontFamily(): string
    {
        $locale = self::getCurrentLocale();
        
        $fonts = [
            'en' => "'Inter', 'Segoe UI', sans-serif",
            'ar' => "'Noto Sans Arabic', 'Arial', sans-serif",
            'zh' => "'Noto Sans SC', 'Microsoft YaHei', sans-serif",
            'hi' => "'Noto Sans Devanagari', 'Arial Unicode MS', sans-serif",
            'es' => "'Inter', 'Segoe UI', sans-serif",
            'fr' => "'Inter', 'Segoe UI', sans-serif",
            'de' => "'Inter', 'Segoe UI', sans-serif",
            'pt' => "'Inter', 'Segoe UI', sans-serif",
            'ru' => "'Inter', 'Segoe UI', sans-serif",
            'ja' => "'Noto Sans JP', 'Hiragino Sans', sans-serif",
        ];

        return $fonts[$locale] ?? "'Inter', 'Segoe UI', sans-serif";
    }

    /**
     * Get locale-specific CSS classes
     */
    public static function getLocaleClasses(): string
    {
        $locale = self::getCurrentLocale();
        $classes = ["locale-{$locale}"];
        
        if (self::isRTL()) {
            $classes[] = 'rtl';
        }
        
        return implode(' ', $classes);
    }
}