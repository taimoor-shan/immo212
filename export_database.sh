#!/bin/bash

# Database Export Script for SiteGround Deployment
# This script exports your local database for upload to SiteGround

echo "🗄️  Exporting Laravel Database for SiteGround Deployment"
echo "=================================================="

# Database credentials from your .env file
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_DATABASE="homzen"
DB_USERNAME="root"
DB_PASSWORD="root123"

# Output file
OUTPUT_FILE="database_export_$(date +%Y%m%d_%H%M%S).sql"

echo "📊 Database: $DB_DATABASE"
echo "🏠 Host: $DB_HOST:$DB_PORT"
echo "👤 User: $DB_USERNAME"
echo "📁 Output: $OUTPUT_FILE"
echo ""

# Export database
echo "🚀 Starting export..."
mysqldump -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" \
  --single-transaction \
  --routines \
  --triggers \
  --add-drop-table \
  --extended-insert \
  --create-options \
  --quick \
  --lock-tables=false \
  "$DB_DATABASE" > "$OUTPUT_FILE"

# Check if export was successful
if [ $? -eq 0 ]; then
    echo "✅ Database export completed successfully!"
    echo "📁 File saved as: $OUTPUT_FILE"
    echo "📊 File size: $(du -h "$OUTPUT_FILE" | cut -f1)"
    echo ""
    echo "📋 Next steps:"
    echo "1. Upload this SQL file to SiteGround"
    echo "2. Import it via phpMyAdmin to database: dbytg49qeagiar"
    echo "3. Deploy your Laravel files"
    echo ""
    echo "⚠️  IMPORTANT: This file contains sensitive data. Delete it after upload!"
else
    echo "❌ Database export failed!"
    echo "Please check your database credentials and connection."
    exit 1
fi
