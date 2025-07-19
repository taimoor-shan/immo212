# Final Deployment Steps for staging2.immo212.ma

## ✅ Completed Steps:
- [x] Database exported: `database_export_20250719_224205.sql` (924K)
- [x] Environment configured for staging2.immo212.ma
- [x] Database credentials configured for SiteGround
- [x] Deployment files prepared

## 🚀 Deployment Process (No Migrations Needed!)

### Step 1: Database Import
1. **Access SiteGround cPanel** → phpMyAdmin
2. **Select database**: `dbytg49qeagiar`
3. **Drop existing tables** (WordPress tables)
4. **Import**: Upload `database_export_20250719_224205.sql`
5. **Verify**: Check that all your Laravel tables are imported

### Step 2: Prepare Laravel Files Locally
```bash
# Run these commands in your project directory:
composer install --optimize-autoloader --no-dev
npm run build
php artisan config:clear
php artisan cache:clear
```

### Step 3: Upload Files to SiteGround

#### File Structure on SiteGround:
```
public_html/
├── app/                    # ALL Laravel files (except public/ contents)
│   ├── vendor/
│   ├── platform/
│   ├── storage/
│   ├── bootstrap/
│   ├── .env
│   └── ... (all other Laravel files)
├── index.php              # Use deployment_files/index.php
├── .htaccess              # From your Laravel public/.htaccess
├── css/                   # From public/css/
├── js/                    # From public/js/
├── images/                # From public/images/
├── storage/               # Symlink (will be created by setup.php)
└── setup.php              # Use deployment_files/setup.php
```

#### Upload Process:
1. **Upload Laravel app files** to `public_html/app/`
   - Everything EXCEPT the contents of `public/` folder
   - Include `.env`, `vendor/`, `platform/`, etc.

2. **Upload public folder contents** to `public_html/` root
   - Copy contents of `public/` to root
   - Don't copy the `public/` folder itself

3. **Replace index.php** with `deployment_files/index.php`

4. **Upload setup.php** from `deployment_files/setup.php`

### Step 4: Set File Permissions
Via SiteGround File Manager:
- `app/storage/` and subdirectories: **755**
- `app/bootstrap/cache/`: **755**
- `app/.env`: **644**

### Step 5: Run Setup Script
1. Visit: `https://staging2.immo212.ma/setup.php`
2. The script will:
   - Test database connection
   - Create storage symlink
   - Cache configuration, routes, and views
3. **Delete setup.php immediately after success!**

### Step 6: Test Your Site
- **Homepage**: https://staging2.immo212.ma/
- **Admin Panel**: https://staging2.immo212.ma/admin
- **Test key functionality**: login, file uploads, etc.

## 🔧 Troubleshooting

### If you get 500 Error:
1. Check `.htaccess` exists in public_html root
2. Verify file permissions
3. Check SiteGround error logs in cPanel

### If database connection fails:
1. Verify database import was successful
2. Check .env database credentials
3. Test connection via phpMyAdmin

### If assets don't load:
1. Ensure storage symlink was created
2. Check that public folder contents are in root
3. Verify CSS/JS files are accessible

## 📋 Post-Deployment Checklist
- [ ] Homepage loads correctly
- [ ] Admin panel accessible at /admin
- [ ] Database connection working
- [ ] Images and assets loading
- [ ] File uploads working (test in admin)
- [ ] setup.php deleted for security

## 🔒 Security Notes
- Admin URL is currently `/admin` - consider changing ADMIN_DIR in .env
- APP_DEBUG is set to false ✅
- Delete setup.php after use ✅
- Database export file contains sensitive data - delete after upload

## 📁 Files Ready for Upload:
1. `database_export_20250719_224205.sql` - Import to database
2. `deployment_files/index.php` - Upload to public_html root
3. `deployment_files/setup.php` - Upload to public_html root
4. All your Laravel files - Upload to public_html/app/
5. Contents of public/ folder - Upload to public_html root

## ❓ Questions?
Since you're importing the complete database, you should have all your:
- Users and authentication
- Content and pages
- Settings and configurations
- Media files and uploads

The setup.php script will just handle the technical Laravel setup (caching, storage links) without touching your data.

Ready to proceed? 🚀
