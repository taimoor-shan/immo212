<?php

declare(strict_types=1);

namespace Botble\LicenseBypass;

use Botble\LicenseBypass\Services\LicenseBypassService;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\App;

/**
 * Plugin lifecycle management class
 */
class Plugin extends PluginOperationAbstract
{
    /**
     * Remove plugin data and cleanup
     */
    public static function remove(): void
    {
        try {
            static::cleanupBypassFiles();
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to remove plugin data: ' . $e->getMessage());
            }
        }
    }

    /**
     * Activate the plugin
     */
    public static function activate(): void
    {
        try {
            // Environment restrictions removed per user request
            // Create bypass service and initialize files
            $bypassService = App::make(LicenseBypassService::class);
            $bypassService->createBypassFiles();

            if (function_exists('logger')) {
                logger()->info('[License Bypass] Plugin activated successfully in ' . App::environment() . ' environment');
            }
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to activate plugin: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    /**
     * Deactivate the plugin
     */
    public static function deactivate(): void
    {
        try {
            // Clean up bypass files
            static::cleanupBypassFiles();

            if (function_exists('logger')) {
                logger()->info('[License Bypass] Plugin deactivated successfully');
            }
        } catch (\Exception $e) {
            if (function_exists('logger')) {
                logger()->error('[License Bypass] Failed to deactivate plugin: ' . $e->getMessage());
            }
        }
    }

    /**
     * Check if current environment is allowed for this plugin
     * Environment restrictions removed per user request - always returns true
     */
    private static function isAllowedEnvironment(): bool
    {
        // Environment restrictions removed per user request
        return true;
    }

    /**
     * Clean up all bypass files
     */
    private static function cleanupBypassFiles(): void
    {
        $filePaths = config('plugins.license-bypass.license-bypass.file_paths', [
            'license_file' => 'license',
            'skip_reminder_file' => 'skip_license_reminder',
            'core_data_file' => 'bypassed_core_data.json',
        ]);

        foreach ($filePaths as $relativePath) {
            $fullPath = storage_path("app/{$relativePath}");

            if (file_exists($fullPath)) {
                if (is_dir($fullPath)) {
                    static::removeDirectory($fullPath);
                } else {
                    unlink($fullPath);
                }
            }
        }

        // Also clean up legacy files from old versions
        $legacyFiles = [
            storage_path('app/bypassed_license.txt'),
            storage_path('app/bypassed_skip_reminder.txt'),
            storage_path('app/bypassed_update'),
        ];

        foreach ($legacyFiles as $file) {
            if (file_exists($file)) {
                if (is_dir($file)) {
                    static::removeDirectory($file);
                } else {
                    unlink($file);
                }
            }
        }
    }

    /**
     * Recursively remove a directory
     */
    private static function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;

            if (is_dir($path)) {
                static::removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        rmdir($dir);
    }
}
