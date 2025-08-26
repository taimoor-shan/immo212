<?php

namespace VigStudio\VigAutoTranslations\Providers;

use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Translation\AutoTranslateManager as BotbleAutoTranslateManager;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use VigStudio\VigAutoTranslations\Commands\AutoTranslateCommand;
use VigStudio\VigAutoTranslations\Commands\EnhancedAutoTranslateCoreCommand;
use VigStudio\VigAutoTranslations\Commands\EnhancedAutoTranslateThemeCommand;
use VigStudio\VigAutoTranslations\Commands\TranslationCacheCommand;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;
use VigStudio\VigAutoTranslations\Manager;
use VigStudio\VigAutoTranslations\Services\AWSTranslator;
use VigStudio\VigAutoTranslations\Services\ChatGPTTranslator;
use VigStudio\VigAutoTranslations\Services\GoogleTranslator;

class VigAutoTranslationsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('plugins/vig-auto-translations')->loadHelpers();

        // Register the legacy manager for backward compatibility
        $this->app->singleton(Manager::class, function () {
            $manager = new Manager();

            $driver = setting('vig_translate_driver');
            $withoutDatabase = setting('vig_translate_without_database', false);

            return match ($driver) {
                'chatgpt' => $manager->setDriver(new ChatGPTTranslator())->setWithoutDatabase($withoutDatabase),
                'aws' => $manager->setDriver(new AWSTranslator())->setWithoutDatabase($withoutDatabase),
                default => $manager->setDriver(new GoogleTranslator())->setWithoutDatabase($withoutDatabase),
            };
        });

        // Register the enhanced manager that extends Botble's system
        $this->app->singleton(EnhancedAutoTranslateManager::class, function () {
            return new EnhancedAutoTranslateManager();
        });

        // Extend Botble's AutoTranslateManager to use our enhanced version
        $this->app->extend(BotbleAutoTranslateManager::class, function ($manager, $app) {
            return $app->make(EnhancedAutoTranslateManager::class);
        });
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigurations(['permissions', 'general'])
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes();
            
        // Load migrations only if database caching is explicitly enabled
        // Modern Botble uses file-based translations, so database is optional
        if (setting('vig_translate_enable_database_cache', false)) {
            $this->loadMigrations();
        }

        // Register all commands
        $this->commands([
            AutoTranslateCommand::class,
            EnhancedAutoTranslateThemeCommand::class,
            EnhancedAutoTranslateCoreCommand::class,
            TranslationCacheCommand::class,
        ]);

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        PanelSectionManager::default()->beforeRendering(function () {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('blog')
                    ->setTitle('VigAutoTranslations')
                    ->withIcon('ti ti-language')
                    ->withDescription('Dịch tự động')
                    ->withPriority(120)
                    ->withRoute('vig-auto-translations.settings')
            );
        });

        // Register dashboard menu items for easy end-user access
        $this->app['events']->listen(RouteMatched::class, function () {
            // Main menu item
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-vig-auto-translations',
                    'priority' => 80, 
                    'parent_id' => null,
                    'name' => 'plugins/vig-auto-translations::vig-auto-translations.title',
                    'icon' => 'ti ti-language',
                    'url' => route('vig-auto-translations.theme'),
                    'permissions' => ['vig-auto-translations.index'],
                ]);
                
            // Sub-menu items
            dashboard_menu()
                ->registerItem([
                    'id' => 'cms-plugins-vig-auto-translations-theme',
                    'priority' => 1,
                    'parent_id' => 'cms-plugins-vig-auto-translations',
                    'name' => 'plugins/vig-auto-translations::vig-auto-translations.name_theme',
                    'icon' => null,
                    'url' => route('vig-auto-translations.theme'),
                    'permissions' => ['vig-auto-translations.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-vig-auto-translations-plugin',
                    'priority' => 2,
                    'parent_id' => 'cms-plugins-vig-auto-translations', 
                    'name' => 'plugins/vig-auto-translations::vig-auto-translations.name_plugin',
                    'icon' => null,
                    'url' => route('vig-auto-translations.plugin'),
                    'permissions' => ['vig-auto-translations.index'],
                ])
                ->registerItem([
                    'id' => 'cms-plugins-vig-auto-translations-settings',
                    'priority' => 3,
                    'parent_id' => 'cms-plugins-vig-auto-translations',
                    'name' => 'core/setting::setting.title',
                    'icon' => null,
                    'url' => route('vig-auto-translations.settings'),
                    'permissions' => ['vig-auto-translations.index'],
                ]);
        });
    }
}
