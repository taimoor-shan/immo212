<?php

return [
    /*
    |--------------------------------------------------------------------------
    | License Bypass Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration file controls the behavior of the License Bypass plugin.
    | Only enable this plugin in local development environments.
    |
    */

    'enabled' => env('LICENSE_BYPASS_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Environment Restrictions
    |--------------------------------------------------------------------------
    |
    | The plugin will work in all environments.
    | Restrictions have been removed per user request.
    |
    */
    'allowed_environments' => ['local', 'development', 'dev', 'testing', 'production', 'staging'],

    /*
    |--------------------------------------------------------------------------
    | Blocked Domains
    |--------------------------------------------------------------------------
    |
    | External domains that should be blocked to prevent license checks.
    | Google and Facebook tracking have been enabled per user request.
    |
    */
    'blocked_domains' => [
        'license.botble.com',
        // Google Analytics and Facebook tracking enabled per user request
        // 'google-analytics.com',
        // 'googletagmanager.com',
        // 'facebook.com',
        // 'connect.facebook.net',
        // 'analytics.google.com',
        // 'www.google-analytics.com',
        // 'ssl.google-analytics.com',
        // 'stats.g.doubleclick.net',
        // 'www.googletagmanager.com',
        // 'doubleclick.net',
    ],

    /*
    |--------------------------------------------------------------------------
    | License Override Settings
    |--------------------------------------------------------------------------
    |
    | These settings will be used to override license-related configurations.
    |
    */
    'license_override' => [
        'licensed_to' => 'Local Development (License Bypassed)',
        'activated_at' => null, // Will use current date
        'status' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracking Disable Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for disabling various tracking and analytics services.
    | Google Analytics and Facebook tracking enabled per user request.
    |
    */
    'disable_tracking' => [
        'google_analytics' => false,  // Enabled per user request
        'facebook_integration' => false,  // Enabled per user request
        'external_fonts' => false,  // Enabled to allow Google Fonts
        'external_scripts' => false,  // Enabled to allow external scripts
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable logging of bypass activities for debugging purposes.
    |
    */
    'logging' => [
        'enabled' => env('LICENSE_BYPASS_LOGGING', false),
        'channel' => 'single',
        'level' => 'info',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Paths
    |--------------------------------------------------------------------------
    |
    | Paths for creating bypass files that the system expects.
    |
    */
    'file_paths' => [
        'license_file' => 'license',
        'skip_reminder_file' => 'skip_license_reminder',
        'core_data_file' => 'bypassed_core_data.json',
    ],
];
