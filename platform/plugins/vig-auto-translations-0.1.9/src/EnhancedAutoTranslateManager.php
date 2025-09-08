<?php

namespace VigStudio\VigAutoTranslations;

use Botble\Translation\AutoTranslateManager as BaseAutoTranslateManager;
use Botble\Translation\Dictionary;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use VigStudio\VigAutoTranslations\Contracts\Translator;
use VigStudio\VigAutoTranslations\Services\AWSTranslator;
use VigStudio\VigAutoTranslations\Services\ChatGPTTranslator;
use VigStudio\VigAutoTranslations\Services\GoogleTranslator;

/**
 * Enhanced AutoTranslateManager that extends Botble's file-based translation system
 * with multiple providers, smart caching, and enhanced performance.
 * 
 * This manager is purely file-based following Botble's modern approach.
 */
class EnhancedAutoTranslateManager extends BaseAutoTranslateManager
{
    protected Translator $translator;
    
    protected bool $enableCaching = true;
    
    protected string $cachePrefix = 'vig_translation';
    
    protected int $cacheTtlDays = 30;

    public function __construct()
    {
        // Set default translator based on settings
        $this->setTranslatorDriver();
    }

    public function setTranslatorDriver(?string $driver = null): self
    {
        $driver = $driver ?: setting('vig_translate_driver', 'google');

        $this->translator = match ($driver) {
            'chatgpt' => new ChatGPTTranslator(),
            'aws' => new AWSTranslator(),
            default => new GoogleTranslator(),
        };
        
        return $this;
    }

    public function enableCaching(bool $enable = true): self
    {
        $this->enableCaching = $enable;
        return $this;
    }
    
    public function setCacheTtl(int $days): self
    {
        $this->cacheTtlDays = $days;
        return $this;
    }

    /**
     * Enhanced translate method that uses multiple providers and caching.
     * This method is purely file-based and doesn't use database storage.
     */
    public function translate(string $source, string $target, string $value): ?string
    {
        // First check dictionary (built-in Botble functionality)
        $dictionaryTranslation = app(Dictionary::class)->locale($target)->getTranslate($value);
        if ($dictionaryTranslation) {
            return $dictionaryTranslation;
        }

        // Check cache if enabled
        if ($this->enableCaching) {
            $cacheKey = $this->getCacheKey($source, $target, $value);
            $cachedTranslation = Cache::get($cacheKey);
            if ($cachedTranslation) {
                return $cachedTranslation;
            }
        }

        // Perform new translation using our enhanced translator
        $translated = $this->performTranslation($source, $target, $value);
        
        if ($translated && $translated !== $value) {
            // Cache the result for future use
            if ($this->enableCaching) {
                $this->cacheTranslation($source, $target, $value, $translated);
            }
            
            return $translated;
        }

        // Return original value if translation failed
        return $value;
    }

    /**
     * Perform the actual translation using the configured provider
     */
    protected function performTranslation(string $source, string $target, string $value): ?string
    {
        try {
            return $this->translator->translate($source, $target, $value);
        } catch (\Exception $e) {
            // Log error and fall back to parent implementation (Google Translate)
            logger()->warning('VIG Auto Translation Error: ' . $e->getMessage(), [
                'source' => $source,
                'target' => $target, 
                'value' => substr($value, 0, 100),
                'driver' => get_class($this->translator)
            ]);
            
            try {
                return parent::translate($source, $target, $value);
            } catch (\Exception $fallbackException) {
                logger()->error('VIG Translation fallback also failed: ' . $fallbackException->getMessage());
                return null;
            }
        }
    }
    
    /**
     * Generate cache key for translation
     */
    protected function getCacheKey(string $source, string $target, string $value): string
    {
        return $this->cachePrefix . ':' . $source . ':' . $target . ':' . md5($value);
    }
    
    /**
     * Cache a translation result
     */
    protected function cacheTranslation(string $source, string $target, string $original, string $translated): void
    {
        $cacheKey = $this->getCacheKey($source, $target, $original);
        Cache::put($cacheKey, $translated, now()->addDays($this->cacheTtlDays));
    }

