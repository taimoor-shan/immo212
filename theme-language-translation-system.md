# How Theme Language Files Are Translated When You Switch Languages

## 🔄 **Language Translation System Overview**

When you switch languages in your Laravel-based application (Botble CMS), here's exactly what happens to theme language files and translations:

## 📁 **File Structure**

### Current Language Files Location:
```
platform/themes/homzen/lang/
└── en.json (English translations - 813+ strings)

platform/plugins/real-estate/resources/lang/
├── en/
│   ├── vacation-rental.php (Plugin-specific translations)
│   ├── property.php
│   └── other files...
└── [other language folders when added]
```

## 🔧 **Language Switching Mechanism**

### 1. **Language Detection & Setting**
When a user switches language via the language switcher:

```php
// From language-switcher.blade.php
<a href=\"{{ Language::getSwitcherUrl($localeCode, $properties['lang_code']) }}\">
```

The system:
1. **Detects the language** through URL, session, or user preference
2. **Sets the Laravel locale** using `Language::setLocale()`
3. **Updates the current locale** in the session and application

### 2. **Translation File Loading Process**

#### **Theme Translations (JSON format):**
```json
// platform/themes/homzen/lang/en.json
{
    "Book This Vacation Rental": "Book This Vacation Rental",
    "Available for booking": "Available for booking",
    "Total Properties": "Total Properties"
}
```

#### **Plugin Translations (PHP array format):**
```php
// platform/plugins/real-estate/resources/lang/en/vacation-rental.php
return [
    'book_this_vacation_rental' => 'Book This Vacation Rental',
    'available_for_booking' => 'Available for booking',
    'total_properties' => 'Total Properties'
];
```

### 3. **Translation Loading Sequence**

When language is switched, Laravel's translation system:

#### **Step 1: Locale Resolution**
```php
// From LanguageManager.php
public function setLocale(): ?string
{
    $locale = $this->getCurrentLocale();
    app()->setLocale($locale);
    return $locale;
}
```

#### **Step 2: Translation File Loading**
Laravel automatically loads translation files in this order:
1. **Core Laravel translations** (`resources/lang/{locale}/`)
2. **Plugin translations** (`platform/plugins/*/resources/lang/{locale}/`)
3. **Theme translations** (`platform/themes/*/lang/{locale}.json`)

#### **Step 3: Translation Resolution**
When `__('string')` or `trans()` is called:

```php
// In Blade views:
{{ __('Book This Vacation Rental') }}           // Looks in theme JSON
{{ __('plugins/real-estate::vacation-rental.book_this_vacation_rental') }} // Plugin translation
```

## 🌍 **Language Switching Flow**

### **Frontend Process:**
1. **User clicks language switcher** → Language dropdown/links
2. **URL changes** → `/es/properties` (for Spanish)
3. **Middleware processes request** → `LocalizationRoutes`, `LocalizationRedirectFilter`
4. **Locale is set** → `app()->setLocale('es')`
5. **Translations load** → `es.json` and `es/*.php` files
6. **Page renders** → All text displays in Spanish

### **Translation File Lookup:**
```
Language: English (en)
├── theme/homzen/lang/en.json
├── plugins/real-estate/lang/en/vacation-rental.php
└── plugins/real-estate/lang/en/property.php

Language: Spanish (es) [When added]
├── theme/homzen/lang/es.json
├── plugins/real-estate/lang/es/vacation-rental.php
└── plugins/real-estate/lang/es/property.php
```

## ⚙️ **How It Works Technically**

### **1. Language Manager Service**
```php
// From LanguageManager.php
public function getSupportedLocales(): array
{
    // Returns all active languages from database
    $languages = $this->getActiveLanguage();
    // Formats them for use by the translation system
}
```

### **2. Middleware Stack**
```php
// Applied to all routes
'middleware' => [
    'localeSessionRedirect',     // Redirects based on session
    'localizationRedirect',      // Handles URL localization
]
```

### **3. Translation Loading**
```php
// When __('text') is called:
1. Laravel checks current locale: app()->getLocale()
2. Looks for translation files in order:
   - Core translations
   - Plugin translations  
   - Theme translations
3. Returns translated string or fallback to key
```

## 🔄 **Adding New Languages**

### **To add Spanish support:**

#### **1. Create Theme Language File:**
```bash
# Copy and translate
cp platform/themes/homzen/lang/en.json platform/themes/homzen/lang/es.json
```

#### **2. Create Plugin Language Files:**
```bash
# Create Spanish plugin translations
mkdir platform/plugins/real-estate/resources/lang/es/
cp platform/plugins/real-estate/resources/lang/en/* platform/plugins/real-estate/resources/lang/es/
```

#### **3. Add Language to Database:**
```sql
INSERT INTO languages (lang_name, lang_locale, lang_code, lang_flag, lang_is_default, lang_order)
VALUES ('Español', 'es', 'es_ES', 'es', 0, 2);
```

## 📝 **Translation Usage in Views**

### **Direct Translation (Theme JSON):**
```blade
<!-- Uses theme/homzen/lang/{locale}.json -->
{{ __('Book This Vacation Rental') }}
{{ __('Total Properties') }}
{{ __('Available for booking') }}
```

### **Namespaced Translation (Plugin PHP):**
```blade
<!-- Uses plugins/real-estate/lang/{locale}/vacation-rental.php -->
{{ __('plugins/real-estate::vacation-rental.book_this_vacation_rental') }}
{{ trans('plugins/real-estate::vacation-rental.total_properties') }}
```

## 🔍 **Translation Resolution Priority**

When `__('text')` is called, Laravel looks for translations in this order:

1. **Theme JSON files** → `theme/lang/{locale}.json`
2. **Plugin PHP files** → `plugins/*/lang/{locale}/*.php`
3. **Core translations** → `resources/lang/{locale}/*.php`
4. **Fallback** → Returns the key itself if not found

## 💾 **Caching & Performance**

### **Translation Caching:**
```php
// Translations are cached for performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **Dynamic Loading:**
- Translations load **automatically** when locale changes
- **No manual reload** required
- **Cached efficiently** by Laravel

## 🚀 **Best Practices**

### **1. Use Proper Keys:**
```php
// Good - Using proper translation keys
__('plugins/real-estate::vacation-rental.book_this_vacation_rental')

// Avoid - Direct string (harder to manage)
__('Book This Vacation Rental')
```

### **2. Organize Translations:**
```php
// Group related translations
'frontend' => [
    'book_this_vacation_rental' => 'Book This Vacation Rental',
    'available_for_booking' => 'Available for booking',
],
'admin' => [
    'vacation_rental_properties' => 'Vacation Rental Properties',
    'add_new_property' => 'Add New Property',
]
```

### **3. Fallback Strategy:**
```php
// Always provide fallback text
{{ __('plugins/real-estate::vacation-rental.book_this_vacation_rental', [], 'Book This Vacation Rental') }}
```

## 🔧 **Current Status**

✅ **English (en)** - Fully supported with 813+ theme strings
❌ **Other languages** - Need to be added manually
✅ **Translation system** - Fully functional and ready for multi-language

## 📋 **Next Steps for Multi-language**

1. **Create language files** for target languages (Spanish, French, etc.)
2. **Translate all strings** in both theme JSON and plugin PHP files
3. **Add languages to database** via admin panel
4. **Test language switching** functionality
5. **Configure RTL support** if needed (Arabic, Hebrew, etc.)

The system is designed to seamlessly switch between languages once the translation files are properly set up!
