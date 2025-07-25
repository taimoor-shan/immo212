<?php

namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'IMMO212 Real Estate Platform');

// Project repository - Update with your actual repository
set('repository', 'git@github.com:taimoor-shan/immo212');

// CloudPanel specific configuration
set('bin/php', '/usr/bin/php8.2'); // CloudPanel PHP path
set('bin/composer', '/usr/local/bin/composer');
set('bin/npm', '/usr/bin/npm');

// Database configuration (will be read from .env on server)
set('db_user', '{{env_db_username}}');
set('db_password', '{{env_db_password}}');
set('db_name', '{{env_db_database}}');

// Shared files/dirs between deploys
add('shared_files', [
    '.env',
    'storage/installed',
    'storage/cache_keys.json',
]);

add('shared_dirs', [
    'storage',
    'public/storage',
    'public/uploads',
    'public/themes/homzen/uploads',
    'bootstrap/cache',
]);

// Writable dirs by web server
add('writable_dirs', [
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
]);

// Hosts configuration for CloudPanel
host('production')
    ->setHostname('srv925600.hstgr.cloud') // Your CloudPanel server hostname
    ->setPort(22) // Standard SSH port (update if different)
    ->setRemoteUser('taimoor2') // CloudPanel default user
    ->setDeployPath('/home/taimoor2/htdocs/staging.immo212.com') // UPDATE: Replace with your actual domain
    ->set('branch', 'features/rentals') // UPDATE: Your production branch (main/master)
    ->set('http_user', 'immo212-staging') // Web server user (matches directory ownership)
    ->set('writable_mode', 'chmod')
    ->set('keep_releases', 3); // Keep 3 releases for rollback

// Staging host removed - deploying directly to production only

// Tasks for CloudPanel deployment
desc('Install npm dependencies');
task('npm:install', function () {
    writeln('<info>Installing npm dependencies...</info>');

    // Check if package.json exists
    if (!testLocally('[ -f package.json ]')) {
        writeln('<info>No package.json found, skipping npm install</info>');
        return;
    }

    // Clean install for production
    runLocally('npm ci --production');
    writeln('<info>npm dependencies installed successfully!</info>');
});

desc('Build and optimize assets locally');
task('build:assets', function () {
    writeln('<info>Building and optimizing assets...</info>');

    // Check if package.json exists
    if (!testLocally('[ -f package.json ]')) {
        writeln('<info>No package.json found, skipping asset build</info>');
        return;
    }

    // Install dependencies first
    invoke('npm:install');

    // Build assets for production
    runLocally('npm run production');

    // Verify assets were built
    if (!testLocally('[ -d public/themes ] || [ -d public/vendor ]')) {
        throw new Exception('Asset build failed - no compiled assets found');
    }

    writeln('<info>Assets built and optimized successfully!</info>');
});

desc('Clean local build artifacts');
task('build:clean', function () {
    writeln('<info>Cleaning local build artifacts...</info>');

    // Clean node_modules if needed (optional)
    if (testLocally('[ -d node_modules ]') && askConfirmation('Remove node_modules directory?', false)) {
        runLocally('rm -rf node_modules');
        writeln('<info>node_modules directory removed</info>');
    }

    // Clean npm cache
    runLocally('npm cache clean --force 2>/dev/null || true');

    writeln('<info>Build artifacts cleaned!</info>');
});

