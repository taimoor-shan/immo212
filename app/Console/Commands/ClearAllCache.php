<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAllCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-all {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Laravel caches (application, config, route, view, compiled)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Starting comprehensive cache clearing...');
        $this->newLine();

        // Clear application cache
        $this->info('📦 Clearing application cache...');
        $this->call('cache:clear');

        // Clear configuration cache
        $this->info('⚙️  Clearing configuration cache...');
        $this->call('config:clear');

        // Clear route cache
        $this->info('🛣️  Clearing route cache...');
        $this->call('route:clear');

        // Clear view cache
        $this->info('👁️  Clearing compiled views...');
        $this->call('view:clear');

        // Clear compiled classes cache
        $this->info('🔧 Clearing compiled classes...');
        $this->call('clear-compiled');

        // Clear OPcache if available
        if (function_exists('opcache_reset')) {
            $this->info('⚡ Clearing OPcache...');
            opcache_reset();
        }

        $this->newLine();
        $this->info('✅ All caches cleared successfully!');

        return self::SUCCESS;
    }
}
