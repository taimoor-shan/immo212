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

#[AsCommand('vig:translate:core', 'Enhanced auto translate core/plugins with multiple providers and caching')]
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

        $this->components->info(sprintf('Translating core/plugins to %s using enhanced manager...', $locale));

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

        // Display summary
        $this->components->info(sprintf('Core/Plugin Translation Summary for %s:', $locale));
        $this->table(['Metric', 'Count'], [
            ['New Translations', $count],
            ['Skipped (Already Translated)', $skipped],
            ['Errors', $errors],
            ['Total Processed', count($translations)],
            ['Groups Updated', count($translations->groupBy('group'))],
        ]);

        $this->components->info('Core/Plugin translations completed successfully!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to translate')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Force translate core again')
            ->addOption('driver', 'd', InputOption::VALUE_REQUIRED, 'Translation driver (google, aws, chatgpt)', null)
            ->addOption('clear-cache', 'c', InputOption::VALUE_NONE, 'Clear translation cache before processing')
            ->addOption('group', 'g', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Specific translation groups to process');
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'locale' => 'Which locale would you like to translate?',
        ];
    }

    /**
     * Get translations with optional group filtering
     */
    protected function getTranslations(string $locale, ?array $groups = null): Collection
    {
        $translations = (new GetGroupedTranslationsService())
            ->handle()
            ->transform(fn ($translation) => [
                'key' => sprintf('%s::%s', $translation['group'], $translation['key']),
                'en' => $translation['value'],
            ])
            ->transform(function ($translation) use ($locale) {
                [$group, $key] = explode('::', $translation['key']);

                return [
                    ...$translation,
                    'group' => $group,
                    $locale => trans(
                        Str::of($group)
                            ->replaceLast(DIRECTORY_SEPARATOR, '::')
                            ->append(".$key")
                            ->toString(),
                        [],
                        $locale
                    ),
                ];
            });

        // Filter by specific groups if provided
        if ($groups) {
            $translations = $translations->filter(function ($translation) use ($groups) {
                return in_array($translation['group'], $groups);
            });
        }

        return $translations;
    }
}