desc('Upload built assets to server');
task('upload:assets', function () {
    writeln('<info>Uploading compiled assets...</info>');

    $uploadedFiles = 0;

    // Upload theme assets
    if (testLocally('[ -d public/themes ]')) {
        upload('public/themes/', '{{release_path}}/public/themes/');
        $uploadedFiles++;
        writeln('<info>Theme assets uploaded</info>');
    }

    // Upload vendor assets
    if (testLocally('[ -d public/vendor ]')) {
        upload('public/vendor/', '{{release_path}}/public/vendor/');
        $uploadedFiles++;
        writeln('<info>Vendor assets uploaded</info>');
    }

    // Upload mix manifest if exists
    if (testLocally('[ -f public/mix-manifest.json ]')) {
        upload('public/mix-manifest.json', '{{release_path}}/public/mix-manifest.json');
        $uploadedFiles++;
        writeln('<info>Mix manifest uploaded</info>');
    }

    // Upload webpack manifest if exists
    if (testLocally('[ -f public/webpack-manifest.json ]')) {
        upload('public/webpack-manifest.json', '{{release_path}}/public/webpack-manifest.json');
        $uploadedFiles++;
        writeln('<info>Webpack manifest uploaded</info>');
    }

    // Upload any additional CSS/JS files in public root
    if (testLocally('[ -f public/app.css ] || [ -f public/app.js ]')) {
        if (testLocally('[ -f public/app.css ]')) {
            upload('public/app.css', '{{release_path}}/public/app.css');
            $uploadedFiles++;
        }
        if (testLocally('[ -f public/app.js ]')) {
            upload('public/app.js', '{{release_path}}/public/app.js');
            $uploadedFiles++;
        }
        writeln('<info>Root assets uploaded</info>');
    }

    if ($uploadedFiles === 0) {
        writeln('<info>No compiled assets found to upload</info>');
    } else {
        writeln("<info>Successfully uploaded $uploadedFiles asset group(s)!</info>");
    }
});

desc('Optimize uploaded assets on server');
task('assets:optimize', function () {
    writeln('<info>Optimizing assets on server...</info>');

    // Set proper permissions for asset files
    run('find {{release_path}}/public/themes -type f -name "*.css" -o -name "*.js" -o -name "*.png" -o -name "*.jpg" -o -name "*.gif" -o -name "*.svg" | xargs chmod 644 2>/dev/null || true');

    // Enable gzip compression for text assets (if not handled by web server)
    run('find {{release_path}}/public/themes -name "*.css" -o -name "*.js" | xargs gzip -k -f 2>/dev/null || true');

    writeln('<info>Assets optimized successfully!</info>');
});

desc('Optimize Laravel application for production');
task('artisan:optimize', function () {
    writeln('<info>Optimizing Laravel application...</info>');

    // Clear all caches first
    run('cd {{release_path}} && {{bin/php}} artisan config:clear');
    run('cd {{release_path}} && {{bin/php}} artisan route:clear');
    run('cd {{release_path}} && {{bin/php}} artisan view:clear');

    // Cache for production
    run('cd {{release_path}} && {{bin/php}} artisan config:cache');
    run('cd {{release_path}} && {{bin/php}} artisan route:cache');
    run('cd {{release_path}} && {{bin/php}} artisan view:cache');

    writeln('<info>Laravel optimization complete!</info>');
});

desc('Clear all Laravel caches');
task('artisan:cache:clear', function () {
    writeln('<info>Clearing Laravel caches...</info>');
    run('cd {{release_path}} && {{bin/php}} artisan cache:clear');
    run('cd {{release_path}} && {{bin/php}} artisan config:clear');
    run('cd {{release_path}} && {{bin/php}} artisan route:clear');
    run('cd {{release_path}} && {{bin/php}} artisan view:clear');
    writeln('<info>Caches cleared!</info>');
});

desc('Create storage symbolic link');
task('artisan:storage:link', function () {
    writeln('<info>Creating storage link...</info>');

    // Remove existing link if it exists
    run('cd {{release_path}} && rm -f public/storage');

    // Create new storage link
    run('cd {{release_path}} && {{bin/php}} artisan storage:link');

    writeln('<info>Storage link created!</info>');
});

desc('Run database migrations with force flag');
task('artisan:migrate', function () {
    writeln('<info>Running database migrations...</info>');

    // Check if there are pending migrations
    $pendingMigrations = run('cd {{release_path}} && {{bin/php}} artisan migrate:status --pending');

    if (empty(trim($pendingMigrations))) {
        writeln('<info>No pending migrations found</info>');
        return;
    }

    writeln('<info>Pending migrations found:</info>');
    writeln($pendingMigrations);

    // Run migrations
    $output = run('cd {{release_path}} && {{bin/php}} artisan migrate --force');
    writeln('<info>' . $output . '</info>');
    writeln('<info>Migrations completed successfully!</info>');
});

