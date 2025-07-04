<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Service to intercept and block external HTTP requests
 */
class HttpInterceptorService
{
    private LicenseBypassService $bypassService;

    public function __construct(LicenseBypassService $bypassService)
    {
        $this->bypassService = $bypassService;
    }

    /**
     * Set up HTTP request interception
     */
    public function setupInterception(): void
    {
        if (!$this->bypassService->isEnabled()) {
            return;
        }

        // Create fake responses for blocked domains
        Http::fake([
            '*license.botble.com/*' => Http::response([
                'status' => true,
                'message' => 'License bypassed - tracking enabled per user request',
                'lic_response' => 'BYPASSED_LICENSE_RESPONSE',
                'data' => [
                    'licensed_to' => 'License Bypassed',
                    'activated_at' => now()->format('M d Y'),
                    'status' => 'active'
                ]
            ], 200, [
                'Content-Type' => 'application/json',
                'X-Bypass-Plugin' => 'license-bypass'
            ]),
        ]);
    }

    /**
     * Check if a specific URL should be intercepted
     */
    public function shouldIntercept(string $url): bool
    {
        return $this->bypassService->shouldBlockUrl($url);
    }

    /**
     * Create a bypass response for license-related requests
     */
    public function createLicenseBypassResponse(): array
    {
        $overrideSettings = $this->bypassService->getLicenseOverrideSettings();
        
        return [
            'error' => false,
            'message' => 'License operation bypassed for local development',
            'data' => [
                'status' => $overrideSettings['status'] ?? true,
                'licensed_to' => $overrideSettings['licensed_to'] ?? 'Local Development',
                'activated_at' => $overrideSettings['activated_at'] ?? now()->format('M d Y'),
                'environment' => app()->environment(),
                'bypass_plugin' => true
            ]
        ];
    }
}
