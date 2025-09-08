<?php

/*
 * VIG Auto Translations Dashboard Test Script
 * Run this script to verify the new admin dashboard is working
 */

require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\Route;
use VigStudio\VigAutoTranslations\Http\Controllers\AdminTranslationController;
use VigStudio\VigAutoTranslations\Services\FileBased\TranslationService;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;

echo "🚀 VIG Auto Translations Dashboard Test\n";
echo "=====================================\n\n";

// Test 1: Check if controller exists
echo "1. 📂 Testing Controller Class...\n";
if (class_exists(AdminTranslationController::class)) {
    echo "   ✅ AdminTranslationController exists\n\n";
} else {
    echo "   ❌ AdminTranslationController not found\n\n";
    exit(1);
}

// Test 2: Check if routes are registered
echo "2. 🛣️ Testing Routes...\n";
$dashboardRoute = null;
foreach (Route::getRoutes() as $route) {
    if ($route->getName() === 'vig-auto-translations.dashboard') {
        $dashboardRoute = $route;
        break;
    }
}

if ($dashboardRoute) {
    echo "   ✅ Dashboard route exists: " . $dashboardRoute->uri() . "\n\n";
} else {
    echo "   ❌ Dashboard route not found\n\n";
    exit(1);
}

// Test 3: Check if view exists
echo "3. 🎨 Testing View File...\n";
$viewPath = resource_path('views/plugins/vig-auto-translations/dashboard.blade.php');
if (file_exists($viewPath)) {
    echo "   ✅ Dashboard view exists: {$viewPath}\n\n";
} else {
    echo "   ❌ Dashboard view not found at: {$viewPath}\n\n";
}

// Test 4: Test basic functionality
echo "4. ⚙️ Testing Core Components...\n";
try {
    // Test Translation Service
    $translationService = new TranslationService();
    echo "   ✅ TranslationService instantiated\n";
    
    // Test Enhanced Manager  
    $enhancedManager = app(EnhancedAutoTranslateManager::class);
    echo "   ✅ EnhancedAutoTranslateManager instantiated\n";
    
    // Test getting statistics
    $stats = $enhancedManager->getTranslationStats();
    echo "   ✅ Statistics method works\n";
    
    echo "\n";
} catch (Exception $e) {
    echo "   ❌ Error testing components: " . $e->getMessage() . "\n\n";
}

// Test 5: Check file permissions
echo "5. 🔐 Testing File Permissions...\n";
$pluginPath = base_path('platform/plugins/vig-auto-translations-0.1.9');
if (is_readable($pluginPath)) {
    echo "   ✅ Plugin directory is readable\n";
} else {
    echo "   ❌ Plugin directory permission issues\n";
}

$controllerPath = $pluginPath . '/src/Http/Controllers/AdminTranslationController.php';
if (is_readable($controllerPath)) {
    echo "   ✅ Controller file is readable\n";
} else {
    echo "   ❌ Controller file permission issues\n";
}

$viewsPath = $pluginPath . '/resources/views';
if (is_readable($viewsPath)) {
    echo "   ✅ Views directory is readable\n";
} else {
    echo "   ❌ Views directory permission issues\n";
}

echo "\n";

// Final instructions
echo "🎯 Dashboard Access Instructions:\n";
echo "=================================\n";
echo "1. Make sure you're logged into the admin panel\n";
echo "2. Navigate to: http://your-domain/admin/vig-auto-translations/dashboard\n";
echo "3. Or look for 'Smart Translations Pro' in your admin menu\n\n";

echo "🔧 If you don't see the menu item:\n";
echo "- Check your user permissions for 'vig-auto-translations.index'\n";
echo "- Clear cache: php artisan cache:clear\n";
echo "- Check Laravel logs for any errors\n\n";

echo "📊 Current Route List (Dashboard routes):\n";
foreach (Route::getRoutes() as $route) {
    if (str_contains($route->getName() ?: '', 'vig-auto-translations.dashboard')) {
        echo "   🛣️ " . $route->methods()[0] . " " . $route->uri() . " -> " . $route->getName() . "\n";
    }
}

echo "\n✨ Test completed! Your dashboard should be accessible.\n";
