#!/bin/bash

# Safe Testing Script - Prevents Production Database Wipes
# This script ensures tests never run against production database

echo "==================================="
echo "SAFE TEST RUNNER"
echo "==================================="
echo ""

# Check current environment
if [ -f .env ]; then
    CURRENT_DB=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
    echo "⚠️  Current database in .env: $CURRENT_DB"
    echo ""
fi

# Safety check - never run tests if production keywords are found
if grep -q -E "(production|live|staging|dbytg49qeagiar)" .env 2>/dev/null; then
    echo "❌ ERROR: Production/staging database detected in .env!"
    echo "Tests will NOT run to protect your data."
    echo ""
    echo "To run tests safely, please:"
    echo "1. Create a test database"
    echo "2. Use phpunit.xml.testing configuration"
    echo "3. Or use SQLite in-memory database"
    echo ""
    echo "Example command for safe testing:"
    echo "  php artisan test --configuration=phpunit.xml.testing"
    exit 1
fi

# Option 1: Use SQLite in-memory database (safest)
echo "Option 1: Run tests with SQLite in-memory database (safest):"
echo "  DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test"
echo ""

# Option 2: Use separate test database
echo "Option 2: Use separate test database:"
echo "  1. Create a test database: 'test_immo212' or similar"
echo "  2. Update phpunit.xml.testing with test database name"
echo "  3. Run: php artisan test --configuration=phpunit.xml.testing"
echo ""

# Ask for confirmation
read -p "Do you want to run tests with SQLite in-memory database? (y/n): " -n 1 -r
echo ""

if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "Running tests with SQLite in-memory database..."
    DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test $@
else
    echo "Tests cancelled for safety. Please configure a test database first."
fi
