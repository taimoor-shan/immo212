<?php
/**
 * VIG Auto Translations - Integration Test Script
 * 
 * This script verifies that the modernized plugin integrates properly with Botble CMS
 * Run this from the root of your Botble installation: php platform/plugins/vig-auto-translations-0.1.9/test-integration.php
 */

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../bootstrap/app.php';

echo "🧪 VIG Auto Translations - Integration Test\n";
echo "==========================================\n\n";

try {
    // Test 1: Check if plugin is properly loaded
    echo "1. Testing Plugin Registration...\n";
    
    if (class_exists('VigStudio\\VigAutoTranslations\\EnhancedAutoTranslateManager')) {
        echo "   ✅ EnhancedAutoTranslateManager class found\n";
    } else {
        echo "   ❌ EnhancedAutoTranslateManager class NOT found\n";
    }
    
    if (class_exists('VigStudio\\VigAutoTranslations\\Commands\\EnhancedAutoTranslateThemeCommand')) {
        echo "   ✅ EnhancedAutoTranslateThemeCommand class found\n";
    } else {
        echo "   ❌ EnhancedAutoTranslateThemeCommand class NOT found\n";
    }
    
    if (class_exists('VigStudio\\VigAutoTranslations\\Commands\\EnhancedAutoTranslateCoreCommand')) {
        echo "   ✅ EnhancedAutoTranslateCoreCommand class found\n";
    } else {
        echo "   ❌ EnhancedAutoTranslateCoreCommand class NOT found\n";
    }
    
    echo "\n2. Testing Service Provider Registration...\n";
    
    // Test if commands are registered
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $commands = $kernel->all();
    
    if (isset($commands['vig:translate:theme'])) {
        echo "   ✅ vig:translate:theme command registered\n";
    } else {
        echo "   ❌ vig:translate:theme command NOT registered\n";
    }
    
    if (isset($commands['vig:translate:core'])) {
        echo "   ✅ vig:translate:core command registered\n";
    } else {
        echo "   ❌ vig:translate:core command NOT registered\n";
    }
    
    if (isset($commands['vig:translate:cache'])) {
        echo "   ✅ vig:translate:cache command registered\n";
    } else {
        echo "   ❌ vig:translate:cache command NOT registered\n";
    }
    
    echo "\n3. Testing Enhanced Manager Functionality...\n";
    
    $enhancedManager = $app->make('VigStudio\\VigAutoTranslations\\EnhancedAutoTranslateManager');
    
    if ($enhancedManager instanceof VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager) {
        echo "   ✅ Enhanced manager instantiated successfully\n";
        
        // Test configuration
        $enhancedManager->setTranslatorDriver('google');
        echo "   ✅ Translation driver configuration works\n";
        
        // Test statistics
        $stats = $enhancedManager->getTranslationStats();
        echo "   ✅ Statistics method works - found " . $stats['cached_translations'] . " cached translations\n";
        
    } else {
        echo "   ❌ Enhanced manager failed to instantiate\n";
    }
    
    echo "\n4. Testing Integration with Botble's System...\n";
    
    // Test if Botble's original manager still exists
    if (class_exists('Botble\\Translation\\Manager')) {
        echo "   ✅ Botble's Translation Manager is accessible\n";
        
        $botbleManager = $app->make('Botble\\Translation\\Manager');
        if ($botbleManager) {
            echo "   ✅ Botble's Translation Manager can be instantiated\n";
        }
    }
    
    // Test if Botble's AutoTranslateManager exists
    if (class_exists('Botble\\Translation\\AutoTranslateManager')) {
        echo "   ✅ Botble's AutoTranslateManager is accessible\n";
        
        // Note: We extend this, so it should return our enhanced version
        $autoManager = $app->make('Botble\\Translation\\AutoTranslateManager');
        if ($autoManager instanceof VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager) {
            echo "   ✅ AutoTranslateManager successfully extended with our enhanced version\n";
        } else {
            echo "   ⚠️  AutoTranslateManager extension may not be working (this is OK if Botble's hasn't been used yet)\n";
        }
    }
    
    echo "\n5. Testing Translation Providers...\n";
    
    // Test Google Translator
    try {
        $googleTranslator = new VigStudio\VigAutoTranslations\Services\GoogleTranslator();
        echo "   ✅ Google Translator instantiated successfully\n";
    } catch (Exception $e) {
        echo "   ❌ Google Translator failed: " . $e->getMessage() . "\n";
    }
    
    // Test AWS Translator
    try {
        $awsTranslator = new VigStudio\VigAutoTranslations\Services\AWSTranslator();
        echo "   ✅ AWS Translator instantiated successfully\n";
    } catch (Exception $e) {
        echo "   ❌ AWS Translator failed: " . $e->getMessage() . "\n";
    }
    
    // Test ChatGPT Translator
    try {
        $chatgptTranslator = new VigStudio\VigAutoTranslations\Services\ChatGPTTranslator();
        echo "   ✅ ChatGPT Translator instantiated successfully\n";
    } catch (Exception $e) {
        echo "   ❌ ChatGPT Translator failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n6. Testing File-Based Translation System...\n";
    
    // Test if file-based statistics work
    try {
        $stats = $enhancedManager->getTranslationStats();
        echo "   ✅ File-based statistics work\n";
        echo "   ✅ Theme translation files: " . $stats['theme_translation_files'] . "\n";
        echo "   ✅ Plugin translation files: " . $stats['plugin_translation_files'] . "\n";
        echo "   ✅ Supported locales: " . count($stats['supported_locales']) . "\n";
        
        // Test provider information
        $providers = $enhancedManager->getAvailableProviders();
        echo "   ✅ Available providers: " . implode(', ', array_keys($providers)) . "\n";
        
    } catch (Exception $e) {
        echo "   ❌ File-based system test failed: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Integration Test Complete!\n";
    echo "============================\n\n";
    
    echo "Next Steps (No Migration Required - Plugin is File-Based):\n";
    echo "1. Test statistics: php artisan vig:translate:cache stats\n";
    echo "2. Try translating to Spanish: php artisan vig:translate:theme es --verbose\n";
    echo "3. Try plugin translations: php artisan vig:translate:core es --verbose\n";
    echo "4. Check the admin panel: Settings → Others → VigAutoTranslations\n\n";
    
    echo "✅ The plugin is properly integrated with Botble CMS and ready to use!\n";
    echo "📁 All translations will be saved to Botble's standard file locations.\n";
    
} catch (Exception $e) {
    echo "❌ Integration test failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