    /**
     * Bulk translate method for efficient processing with smart caching
     */
    public function bulkTranslate(string $source, string $target, array $texts): array
    {
        if (empty($texts)) {
            return [];
        }

        logger()->info('Enhanced bulk translation started', [
            'provider' => get_class($this->translator),
            'source' => $source,
            'target' => $target,
            'count' => count($texts)
        ]);

        $translations = [];
        $uncachedTexts = [];
        $cacheHits = 0;
        
        // First pass: check cache for all texts
        if ($this->enableCaching) {
            foreach ($texts as $key => $text) {
                // Skip empty texts
                if (empty(trim($text))) {
                    $translations[$key] = $text;
                    continue;
                }
                
                $cacheKey = $this->getCacheKey($source, $target, $text);
                $cachedTranslation = Cache::get($cacheKey);
                
                if ($cachedTranslation) {
                    $translations[$key] = $cachedTranslation;
                    $cacheHits++;
                } else {
                    $uncachedTexts[$key] = $text;
                }
            }
        } else {
            // Filter out empty texts
            foreach ($texts as $key => $text) {
                if (empty(trim($text))) {
                    $translations[$key] = $text;
                } else {
                    $uncachedTexts[$key] = $text;
                }
            }
        }
        
        logger()->info('Cache analysis complete', [
            'cache_hits' => $cacheHits,
            'needs_translation' => count($uncachedTexts),
            'cache_hit_rate' => count($texts) > 0 ? round(($cacheHits / count($texts)) * 100, 2) . '%' : '0%'
        ]);
        
        // Second pass: translate uncached texts efficiently
        if (!empty($uncachedTexts)) {
            // Use provider-specific bulk translation if available
            if (method_exists($this->translator, 'bulkTranslateEfficient')) {
                $bulkResults = $this->translator->bulkTranslateEfficient($source, $target, $uncachedTexts);
                
                foreach ($bulkResults as $key => $translated) {
                    if ($translated && $translated !== $uncachedTexts[$key]) {
                        $translations[$key] = $translated;
                        
                        // Cache the result
                        if ($this->enableCaching) {
                            $this->cacheTranslation($source, $target, $uncachedTexts[$key], $translated);
                        }
                    } else {
                        // Fallback to original text if translation failed
                        $translations[$key] = $uncachedTexts[$key];
                    }
                }
            } else {
                // Fallback to individual translations
                foreach ($uncachedTexts as $key => $text) {
                    $translated = $this->translate($source, $target, $text);
                    $translations[$key] = $translated;
                }
            }
        }
        
        logger()->info('Bulk translation completed', [
            'total_items' => count($translations),
            'cache_hits' => $cacheHits,
            'new_translations' => count($uncachedTexts)
        ]);
        
        return $translations;
    }

