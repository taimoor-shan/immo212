# SiteGround Deployment Checklist - Your Specific Setup

## Database Configuration ✅
Your database credentials are now configured:
- **Database**: `dbytg49qeagiar`
- **Username**: `u0fzbmhrasbke`
- **Host**: `127.0.0.1` (as per your WordPress config)
- **Password**: `pmcrwbxqcfnf`

## Pre-Deployment Steps

### 1. Backup Current WordPress Site
- [ ] Export WordPress database (if you want to keep it)
- [ ] Download WordPress files (if needed for rollback)

### 2. Prepare Laravel Application
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `npm run build` (if you have frontend assets)
- [ ] Clear all caches: `php artisan cache:clear`
- [ ] Generate optimized autoloader: `composer dump-autoload --optimize`

### 3. Environment Configuration
- [ ] Update APP_URL in .env to your actual domain
- [ ] Set APP_DEBUG=false
- [ ] Set APP_ENV=production
- [ ] Generate new APP_KEY: `php artisan key:generate`

## Deployment Process

### Step 1: Database Preparation
1. Access SiteGround cPanel → phpMyAdmin
2. Select database: `dbytg49qeagiar`
3. Drop all WordPress tables (or export first if needed)
4. Import your Laravel database dump

### Step 2: File Upload Strategy
**Recommended Approach: Subdirectory Method**

1. **Create app directory in public_html:**
   ```
   public_html/
   ├── app/                    # All Laravel files except public/
   ├── index.php              # Modified Laravel public/index.php
   ├── .htaccess              # Laravel public/.htaccess
   └── [other public assets]  # CSS, JS, images from public/
   ```

2. **Upload files:**
   - Upload ALL Laravel files to `public_html/app/`
   - Copy contents of `public/` folder to `public_html/` root
   - Modify `public_html/index.php` (see below)

### Step 3: Modify index.php
Update `public_html/index.php`:

```php
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/app/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
$app = require_once __DIR__ . '/app/bootstrap/app.php';

$app->usePublicPath(__DIR__);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
```

### Step 4: File Permissions
Set via SiteGround File Manager:
- `app/storage/` and all subdirectories: **755**
- `app/bootstrap/cache/`: **755**
- `app/.env`: **644**
- `public_html/.htaccess`: **644**

### Step 5: Run Migrations
Create temporary migration script `public_html/setup.php`:

```php
<?php
// DELETE THIS FILE AFTER SETUP!
require_once 'app/vendor/autoload.php';

$app = require_once 'app/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Running migrations...\n";
$kernel->call('migrate', ['--force' => true]);

echo "Creating storage link...\n";
$kernel->call('storage:link');

echo "Optimizing application...\n";
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');

echo "Setup completed! DELETE THIS FILE NOW!";
```

Access: `https://yourdomain.com/setup.php`

## Post-Deployment Testing

### Critical Tests:
- [ ] Homepage loads: `https://yourdomain.com`
- [ ] Admin panel: `https://yourdomain.com/admin`
- [ ] Database connection working
- [ ] File uploads working
- [ ] Images/assets loading correctly

### Common Issues & Solutions:

**500 Internal Server Error:**
- Check `.htaccess` file exists in root
- Verify file permissions
- Check SiteGround error logs

**Database Connection Error:**
- Verify credentials in .env
- Check if database exists and has tables
- Test connection via phpMyAdmin

**Assets Not Loading:**
- Run storage:link command
- Check asset paths in .env
- Verify public folder structure

**Admin Panel 404:**
- Check ADMIN_DIR in .env
- Verify routes are cached properly

## Security Checklist
- [ ] Delete setup.php after use
- [ ] Set CMS_ENABLE_INSTALLER=false
- [ ] Change ADMIN_DIR to something unique
- [ ] Verify APP_DEBUG=false
- [ ] Check file permissions are correct

## Environment Variables to Update
```env
APP_NAME="Your Site Name"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
ADMIN_DIR=your-secret-admin-path
CMS_ENABLE_INSTALLER=false
```

## Rollback Plan
If deployment fails:
1. Restore WordPress files from backup
2. Import WordPress database
3. Update DNS if changed

## Next Steps After Successful Deployment
1. Set up SSL certificate (SiteGround provides free SSL)
2. Configure email settings
3. Set up regular backups
4. Monitor error logs
5. Optimize performance settings

## Support Contacts
- **SiteGround Support**: For hosting/server issues
- **Laravel Documentation**: For framework issues
- **Botble CMS Docs**: For CMS-specific issues
