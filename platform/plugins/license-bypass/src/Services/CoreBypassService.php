<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Services;

use Botble\Base\Supports\Core;

/**
 * Core Bypass Service
 *
 * This service wraps the original Core class and overrides license-related methods
 * to bypass license verification when the bypass plugin is enabled.
 */
class CoreBypassService
{
    private Core $originalCore;
    private LicenseBypassService $bypassService;

    public function __construct(
        Core $originalCore,
        LicenseBypassService $bypassService
    ) {
        $this->originalCore = $originalCore;
        $this->bypassService = $bypassService;
    }

    /**
     * Override license verification to always return true when bypass is enabled
     */
    public function verifyLicense(bool $timeBasedCheck = false, int $timeoutInSeconds = 300): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('License verification bypassed - returning true');
            return true;
        }

        return $this->originalCore->verifyLicense($timeBasedCheck, $timeoutInSeconds);
    }

    /**
     * Override license reminder skip check to always return true when bypass is enabled
     */
    public function isSkippedLicenseReminder(): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('License reminder check bypassed - returning true');
            return true;
        }

        return $this->originalCore->isSkippedLicenseReminder();
    }

    /**
     * Override license activation to return success when bypass is enabled
     */
    public function activateLicense(string $license, string $client): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('License activation bypassed - returning true');
            return true;
        }

        return $this->originalCore->activateLicense($license, $client);
    }

    /**
     * Override license deactivation to return success when bypass is enabled
     */
    public function deactivateLicense(): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('License deactivation bypassed - returning true');
            return true;
        }

        return $this->originalCore->deactivateLicense();
    }

    /**
     * Override skip license reminder to always succeed when bypass is enabled
     */
    public function skipLicenseReminder(): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('Skip license reminder bypassed - returning true');
            return true;
        }

        return $this->originalCore->skipLicenseReminder();
    }

    /**
     * Override connection check to return true when bypass is enabled
     */
    public function checkConnection(): bool
    {
        if ($this->bypassService->isEnabled()) {
            $this->log('Connection check bypassed - returning true');
            return true;
        }

        return $this->originalCore->checkConnection();
    }

    /**
     * Log bypass activities if logging is enabled
     */
    private function log(string $message): void
    {
        if ($this->bypassService->isLoggingEnabled()) {
            logger()->info('[License Bypass Core] ' . $message);
        }
    }

    /**
     * Delegate all other method calls to the original Core instance
     */
    public function __call(string $method, array $arguments)
    {
        return $this->originalCore->$method(...$arguments);
    }

    /**
     * Delegate property access to the original Core instance
     */
    public function __get(string $property)
    {
        return $this->originalCore->$property;
    }

    /**
     * Delegate property setting to the original Core instance
     */
    public function __set(string $property, $value): void
    {
        $this->originalCore->$property = $value;
    }

    /**
     * Delegate property existence check to the original Core instance
     */
    public function __isset(string $property): bool
    {
        return isset($this->originalCore->$property);
    }
}
