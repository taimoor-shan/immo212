<?php

use Botble\LicenseBypass\Http\Controllers\BypassController;
use Botble\Base\Facades\AdminHelper;
use Illuminate\Support\Facades\Route;

AdminHelper::registerRoutes(function (): void {
    Route::group(['prefix' => 'license-bypass', 'permission' => false], function (): void {
        // Override license activation routes
        Route::post('activate', [BypassController::class, 'activateLicense'])
            ->name('settings.license.activate');
        
        Route::get('verify', [BypassController::class, 'verifyLicense'])
            ->name('settings.license.verify');
        
        Route::post('deactivate', [BypassController::class, 'deactivateLicense'])
            ->name('settings.license.deactivate');
        
        Route::post('skip-reminder', [BypassController::class, 'skipReminder'])
            ->name('unlicensed.skip');
    });
});