desc('Rollback database migrations');
task('artisan:migrate:rollback', function () {
    writeln('<info>Rolling back database migrations...</info>');

    // Get the last batch number
    $lastBatch = run('cd {{release_path}} && {{bin/php}} artisan migrate:status | grep "Y" | tail -1 | awk \'{print $3}\' || echo "0"');

    if ($lastBatch === '0') {
        writeln('<info>No migrations to rollback</info>');
        return;
    }

    writeln("<info>Rolling back batch: $lastBatch</info>");

    // Rollback the last batch
    $output = run('cd {{release_path}} && {{bin/php}} artisan migrate:rollback --force');
    writeln('<info>' . $output . '</info>');
    writeln('<info>Migration rollback completed!</info>');
});

desc('Create backup directory');
task('backup:create-dir', function () {
    run('mkdir -p {{deploy_path}}/backups');
    writeln('<info>Backup directory created</info>');
});

desc('Backup database before deployment');
task('database:backup', function () {
    writeln('<info>Creating database backup...</info>');

    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = "backup_$timestamp.sql";
    $backupPath = "{{deploy_path}}/backups/$backupFile";

    // Read database credentials from .env file
    $envFile = run('cat {{deploy_path}}/shared/.env');
    preg_match('/DB_DATABASE=(.*)/', $envFile, $dbName);
    preg_match('/DB_USERNAME=(.*)/', $envFile, $dbUser);
    preg_match('/DB_PASSWORD=(.*)/', $envFile, $dbPassword);

    $dbName = trim($dbName[1] ?? '', '"');
    $dbUser = trim($dbUser[1] ?? '', '"');
    $dbPassword = trim($dbPassword[1] ?? '', '"');

    if (empty($dbName) || empty($dbUser)) {
        throw new Exception('Database credentials not found in .env file');
    }

    // Create database backup
    $mysqldumpCmd = "mysqldump -u '$dbUser' -p'$dbPassword' '$dbName' > $backupPath";
    run($mysqldumpCmd);

    // Verify backup was created
    $backupSize = run("stat -c%s $backupPath 2>/dev/null || echo '0'");
    if ((int)$backupSize === 0) {
        throw new Exception('Database backup failed - backup file is empty');
    }

    writeln("<info>Database backed up successfully to: $backupPath</info>");
    writeln("<info>Backup size: " . number_format($backupSize) . " bytes</info>");

    // Store backup info for potential rollback
    set('backup_file', $backupPath);
    set('backup_timestamp', $timestamp);
});

desc('Clean old database backups');
task('database:cleanup-backups', function () {
    writeln('<info>Cleaning old database backups...</info>');

    // Keep only the last 10 backups
    $backups = run('ls -t {{deploy_path}}/backups/backup_*.sql 2>/dev/null | tail -n +11 || true');

    if (!empty(trim($backups))) {
        run("rm -f $backups");
        $removedCount = count(explode("\n", trim($backups)));
        writeln("<info>Removed $removedCount old backup(s)</info>");
    } else {
        writeln('<info>No old backups to clean</info>');
    }
});

desc('Restore database from backup');
task('database:restore', function () {
    $backupFile = get('backup_file');

    if (empty($backupFile)) {
        throw new Exception('No backup file specified for restore');
    }

    writeln("<info>Restoring database from backup: $backupFile</info>");

    // Read database credentials from .env file
    $envFile = run('cat {{deploy_path}}/shared/.env');
    preg_match('/DB_DATABASE=(.*)/', $envFile, $dbName);
    preg_match('/DB_USERNAME=(.*)/', $envFile, $dbUser);
    preg_match('/DB_PASSWORD=(.*)/', $envFile, $dbPassword);

    $dbName = trim($dbName[1] ?? '', '"');
    $dbUser = trim($dbUser[1] ?? '', '"');
    $dbPassword = trim($dbPassword[1] ?? '', '"');

    // Restore database
    $mysqlCmd = "mysql -u '$dbUser' -p'$dbPassword' '$dbName' < $backupFile";
    run($mysqlCmd);

    writeln('<info>Database restored successfully!</info>');
});

