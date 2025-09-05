# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

**VIG Auto Translations Pro v1.0.0** - A professional translation suite for Botble CMS that extends the native file-based translation system with enhanced functionality, multiple translation providers, smart caching, and bulk processing capabilities.

## Architecture

This plugin follows a **composition-over-inheritance** approach, extending Botble CMS's native translation system rather than replacing it:

- **Enhanced Manager Pattern**: `EnhancedAutoTranslateManager` extends Botble's `AutoTranslateManager`
- **Service Provider Extension**: Registers enhanced services alongside native Botble services 
- **Command Enhancement**: New commands coexist with existing Botble translation commands
- **File-Based Architecture**: Follows Botble's modern JSON (themes) + PHP arrays (plugins) approach

### Core Components

1. **Enhanced Manager** (`src/EnhancedAutoTranslateManager.php`):
   - Extends Botble's `AutoTranslateManager`
   - Provides multi-provider translation (Google, AWS, ChatGPT)
   - Implements smart caching with 30-day expiration
   - Supports bulk processing with configurable batch sizes

2. **Translation Providers** (`src/Services/`):
   - `GoogleTranslator` - Free tier Google Translate API
   - `AWSTranslator` - Enterprise AWS Translate service  
   - `ChatGPTTranslator` - High-quality OpenAI translations with GPT-4o support and customizable system messages

3. **Enhanced Commands** (`src/Commands/`):
   - `EnhancedAutoTranslateThemeCommand` - Theme translation with progress bars
   - `EnhancedAutoTranslateCoreCommand` - Plugin/core translation with group filtering
   - `TranslationCacheCommand` - Cache management and statistics

## Development Commands

### Core Translation Commands

```bash
# Enhanced theme translation
php artisan vig:translate:theme {locale} [--driver=google|aws|chatgpt] [--batch-size=50] [--verbose] [--override] [--clear-cache]

# Enhanced core/plugin translation  
php artisan vig:translate:core {locale} [--driver=google|aws|chatgpt] [--group=plugin-name] [--verbose] [--override] [--clear-cache]

# Cache management
php artisan vig:translate:cache {clear|stats|warm-up} [--locale=locale]
```

### Example Workflows

```bash
# Complete Spanish translation workflow
php artisan vig:translate:theme es --verbose --driver=chatgpt
php artisan vig:translate:core es --verbose --driver=chatgpt
php artisan vig:translate:cache stats

# Target specific plugins
php artisan vig:translate:core es --group=real-estate --group=blog

# Performance optimization
php artisan vig:translate:theme es --batch-size=25 --clear-cache
```

### Native Botble Commands (Still Available)

```bash
# Original Botble commands work alongside enhanced versions
php artisan cms:translation:auto-translate-theme es
php artisan cms:translation:auto-translate-core es
```

### Testing & Debugging

```bash
# Integration test script
php test-integration.php

# Test all translation providers
php test-providers.php

# Statistics and monitoring
php artisan vig:translate:cache stats

# Debug mode with verbose output
php artisan vig:translate:theme es --verbose --driver=google
```

## Configuration

### Environment Variables

```env
# Translation providers
VIG_TRANSLATE_DRIVER=google|aws|chatgpt

# AWS Translate (if using aws driver)
AWS_ACCESS_KEY_ID=your_key
AWS_SECRET_ACCESS_KEY=your_secret
AWS_DEFAULT_REGION=us-east-1

# ChatGPT/OpenAI (if using chatgpt driver)
OPENAI_API_KEY=your_openai_api_key
OPENAI_MODEL=gpt-4.1  # GPT-4.1 flagship (can also use gpt-4.1-mini, gpt-4.1-nano, gpt-4o, gpt-3.5-turbo)
OPENAI_SYSTEM_MESSAGE="Your custom translation instructions here"

# Plugin settings
VIG_TRANSLATE_WITHOUT_DATABASE=false
VIG_TRANSLATE_CACHE_ENABLED=true
VIG_TRANSLATE_ENABLE_DATABASE_CACHE=false
```

### Admin Panel Configuration

Navigate to: **Settings → Others → Smart Auto Translations Pro**

#### ChatGPT/OpenAI Advanced Configuration

