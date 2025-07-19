# SiteGround Deployment Guide for Laravel Application

## ⚠️ Important Warnings
- SiteGround WordPress hosting is NOT optimized for Laravel applications
- You may encounter PHP extension limitations
- File permissions may be restricted
- Performance may be suboptimal compared to Laravel-specific hosting

## Pre-Deployment Checklist

### 1. SiteGround Account Setup
- [ ] Access to SiteGround cPanel
- [ ] MySQL database creation privileges
- [ ] File Manager access
- [ ] Domain properly configured

### 2. Database Configuration

#### Step 1: Create Database in cPanel
1. Login to SiteGround cPanel
2. Go to "MySQL Databases"
3. Create new database: `homzen` (will become `username_homzen`)
4. Create database user (will become `username_dbuser`)
5. Assign user to database with ALL PRIVILEGES

#### Step 2: Update .env File
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE="your_cpanel_username_homzen"
DB_USERNAME="your_cpanel_username_dbuser"
DB_PASSWORD="your_database_password"
```

### 3. File Upload Strategy

#### Option A: Subdirectory Approach (Recommended)
1. Create subdirectory: `public_html/app/`
2. Upload all Laravel files to `public_html/app/`
3. Copy contents of `public/` folder to `public_html/`
4. Update `public_html/index.php`:

```php
<?php
// Change this line:
require __DIR__ . '/../vendor/autoload.php';
// To:
require __DIR__ . '/app/vendor/autoload.php';

// Change this line:
$app = require_once __DIR__ . '/../bootstrap/app.php';
// To:
$app = require_once __DIR__ . '/app/bootstrap/app.php';
```

#### Option B: Root Directory Approach (Risky)
1. Upload all files to `public_html/`
2. Move contents of `public/` to root
3. Update paths accordingly

### 4. Environment Configuration

#### Update .env for Production:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

#### SSL Configuration:
```env
FORCE_SCHEMA=https
FORCE_ROOT_URL=https://yourdomain.com
```

### 5. File Permissions
Set these permissions via cPanel File Manager:
- `storage/` and subdirectories: 755
- `bootstrap/cache/`: 755
- `.env`: 644

### 6. Required PHP Extensions
Verify these are available in SiteGround:
- OpenSSL
- PDO
- Mbstring
- Tokenizer
- JSON
- cURL
- GD
- Fileinfo
- XML
- Ctype

### 7. Database Migration
After upload, run migrations via SSH (if available) or create a temporary migration script:

```php
// Create: public_html/migrate.php (DELETE AFTER USE!)
<?php
require_once 'app/vendor/autoload.php';
$app = require_once 'app/bootstrap/app.php';
Artisan::call('migrate', ['--force' => true]);
echo "Migrations completed";
```

## Troubleshooting Common Issues

### 1. 500 Internal Server Error
- Check `.htaccess` file is uploaded
- Verify file permissions
- Check PHP error logs in cPanel

### 2. Database Connection Issues
- Verify database credentials
- Check if database user has proper privileges
- Ensure database exists

### 3. Asset Loading Issues
- Run `php artisan storage:link` (if SSH available)
- Check asset paths in .env
- Verify public folder structure

### 4. Route Not Found
- Ensure mod_rewrite is enabled
- Check .htaccess file
- Verify Laravel routes are properly configured

## Alternative Hosting Recommendations

For better Laravel support, consider:
- **DigitalOcean App Platform**
- **Laravel Forge + DigitalOcean**
- **Cloudways**
- **Heroku**
- **AWS Elastic Beanstalk**

## Security Considerations

1. **Remove installer after deployment:**
   ```env
   CMS_ENABLE_INSTALLER=false
   ```

2. **Change admin URL:**
   ```env
   ADMIN_DIR=your-secret-admin-path
   ```

3. **Generate new APP_KEY:**
   ```bash
   php artisan key:generate
   ```

## Post-Deployment Testing

1. [ ] Homepage loads correctly
2. [ ] Admin panel accessible
3. [ ] Database connection working
4. [ ] File uploads working
5. [ ] Email configuration working
6. [ ] SSL certificate active

## Emergency Rollback Plan

Keep backup of:
- Original database
- Original files
- Working .env configuration

## Support Resources

- SiteGround Support (for hosting issues)
- Laravel Documentation
- Botble CMS Documentation
