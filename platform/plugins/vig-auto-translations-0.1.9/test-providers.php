<?php

/**
 * VIG Auto Translations - Provider Test Script
 * 
 * This script tests all translation providers to ensure they work correctly
 * with the latest API configurations.
 */

require_once __DIR__ . '/../../../../../../bootstrap/app.php';

$app = require_once __DIR__ . '/../../../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use VigStudio\VigAutoTranslations\Services\GoogleTranslator;
use VigStudio\VigAutoTranslations\Services\AWSTranslator;
use VigStudio\VigAutoTranslations\Services\ChatGPTTranslator;
use Illuminate\Support\Facades\Log;

echo "🧪 VIG Auto Translations - Provider Testing Suite\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Test configuration
$testText = "Hello, this is a test message for translation.";
$sourceLanguage = 'en';
$targetLanguage = 'es';

// Test results
$results = [];

/**
 * Test a translation provider
 */
function testProvider(string $name, $provider, string $source, string $target, string $text): array
{
    echo "🔍 Testing {$name} Provider...\n";
    
    $startTime = microtime(true);
    $result = [
        'provider' => $name,
        'success' => false,
        'translation' => null,
        'error' => null,
        'duration' => 0,
        'supports_language_pair' => false,
    ];
    
    try {
        // Check language support if method exists
        if (method_exists($provider, 'supportsLanguagePair')) {
            $result['supports_language_pair'] = $provider->supportsLanguagePair($source, $target);
            echo "   Language pair support ({$source} → {$target}): " . 
                 ($result['supports_language_pair'] ? '✅ Yes' : '❌ No') . "\n";
        }
        
        // Attempt translation
        $translation = $provider->translate($source, $target, $text);
        $result['duration'] = round((microtime(true) - $startTime) * 1000, 2);
        
        if ($translation && $translation !== $text) {
            $result['success'] = true;
            $result['translation'] = $translation;
            echo "   ✅ Translation successful ({$result['duration']}ms)\n";
            echo "   📝 Original: \"{$text}\"\n";
            echo "   🌍 Translated: \"{$translation}\"\n";
        } else if ($translation === $text) {
            echo "   ⚠️  Translation returned unchanged text\n";
            $result['error'] = 'Translation returned unchanged text';
        } else {
            echo "   ❌ Translation failed (empty result)\n";
            $result['error'] = 'Empty translation result';
        }
        
    } catch (\Exception $e) {
        $result['duration'] = round((microtime(true) - $startTime) * 1000, 2);
        $result['error'] = $e->getMessage();
        echo "   ❌ Translation failed ({$result['duration']}ms)\n";
        echo "   🔥 Error: {$e->getMessage()}\n";
    }
    
    echo "\n";
    return $result;
}

/**
 * Check environment configuration
 */
function checkConfiguration(): void
{
    echo "⚙️  Configuration Check\n";
    echo "-" . str_repeat("-", 30) . "\n";
    
    // Check Google Translate (no configuration needed)
    echo "🟢 Google Translate: Ready (no configuration required)\n";
    
    // Check AWS configuration
    $awsKey = env('AWS_ACCESS_KEY_ID');
    $awsSecret = env('AWS_SECRET_ACCESS_KEY');
    $awsRegion = env('AWS_DEFAULT_REGION', 'us-east-1');
    
    if ($awsKey && $awsSecret) {
        echo "🟢 AWS Translate: Configured\n";
        echo "   Region: {$awsRegion}\n";
        echo "   Key: " . substr($awsKey, 0, 8) . "***\n";
    } else {
        echo "🟡 AWS Translate: Not configured (missing AWS_ACCESS_KEY_ID or AWS_SECRET_ACCESS_KEY)\n";
    }
    
    // Check OpenAI configuration
    $openaiKey = env('OPENAI_API_KEY');
    $openaiModel = env('OPENAI_MODEL', 'gpt-4.1');
    $customSystemMessage = env('OPENAI_SYSTEM_MESSAGE', '');
    
    if ($openaiKey) {
        echo "🟢 OpenAI/ChatGPT: Configured\n";
        echo "   Model: {$openaiModel} (Latest: GPT-4.1 - Superior coding +21.4%, Better instruction following +10.5%)\n";
        echo "   Key: " . substr($openaiKey, 0, 12) . "***\n";
        
        // Show model capabilities
        $modelCapabilities = [
            'gpt-4.1' => '1M tokens, flagship performance',
            'gpt-4.1-mini' => '128K tokens, balanced speed/cost',
            'gpt-4.1-nano' => '32K tokens, ultra-low latency'
        ];
        
        if (isset($modelCapabilities[$openaiModel])) {
            echo "   Capabilities: {$modelCapabilities[$openaiModel]}\n";
        }
        
        if ($customSystemMessage) {
            echo "   Custom System Message: Configured (" . strlen($customSystemMessage) . " chars)\n";
        } else {
            echo "   Custom System Message: Using enhanced GPT-4.1 optimized default\n";
        }
    } else {
        echo "🟡 OpenAI/ChatGPT: Not configured (missing OPENAI_API_KEY)\n";
    }
    
    echo "\n";
}