    /**
     * Clear translation cache
     */
    public function clearCache(?string $locale = null): bool
    {
        try {
            if ($locale) {
                // Clear cache for specific locale
                $pattern = $this->cachePrefix . ':*:' . $locale . ':*';
            } else {
                // Clear all VIG translation cache
                $pattern = $this->cachePrefix . ':*';
            }
            
            // Try to clear using Redis if available
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getStore()->getRedis();
                $keys = $redis->keys($pattern);
                
                if (!empty($keys)) {
                    return $redis->del($keys) > 0;
                }
            } else {
                // For file-based cache, we can't easily pattern match,
                // so we'll just flush the entire cache store
                Cache::flush();
            }
            
            return true;
        } catch (\Exception $e) {
            logger()->error('Failed to clear VIG translation cache: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get translation statistics from cache and file system
     */
    public function getTranslationStats(): array
    {
        $cacheStats = $this->getCacheStats();
        $fileStats = $this->getFileStats();
        
        return [
            'cache_enabled' => $this->enableCaching,
            'current_driver' => class_basename($this->translator),
            'cache_ttl_days' => $this->cacheTtlDays,
            'cached_translations' => $cacheStats['total_cached'],
            'theme_translation_files' => $fileStats['theme_files'],
            'plugin_translation_files' => $fileStats['plugin_files'],
            'total_translation_files' => $fileStats['theme_files'] + $fileStats['plugin_files'],
            'supported_locales' => $fileStats['locales'],
        ];
    }
    
    /**
     * Get cache statistics
     */
    protected function getCacheStats(): array
    {
        // This is an approximation since we can't easily count cache entries
        // without iterating through all keys
        return [
            'total_cached' => 'N/A (file-based cache)',
        ];
    }
    
    /**
     * Get file-based translation statistics
     */
    protected function getFileStats(): array
    {
        $langPath = lang_path('vendor');
        $themeFiles = 0;
        $pluginFiles = 0;
        $locales = [];
        
        try {
            // Count theme translation files
            $themePath = $langPath . '/themes';
            if (File::exists($themePath)) {
                $themeFiles = count(File::allFiles($themePath));
                
                // Get locales from theme files
                foreach (File::directories($themePath) as $themeDir) {
                    foreach (File::files($themeDir) as $file) {
                        $locale = pathinfo($file, PATHINFO_FILENAME);
                        if (!in_array($locale, $locales)) {
                            $locales[] = $locale;
                        }
                    }
                }
            }
            
            // Count plugin translation files
            $pluginPath = $langPath . '/plugins';
            if (File::exists($pluginPath)) {
                $pluginFiles = count(File::allFiles($pluginPath));
                
                // Get locales from plugin files
                foreach (File::directories($pluginPath) as $pluginDir) {
                    foreach (File::directories($pluginDir) as $localeDir) {
                        $locale = basename($localeDir);
                        if (!in_array($locale, $locales)) {
                            $locales[] = $locale;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Could not gather translation file statistics: ' . $e->getMessage());
        }
        
        return [
            'theme_files' => $themeFiles,
            'plugin_files' => $pluginFiles,
            'locales' => $locales,
        ];
    }
    
    /**
     * Get available translation providers
     */
    public function getAvailableProviders(): array
    {
        return [
            'google' => [
                'name' => 'Google Translate',
                'description' => 'Free tier available, high quality translations',
                'requires_config' => false,
            ],
            'aws' => [
                'name' => 'Amazon Translate', 
                'description' => 'Enterprise-grade translation service',
                'requires_config' => true,
                'config_keys' => ['AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'AWS_DEFAULT_REGION'],
            ],
            'chatgpt' => [
                'name' => 'ChatGPT/OpenAI',
                'description' => 'Highest quality translations, slower and more expensive',
                'requires_config' => true,
                'config_keys' => ['OPENAI_API_KEY'],
            ],
        ];
    }
    
    /**
     * Get current model information for the active translator
     */
    public function getCurrentModelInfo(): array
    {
        $defaultInfo = ['name' => 'Unknown', 'version' => 'Unknown'];
        
        // Return model info based on current translator
        if ($this->translator instanceof ChatGPTTranslator) {
            $model = config('vig-auto-translations.chatgpt_model', 'gpt-4.1');
            return [
                'name' => $this->getModelDisplayName($model),
                'version' => $model,
                'provider' => 'OpenAI'
            ];
        }
        
        if ($this->translator instanceof AWSTranslator) {
            return [
                'name' => 'Amazon Translate',
                'version' => 'Latest',
                'provider' => 'AWS'
            ];
        }
        
        if ($this->translator instanceof GoogleTranslator) {
            return [
                'name' => 'Google Translate',
                'version' => 'v3',
                'provider' => 'Google'
            ];
        }
        
        return $defaultInfo;
    }
    
    /**
     * Get user-friendly model display name
     */
    private function getModelDisplayName(string $model): string
    {
        return match ($model) {
            'gpt-4.1' => 'GPT-4.1 Flagship',
            'gpt-4.1-mini' => 'GPT-4.1 Mini',
            'gpt-4.1-nano' => 'GPT-4.1 Nano',
            'gpt-4o' => 'GPT-4o',
            'gpt-4-turbo' => 'GPT-4 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            default => $model,
        };
    }
}
