<?php

declare(strict_types=1);

namespace Botble\LicenseBypass\Tests;

use Botble\LicenseBypass\Services\LicenseBypassService;
use Botble\LicenseBypass\Services\HttpInterceptorService;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

/**
 * Test suite for License Bypass plugin
 */
class LicenseBypassTest extends TestCase
{
    protected LicenseBypassService $bypassService;
    protected HttpInterceptorService $interceptorService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Set test environment
        App::shouldReceive('environment')->andReturn('testing');
        
        $this->bypassService = new LicenseBypassService();
        $this->interceptorService = new HttpInterceptorService($this->bypassService);
    }

    /** @test */
    public function it_should_be_enabled_in_testing_environment(): void
    {
        $this->assertTrue($this->bypassService->shouldEnable());
        $this->assertTrue($this->bypassService->isEnabled());
    }

    /** @test */
    public function it_should_block_license_domains(): void
    {
        $testUrls = [
            'https://license.botble.com/api/verify',
            'https://google-analytics.com/collect',
            'https://facebook.com/tr',
        ];

        foreach ($testUrls as $url) {
            $this->assertTrue(
                $this->bypassService->shouldBlockUrl($url),
                "URL should be blocked: {$url}"
            );
        }
    }

    /** @test */
    public function it_should_not_block_allowed_domains(): void
    {
        $allowedUrls = [
            'https://example.com/api',
            'https://localhost:8000/admin',
            'https://github.com/botble/botble',
        ];

        foreach ($allowedUrls as $url) {
            $this->assertFalse(
                $this->bypassService->shouldBlockUrl($url),
                "URL should not be blocked: {$url}"
            );
        }
    }

    /** @test */
    public function it_should_provide_license_override_settings(): void
    {
        $settings = $this->bypassService->getLicenseOverrideSettings();
        
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('licensed_to', $settings);
        $this->assertArrayHasKey('status', $settings);
        $this->assertTrue($settings['status']);
    }

    /** @test */
    public function it_should_provide_tracking_disable_settings(): void
    {
        $settings = $this->bypassService->getTrackingDisableSettings();
        
        $this->assertIsArray($settings);
        $this->assertArrayHasKey('google_analytics', $settings);
        $this->assertArrayHasKey('facebook_integration', $settings);
    }

    /** @test */
    public function it_should_create_license_bypass_response(): void
    {
        $response = $this->interceptorService->createLicenseBypassResponse();
        
        $this->assertIsArray($response);
        $this->assertFalse($response['error']);
        $this->assertArrayHasKey('data', $response);
        $this->assertTrue($response['data']['status']);
        $this->assertTrue($response['data']['bypass_plugin']);
    }

    /** @test */
    public function it_should_not_enable_in_production_environment(): void
    {
        // Mock production environment
        App::shouldReceive('environment')->andReturn('production');
        
        $productionService = new LicenseBypassService();
        
        $this->assertFalse($productionService->shouldEnable());
        $this->assertFalse($productionService->isEnabled());
    }

    /** @test */
    public function it_should_respect_disabled_configuration(): void
    {
        // Mock disabled configuration
        Config::shouldReceive('get')
            ->with('plugins.license-bypass.license-bypass', [])
            ->andReturn(['enabled' => false]);
        
        $disabledService = new LicenseBypassService();
        
        $this->assertFalse($disabledService->shouldEnable());
        $this->assertFalse($disabledService->isEnabled());
    }

    protected function tearDown(): void
    {
        // Clean up any test files
        $testFiles = [
            storage_path('app/license'),
            storage_path('app/skip_license_reminder'),
            storage_path('app/bypassed_core_data.json'),
        ];

        foreach ($testFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }
}
