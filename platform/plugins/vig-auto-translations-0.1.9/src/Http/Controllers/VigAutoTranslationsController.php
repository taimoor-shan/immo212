<?php

namespace VigStudio\VigAutoTranslations\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Language;
use Botble\Translation\Http\Requests\TranslationRequest;
use Botble\Translation\Manager;
use Botble\Translation\Models\Translation;
use Botble\Translation\Services\GetGroupedTranslationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;
use VigStudio\VigAutoTranslations\Manager as AutoTranslateManager;

class VigAutoTranslationsController extends BaseController
{
    public function __construct(
        protected AutoTranslateManager $autoTranslateManager,
        protected EnhancedAutoTranslateManager $enhancedManager
    ) {
    }

    public function getThemeTranslations(Request $request)
    {
        page_title()->setTitle(trans('plugins/vig-auto-translations::vig-auto-translations.title'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        $data = $this->getDataTranslations($request->input('ref_lang'));

        $translations = $data['translations'];
        $groups = $data['groups'];
        $group = $data['group'];
        $defaultLanguage = $data['defaultLanguage'];

        return view(
            'plugins/vig-auto-translations::theme-translations',
            compact('translations', 'groups', 'group', 'defaultLanguage')
        );
    }

    public function getDataTranslations(string|null $refLang): array
    {
        $groups = Language::getAvailableLocales();
        $defaultLanguage = Arr::get($groups, 'en');

        if (! $refLang) {
            $group = $defaultLanguage;
        } else {
            $group = Arr::first(
                Arr::where($groups, function ($item) use ($refLang) {
                    return $item['locale'] == $refLang;
                })
            );
        }

        $translations = $this->autoTranslateManager->getThemeTranslations($group['locale']);

        return [
            'translations' => $translations,
            'groups' => $groups,
            'group' => $group,
            'defaultLanguage' => $defaultLanguage,
        ];
    }

    public function postThemeTranslations(Request $request, BaseHttpResponse $response)
    {
        if (! File::isDirectory(lang_path())) {
            File::makeDirectory(lang_path());
        }

        if (! File::isWritable(lang_path())) {
            return $response
                ->setError()
                ->setMessage(
                    trans('plugins/translation::translation.folder_is_not_writeable', ['lang_path' => lang_path()])
                );
        }

        $locale = $request->input('pk');
        $name = $request->input('name');
        $value = $request->input('value');

        if ($request->input('auto') == 'true') {
            // Use enhanced manager for better translation quality
            $value = $this->enhancedManager->translate('en', $locale, $name) ?: $name;
        }

        if ($locale) {
            $translations = $this->autoTranslateManager->getThemeTranslations($locale);

            if ($request->has('name') && Arr::has($translations, $request->input('name'))) {
                $translations[$request->input('name')] = $value;
            }

            $this->autoTranslateManager->saveThemeTranslations($locale, $translations);
        }

        return $response
            ->setPreviousUrl(route('vig-auto-translations.theme'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function postThemeAllTranslations(Request $request, BaseHttpResponse $response)
    {
        $locale = $request->input('locale');
        $data = $this->getDataTranslations($locale);
        $translations = $data['translations'];

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        foreach ($translations as $key => $translation) {
            if ($key !== $translation) {
                continue;
            }

            // Use enhanced manager for better translation quality and caching
            $translations[$key] = $this->enhancedManager->translate('en', $locale, $key) ?: $key;
        }

        $this->autoTranslateManager->saveThemeTranslations($locale, $translations);

        return $response
            ->setPreviousUrl(route('vig-auto-translations.plugin'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * Get bulk translations interface - integrated into plugin translations page
     * Shows all available groups when no specific group is selected (like Botble's behavior)
     */
    protected function getBulkTranslationsInterface(Request $request, array $allGroups, string $refLang)
    {
        $locales = Language::getAvailableLocales();
        
        // Get current provider info
        $currentDriver = setting('vig_translate_driver', 'google');
        $providerNames = [
            'google' => 'Google Translate',
            'aws' => 'Amazon Translate',
            'chatgpt' => 'ChatGPT/OpenAI'
        ];
        $providerName = $providerNames[$currentDriver] ?? 'Google Translate';
        
        // Return the same plugin-translations view but with bulk mode data
        return view('plugins/vig-auto-translations::plugin-translations', compact(
            'allGroups',
            'locales',
            'refLang',
            'providerName',
            'currentDriver'
        ) + [
            'translations' => [], // Empty for bulk mode
            'translationData' => [], // Empty for bulk mode
            'group' => null, // No specific group selected = bulk mode
            'ref_lang' => $refLang,
            'editUrl' => route('vig-auto-translations.plugin.post'),
            'isBulkMode' => true // Flag to indicate bulk mode
        ]);
    }

    /**
     * Bulk translate ALL groups at once - integrated into plugin translations interface
     * This is the web UI equivalent of: php artisan vig:translate:core {locale}
     */
    public function postBulkTranslateAll(Request $request, BaseHttpResponse $response)
    {
        $locale = $request->input('locale');
        
        if (!$locale || $locale === 'en') {
            return $response
                ->setError()
                ->setMessage('Please select a valid target language (not English)');
        }

        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        try {
            // Use GetGroupedTranslationsService to get all translation groups (same as our enhanced command)
            $groupedTranslationsService = new GetGroupedTranslationsService();
            $allTranslations = $groupedTranslationsService->handle();
            
            $translated = 0;
            $skipped = 0;
            $errors = 0;
            $processedGroups = [];
            
            // Group translations by group name for batch processing
            $translationsByGroup = $allTranslations->groupBy('group');
            
            foreach ($translationsByGroup as $group => $translations) {
                $autoTranslations = [];
                
                foreach ($translations as $translation) {
                    $key = $translation['key'];
                    $englishValue = $translation['value'];
                    
                    // Get existing translation to check if already translated
                    $existingTranslation = $this->getExistingTranslationValue($group, $key, $locale);
                    
                    // Skip if already translated (different from English)
                    if ($existingTranslation && $existingTranslation !== $englishValue) {
                        $skipped++;
                        continue;
                    }
                    
                    // Use enhanced manager for translation (with caching and multiple providers)
                    $translatedValue = $this->enhancedManager->translate('en', $locale, $englishValue);
                    
                    if ($translatedValue && $translatedValue !== $englishValue) {
                        $autoTranslations[$key] = $translatedValue;
                        $translated++;
                    } else {
                        $errors++;
                    }
                }
                
                // Save translations to database (so they can be published later)
                if (!empty($autoTranslations)) {
                    foreach ($autoTranslations as $key => $translatedValue) {
                        $this->firstOrNewTranslation($locale, $group, $key, $translatedValue);
                    }
                    $processedGroups[] = $group;
                }
            }
            
            $message = sprintf(
                'Bulk translation completed! Translated %d strings across %d groups. Skipped: %d, Errors: %d',
                $translated,
                count($processedGroups),
                $skipped,
                $errors
            );
            
            return $response
                ->setPreviousUrl(route('vig-auto-translations.plugin'))
                ->setMessage($message);
                
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage('Bulk translation failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper method to get existing translation value from files
     */
    protected function getExistingTranslationValue(string $group, string $key, string $locale): ?string
    {
        try {
            $translationKey = Str::of($group)
                ->replaceLast(DIRECTORY_SEPARATOR, '::')
                ->append(".{$key}")
                ->toString();
            
            $translation = trans($translationKey, [], $locale);
            
            // Return null if translation is the same as the key (not found)
            return $translation === $translationKey ? null : $translation;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getPluginsTranslations(Request $request)
    {
        page_title()->setTitle(trans('plugins/translation::translation.translations'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        $group = $request->input('group');
        $refLang = $request->input('ref_lang', 'es'); // Default to Spanish
        
        // Get all available groups using GetGroupedTranslationsService (same as Botble)
        $groupedTranslationsService = new GetGroupedTranslationsService();
        $allGroups = $groupedTranslationsService->getGroups();
        
        // BULK MODE: When no specific group is selected, show all groups (like Botble's /admin/translations)
        if (!$group) {
            return $this->getBulkTranslationsInterface($request, $allGroups, $refLang);
        }

        // SINGLE GROUP MODE: When specific group is selected, show individual translations
        // Get English source translations using the same method as getLang but only for specific group
        $translations = $this->getLang();
        
        // Get existing translations using the same mechanism as the enhanced core command
        $existingTranslations = [];
        if ($group && $refLang) {
            // First, get database translations
            $dbTranslations = Translation::query()
                ->where('group', $group)
                ->where('locale', $refLang)
                ->get()
                ->keyBy('key');
                
            foreach ($dbTranslations as $translation) {
                $existingTranslations[$translation->key] = $translation;
            }
            
            // Then, load from files using the correct mechanism from enhanced core command
            // Use GetGroupedTranslationsService to get proper translations structure
            $allTranslations = (new GetGroupedTranslationsService())
                ->handle()
                ->filter(function ($translation) use ($group) {
                    return $translation['group'] === $group;
                });
                
            foreach ($allTranslations as $translation) {
                $key = $translation['key'];
                $englishValue = $translation['value'];
                
                // Get the translated value using the correct approach from enhanced core command
                $translatedValue = trans(
                    Str::of($group)
                        ->replaceLast(DIRECTORY_SEPARATOR, '::')
                        ->append(".{$key}")
                        ->toString(),
                    [],
                    $refLang
                );
                
                // If we got a real translation (different from English and not the key itself)
                if ($translatedValue !== $englishValue && $translatedValue !== $key) {
                    if (!isset($existingTranslations[$key])) {
                        // Create a temporary Translation object for display (file-based translation)
                        $tempTranslation = new Translation();
                        $tempTranslation->key = $key;
                        $tempTranslation->value = $translatedValue;
                        $tempTranslation->locale = $refLang;
                        $tempTranslation->group = $group;
                        $tempTranslation->status = Translation::STATUS_SAVED; // Mark as saved since it's from files
                        $tempTranslation->id = 0; // Temporary ID for file-based translations
                        
                        $existingTranslations[$key] = $tempTranslation;
                    }
                }
            }
        }

        $locales = Language::getAvailableLocales();

        return view('plugins/vig-auto-translations::plugin-translations')
            ->with('translations', $translations)
            ->with('translationData', $existingTranslations)
            ->with('locales', $locales)
            ->with('group', $group)
            ->with('ref_lang', $refLang)
            ->with('editUrl', route('translations.group.edit', ['group' => $group]));
    }

    public function getLang(): array
    {
        $basePath = base_path();
        $arrayPathGet = [
            'core' => $basePath . '/platform/core',
            'packages' => $basePath . '/platform/packages',
            'plugins' => $basePath . '/platform/plugins',
        ];

        $langArray = [];

        foreach ($arrayPathGet as $key => $vendorPath) {
            if (is_dir($vendorPath)) {
                $packages = File::directories($vendorPath);
                foreach ($packages as $package) {
                    $packageName = basename($package);
                    $packagePath = $vendorPath . '/' . $packageName;

                    $langPath = $packagePath . '/resources/lang';
                    if (! is_dir($langPath)) {
                        continue;
                    }

                    $files = File::allFiles($langPath);
                    foreach ($files as $file) {
                        $info = pathinfo($file);
                        $group = $info['filename'];
                        $subLangPath = str_replace($langPath . DIRECTORY_SEPARATOR, '', $info['dirname']);
                        $subLangPath = str_replace(DIRECTORY_SEPARATOR, '/', $subLangPath);

                        if ($subLangPath != $langPath) {
                            $group = substr($subLangPath, 0, -3) . '/' . $group;
                        }
                        $filePath = $key . '/' . $packageName . $group;

                        if (! is_readable($file->getPathname())) {
                            continue;
                        }

                        $translations = require $file->getPathname();
                        $langArray[$filePath] = Arr::dot($translations);
                    }
                }
            }
        }

        return $langArray;
    }

    public function postAllPluginsTranslations(Request $request, BaseHttpResponse $response)
    {
        $group = $request->input('group');
        $locale = $request->input('ref_lang');

        $allTranslations = $this->getLang()[$group];

        foreach ($allTranslations as $key => $value) {
            // Use enhanced manager for better translation quality and caching
            $translatedValue = $this->enhancedManager->translate('en', $locale, $value) ?: $value;
            $this->firstOrNewTranslation($locale, $group, $key, $translatedValue);
        }

        return $response;
    }

    public function postPluginsTranslations(TranslationRequest $request, Manager $manager, BaseHttpResponse $response)
    {
        $group = $request->input('group');

        if (! in_array($group, $manager->getConfig('exclude_groups'))) {
            $name = $request->input('name');
            $value = $request->input('value');

            [$locale, $key] = explode('|', $name, 2);

            if ($request->input('auto') == 'true') {
                // Use enhanced manager for better translation quality and caching
                $value = $this->enhancedManager->translate('en', $locale, $value) ?: $value;
            }

            $this->firstOrNewTranslation($locale, $group, $key, $value);
        }

        return $response;
    }

    public function firstOrNewTranslation(string $locale, string $group, string $key, string $value): void
    {
        $translation = Translation::query()->firstOrNew([
            'locale' => $locale,
            'group' => $group,
            'key' => $key,
        ]);
        $translation->value = $value ?: null;
        $translation->status = Translation::STATUS_CHANGED;
        $translation->save();
    }

    public function getAutoTranslate(Request $request, BaseHttpResponse $response)
    {
        if (($locale = $request->input('locale')) &&
            ($name = $request->input('name')) &&
            in_array($locale, array_keys(Language::getAvailableLocales()))) {
            // Use enhanced manager for better translation quality and caching
            $value = $this->enhancedManager->translate('en', $locale, $name) ?: $name;

            return $response->setData([$locale => $value]);
        }

        return $response->setCode(404)->setError();
    }

    /**
     * Publish translation group to files
     * This method handles the "Publish Translations" button functionality
     */
    public function publishTranslationGroup(Request $request, BaseHttpResponse $response)
    {
        $group = $request->input('group');
        
        if (empty($group)) {
            return $response
                ->setError()
                ->setMessage('Translation group is required');
        }

        try {
            // Get all translations for this group from database (including changed and saved)
            $changedTranslations = Translation::query()
                ->where('group', $group)
                ->where('status', Translation::STATUS_CHANGED)
                ->get()
                ->groupBy('locale');
                
            // Also get all translations for this group to show total count
            $allTranslations = Translation::query()
                ->where('group', $group)
                ->get()
                ->groupBy('locale');

            $publishedCount = 0;
            $totalCount = 0;
            
            // Process changed translations
            foreach ($changedTranslations as $locale => $localeTranslations) {
                $translationArray = [];
                
                foreach ($localeTranslations as $translation) {
                    $translationArray[$translation->key] = $translation->value;
                }
                
                if (!empty($translationArray)) {
                    // Use Botble's translation manager to save to files  
                    app(Manager::class)->updateTranslation(
                        $locale,
                        str_replace('/', DIRECTORY_SEPARATOR, $group),
                        $translationArray
                    );
                    $publishedCount += count($translationArray);
                }
                
                // Mark translations as published
                Translation::query()
                    ->where('group', $group)
                    ->where('locale', $locale)
                    ->where('status', Translation::STATUS_CHANGED)
                    ->update(['status' => Translation::STATUS_SAVED]);
            }
            
            // Count total translations for this group
            foreach ($allTranslations as $locale => $localeTranslations) {
                $totalCount += count($localeTranslations);
            }
            
            // Provide appropriate message based on what happened
            if ($publishedCount > 0) {
                $message = "Successfully published {$publishedCount} new/changed translations for group '{$group}'. Files have been updated and are ready to use.";
            } elseif ($totalCount > 0) {
                $message = "No new translations to publish for group '{$group}'. All {$totalCount} translations are already up to date in files.";
            } else {
                $message = "No translations found for group '{$group}'. Please translate some strings first, then publish.";
            }

            return $response->setMessage($message);
                
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage('Failed to publish translations: ' . $e->getMessage());
        }
    }
}
