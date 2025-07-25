# IMMO212 Real Estate Platform - Deployment Setup Guide

## 🚀 Quick Setup (5 Minutes)

### Step 1: Install Dependencies

```bash
# Install Deployer PHP (already done)
composer require deployer/deployer --dev

# Install Node.js dependencies
npm install
```

### Step 2: Configure Deployment

1. **Update Repository URL** in `deploy.php`:
```php
set('repository', 'git@github.com:your-username/your-repo.git');
```

2. **Configure Production Host** in `deploy.php`:
```php
host('production')
    ->setHostname('your-server-ip.com')
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/yourdomain.com')
    ->set('branch', 'main');
```

### Step 3: Set Up SSH Keys

```bash
# Generate SSH key (if not exists)
ssh-keygen -t rsa -b 4096 -C "your-email@domain.com"

# Copy to server
ssh-copy-id cloudpanel@your-server-ip.com

# Test connection
ssh cloudpanel@your-server-ip.com
```

### Step 4: Create Server Environment

1. **Create .env file** on server:
```bash
ssh cloudpanel@your-server-ip.com
mkdir -p /home/cloudpanel/htdocs/yourdomain.com/shared
cp .env.production.example /home/cloudpanel/htdocs/yourdomain.com/shared/.env
# Edit the .env file with your actual values
```

2. **Create required directories**:
```bash
mkdir -p /home/cloudpanel/htdocs/yourdomain.com/shared/storage
mkdir -p /home/cloudpanel/htdocs/yourdomain.com/backups
```

### Step 5: Test & Deploy

```bash
# Test deployment setup
./test-deployment.sh

# First deployment
./deploy.sh
```

## 📋 Detailed Setup Instructions

### Server Requirements

- **CloudPanel** installed and configured
- **PHP 8.2+** with required extensions
- **MySQL/MariaDB** database
- **Node.js** and npm (for asset compilation)
- **Git** installed
- **SSH access** with key-based authentication

### CloudPanel Configuration

1. **Create Domain** in CloudPanel:
   - Add your domain
   - Set document root to `/home/cloudpanel/htdocs/yourdomain.com/current/public`
   - Enable SSL certificate

2. **Database Setup**:
   - Create MySQL database
   - Create database user with full privileges
   - Note credentials for .env file

3. **PHP Configuration**:
   - Ensure PHP 8.2+ is selected
   - Enable required extensions: `mbstring`, `xml`, `gd`, `curl`, `zip`
   - Set appropriate memory limits

### Local Development Setup

1. **Clone Repository**:
```bash
git clone git@github.com:your-username/your-repo.git
cd your-repo
```

2. **Install Dependencies**:
```bash
composer install
npm install
```

3. **Configure Environment**:
```bash
cp .env.example .env
php artisan key:generate
```

### Deployment Configuration

#### 1. Update deploy.php

Replace placeholders with your actual values:

```php
// Repository
set('repository', 'git@github.com:your-username/immo212-real-estate.git');

// Production host
host('production')
    ->setHostname('123.456.789.123') // Your server IP
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/immo212.ma')
    ->set('branch', 'main');

// Staging host (optional)
host('staging')
    ->setHostname('staging.immo212.ma')
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/staging.immo212.ma')
    ->set('branch', 'develop');
```

#### 2. Server Environment File

Create `/home/cloudpanel/htdocs/yourdomain.com/shared/.env`:

```env
APP_NAME="IMMO212 Real Estate"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password

# ... other configuration
```

#### 3. SSH Key Setup

```bash
# On your local machine
ssh-keygen -t rsa -b 4096 -C "deployment@yourdomain.com"

# Copy public key to server
ssh-copy-id -i ~/.ssh/id_rsa.pub cloudpanel@your-server-ip

# Test connection
ssh cloudpanel@your-server-ip "echo 'SSH connection successful'"
```

### First Deployment

1. **Test Configuration**:
```bash
./test-deployment.sh
```

2. **Deploy Application**:
```bash
# Standard deployment
./deploy.sh

# Or with migrations (first time)
./deploy.sh migrate
```

3. **Verify Deployment**:
   - Visit your website
   - Check admin panel: `https://yourdomain.com/admin`
   - Verify file uploads work
   - Test property listings

## 🔧 Advanced Configuration

### Multiple Environments

Configure staging environment in `deploy.php`:

```php
host('staging')
    ->setHostname('staging.yourdomain.com')
    ->setRemoteUser('cloudpanel')
    ->setDeployPath('/home/cloudpanel/htdocs/staging.yourdomain.com')
    ->set('branch', 'develop');
```

Deploy to staging:
```bash
./deploy.sh deploy staging
```

### Custom Deployment Tasks

Add custom tasks in `deploy.php`:

```php
desc('Clear application cache');
task('app:cache:clear', function () {
    run('cd {{release_path}} && {{bin/php}} artisan cache:clear');
});

// Add to deployment flow
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'build:assets',
    'upload:assets',
    'app:cache:clear', // Your custom task
    'deploy:publish',
]);
```

### Automated Backups

Set up automated database backups:

```bash
# Add to server crontab
0 2 * * * /usr/bin/mysqldump -u username -ppassword database_name > /home/cloudpanel/backups/daily_$(date +\%Y\%m\%d).sql
```

## 🚨 Troubleshooting

### Common Issues

**Permission Errors**:
```bash
ssh cloudpanel@server "chmod -R 775 /home/cloudpanel/htdocs/yourdomain.com/shared/storage"
```

**Asset Loading Issues**:
```bash
npm run production
./deploy.sh quick
```

**Database Connection**:
```bash
ssh cloudpanel@server "cat /home/cloudpanel/htdocs/yourdomain.com/shared/.env | grep DB_"
```

**Failed Deployment**:
```bash
./deploy.sh rollback
```

### Log Files

- **Deployment logs**: `./vendor/bin/dep logs production`
- **Laravel logs**: `storage/logs/laravel.log` on server
- **Web server logs**: CloudPanel → Logs section

## 📞 Support

For deployment issues:

1. Run diagnostics: `./test-deployment.sh`
2. Check deployment logs: `./vendor/bin/dep logs production`
3. Verify server connectivity: `ssh cloudpanel@your-server`
4. Review Laravel logs on server

## 🎯 Next Steps

After successful deployment:

1. **Set up monitoring** (uptime, performance)
2. **Configure backups** (database, files)
3. **Set up SSL certificate** renewal
4. **Configure email** notifications
5. **Set up staging** environment
6. **Document custom** configurations

---

**🎉 Congratulations!** Your IMMO212 Real Estate Platform is now deployed with CloudPanel + Deployer PHP for zero-downtime deployments!
