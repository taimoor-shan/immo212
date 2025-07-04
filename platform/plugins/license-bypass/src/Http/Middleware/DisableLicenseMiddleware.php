<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Http\Middleware;

use Botble\LicenseBypass\Services\HttpInterceptorService;
use Botble\LicenseBypass\Services\LicenseBypassService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Middleware that completely bypasses license checks
 */
class DisableLicenseMiddleware
{
    private LicenseBypassService $bypassService;
    private HttpInterceptorService $interceptorService;

    public function __construct(
        LicenseBypassService $bypassService,
        HttpInterceptorService $interceptorService
    ) {
        $this->bypassService = $bypassService;
        $this->interceptorService = $interceptorService;
    }

    /**
     * Handle an incoming request
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        // Skip if bypass is not enabled
        if (!$this->bypassService->isEnabled()) {
            return $next($request);
        }

        // Intercept license-related routes and return success responses
        $uri = $request->getRequestUri();

        // Handle license activation requests
        if ($this->isLicenseActivationRequest($uri)) {
            return $this->createLicenseResponse('License bypassed successfully for local development.');
        }

        // Handle license verification requests
        if ($this->isLicenseVerificationRequest($uri)) {
            return $this->createLicenseResponse('License verified successfully (bypassed).');
        }

        // Handle license deactivation requests
        if ($this->isLicenseDeactivationRequest($uri)) {
            return $this->createSimpleResponse('License deactivated successfully (bypassed).');
        }

        // Handle skip reminder requests
        if ($this->isSkipReminderRequest($uri)) {
            return $this->createSimpleResponse('License reminder skipped.');
        }

        // Always allow the request to proceed without any license checks
        return $next($request);
    }

    /**
     * Check if this is a license activation request
     */
    private function isLicenseActivationRequest(string $uri): bool
    {
        $patterns = [
            'license/activate',
            'settings/license/activate',
            'admin/settings/license/activate'
        ];

        return $this->matchesAnyPattern($uri, $patterns);
    }

    /**
     * Check if this is a license verification request
     */
    private function isLicenseVerificationRequest(string $uri): bool
    {
        $patterns = [
            'license/verify',
            'settings/license/verify',
            'admin/settings/license/verify'
        ];

        return $this->matchesAnyPattern($uri, $patterns);
    }

    /**
     * Check if this is a license deactivation request
     */
    private function isLicenseDeactivationRequest(string $uri): bool
    {
        $patterns = [
            'license/deactivate',
            'settings/license/deactivate',
            'admin/settings/license/deactivate'
        ];

        return $this->matchesAnyPattern($uri, $patterns);
    }

    /**
     * Check if this is a skip reminder request
     */
    private function isSkipReminderRequest(string $uri): bool
    {
        $patterns = [
            'skip-reminder',
            'unlicensed/skip',
            'admin/unlicensed/skip'
        ];

        return $this->matchesAnyPattern($uri, $patterns);
    }

    /**
     * Check if URI matches any of the given patterns
     */
    private function matchesAnyPattern(string $uri, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($uri, $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a license response with full data
     */
    private function createLicenseResponse(string $message): JsonResponse
    {
        return response()->json(
            $this->interceptorService->createLicenseBypassResponse() + ['message' => $message]
        );
    }

    /**
     * Create a simple success response
     */
    private function createSimpleResponse(string $message): JsonResponse
    {
        return response()->json([
            'error' => false,
            'message' => $message,
            'data' => ['bypass_plugin' => true]
        ]);
    }
}
