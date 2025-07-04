<?php

namespace Botble\LicenseBypass\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class BypassController extends BaseController
{
    public function activateLicense(Request $request)
    {
        // Always return success for license activation
        return $this
            ->httpResponse()
            ->setMessage('License bypassed successfully for local development.')
            ->setData(['status' => true]);
    }

    public function verifyLicense(Request $request)
    {
        // Always return success for license verification
        return $this
            ->httpResponse()
            ->setMessage('License verified successfully (bypassed).')
            ->setData([
                'status' => true,
                'activated_at' => now()->format('M d Y'),
                'licensed_to' => 'Local Development'
            ]);
    }

    public function deactivateLicense(Request $request)
    {
        // Always return success for license deactivation
        return $this
            ->httpResponse()
            ->setMessage('License deactivated successfully (bypassed).');
    }

    public function skipReminder(Request $request)
    {
        // Always return success for skipping reminders
        return $this
            ->httpResponse()
            ->setMessage('License reminder skipped.');
    }
}
