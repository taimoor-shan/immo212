<?php

namespace VigStudio\VigAutoTranslations\Commands;

use Botble\Translation\Manager;
use Botble\Translation\Services\GetGroupedTranslationsService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;

#[AsCommand('vig:translate:core', 'Enhanced auto translate ALL core/plugins (or specific groups) with multiple providers and caching')]
class EnhancedAutoTranslateCoreCommand extends Command implements PromptsForMissingInput
{
    public function handle(Manager $manager, EnhancedAutoTranslateManager $enhancedManager): int
    {
        $locale = $this->argument('locale');
        $driver = $this->option('driver');
        $clearCache = $this->option('clear-cache');
        $groups = $this->option('group');

        if (!preg_match('/^[a-z0-9\-_]+$/i', $locale)) {
            $this->components->error('Only alphabetic characters are allowed.');
            return self::FAILURE;
        }

        // Set the translation driver if specified
        if ($driver) {
            $enhancedManager->setTranslatorDriver($driver);
        }

        // Clear cache if requested
        if ($clearCache) {
            $enhancedManager->clearCache($locale);
            $this->components->info('Translation cache cleared for ' . $locale);
        }

        if ($this->option('override')) {
            $manager->deleteLocale($locale);
        }

        $manager->downloadLocaleIfMissing($locale);

        // Display provider information  
        $currentDriver = setting('vig_translate_driver', 'google');
        $providerNames = [
            'google' => 'Google Translate (Free)',
            'aws' => 'Amazon Translate (Enterprise)',
            'chatgpt' => 'ChatGPT/OpenAI (' . ($enhancedManager->getCurrentModelInfo()['name'] ?? 'GPT-4.1') . ')'
        ];
        
        $providerName = $providerNames[$currentDriver] ?? $currentDriver;
        
        // Show translation mode
        if ($groups && !empty($groups)) {
            $this->components->info(sprintf('🎯 Targeted Translation Mode: Translating %d specific groups to %s', count($groups), $locale));
            $this->components->info(sprintf('🔍 Groups: %s', implode(', ', $groups)));
        } else {
            $this->components->info(sprintf('🌍 Bulk Translation Mode: Translating ALL core/plugins to %s', $locale));
            $this->components->info('💡 This matches Botble\'s default behavior - all translation groups will be processed');
        }
        
        $this->components->info(sprintf('🔧 Using provider: %s', $providerName));
        $this->components->info('🔧 Processing plugin/core translations (PHP files)...');

        $translations = $this->getTranslations($locale, $groups);

        $this->components->info(sprintf('Found %d translation keys across %d groups.', 
            count($translations), 
            count($translations->groupBy('group'))
        ));

        // Show current statistics
        if ($this->getOutput()->isVerbose()) {
            $stats = $enhancedManager->getTranslationStats();
            $this->table(['Metric', 'Value'], [
                ['Cached Translations', $stats['cached_translations']],
                ['Supported Locales', count($stats['supported_locales'])],
                ['Cache Enabled', $stats['cache_enabled'] ? 'Yes' : 'No'],
                ['Cache TTL (days)', $stats['cache_ttl_days']],
                ['Current Driver', $stats['current_driver']],
            ]);

            // Show groups to be translated
            $groupList = $translations->groupBy('group')->keys()->toArray();
            $this->components->info('Translation groups: ' . implode(', ', $groupList));
        }

        $count = 0;
        $cached = 0;
        $errors = 0;
        $skipped = 0;

        $progressBar = $this->output->createProgressBar(count($translations));
        $progressBar->start();

        foreach ($translations->groupBy('group')->toArray() as $group => $translationGroup) {
            $autoTranslations = [];

            foreach ($translationGroup as $translation) {
                if (is_array($translation[$locale])) {
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                if ($translation['en'] !== $translation[$locale]) {
                    if ($this->getOutput()->isVerbose()) {
                        $this->components->info(sprintf('Already translated, skipped: <comment>%s</comment> => <info>%s</info>', 
                            $translation['en'], 
                            $translation[$locale]
                        ));
                    }
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                [$groupName, $key] = explode('::', $translation['key']);

                $translated = $enhancedManager->translate('en', $locale, $translation[$locale]);

                if ($translated && $translated !== $translation[$locale]) {
                    $autoTranslations[$key] = $translated;

                    if ($this->getOutput()->isVerbose()) {
                        $this->components->info(sprintf('Translate: <comment>%s</comment> => <info>%s</info>', 
                            $translation[$locale], 
                            $translated
                        ));
                    }

                    $count++;
                } else {
                    $errors++;
                    if ($this->getOutput()->isVerbose()) {
                        $this->components->warn(sprintf('Failed to translate: <comment>%s</comment>', 
                            $translation[$locale]
                        ));
                    }
                }

                $progressBar->advance();
            }

            // Save translations for this group
            if (!empty($autoTranslations)) {
                $manager->updateTranslation(
                    $locale,
                    str_replace('/', DIRECTORY_SEPARATOR, $group),
                    $autoTranslations
                );

                if ($this->getOutput()->isVerbose()) {
                    $this->components->info(sprintf('Saved %d translations for group: <info>%s</info>', 
                        count($autoTranslations), 
                        $group
                    ));
                }
            }
        }

        $progressBar->finish();
        $this->newLine();

        // Display comprehensive summary
        $this->newLine();
        $this->components->info("🎆 Translation Summary for {$locale} (Core/Plugins)");
        $this->table(['Metric', 'Count'], [
            ['New Translations', $count],
            ['Skipped (Already Translated)', $skipped],
            ['Errors', $errors],
            ['Total Processed', count($translations)],
            ['Groups Updated', count($translations->groupBy('group'))],
            ['Provider Used', $providerName],
            ['Files Updated', 'Plugin/Core PHP files'],
        ]);

        // Success message with next steps
        if ($count > 0) {
            $translationMode = ($groups && !empty($groups)) ? 'Targeted Group' : 'Bulk (All Groups)';
            $this->components->success("✨ {$translationMode} translation completed successfully! {$count} new translations added.");
            
            $this->components->info('🎆 Next Steps:');
            $this->components->bulletList([
                'Check translation cache: php artisan vig:translate:cache stats',
                'Test your admin panel in ' . $locale . ' language', 
                'Review translations in: lang/vendor/plugins/{plugin-name}/' . $locale . '/',
                'Clear cache if needed: php artisan cache:clear'
            ]);
            
            // Show publish information if there are groups that need publishing
            $this->newLine();
            $this->components->info('📋 Translation groups are ready!');
            $this->components->info('Note: File-based translations are immediately available - no publishing required.');
            
        } else if ($skipped > 0) {
            $this->components->info('📊 All core/plugin translations were already up to date!');
        }
        
        if ($errors > 0) {
            $this->components->warn("⚠️ {$errors} translations failed. Check logs for details.");
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to translate')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Force translate core again')
            ->addOption('driver', 'd', InputOption::VALUE_REQUIRED, 'Translation driver (google, aws, chatgpt)', null)
            ->addOption('clear-cache', 'c', InputOption::VALUE_NONE, 'Clear translation cache before processing')
            ->addOption('group', 'g', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL, 'Specific translation groups to process (optional - if not provided, all groups will be translated)');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'locale' => 'Which locale would you like to translate?',
        ];
    }

    /**
     * Get translations using Botble's GetGroupedTranslationsService for full compatibility
     * Supports bulk "translate all" when no groups specified, matching default Botble behavior
     */
    protected function getTranslations(string $locale, ?array $groups = null): Collection
    {
        $this->components->info('Loading translations using Botble\'s GetGroupedTranslationsService...');
        
        try {
            // Use Botble's official service for maximum compatibility
            $groupedTranslationsService = new GetGroupedTranslationsService();
            
            $translations = $groupedTranslationsService
                ->handle()
                ->transform(fn ($translation) => [
                    'key' => sprintf('%s::%s', $translation['group'], $translation['key']),
                    'en' => $translation['value'],
                ])
                ->transform(function ($translation) use ($locale) {
                    [$group, $key] = explode('::', $translation['key']);

                    // Try to get existing translation from file
                    $existingTranslation = $this->getExistingTranslation($group, $key, $locale);
                    
                    return [
                        ...$translation,
                        'group' => $group,
                        $locale => $existingTranslation ?: $translation['en'], // Use existing or fallback to English
                    ];
                });

            // Filter by specific groups if provided, otherwise translate ALL groups (bulk mode)
            if ($groups && !empty($groups)) {
                $translations = $translations->filter(function ($translation) use ($groups) {
                    return in_array($translation['group'], $groups);
                });
                
                $this->components->info(sprintf('📋 Group Filter Mode: Translating %d specific groups: %s', 
                    count($groups), 
                    implode(', ', $groups)
                ));
            } else {
                $totalGroups = $translations->pluck('group')->unique()->count();
                $this->components->info(sprintf('🌍 Bulk Translation Mode: Translating ALL %d available groups (same as Botble default behavior)', $totalGroups));
                $this->components->info('💡 Tip: Use --group="plugin-name" to translate specific groups only');
            }
            
            $this->components->info(sprintf('Loaded %d translation keys across %d groups', 
                $translations->count(), 
                $translations->pluck('group')->unique()->count()
            ));
            
            return $translations;
            
        } catch (\Exception $e) {
            $this->components->error('Failed to load translations via GetGroupedTranslationsService: ' . $e->getMessage());
            
            // Fallback to empty collection
            return collect();
        }
    }
    
    /**
     * Get existing translation from file system
     */
    protected function getExistingTranslation(string $group, string $key, string $locale): ?string
    {
        try {
            // Convert group format to translation key
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
}
