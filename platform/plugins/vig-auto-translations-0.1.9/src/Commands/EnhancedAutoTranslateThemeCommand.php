<?php

namespace VigStudio\VigAutoTranslations\Commands;

use Botble\Translation\Manager;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;

#[AsCommand('vig:translate:theme', 'Enhanced auto translate theme with multiple providers and caching')]
class EnhancedAutoTranslateThemeCommand extends Command implements PromptsForMissingInput
{
    public function handle(Manager $manager, EnhancedAutoTranslateManager $enhancedManager): int
    {
        $locale = $this->argument('locale');
        $driver = $this->option('driver');
        $clearCache = $this->option('clear-cache');

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

        $this->components->info(sprintf('Translating %s using %s...', $locale, get_class($enhancedManager)));

        $translations = $manager->getThemeTranslations($locale);

        $this->components->info(sprintf('Found %d translation keys.', count($translations)));

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
        }

        $count = 0;
        $cached = 0;
        $errors = 0;

        $progressBar = $this->output->createProgressBar(count($translations));
        $progressBar->start();

        // Process translations in batches for better performance
        $batchSize = (int) $this->option('batch-size');
        $batches = array_chunk($translations, $batchSize, true);

        foreach ($batches as $batch) {
            $results = $enhancedManager->bulkTranslate('en', $locale, $batch);
            
            foreach ($results as $key => $translation) {
                if ($key === $translations[$key]) {
                    // Check if we got a cached result
                    if ($translation !== $key) {
                        $cached++;
                    }
                } else {
                    // Already translated, skip
                    continue;
                }

                if ($translation && $translation !== $key) {
                    if ($this->getOutput()->isVerbose()) {
                        $this->components->info(sprintf('Translate: <comment>%s</comment> => <info>%s</info>', $key, $translation));
                    }

                    $translations[$key] = $translation;
                    $count++;
                } else {
                    $errors++;
                    if ($this->getOutput()->isVerbose()) {
                        $this->components->warn(sprintf('Failed to translate: <comment>%s</comment>', $key));
                    }
                }

                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine();

        $manager->saveThemeTranslations($locale, $translations);

        // Display summary
        $this->components->info(sprintf('Translation Summary for %s:', $locale));
        $this->table(['Metric', 'Count'], [
            ['New Translations', $count],
            ['From Cache', $cached],
            ['Errors', $errors],
            ['Total Processed', count($translations)],
        ]);

        $this->components->info('Theme translations completed successfully!');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locale', InputArgument::REQUIRED, 'The locale name that you want to translate')
            ->addOption('override', 'o', InputOption::VALUE_NONE, 'Force translate theme again')
            ->addOption('driver', 'd', InputOption::VALUE_REQUIRED, 'Translation driver (google, aws, chatgpt)', null)
            ->addOption('clear-cache', 'c', InputOption::VALUE_NONE, 'Clear translation cache before processing')
            ->addOption('batch-size', 'b', InputOption::VALUE_REQUIRED, 'Batch size for bulk processing', 50);
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'locale' => 'Which locale would you like to translate?',
        ];
    }
}
