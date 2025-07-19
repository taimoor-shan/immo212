#!/bin/bash

# Git-based deployment script
# This creates a deployment branch optimized for production

echo "📦 Git-based Deployment Setup"
echo "============================="

# Create deployment branch
git checkout -b deployment 2>/dev/null || git checkout deployment

# Remove development files
rm -rf node_modules/
rm -f .env.example
rm -f docker-compose.yml
rm -f *.md
rm -f deploy.sh
rm -f export_database.sh
rm -f database_export_*.sql

# Build production assets
npm ci --production
npm run build

# Optimize composer
composer install --no-dev --optimize-autoloader

# Create production .env
cp .env .env.backup
cat > .env << EOF
APP_NAME="IMMO212"
APP_DEBUG=false
APP_ENV=production
APP_URL=https://staging2.immo212.ma
APP_KEY=base64:LC9WYL7baA5haD6wU+u5EEBKQbAoU9JlR8A6UETxfTQ=

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE="dbytg49qeagiar"
DB_USERNAME="u0fzbmhrasbke"
DB_PASSWORD="pmcrwbxqcfnf"
DB_STRICT=false

ADMIN_DIR=admin
CMS_ENABLE_INSTALLER=false
EOF

# Commit deployment version
git add .
git commit -m "Production deployment $(date)"

echo "✅ Deployment branch ready!"
echo "📋 Next steps:"
echo "1. Upload this branch to your server"
echo "2. Switch back to main: git checkout main"
echo "3. Restore local .env: mv .env.backup .env"

# Switch back to main
git checkout main
mv .env.backup .env 2>/dev/null

echo "🔄 Switched back to development branch"
