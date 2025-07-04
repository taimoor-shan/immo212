<?php

namespace Botble\LicenseBypass\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * Middleware that blocks external HTTP requests for licensing and tracking
 */
class BlockExternalRequestsMiddleware
{
    private array $blockedDomains = [
        'license.botble.com',
        'google-analytics.com',
        'googletagmanager.com',
        'facebook.com',
        'connect.facebook.net',
        'analytics.google.com',
        'www.google-analytics.com',
        'ssl.google-analytics.com',
        'stats.g.doubleclick.net',
        'www.googletagmanager.com',
    ];

    public function handle(Request $request, Closure $next)
    {
        // Block external requests by faking HTTP responses
        Http::fake([
            '*license.botble.com/*' => Http::response([
                'status' => true,
                'message' => 'License bypassed for local development',
                'lic_response' => 'BYPASSED_LICENSE_RESPONSE'
            ], 200),
            
            '*google-analytics.com/*' => Http::response('', 200),
            '*googletagmanager.com/*' => Http::response('', 200),
            '*facebook.com/*' => Http::response('', 200),
            '*connect.facebook.net/*' => Http::response('', 200),
            '*analytics.google.com/*' => Http::response('', 200),
            '*doubleclick.net/*' => Http::response('', 200),
        ]);

        return $next($request);
    }
}
