<?php

namespace VigStudio\VigAutoTranslations\Services\FileBased;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * File-based translation service that scans translation files
 * instead of relying on database queries
 */
class TranslationService
{
    /**
     * Get grouped translations from file system (not database)
     */
    public function getGroupedTranslations(): Collection
    {
        $translations = collect();
        
        try {
            // Get all plugin translation files
            $pluginTranslations = $this->getPluginTranslations();
            $translations = $translations->merge($pluginTranslations);
            
            // Get core translation files
            $coreTranslations = $this->getCoreTranslations();
            $translations = $translations->merge($coreTranslations);
            
            Log::info('File-based translations loaded', [
                'total_keys' => $translations->count(),
                'groups' => $translations->pluck('group')->unique()->count()
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to load file-based translations', [
                'error' => $e->getMessage()
            ]);
        }
        
        return $translations;
    }
    
    /**
     * Get plugin translations from lang/vendor/plugins directory
     */
    protected function getPluginTranslations(): Collection
    {
        $translations = collect();
        $pluginsPath = lang_path('vendor/plugins');
        
        if (!File::exists($pluginsPath)) {
            return $translations;
        }
        
        // Scan all plugin directories
        $pluginDirs = File::directories($pluginsPath);
        
        foreach ($pluginDirs as $pluginDir) {
            $pluginName = basename($pluginDir);
            
            // Look for English files to get the source keys
            $enDir = $pluginDir . '/en';
            if (!File::exists($enDir)) {
                continue;
            }
            
            $enFiles = File::files($enDir);
            
            foreach ($enFiles as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $group = "plugins/{$pluginName}/{$fileName}";
                
                try {
                    $translations_data = include $file->getPathname();
                    
                    if (is_array($translations_data)) {
                        foreach ($translations_data as $key => $value) {
                            if (is_string($value)) {
                                $translations->push([
                                    'group' => $group,
                                    'key' => $key,
                                    'value' => $value
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to load plugin translation file', [
                        'file' => $file->getPathname(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $translations;
    }
    
    /**
     * Get core translations from lang/vendor/core directory
     */
    protected function getCoreTranslations(): Collection
    {
        $translations = collect();
        $corePath = lang_path('vendor/core');
        
        if (!File::exists($corePath)) {
            return $translations;
        }
        
        // Scan core directories
        $coreDirs = File::directories($corePath);
        
        foreach ($coreDirs as $coreDir) {
            $coreName = basename($coreDir);
            
            // Look for English files
            $enDir = $coreDir . '/en';
            if (!File::exists($enDir)) {
                continue;
            }
            
            $enFiles = File::files($enDir);
            
            foreach ($enFiles as $file) {
                $fileName = pathinfo($file, PATHINFO_FILENAME);
                $group = "core/{$coreName}/{$fileName}";
                
                try {
                    $translations_data = include $file->getPathname();
                    
                    if (is_array($translations_data)) {
                        foreach ($translations_data as $key => $value) {
                            if (is_string($value)) {
                                $translations->push([
                                    'group' => $group,
                                    'key' => $key,
                                    'value' => $value
                                ]);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to load core translation file', [
                        'file' => $file->getPathname(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        return $translations;
    }
    
    /**
     * Check if a translation file exists for the given locale and group
     */
    public function translationFileExists(string $locale, string $group): bool
    {
        $filePath = $this->getTranslationFilePath($locale, $group);
        return File::exists($filePath);
    }
    
    /**
     * Get the file path for a specific translation group and locale
     */
    protected function getTranslationFilePath(string $locale, string $group): string
    {
        // Convert group format like "plugins/real-estate/vacation-rental" to file path
        if (str_starts_with($group, 'plugins/')) {
            $parts = explode('/', $group);
            $pluginName = $parts[1];
            $fileName = $parts[2];
            return lang_path("vendor/plugins/{$pluginName}/{$locale}/{$fileName}.php");
        } elseif (str_starts_with($group, 'core/')) {
            $parts = explode('/', $group);
            $coreName = $parts[1];
            $fileName = $parts[2];
            return lang_path("vendor/core/{$coreName}/{$locale}/{$fileName}.php");
        }
        
        return '';
    }
    
    /**
     * Get available locales by scanning existing translation directories
     */
    public function getAvailableLocales(): array
    {
        $locales = collect();
        
        // Scan plugin locales
        $pluginsPath = lang_path('vendor/plugins');
        if (File::exists($pluginsPath)) {
            $pluginDirs = File::directories($pluginsPath);
            foreach ($pluginDirs as $pluginDir) {
                $localeDirs = File::directories($pluginDir);
                foreach ($localeDirs as $localeDir) {
                    $locale = basename($localeDir);
                    if (strlen($locale) >= 2 && $locale !== 'vendor') {
                        $locales->push($locale);
                    }
                }
            }
        }
        
        // Scan core locales
        $corePath = lang_path('vendor/core');
        if (File::exists($corePath)) {
            $coreDirs = File::directories($corePath);
            foreach ($coreDirs as $coreDir) {
                $localeDirs = File::directories($coreDir);
                foreach ($localeDirs as $localeDir) {
                    $locale = basename($localeDir);
                    if (strlen($locale) >= 2 && $locale !== 'vendor') {
                        $locales->push($locale);
                    }
                }
            }
        }
        
        return $locales->unique()->sort()->values()->toArray();
    }
}
