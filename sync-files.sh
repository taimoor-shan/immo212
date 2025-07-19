#!/bin/bash

# Simple file sync script for quick updates
# Use this for small changes when you don't want full deployment

echo "🔄 Quick File Sync to Staging"
echo "============================="

STAGING_USER="your_cpanel_username"
STAGING_URL="staging2.immo212.ma"

echo "What do you want to sync?"
echo "1. PHP files only (app/, platform/, config/, routes/)"
echo "2. Views and templates (resources/views/, platform/themes/)"
echo "3. Assets (CSS, JS, images)"
echo "4. Specific file/folder"
echo "5. Everything except node_modules"

read -p "Choose option (1-5): " option

case $option in
    1)
        echo "📄 Syncing PHP files..."
        rsync -avz app/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/app/
        rsync -avz platform/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/platform/
        rsync -avz config/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/config/
        rsync -avz routes/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/routes/
        ;;
    2)
        echo "🎨 Syncing views and templates..."
        rsync -avz resources/views/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/resources/views/
        rsync -avz platform/themes/ ${STAGING_USER}@${STAGING_URL}:/public_html/app/platform/themes/
        ;;
    3)
        echo "🖼️  Syncing assets..."
        npm run build
        rsync -avz public/css/ ${STAGING_USER}@${STAGING_URL}:/public_html/css/
        rsync -avz public/js/ ${STAGING_USER}@${STAGING_URL}:/public_html/js/
        rsync -avz public/images/ ${STAGING_USER}@${STAGING_URL}:/public_html/images/
        ;;
    4)
        read -p "Enter file/folder path: " custom_path
        if [[ $custom_path == public/* ]]; then
            # Remove 'public/' prefix and sync to root
            target_path=${custom_path#public/}
            rsync -avz $custom_path ${STAGING_USER}@${STAGING_URL}:/public_html/$target_path
        else
            rsync -avz $custom_path ${STAGING_USER}@${STAGING_URL}:/public_html/app/$custom_path
        fi
        ;;
    5)
        echo "🚀 Syncing everything..."
        ./deploy.sh
        ;;
    *)
        echo "Invalid option"
        exit 1
        ;;
esac

echo "✅ Sync completed!"
echo "🌐 Check your site: https://staging2.immo212.ma"