desc('Set correct permissions for CloudPanel');
task('deploy:set_permissions', function () {
    writeln('<info>Setting CloudPanel permissions...</info>');

    // Set general file and directory permissions
    run('find {{release_path}} -type f -exec chmod 644 {} \;');
    run('find {{release_path}} -type d -exec chmod 755 {} \;');

    // Set writable permissions for Laravel directories
    run('chmod -R 775 {{release_path}}/storage');
    run('chmod -R 775 {{release_path}}/bootstrap/cache');

    // Secure .env file
    run('chmod 600 {{release_path}}/.env');

    // Set executable permissions for artisan
    run('chmod +x {{release_path}}/artisan');

    writeln('<info>Permissions set successfully!</info>');
});

desc('Restart PHP-FPM and clear OPcache');
task('php:restart', function () {
    writeln('<info>Restarting PHP-FPM and clearing OPcache...</info>');

    // Clear OPcache
    run('cd {{release_path}} && {{bin/php}} -r "if (function_exists(\'opcache_reset\')) { opcache_reset(); echo \'OPcache cleared\'; } else { echo \'OPcache not available\'; }"');

    // Restart PHP-FPM (CloudPanel specific)
    run('sudo systemctl reload php8.2-fpm || true');

    writeln('<info>PHP services restarted!</info>');
});

desc('Run Laravel queue restart');
task('queue:restart', function () {
    writeln('<info>Restarting Laravel queues...</info>');
    run('cd {{release_path}} && {{bin/php}} artisan queue:restart');
    writeln('<info>Queues restarted!</info>');
});

desc('Warm up application cache');
task('cache:warmup', function () {
    writeln('<info>Warming up application cache...</info>');

    // Warm up config cache
    run('cd {{release_path}} && {{bin/php}} artisan config:cache');

    // Warm up route cache
    run('cd {{release_path}} && {{bin/php}} artisan route:cache');

    // Warm up view cache
    run('cd {{release_path}} && {{bin/php}} artisan view:cache');

    writeln('<info>Cache warmed up successfully!</info>');
});

// Main deployment workflow (matches CloudPanel + Deployer PHP process)
desc('Deploy your project (standard deployment)');
task('deploy', [
    'deploy:prepare',           // Create new release directory
    'deploy:vendors',           // Run composer install
    'build:assets',            // Build assets locally
    'upload:assets',           // Upload compiled assets
    'assets:optimize',         // Optimize assets on server
    'artisan:storage:link',    // Create storage symlink
    'cache:warmup',            // Warm up Laravel caches
    'deploy:set_permissions',  // Set CloudPanel permissions
    'deploy:publish',          // Update current symlink (zero-downtime)
    'php:restart',             // Restart PHP-FPM and clear OPcache
    'queue:restart',           // Restart Laravel queues
]);

// Deployment with database migrations (use with caution in production)
desc('Deploy with database migrations');
task('deploy:migrate', [
    'deploy:prepare',
    'backup:create-dir',
    'database:backup',         // Backup database before migrations
    'deploy:vendors',
    'build:assets',
    'upload:assets',
    'artisan:storage:link',
    'artisan:migrate',         // Run migrations with --force
    'cache:warmup',            // Warm up Laravel caches
    'deploy:set_permissions',
    'deploy:publish',
    'php:restart',             // Restart PHP-FPM and clear OPcache
    'queue:restart',           // Restart Laravel queues
    'database:cleanup-backups', // Clean old backups
]);

