<?php

/**
 * Deployment Configuration Template for IMMO212 Real Estate Platform
 * 
 * Copy this file to deploy-config.php and customize for your server setup.
 * This file contains sensitive information and should not be committed to version control.
 */

return [
    // Production server configuration
    'production' => [
        'hostname' => 'your-server-ip-or-domain.com',
        'port' => 22,
        'user' => 'cloudpanel',
        'deploy_path' => '/home/cloudpanel/htdocs/yourdomain.com',
        'repository' => 'git@github.com:your-username/your-repo.git',
        'branch' => 'main',
        
        // CloudPanel specific settings
        'cloudpanel' => [
            'php_version' => '8.2',
            'php_bin' => '/usr/bin/php8.2',
            'composer_bin' => '/usr/local/bin/composer',
            'npm_bin' => '/usr/bin/npm',
            'web_user' => 'cloudpanel',
        ],
        
        // Database settings (for backups)
        'database' => [
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'your_database_name',
            'user' => 'your_db_user',
            'password' => 'your_db_password',
            'backup_path' => '/home/cloudpanel/backups',
            'max_backups' => 10,
        ],
        
        // SSL and domain settings
        'ssl' => [
            'enabled' => true,
            'force_https' => true,
        ],
        
        // Performance settings
        'opcache' => [
            'enabled' => true,
            'reset_after_deploy' => true,
        ],
    ],
    
    // Staging server configuration (optional)
    'staging' => [
        'hostname' => 'staging.yourdomain.com',
        'port' => 22,
        'user' => 'cloudpanel',
        'deploy_path' => '/home/cloudpanel/htdocs/staging.yourdomain.com',
        'repository' => 'git@github.com:your-username/your-repo.git',
        'branch' => 'develop',
        
        'cloudpanel' => [
            'php_version' => '8.2',
            'php_bin' => '/usr/bin/php8.2',
            'composer_bin' => '/usr/local/bin/composer',
            'npm_bin' => '/usr/bin/npm',
            'web_user' => 'cloudpanel',
        ],
        
        'database' => [
            'host' => '127.0.0.1',
            'port' => 3306,
            'name' => 'staging_database_name',
            'user' => 'staging_db_user',
            'password' => 'staging_db_password',
            'backup_path' => '/home/cloudpanel/backups',
            'max_backups' => 5,
        ],
    ],
    
    // Deployment settings
    'deployment' => [
        'timeout' => 600, // 10 minutes
        'keep_releases' => 3,
        'shared_files' => [
            '.env',
            'storage/installed',
            'storage/cache_keys.json',
        ],
        'shared_dirs' => [
            'storage',
            'public/storage',
            'public/uploads',
            'public/themes/homzen/uploads',
            'bootstrap/cache',
        ],
        'writable_dirs' => [
            'bootstrap/cache',
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views',
            'storage/logs',
            'public/storage',
            'public/uploads',
            'public/themes/homzen/uploads',
        ],
    ],
    
    // Asset compilation settings
    'assets' => [
        'build_locally' => true,
        'npm_timeout' => 300,
        'build_command' => 'npm run production',
        'upload_paths' => [
            'public/themes/',
            'public/vendor/',
            'public/mix-manifest.json',
        ],
    ],
    
    // Notification settings
    'notifications' => [
        'slack' => [
            'enabled' => false,
            'webhook_url' => 'https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK',
            'channel' => '#deployments',
            'username' => 'DeployBot',
        ],
        'email' => [
            'enabled' => false,
            'recipients' => [
                'admin@yourdomain.com',
                'dev@yourdomain.com',
            ],
            'smtp' => [
                'host' => 'smtp.yourdomain.com',
                'port' => 587,
                'username' => 'notifications@yourdomain.com',
                'password' => 'your_smtp_password',
            ],
        ],
    ],
    
    // Security settings
    'security' => [
        'file_permissions' => [
            'files' => '644',
            'directories' => '755',
            'storage' => '775',
            'env_file' => '600',
        ],
        'allowed_deploy_ips' => [
            '127.0.0.1',
            // Add your deployment machine IPs here
        ],
    ],
    
    // Maintenance mode settings
    'maintenance' => [
        'template' => 'maintenance.blade.php',
        'allowed_ips' => [
            '127.0.0.1',
            // Add IPs that should bypass maintenance mode
        ],
        'retry_after' => 60, // seconds
    ],
    
    // Health check settings
    'health_check' => [
        'url' => '/health',
        'expected_status' => 200,
        'timeout' => 30,
        'retry_attempts' => 3,
    ],
];
