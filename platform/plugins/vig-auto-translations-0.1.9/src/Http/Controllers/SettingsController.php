<?php

namespace VigStudio\VigAutoTranslations\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use VigStudio\VigAutoTranslations\Forms\Settings\AutoTranslateSettingForm;
use VigStudio\VigAutoTranslations\Http\Requests\SettingRequest;

class SettingsController extends SettingController
{
    public function settings(Request $request)
    {
        $this->pageTitle('VIG Auto Translations Pro Settings');

        $form = AutoTranslateSettingForm::create();
        
        return view('plugins/vig-auto-translations::settings.index', compact('form'));
    }

    public function update(SettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }

    /**
     * Clear all caches (translation cache + Laravel caches)
     */
    public function clearAllCaches(Request $request, BaseHttpResponse $response)
    {
        try {
            $clearedCaches = [];
            
            // Clear translation-specific caches
            $translationCacheCleared = Cache::flush();
            if ($translationCacheCleared) {
                $clearedCaches[] = 'Translation cache';
            }
            
            // Clear Laravel application cache
            try {
                Artisan::call('cache:clear');
                $clearedCaches[] = 'Application cache';
            } catch (\Exception $e) {
                // Log but don't fail
                logger()->warning('Failed to clear application cache: ' . $e->getMessage());
            }
            
            // Clear config cache
            try {
                Artisan::call('config:clear');
                $clearedCaches[] = 'Configuration cache';
            } catch (\Exception $e) {
                logger()->warning('Failed to clear config cache: ' . $e->getMessage());
            }
            
            // Clear route cache
            try {
                Artisan::call('route:clear');
                $clearedCaches[] = 'Route cache';
            } catch (\Exception $e) {
                logger()->warning('Failed to clear route cache: ' . $e->getMessage());
            }
            
            // Clear view cache
            try {
                Artisan::call('view:clear');
                $clearedCaches[] = 'View cache';
            } catch (\Exception $e) {
                logger()->warning('Failed to clear view cache: ' . $e->getMessage());
            }
            
            // Clear optimization caches (if available)
            try {
                Artisan::call('optimize:clear');
                $clearedCaches[] = 'Optimization cache';
            } catch (\Exception $e) {
                // This is optional, so just log
                logger()->info('Optimize:clear not available or failed: ' . $e->getMessage());
            }
            
            $message = count($clearedCaches) > 0 
                ? 'Successfully cleared: ' . implode(', ', $clearedCaches)
                : 'Cache clearing completed';
                
            return $response
                ->setData(['cleared_caches' => $clearedCaches])
                ->setMessage($message);
                
        } catch (\Exception $e) {
            logger()->error('Failed to clear caches: ' . $e->getMessage());
            
            return $response
                ->setError()
                ->setMessage('Failed to clear caches: ' . $e->getMessage());
        }
    }
    
    /**
     * Clear only translation-specific caches
     */
    public function clearTranslationCache(Request $request, BaseHttpResponse $response)
    {
        try {
            // Clear translation cache using the enhanced manager
            $enhancedManager = app(\VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager::class);
            $stats = $enhancedManager->clearCache(); // Clear all locales
            
            // Also clear general cache tags that might contain translations
            $cacheKeys = [];
            foreach (['es', 'fr', 'de', 'it', 'pt', 'ru', 'zh', 'ja', 'ar'] as $locale) {
                $pattern = "vig_translation:*:{$locale}:*";
                try {
                    // Try to clear by pattern (depends on cache driver)
                    Cache::forget($pattern);
                    $cacheKeys[] = $locale;
                } catch (\Exception $e) {
                    // Pattern clearing might not work with all cache drivers
                }
            }
            
            $message = count($cacheKeys) > 0 
                ? 'Translation cache cleared for locales: ' . implode(', ', $cacheKeys)
                : 'Translation cache cleared successfully';
                
            return $response
                ->setData([
                    'cleared_locales' => $cacheKeys,
                    'stats' => $stats
                ])
                ->setMessage($message);
                
        } catch (\Exception $e) {
            logger()->error('Failed to clear translation cache: ' . $e->getMessage());
            
            return $response
                ->setError()
                ->setMessage('Failed to clear translation cache: ' . $e->getMessage());
        }
    }
}