// Quick deployment for minor changes (assets/code only, no migrations)
desc('Quick deploy - assets and code only');
task('deploy:quick', [
    'deploy:prepare',
    'deploy:vendors',
    'build:assets',
    'upload:assets',
    'artisan:optimize',
    'deploy:set_permissions',
    'deploy:publish',
]);

// Fast rollback using Deployer's built-in rollback
desc('Rollback to previous release');
task('app:rollback', [
    'deploy:rollback',         // Switch current symlink to previous release
    'artisan:cache:clear',     // Clear caches for clean state
    'cache:warmup',            // Re-optimize for the rolled-back version
    'php:restart',             // Restart PHP-FPM and clear OPcache
    'queue:restart',           // Restart Laravel queues
]);

// Complete rollback including database restoration
desc('Complete rollback with database restoration');
task('app:rollback:complete', function () {
    writeln('<info>Starting complete rollback with database restoration...</info>');

    // Confirm this is what the user wants
    if (!askConfirmation('This will rollback code AND restore database from backup. Continue?', false)) {
        writeln('<info>Rollback cancelled</info>');
        return;
    }

    // Get the latest backup file
    $latestBackup = run('ls -t {{deploy_path}}/backups/backup_*.sql 2>/dev/null | head -1 || echo ""');

    if (empty(trim($latestBackup))) {
        throw new Exception('No database backup found for restoration');
    }

    writeln("<info>Using backup: $latestBackup</info>");
    set('backup_file', $latestBackup);

    // Perform rollback
    invoke('deploy:rollback');
    invoke('database:restore');
    invoke('artisan:cache:clear');
    invoke('cache:warmup');
    invoke('php:restart');
    invoke('queue:restart');

    writeln('<info>Complete rollback finished successfully!</info>');
});

desc('Validate environment configuration');
task('env:validate', function () {
    writeln('<info>Validating environment configuration...</info>');

    // Check if .env file exists
    if (!test('[ -f {{deploy_path}}/shared/.env ]')) {
        throw new Exception('.env file not found in shared directory. Please create it first.');
    }

    // Read .env file and check critical variables
    $envContent = run('cat {{deploy_path}}/shared/.env');

    $requiredVars = ['APP_KEY', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
    $missingVars = [];

    foreach ($requiredVars as $var) {
        if (!preg_match("/^$var=/m", $envContent)) {
            $missingVars[] = $var;
        }
    }

    if (!empty($missingVars)) {
        throw new Exception('Missing required environment variables: ' . implode(', ', $missingVars));
    }

    // Check APP_KEY format
    if (!preg_match('/APP_KEY=base64:/', $envContent)) {
        writeln('<comment>Warning: APP_KEY should be base64 encoded. Run: php artisan key:generate</comment>');
    }

    writeln('<info>Environment configuration validated successfully!</info>');
});

desc('Setup environment-specific configurations');
task('env:setup', function () {
    $environment = get('environment', 'production');
    writeln("<info>Setting up $environment environment configurations...</info>");

    // Environment-specific optimizations
    if ($environment === 'production') {
        // Production optimizations
        run('cd {{release_path}} && {{bin/php}} artisan config:cache');
        run('cd {{release_path}} && {{bin/php}} artisan route:cache');
        run('cd {{release_path}} && {{bin/php}} artisan view:cache');
        writeln('<info>Production optimizations applied</info>');
    } else {
        // Development/staging - clear caches for easier debugging
        run('cd {{release_path}} && {{bin/php}} artisan config:clear');
        run('cd {{release_path}} && {{bin/php}} artisan route:clear');
        run('cd {{release_path}} && {{bin/php}} artisan view:clear');
        writeln('<info>Development caches cleared</info>');
    }
});

// After deployment tasks
after('deploy:failed', 'deploy:unlock');
after('deploy', 'deploy:cleanup');

// Before deployment validation
before('deploy:prepare', 'env:validate');

// Set deployment timeout
set('default_timeout', 600); // 10 minutes

// Keep only 3 releases
set('keep_releases', 3);

// Disable anonymous stats
set('allow_anonymous_stats', false);
