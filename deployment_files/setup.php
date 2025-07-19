<?php
/**
 * Laravel Setup Script for SiteGround Deployment
 * 
 * IMPORTANT: DELETE THIS FILE AFTER SETUP IS COMPLETE!
 * 
 * This script helps set up your Laravel application on SiteGround
 * by running necessary artisan commands.
 */

// Security check - only allow access from specific IPs if needed
// Uncomment and modify the line below to restrict access
// if (!in_array($_SERVER['REMOTE_ADDR'], ['your.ip.address.here'])) die('Access denied');

echo "<h1>Laravel Setup Script</h1>";
echo "<p><strong>WARNING: Delete this file after setup!</strong></p>";

try {
    // Load Laravel
    require_once 'app/vendor/autoload.php';
    $app = require_once 'app/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    echo "<h2>Running Setup Commands...</h2>";

    // Test database connection
    echo "<p>Testing database connection...</p>";
    try {
        $pdo = new PDO(
            'mysql:host=127.0.0.1;dbname=dbytg49qeagiar',
            'u0fzbmhrasbke',
            'pmcrwbxqcfnf'
        );
        echo "<p style='color: green;'>✓ Database connection successful!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
        exit;
    }

    // Skip migrations since database is imported directly
    echo "<p>Skipping migrations (database imported directly)...</p>";
    echo "<p style='color: green;'>✓ Using imported database!</p>";

    // Create storage link
    echo "<p>Creating storage symbolic link...</p>";
    try {
        $kernel->call('storage:link');
        echo "<p style='color: green;'>✓ Storage link created!</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠ Storage link may already exist or failed: " . $e->getMessage() . "</p>";
    }

    // Cache configuration
    echo "<p>Caching configuration...</p>";
    $kernel->call('config:cache');
    echo "<p style='color: green;'>✓ Configuration cached!</p>";

    // Cache routes
    echo "<p>Caching routes...</p>";
    $kernel->call('route:cache');
    echo "<p style='color: green;'>✓ Routes cached!</p>";

    // Cache views
    echo "<p>Caching views...</p>";
    $kernel->call('view:cache');
    echo "<p style='color: green;'>✓ Views cached!</p>";

    echo "<h2 style='color: green;'>Setup Completed Successfully!</h2>";
    echo "<p><strong>IMPORTANT: Delete this file (setup.php) immediately for security!</strong></p>";
    echo "<p>Your Laravel application should now be accessible at: <a href='/'>/</a></p>";
    echo "<p>Admin panel should be accessible at: <a href='/admin'>/admin</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Setup Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check your configuration and try again.</p>";
}

echo "<hr>";
echo "<p><small>Laravel Setup Script - Delete after use!</small></p>";
?>
