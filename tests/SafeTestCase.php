<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class SafeTestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        // CRITICAL: Prevent tests from running on production database
        $this->checkDatabaseSafety();
        
        parent::setUp();
    }

    /**
     * Check if it's safe to run tests on current database
     *
     * @throws \Exception
     */
    protected function checkDatabaseSafety(): void
    {
        $dbName = env('DB_DATABASE');
        $appEnv = env('APP_ENV');
        
        // List of production database names to protect
        $protectedDatabases = [
            'dbytg49qeagiar', // Your production database
            'production',
            'live',
            'staging',
            'default', // Common production name
        ];
        
        // Check if current database is protected
        if (in_array($dbName, $protectedDatabases)) {
            throw new \Exception(
                "CRITICAL: Tests cannot run on production database '{$dbName}'! " .
                "Please use a test database or SQLite in-memory database."
            );
        }
        
        // Check if environment is production
        if ($appEnv === 'production' || $appEnv === 'staging') {
            throw new \Exception(
                "CRITICAL: Tests cannot run in {$appEnv} environment! " .
                "Please set APP_ENV=testing in your test configuration."
            );
        }
        
        // Warning for non-test databases
        if (!str_contains($dbName, 'test') && $dbName !== ':memory:') {
            echo "\n⚠️  WARNING: Running tests on database '{$dbName}' which doesn't contain 'test' in its name.\n";
            echo "Consider using a dedicated test database or SQLite in-memory database.\n\n";
        }
    }
}
