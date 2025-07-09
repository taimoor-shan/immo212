<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\App;

/**
 * Service class to handle license bypass functionality
 */
class LicenseBypassService
{
    private array $config;
    private bool $isEnabled;

    public function __construct()
    {
        $this->config = config('plugins.license-bypass.license-bypass', []);
        $this->isEnabled = $this->shouldEnable();
    }

    /**
     * Check if the bypass should be enabled based on configuration
     * Environment restrictions removed per user request
     */
    public function shouldEnable(): bool
    {
        // Check if explicitly disabled
        if (!($this->config['enabled'] ?? true)) {
            return false;
        }

        // Environment restrictions removed per user request
        return true;
    }

    /**
     * Check if the service is enabled
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Get blocked domains list
     */
    public function getBlockedDomains(): array
    {
        return $this->config['blocked_domains'] ?? [];
    }

    /**
     * Check if a URL should be blocked
     */
    public function shouldBlockUrl(string $url): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $blockedDomains = $this->getBlockedDomains();
        
        foreach ($blockedDomains as $domain) {
            if (str_contains($url, $domain)) {
                $this->log('info', "Blocked external request to: {$url}");
                return true;
            }
        }

        return false;
    }

    /**
     * Create necessary bypass files
     */
    public function createBypassFiles(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $filePaths = $this->config['file_paths'] ?? [];

        try {
            // Create license file
            if (isset($filePaths['license_file'])) {
                $this->createFileIfNotExists(
                    $filePaths['license_file'],
                    'BYPASSED_LICENSE_FOR_LOCAL_DEVELOPMENT'
                );
            }

            // Create skip reminder file
            if (isset($filePaths['skip_reminder_file'])) {
                $this->createFileIfNotExists(
                    $filePaths['skip_reminder_file'],
                    '1'
                );
            }

            // Create core data file
            if (isset($filePaths['core_data_file'])) {
                $coreData = [
                    'status' => 'bypassed',
                    'created_at' => now()->toISOString(),
                    'environment' => App::environment(),
                ];
                
                $this->createFileIfNotExists(
                    $filePaths['core_data_file'],
                    json_encode($coreData, JSON_PRETTY_PRINT)
                );
            }

            $this->log('info', 'License bypass files created successfully');
        } catch (\Exception $e) {
            $this->log('error', 'Failed to create bypass files: ' . $e->getMessage());
        }
    }

    /**
     * Clean up bypass files
     */
    public function cleanupBypassFiles(): void
    {
        $filePaths = $this->config['file_paths'] ?? [];

        try {
            foreach ($filePaths as $filePath) {
                $fullPath = storage_path("app/{$filePath}");
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $this->log('info', 'License bypass files cleaned up successfully');
        } catch (\Exception $e) {
            $this->log('error', 'Failed to cleanup bypass files: ' . $e->getMessage());
        }
    }

    /**
     * Get license override settings
     */
    public function getLicenseOverrideSettings(): array
    {
        $override = $this->config['license_override'] ?? [];
        
        // Set current date if activated_at is null
        if (isset($override['activated_at']) && $override['activated_at'] === null) {
            $override['activated_at'] = now()->format('M d Y');
        }

        return $override;
    }

    /**
     * Get tracking disable settings
     */
    public function getTrackingDisableSettings(): array
    {
        return $this->config['disable_tracking'] ?? [];
    }

    /**
     * Create a file if it doesn't exist
     */
    private function createFileIfNotExists(string $relativePath, string $content): void
    {
        $fullPath = storage_path("app/{$relativePath}");
        $directory = dirname($fullPath);

        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Create file if it doesn't exist
        if (!file_exists($fullPath)) {
            file_put_contents($fullPath, $content);
        }
    }

    /**
     * Check if logging is enabled
     */
    public function isLoggingEnabled(): bool
    {
        return $this->config['logging']['enabled'] ?? false;
    }

    /**
     * Log messages if logging is enabled
     */
    private function log(string $level, string $message): void
    {
        $loggingConfig = $this->config['logging'] ?? [];
        
        if (!($loggingConfig['enabled'] ?? false)) {
            return;
        }

        $channel = $loggingConfig['channel'] ?? 'single';
        $logLevel = $loggingConfig['level'] ?? 'info';

        // Only log if the level is appropriate
        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];
        $currentLevelIndex = array_search($logLevel, $levels, true);
        $messageLevelIndex = array_search($level, $levels, true);

        if ($messageLevelIndex >= $currentLevelIndex) {
            Log::channel($channel)->$level("[License Bypass] {$message}");
        }
    }
}
