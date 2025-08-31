# Multi-Lingual System Documentation

## 🌍 Overview

The Restaurant Management SaaS now features a comprehensive multi-lingual system that allows each user within a tenant to customize their interface language independently. This enables diverse restaurant teams to work in their preferred languages.

## ✨ Key Features

### 🔧 **Individual User Preferences**
- **Owner**: Can view system in Arabic
- **Manager**: Can use English interface
- **Accountant**: Can work in Chinese
- **Operator**: Can operate in Hindi
- Each user's language preference is stored and persists across sessions

### 🌐 **Supported Languages**
1. **English (en)** 🇺🇸 - Default, LTR
2. **Arabic (ar)** 🇸🇦 - RTL support included
3. **Chinese Simplified (zh)** 🇨🇳 - LTR
4. **Hindi (hi)** 🇮🇳 - LTR with Devanagari script
5. **Spanish (es)** 🇪🇸 - LTR
6. **French (fr)** 🇫🇷 - LTR
7. **German (de)** 🇩🇪 - LTR
8. **Portuguese (pt)** 🇧🇷 - LTR
9. **Russian (ru)** 🇷🇺 - LTR with Cyrillic script
10. **Japanese (ja)** 🇯🇵 - LTR

## 🏗️ System Architecture

### **Database Structure**
```sql
-- Added to users table
ALTER TABLE users ADD COLUMN preferred_language VARCHAR(5) DEFAULT 'en';
ALTER TABLE users ADD COLUMN timezone VARCHAR(50) DEFAULT 'UTC';
ALTER TABLE users ADD COLUMN language_preferences JSON NULL;
ALTER TABLE users ADD INDEX idx_preferred_language (preferred_language);
```

### **Language Configuration**
Located in `config/app.php`:
```php
'available_locales' => [
    'en' => [
        'name' => 'English',
        'native' => 'English',
        'flag' => '🇺🇸',
        'rtl' => false,
    ],
    'ar' => [
        'name' => 'Arabic',
        'native' => 'العربية',
        'flag' => '🇸🇦',
        'rtl' => true,
    ],
    // ... more languages
]
```

## 🔄 Language Detection Priority

The system uses intelligent language detection with the following priority:

1. **URL Parameter** (`?lang=ar`)
2. **Authenticated User Preference** (from database)
3. **Session Storage** (temporary selection)
4. **Browser Accept-Language Header** (automatic detection)
5. **Application Default** (English)

## 🛠️ Core Components

### **1. LanguageMiddleware**
- `app/Http/Middleware/LanguageMiddleware.php`
- Automatically detects and sets locale for each request
- Updates user preferences when language is changed

### **2. TranslationService**
- `app/Services/TranslationService.php`
- Manages translation files and keys
- Handles import/export of translations
- Tracks translation completion statistics

### **3. LanguageController**
- `app/Http/Controllers/LanguageController.php`
- API endpoints for language switching
- Translation management functionality

### **4. LanguageHelper**
- `app/Helpers/LanguageHelper.php`
- Utility functions for locale-specific formatting
- Currency, number, and date formatting
- Font family recommendations

## 🎨 RTL Support

### **CSS Implementation**
- `resources/css/rtl.css`
- Complete RTL support for Arabic
- Automatic direction switching
- Margin, padding, and layout adjustments

### **Usage in Blade Templates**
```php
<html dir="{{ \App\Helpers\LanguageHelper::getDirection() }}">
<body class="{{ \App\Helpers\LanguageHelper::getLocaleClasses() }}">
```

## 📁 Translation Files Structure

```
lang/
├── en/
│   └── common.php      # English translations
├── ar/
│   └── common.php      # Arabic translations
├── zh/
│   └── common.php      # Chinese translations
└── hi/
    └── common.php      # Hindi translations
```

### **Translation File Example**
```php
return [
    'dashboard' => 'Dashboard',        // English
    'dashboard' => 'لوحة التحكم',      // Arabic
    'dashboard' => '仪表板',           // Chinese
    'dashboard' => 'डैशबोर्ड',         // Hindi
];
```

## 🔌 API Endpoints

### **Language Management**
```
POST /api/language/switch           # Switch user language
GET  /api/language/current          # Get current language info
GET  /api/language/translations     # Get all translations
GET  /api/language/stats           # Translation completion stats
```

### **Translation Management**
```
GET  /api/language/{locale}/export  # Export translations
POST /api/language/{locale}/import  # Import translations
POST /api/language/update          # Update single translation
GET  /api/language/{locale}/missing # Get missing translations
```

## 🎛️ UI Components

### **Language Selector Component**
```php
<x-language-selector />
```
- Dropdown with flag icons
- Native language names
- RTL-aware positioning

### **Usage in Templates**
```php
// Get current locale
{{ app()->getLocale() }}

// Check if RTL
@if(\App\Helpers\LanguageHelper::isRTL())
    <div class="rtl-content">
@endif

// Format currency by locale
{{ \App\Helpers\LanguageHelper::formatCurrency(100.50, 'USD') }}

// Format numbers by locale
{{ \App\Helpers\LanguageHelper::formatNumber(1234567.89) }}
```

## 📊 Translation Management

### **Automatic Key Scanning**
```php
// Scan codebase for translation keys
$translationService = app(TranslationService::class);
$keys = $translationService->scanForTranslationKeys();
```

### **Generate Missing Files**
```php
// Auto-generate missing translation files
$translationService->generateMissingFiles();
```

### **Import/Export Support**
```php
// Export translations to JSON
$translations = $translationService->exportTranslations('ar');

// Import from JSON
$translationService->importTranslations('ar', $translations);
```

## 🔧 Implementation Examples

### **Frontend Language Switching**
```javascript
function changeLanguage(locale) {
    fetch('/api/language/switch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ language: locale })
    })
    .then(() => window.location.reload());
}
```

### **Backend Translation Usage**
```php
// In controllers
return response()->json([
    'message' => __('common.saved_successfully')
]);

// In Blade templates
{{ __('common.welcome') }}

// With parameters
{{ __('common.welcome_user', ['name' => $user->name]) }}
```

## 🚀 Benefits

1. **🎯 User-Specific**: Each team member can use their preferred language
2. **🔄 Dynamic Switching**: Real-time language changes without logout
3. **🌍 Global Ready**: Support for 10+ languages with proper formatting
4. **📱 RTL Support**: Complete right-to-left interface for Arabic
5. **🎨 Locale-Aware**: Proper currency, number, and date formatting
6. **⚡ Performance**: Cached translations with efficient middleware
7. **🔧 Developer Friendly**: Easy to add new languages and translations

## 📈 Usage Statistics

The system tracks:
- Translation completion percentage per language
- Missing translation keys
- User language preferences
- Popular language combinations per tenant

## 🔮 Future Enhancements

1. **Machine Translation Integration** (Google Translate API)
2. **Collaborative Translation Interface** for team members
3. **Locale-Specific Content Management**
4. **Advanced Number Systems** (Arabic numerals, etc.)
5. **Voice Interface Localization**

---

*The multi-lingual system is now fully integrated and ready to support diverse restaurant teams worldwide. Each user can work in their preferred language while maintaining system consistency and performance.*