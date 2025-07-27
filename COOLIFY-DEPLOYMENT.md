# Coolify Deployment Guide for Botble Real Estate

This guide provides step-by-step instructions for deploying your Botble Real Estate application using Coolify with a custom Dockerfile.

## 📁 Essential Files for Deployment

The following files in the root directory are required for Coolify deployment:

- **`Dockerfile`** - Production-ready container configuration
- **`nginx.conf`** - Web server configuration
- **`php.ini`** - PHP runtime configuration
- **`.dockerignore`** - Build optimization (excludes unnecessary files)
- **`COOLIFY-DEPLOYMENT.md`** - This deployment guide

All other Docker-related files have been cleaned up to avoid confusion.

## Prerequisites

- Coolify instance running
- GitHub repository access
- Database server (MySQL/PostgreSQL)

## Deployment Steps

### 1. Configure Coolify Project

1. **Create New Application**
   - Go to your Coolify dashboard
   - Click "New Application"
   - Select "Docker" as the build pack
   - Connect your GitHub repository: `taimoor-shan/immo212`
   - Select branch: `features/rentals`

2. **Build Configuration**
   - **Build Pack**: Docker
   - **Dockerfile Location**: `./Dockerfile` (root directory)
   - **Build Context**: `.` (root directory)

### 2. Environment Variables

Set the following environment variables in Coolify:

```bash
# Application
APP_NAME="Botble Real Estate"
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_KEY_HERE
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=your-database-name
DB_USERNAME=your-db-username
DB_PASSWORD=your-db-password

# Cache & Session
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=null
REDIS_PORT=6379

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=public

# Botble Specific
CMS_NAME="Botble Real Estate"
CMS_ADMIN_PREFIX=admin
```

### 3. Generate Application Key

If you don't have an APP_KEY, generate one:

```bash
php artisan key:generate --show
```

### 4. Database Setup

Before first deployment, ensure your database is created and accessible.

### 5. Deploy

1. Click "Deploy" in Coolify
2. Monitor the build logs
3. Wait for deployment to complete (usually 5-10 minutes)

## Post-Deployment Steps

### 1. Run Database Migrations

Access your container and run:

```bash
php artisan migrate --force
php artisan db:seed --force
```

### 2. Create Admin User

```bash
php artisan cms:user:create
```

### 3. Set Storage Permissions

```bash
php artisan storage:link
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Dockerfile Features

This Dockerfile includes:

- ✅ **PHP 8.2-FPM** with all required extensions
- ✅ **Nginx** web server
- ✅ **Redis** support for caching and sessions
- ✅ **Node.js 18** for asset compilation
- ✅ **Supervisor** for process management
- ✅ **Optimized** for production use
- ✅ **Health checks** for monitoring
- ✅ **Laravel scheduler** via cron
- ✅ **Security headers** and configurations

## Troubleshooting

### Build Fails

1. Check build logs in Coolify
2. Ensure all files are committed to Git
3. Verify Dockerfile syntax

### Application Not Loading

1. Check container logs
2. Verify environment variables
3. Ensure database connectivity

### Permission Issues

```bash
chown -R www:www /var/www/html/storage
chmod -R 755 /var/www/html/storage
```

### Database Connection Issues

1. Verify database credentials
2. Check database host accessibility
3. Ensure database exists

## Performance Optimization

The Dockerfile includes several optimizations:

- OPcache enabled for PHP
- Gzip compression in Nginx
- Static asset caching
- Optimized Composer autoloader
- Production asset compilation

## Monitoring

The application includes a health check endpoint:

```
GET /health
```

This returns `200 OK` with "healthy" response when the application is running properly.

## Support

For issues specific to:
- **Botble CMS**: Check Botble documentation
- **Coolify**: Check Coolify documentation
- **Docker**: Review container logs

## Security Notes

- All sensitive files are excluded via .dockerignore
- PHP version headers are hidden
- Security headers are enabled
- File upload restrictions are in place
- Admin panel has rate limiting
