# IMMO212 Real Estate Platform - Deployment Guide

## Overview

This Laravel application uses **CloudPanel** for server management and **Deployer PHP** for automated, zero-downtime deployments. The deployment process follows Git-based workflows with atomic releases and instant rollback capabilities.

## 🚀 Quick Start

### Prerequisites

1. **Server Setup**: CloudPanel installed on VPS
2. **SSH Access**: Key-based authentication configured
3. **Git Repository**: Code hosted on GitHub/GitLab
4. **Local Environment**: Composer and Node.js installed

### Basic Deployment

```bash
# Standard deployment
./deploy.sh

# Deploy with database migrations
./deploy.sh migrate

# Quick deployment (assets only)
./deploy.sh quick

# Rollback to previous release
./deploy.sh rollback
```

## 📁 Server Directory Structure

CloudPanel + Deployer PHP creates this structure:

```
/home/cloudpanel/htdocs/yourdomain.com/
├── current -> releases/20250725120000/    # Symlink to active release
├── releases/                              # All deployment releases
│   ├── 20250725120000/                   # Latest release
│   ├── 20250724110000/                   # Previous release
│   └── 20250723100000/                   # Older release
├── shared/                               # Shared files between releases
│   ├── .env                             # Environment configuration
│   ├── storage/                         # Laravel storage directory
│   ├── public/storage -> ../../shared/storage/app/public
│   └── bootstrap/cache/                 # Bootstrap cache
└── backups/                             # Database backups
    ├── backup_20250725_120000.sql
    └── ...
```

## ⚙️ Configuration

### 1. Update deploy.php

Edit the main deployment configuration:

```php
// Update repository URL
set('repository', 'git@github.com:your-username/your-repo.git');

// Update production host
host('production')
    ->setHostname('your-server-ip.com')
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/yourdomain.com')
    ->set('branch', 'main');
```

### 2. Create deploy-config.php

Copy and customize the configuration template:

```bash
cp deploy-config.example.php deploy-config.php
# Edit deploy-config.php with your server details
```

### 3. Set up SSH Keys

```bash
# Generate SSH key (if not exists)
ssh-keygen -t rsa -b 4096 -C "your-email@domain.com"

# Copy public key to server
ssh-copy-id cloudpanel@your-server-ip.com

# Test connection
ssh cloudpanel@your-server-ip.com
```

## 🔄 Deployment Workflows

### Standard Deployment

Perfect for regular code updates without database changes:

```bash
./deploy.sh
```

**Process:**
1. Creates new release directory
2. Clones latest code from Git
3. Runs `composer install --no-dev`
4. Builds assets locally (`npm run production`)
5. Uploads compiled assets
6. Creates storage symlink
7. Caches Laravel configuration
8. Sets CloudPanel permissions
9. **Atomically switches `current` symlink** (zero-downtime)
10. Restarts PHP-FPM and queues

### Migration Deployment

Use when database schema changes are needed:

```bash
./deploy.sh migrate
```

**Additional steps:**
- Creates database backup before migrations
- Runs `php artisan migrate --force`
- Includes all standard deployment steps

### Quick Deployment

For minor updates or asset-only changes:

```bash
./deploy.sh quick
```

**Faster process:**
- Skips some optimization steps
- Focuses on code and asset updates
- Ideal for CSS/JS changes

## 🔙 Rollback Process

Instant rollback to previous working release:

```bash
./deploy.sh rollback
```

**What happens:**
1. Switches `current` symlink to previous release
2. Clears Laravel caches
3. Re-optimizes for rolled-back version
4. **No downtime** - instant switch

## 🛡️ Zero-Downtime Strategy

### How It Works

1. **Atomic Deployments**: New code is prepared in separate directory
2. **Symlink Switch**: `current` directory is a symlink that switches instantly
3. **Shared Resources**: Database, uploads, logs remain consistent
4. **Rollback Ready**: Previous releases kept for instant rollback

### Shared Resources

These directories/files are shared between all releases:

- `.env` - Environment configuration
- `storage/` - File uploads, logs, cache
- `public/storage` - Public file access
- `bootstrap/cache/` - Laravel bootstrap cache

## 📊 Monitoring & Health Checks

### Deployment Status

```bash
# Check current releases
./vendor/bin/dep releases production

# View deployment logs
./vendor/bin/dep logs production
```

### Health Verification

After deployment, verify:

1. **Website loads**: Check main pages
2. **Admin panel**: Verify `/admin` access
3. **Database**: Confirm data integrity
4. **File uploads**: Test image uploads
5. **Performance**: Check page load times

## 🚨 Troubleshooting

### Common Issues

**Permission Errors:**
```bash
# Fix storage permissions
ssh cloudpanel@server "chmod -R 775 /home/cloudpanel/htdocs/yourdomain.com/shared/storage"
```

**Asset Loading Issues:**
```bash
# Rebuild and redeploy assets
npm run production
./deploy.sh quick
```

**Database Connection:**
```bash
# Check .env file on server
ssh cloudpanel@server "cat /home/cloudpanel/htdocs/yourdomain.com/shared/.env"
```

**Failed Deployment:**
```bash
# Rollback immediately
./deploy.sh rollback

# Check deployment logs
./vendor/bin/dep logs production
```

### Emergency Procedures

**Complete Rollback:**
```bash
./deploy.sh rollback
```

**Manual Symlink Fix:**
```bash
ssh cloudpanel@server
cd /home/cloudpanel/htdocs/yourdomain.com
ln -sfn releases/PREVIOUS_RELEASE current
```

## 🔧 Advanced Configuration

### Custom Tasks

Add custom deployment tasks in `deploy.php`:

```php
desc('Custom task');
task('custom:task', function () {
    run('cd {{release_path}} && your-command');
});

// Add to deployment flow
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'custom:task',  // Your custom task
    'deploy:publish',
]);
```

### Environment-Specific Deployments

```bash
# Deploy to staging
./deploy.sh deploy staging

# Deploy to production
./deploy.sh deploy production
```

### Notifications

Configure Slack/email notifications in `deploy-config.php` for deployment status updates.

## 📝 Best Practices

1. **Always test locally** before deploying
2. **Use staging environment** for testing migrations
3. **Backup database** before major changes
4. **Monitor logs** after deployment
5. **Keep rollback ready** for quick recovery
6. **Use semantic versioning** for releases
7. **Document changes** in commit messages

## 🆘 Support

For deployment issues:

1. Check deployment logs: `./vendor/bin/dep logs production`
2. Verify server connectivity: `ssh cloudpanel@your-server`
3. Review Laravel logs: `storage/logs/laravel.log`
4. Test rollback procedure: `./deploy.sh rollback`

---

**Remember**: This deployment system provides zero-downtime deployments with instant rollback capabilities. Always test in staging before production deployments!