- **Model Selection**: 
  - **GPT-4.1** (Latest Flagship): Superior coding (+21.4% vs GPT-4o), better instruction following (+10.5%), 1M token context, June 2024 knowledge
  - **GPT-4.1 Mini**: Smaller, faster variant with excellent performance-to-cost ratio
  - **GPT-4.1 Nano**: Smallest, lowest-latency variant optimized for speed
  - Legacy models: GPT-4o, GPT-4 Turbo, GPT-4, GPT-3.5 Turbo
- **Custom System Message**: Define your own translation instructions and style
- **Available Placeholders**: `{source_language}`, `{target_language}`, `{source}`, `{target}`

**Example Custom System Message:**
```
You are a professional translator specializing in {source_language} to {target_language} translations for e-commerce websites. Maintain a friendly, commercial tone and use terminology appropriate for online shopping. Keep all product names and brand references unchanged.
```

## File Structure & Locations

### Translation Files (Standard Botble Locations)

```
Theme Translations (JSON):
├── lang/vendor/themes/{theme-name}/en.json
├── lang/vendor/themes/{theme-name}/es.json
└── lang/vendor/themes/{theme-name}/fr.json

Plugin Translations (PHP):
├── lang/vendor/plugins/{plugin-name}/es/file.php
├── lang/vendor/plugins/{plugin-name}/es/another.php
└── lang/vendor/plugins/core/es/base.php
```

### Plugin Structure

```
src/
├── Commands/                    # Enhanced CLI commands
├── Contracts/                   # Translation interfaces
├── Services/                    # Translation provider implementations
├── Providers/                   # Laravel service providers
├── Http/Controllers/           # Web interface controllers
├── Forms/Settings/             # Admin configuration forms
└── EnhancedAutoTranslateManager.php  # Core enhanced manager
```

## Key Development Patterns

### Service Provider Pattern

The plugin uses Laravel's service provider pattern to extend Botble's services:

```php
// Registers enhanced manager alongside native Botble manager
$this->app->singleton(EnhancedAutoTranslateManager::class);
$this->app->extend(BotbleAutoTranslateManager::class, function ($manager, $app) {
    return $app->make(EnhancedAutoTranslateManager::class);
});
```

### Command Enhancement Pattern

Enhanced commands coexist with native commands:
- Native: `cms:translation:auto-translate-theme`
- Enhanced: `vig:translate:theme` (with additional features)

### Caching Strategy

Dual-layer caching approach:
1. **Laravel Cache** (Redis/File) - Short-term performance cache
2. **Optional Database** - Long-term translation memory (disabled by default)

### Error Resilience

Graceful fallback mechanism:
1. Try enhanced translator (Google/AWS/ChatGPT)
2. Fall back to Botble's native Google Translate
3. Return original text if all fail

## Development Guidelines

### Adding New Translation Providers

1. Implement `VigStudio\VigAutoTranslations\Contracts\Translator` interface
2. Add provider to `EnhancedAutoTranslateManager::setTranslatorDriver()`
3. Register in service provider
4. Update configuration options

### Extending Commands

Commands inherit from Laravel's `Command` class and implement:
- Progress bars for long operations
- Verbose output modes
- Batch processing capabilities
- Statistics reporting

### File-Based Approach

This plugin follows Botble's modern file-based translation approach:
- **No database migrations** required by default
- **JSON files** for theme translations
- **PHP array files** for plugin/core translations
- **Cache layer** for performance optimization

## Troubleshooting Commands

```bash
# Clear all caches
php artisan vig:translate:cache clear
php artisan cache:clear
php artisan config:clear

# Check system status
php artisan vig:translate:cache stats

# Test specific provider
php artisan vig:translate:theme test --driver=google --verbose

# Integration test
php test-integration.php
```

## Testing

The plugin includes comprehensive testing:
- `test-integration.php` - Full integration testing script
- `test-providers.php` - Provider verification and API testing
- Command validation with `--verbose` flags
- Statistics monitoring via cache commands
- Provider switching validation

### Provider Testing

Use the provider test script to verify all translation services work correctly:

```bash
# Test all configured providers
php test-providers.php
```

This script will:
- ✅ Check API configuration for each provider
- 🧪 Test actual translation functionality
- ⏱️ Measure performance and response times
- 🔍 Validate language pair support
- 📊 Provide detailed results and recommendations

## Backward Compatibility

Full backward compatibility maintained:
- Existing translation files work unchanged
- Native Botble commands remain functional  
- Settings and API keys preserved
- Database tables optional (modern file-based approach preferred)
