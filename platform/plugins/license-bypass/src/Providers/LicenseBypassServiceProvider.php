<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\LicenseBypass\Services\HttpInterceptorService;
use Botble\LicenseBypass\Services\LicenseBypassService;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

/**
 * Service provider for the License Bypass plugin
 *
 * This provider handles the registration and booting of the license bypass functionality.
 * It follows Laravel best practices and includes proper error handling and environment checks.
 */
class LicenseBypassServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * Register services into the container
     */
    public function register(): void
    {
        // Register the main bypass service
        $this->app->singleton(LicenseBypassService::class);

        // Register the HTTP interceptor service
        $this->app->singleton(HttpInterceptorService::class, function ($app) {
            return new HttpInterceptorService($app->make(LicenseBypassService::class));
        });
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        try {
            $this->setNamespace('plugins/license-bypass')
                ->loadAndPublishConfigurations(['permissions', 'license-bypass'])
                ->loadAndPublishViews()
                ->loadRoutes();

            // Only proceed if the bypass service is enabled
            $bypassService = $this->app->make(LicenseBypassService::class);

            if (!$bypassService->isEnabled()) {
                return;
            }

            // Set up all bypass functionality
            $this->setupBypassFunctionality($bypassService);

        } catch (\Exception $e) {
            // Log error but don't break the application
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to boot plugin: ' . $e->getMessage());
            }
        }
    }

    /**
     * Set up all bypass functionality
     */
    private function setupBypassFunctionality(LicenseBypassService $bypassService): void
    {
        // Create necessary bypass files
        $bypassService->createBypassFiles();

        // Set up HTTP interception
        $this->setupHttpInterception();

        // Register middleware
        $this->registerMiddleware();

        // Override license-related functionality
        $this->overrideLicenseFunctionality($bypassService);

        // Disable tracking services
        $this->disableTrackingServices($bypassService);

        // Override views
        $this->overrideViews();
    }

    /**
     * Set up HTTP request interception
     */
    private function setupHttpInterception(): void
    {
        try {
            $interceptorService = $this->app->make(HttpInterceptorService::class);
            $interceptorService->setupInterception();
        } catch (BindingResolutionException $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to setup HTTP interception: ' . $e->getMessage());
            }
        }
    }

    /**
     * Register middleware
     */
    private function registerMiddleware(): void
    {
        try {
            // Register our bypass middleware globally
            $this->app['router']->pushMiddlewareToGroup(
                'web',
                \Botble\LicenseBypass\Http\Middleware\DisableLicenseMiddleware::class
            );

            // Listen for route matched event to ensure middleware is applied
            Event::listen(RouteMatched::class, function (): void {
                $this->ensureMiddlewareRegistration();
            });
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to register middleware: ' . $e->getMessage());
            }
        }
    }

    /**
     * Ensure middleware is properly registered
     */
    private function ensureMiddlewareRegistration(): void
    {
        try {
            $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);
            if (method_exists($kernel, 'prependMiddleware')) {
                $kernel->prependMiddleware(\Botble\LicenseBypass\Http\Middleware\DisableLicenseMiddleware::class);
            }
        } catch (\Exception $e) {
            // Silently continue if we can't modify the kernel
        }
    }

    /**
     * Override license-related functionality
     */
    private function overrideLicenseFunctionality(LicenseBypassService $bypassService): void
    {
        $overrideSettings = $bypassService->getLicenseOverrideSettings();

        // Override license-related settings
        add_filter('setting_licensed_to', function () use ($overrideSettings) {
            return $overrideSettings['licensed_to'] ?? 'Local Development (License Bypassed)';
        });

        add_filter('setting_license_activated_at', function () use ($overrideSettings) {
            return $overrideSettings['activated_at'] ?? now()->format('M d Y');
        });

        // Always return true for license verification
        add_filter('core_license_verified', function () use ($overrideSettings) {
            return $overrideSettings['status'] ?? true;
        });

        // Override license file existence checks
        add_filter('license_file_exists', function () {
            return true;
        });
    }

    /**
     * Configure tracking services (now enabled per user request)
     */
    private function disableTrackingServices(LicenseBypassService $bypassService): void
    {
        $trackingSettings = $bypassService->getTrackingDisableSettings();

        // Google Analytics - now enabled per user request
        if ($trackingSettings['google_analytics'] ?? false) {
            config(['plugins.analytics.general.enabled' => false]);
            config(['plugins.analytics.general.enabled_dashboard_widgets' => false]);
        }

        // Facebook integration - now enabled per user request
        if ($trackingSettings['facebook_integration'] ?? false) {
            add_filter('theme_option_facebook_chat_enabled', fn() => 'no');
            add_filter('theme_option_facebook_app_id', fn() => '');
            add_filter('theme_option_facebook_page_id', fn() => '');
        }

        // External scripts and fonts - now enabled per user request
        if ($trackingSettings['external_scripts'] ?? false) {
            $this->blockExternalAssets($bypassService);
        }
    }

    /**
     * Block external assets (scripts and stylesheets)
     */
    private function blockExternalAssets(LicenseBypassService $bypassService): void
    {
        $blockedDomains = $bypassService->getBlockedDomains();

        // Block external JavaScript
        add_filter('script_loader_src', function ($src) use ($blockedDomains) {
            if (!is_string($src)) {
                return $src;
            }

            foreach ($blockedDomains as $domain) {
                if (str_contains($src, $domain)) {
                    return false;
                }
            }

            return $src;
        });

        // Block external CSS
        add_filter('style_loader_src', function ($src) use ($blockedDomains) {
            if (!is_string($src)) {
                return $src;
            }

            foreach ($blockedDomains as $domain) {
                if (str_contains($src, $domain)) {
                    return false;
                }
            }

            return $src;
        });
    }

    /**
     * Override license-related views
     */
    private function overrideViews(): void
    {
        try {
            // Override license-related views
            view()->composer('core/base::system.license-invalid', function ($view) {
                $view->with('hidden', true);
            });

            // Replace unlicensed view with our bypass message
            view()->composer('core/base::system.unlicensed', function ($view) {
                return view('plugins/license-bypass::license-bypassed');
            });

            // Hide license activation prompts
            view()->composer('core/base::layouts.master', function ($view) {
                $view->with('license_bypassed', true);
            });
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to override views: ' . $e->getMessage());
            }
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            LicenseBypassService::class,
            HttpInterceptorService::class,
        ];
    }
}
