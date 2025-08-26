<?php

namespace VigStudio\VigAutoTranslations\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;

#[AsCommand('vig:translate:cache', 'Manage translation cache (clear, stats, warm-up)')]
class TranslationCacheCommand extends Command
{
    public function handle(EnhancedAutoTranslateManager $enhancedManager): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'clear' => $this->clearCache($enhancedManager),
            'stats' => $this->showStats($enhancedManager),
            'warm-up' => $this->warmUpCache($enhancedManager),
            default => $this->showHelp(),
        };
    }

    protected function clearCache(EnhancedAutoTranslateManager $enhancedManager): int
    {
        $locale = $this->option('locale');
        
        if ($locale) {
            $enhancedManager->clearCache($locale);
            $this->components->info("Translation cache cleared for locale: {$locale}");
        } else {
            $enhancedManager->clearCache();
            $this->components->info('All translation cache cleared');
        }

        return self::SUCCESS;
    }

    protected function showStats(EnhancedAutoTranslateManager $enhancedManager): int
    {
        $stats = $enhancedManager->getTranslationStats();

        $this->components->info('Translation Statistics:');
        $this->table(['Metric', 'Value'], [
            ['Cached Translations', $stats['cached_translations']],
            ['Supported Locales', count($stats['supported_locales'])],
            ['Cache Enabled', $stats['cache_enabled'] ? 'Yes' : 'No'],
            ['Cache TTL (days)', $stats['cache_ttl_days']],
            ['Current Driver', $stats['current_driver']],
            ['Theme Translation Files', $stats['theme_translation_files']],
            ['Plugin Translation Files', $stats['plugin_translation_files']],
            ['Total Translation Files', $stats['total_translation_files']],
        ]);

        return self::SUCCESS;
    }

    protected function warmUpCache(EnhancedAutoTranslateManager $enhancedManager): int
    {
        $this->components->warn('Cache warm-up not implemented yet. Use the translation commands to populate cache.');
        
        return self::SUCCESS;
    }

    protected function showHelp(): int
    {
        $this->components->error('Invalid action. Available actions: clear, stats, warm-up');
        $this->line('');
        $this->line('Examples:');
        $this->line('  php artisan vig:translate:cache clear           # Clear all cache');
        $this->line('  php artisan vig:translate:cache clear --locale=es # Clear Spanish cache');
        $this->line('  php artisan vig:translate:cache stats           # Show statistics');
        $this->line('  php artisan vig:translate:cache warm-up         # Warm up cache');

        return self::FAILURE;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('action', InputArgument::REQUIRED, 'Action to perform: clear, stats, warm-up')
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'Specific locale for cache operations');
    }
}
