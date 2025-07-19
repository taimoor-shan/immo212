#!/bin/bash

# Laravel to SiteGround Deployment Script
# This script syncs only changed files to your staging server

echo "🚀 Laravel Deployment to SiteGround Staging"
echo "============================================"

# Configuration
STAGING_URL="staging2.immo212.ma"
STAGING_USER="your_cpanel_username"
STAGING_PATH="/public_html"
LOCAL_PATH="."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✓${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

# Check if rsync is available
if ! command -v rsync &> /dev/null; then
    print_error "rsync is required but not installed. Please install rsync first."
    exit 1
fi

echo "📋 Pre-deployment checklist:"
echo "1. Have you tested your changes locally? (y/n)"
read -r test_local
if [[ $test_local != "y" ]]; then
    print_warning "Please test your changes locally first!"
    exit 1
fi

echo "2. Do you want to backup the remote site first? (y/n)"
read -r backup_remote
if [[ $backup_remote == "y" ]]; then
    print_warning "Please create a backup via SiteGround cPanel before proceeding"
    echo "Press Enter when backup is complete..."
    read -r
fi

# Build assets if needed
echo "🔨 Building assets..."
if [ -f "package.json" ]; then
    npm run build
    print_status "Assets built successfully"
else
    print_warning "No package.json found, skipping asset build"
fi

# Clear local caches
echo "🧹 Clearing local caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
print_status "Local caches cleared"

# Optimize for production
echo "⚡ Optimizing for production..."
composer dump-autoload --optimize
print_status "Autoloader optimized"

# Sync Laravel app files (excluding public folder contents)
echo "📁 Syncing Laravel application files..."
rsync -avz --delete \
    --exclude='public/' \
    --exclude='node_modules/' \
    --exclude='.git/' \
    --exclude='*.md' \
    --exclude='deploy.sh' \
    --exclude='export_database.sh' \
    --exclude='database_export_*.sql' \
    --exclude='deployment_files/' \
    --exclude='.env.example' \
    --exclude='docker-compose.yml' \
    --exclude='cookies.txt' \
    --exclude='*.json' \
    --exclude='debug-*.md' \
    ./ ${STAGING_USER}@${STAGING_URL}:${STAGING_PATH}/app/

if [ $? -eq 0 ]; then
    print_status "Laravel app files synced successfully"
else
    print_error "Failed to sync Laravel app files"
    exit 1
fi

# Sync public folder contents to web root
echo "🌐 Syncing public assets..."
rsync -avz --delete \
    --exclude='index.php' \
    public/ ${STAGING_USER}@${STAGING_URL}:${STAGING_PATH}/

if [ $? -eq 0 ]; then
    print_status "Public assets synced successfully"
else
    print_error "Failed to sync public assets"
    exit 1
fi

# Update .env for production (create a production version)
echo "⚙️  Updating environment configuration..."
cat > .env.production << EOF
APP_NAME="IMMO212"
APP_DEBUG=false
APP_ENV=production
APP_URL=https://staging2.immo212.ma
APP_KEY=base64:LC9WYL7baA5haD6wU+u5EEBKQbAoU9JlR8A6UETxfTQ=
LOG_CHANNEL=daily

BROADCAST_DRIVER=log
CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# SiteGround Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="dbytg49qeagiar"
DB_USERNAME="u0fzbmhrasbke"
DB_PASSWORD="pmcrwbxqcfnf"
DB_STRICT=false

ADMIN_DIR=admin
CMS_ENABLE_INSTALLER=false

MAIL_MAILER=log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@staging2.immo212.ma
MAIL_FROM_NAME="IMMO212"
EOF

# Upload production .env
scp .env.production ${STAGING_USER}@${STAGING_URL}:${STAGING_PATH}/app/.env

if [ $? -eq 0 ]; then
    print_status "Production environment file updated"
    rm .env.production
else
    print_error "Failed to update environment file"
fi

echo ""
echo "🎉 Deployment completed successfully!"
echo "🌐 Your site: https://${STAGING_URL}"
echo "🔧 Admin panel: https://${STAGING_URL}/admin"
echo ""
print_warning "Remember to:"
echo "  - Test the deployed site"
echo "  - Clear caches if needed via admin panel"
echo "  - Monitor error logs in SiteGround cPanel"