/**
 * Display test summary
 */
function displaySummary(array $results): void
{
    echo "📊 Test Summary\n";
    echo "=" . str_repeat("=", 20) . "\n";
    
    $successful = array_filter($results, fn($r) => $r['success']);
    $failed = array_filter($results, fn($r) => !$r['success']);
    
    echo "Total providers tested: " . count($results) . "\n";
    echo "✅ Successful: " . count($successful) . "\n";
    echo "❌ Failed: " . count($failed) . "\n\n";
    
    if (!empty($successful)) {
        echo "🎉 Working Providers:\n";
        foreach ($successful as $result) {
            echo "   • {$result['provider']} ({$result['duration']}ms)\n";
        }
        echo "\n";
    }
    
    if (!empty($failed)) {
        echo "⚠️  Failed Providers:\n";
        foreach ($failed as $result) {
            echo "   • {$result['provider']}: {$result['error']}\n";
        }
        echo "\n";
    }
    
    // Performance comparison
    if (count($successful) > 1) {
        echo "🏃 Performance Comparison:\n";
        usort($successful, fn($a, $b) => $a['duration'] <=> $b['duration']);
        foreach ($successful as $i => $result) {
            $rank = $i + 1;
            echo "   {$rank}. {$result['provider']}: {$result['duration']}ms\n";
        }
        echo "\n";
    }
}

/**
 * Main test execution
 */
try {
    checkConfiguration();
    
    // Test Google Translate
    $googleTranslator = new GoogleTranslator();
    $results[] = testProvider('Google Translate', $googleTranslator, $sourceLanguage, $targetLanguage, $testText);
    
    // Test AWS Translate (only if configured)
    if (env('AWS_ACCESS_KEY_ID') && env('AWS_SECRET_ACCESS_KEY')) {
        $awsTranslator = new AWSTranslator();
        $results[] = testProvider('AWS Translate', $awsTranslator, $sourceLanguage, $targetLanguage, $testText);
    } else {
        echo "⏭️  Skipping AWS Translate (not configured)\n\n";
    }
    
    // Test ChatGPT (only if configured)
    if (env('OPENAI_API_KEY')) {
        $chatgptTranslator = new ChatGPTTranslator();
        $results[] = testProvider('ChatGPT/OpenAI', $chatgptTranslator, $sourceLanguage, $targetLanguage, $testText);
    } else {
        echo "⏭️  Skipping ChatGPT/OpenAI (not configured)\n\n";
    }
    
    displaySummary($results);
    
    // Additional tests
    echo "🔬 Additional Tests\n";
    echo "-" . str_repeat("-", 20) . "\n";
    
    // Test empty string handling
    echo "Testing empty string handling...\n";
    $emptyResult = $googleTranslator->translate($sourceLanguage, $targetLanguage, '');
    echo $emptyResult === '' ? "✅ Empty string handled correctly\n" : "❌ Empty string not handled correctly\n";
    
    // Test same language pair
    echo "Testing same language pair (en → en)...\n";
    $sameResult = $googleTranslator->translate('en', 'en', $testText);
    echo $sameResult === $testText ? "✅ Same language pair handled correctly\n" : "❌ Same language pair not handled correctly\n";
    
    echo "\n🎯 Test completed successfully!\n";
    echo "💡 To configure missing providers, add the required environment variables to your .env file.\n";
    
} catch (\Exception $e) {
    echo "❌ Test script failed: {$e->getMessage()}\n";
    echo "Stack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
