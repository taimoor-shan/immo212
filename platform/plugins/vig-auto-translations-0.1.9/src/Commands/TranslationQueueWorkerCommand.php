<?php

namespace VigStudio\VigAutoTranslations\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('vig:translation:worker', 'Start optimized queue worker for translation jobs')]
class TranslationQueueWorkerCommand extends Command
{
    protected $signature = 'vig:translation:worker 
                           {--timeout=3600 : Maximum seconds the worker should run}
                           {--memory=256 : Memory limit in MB}
                           {--queue=translations : Queue name to process}
                           {--sleep=3 : Seconds to sleep between jobs}
                           {--tries=3 : Number of times to attempt a job}
                           {--daemon : Run worker in daemon mode}';

    protected $description = 'Start optimized queue worker specifically for translation jobs with production settings';

    public function handle(): int
    {
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');
        $queue = $this->option('queue');
        $sleep = $this->option('sleep');
        $tries = $this->option('tries');
        $daemon = $this->option('daemon');

        $this->components->info('🚀 Starting Translation Queue Worker');
        $this->components->info("Queue: {$queue}");
        $this->components->info("Memory Limit: {$memory}MB");
        $this->components->info("Timeout: {$timeout}s");
        $this->components->info("Max Tries: {$tries}");

        if ($daemon) {
            $this->components->warn('⚠️  Running in daemon mode. Use process manager (supervisor/pm2) for production.');
        }

        // Build queue:work command with optimized settings
        $command = sprintf(
            'queue:work --queue=%s --timeout=%d --memory=%d --sleep=%d --tries=%d',
            $queue,
            $timeout,
            $memory,
            $sleep,
            $tries
        );

        if (!$daemon) {
            $command .= ' --once';
        }

        $this->components->info('📋 Command: php artisan ' . $command);
        $this->newLine();

        // Show helpful information
        $this->displayWorkerInfo();

        // Run the queue worker
        return $this->call($command);
    }

    protected function displayWorkerInfo(): void
    {
        $this->components->info('📚 Translation Worker Information:');
        $this->components->bulletList([
            'Processes translation chunks in background to prevent timeouts',
            'Optimized for ChatGPT, AWS Translate, and Google Translate APIs',
            'Includes automatic retry logic for failed translations',
            'Memory efficient processing with chunking',
            'Progress tracking via cache system'
        ]);

        $this->newLine();
        $this->components->info('💡 Production Setup Tips:');
        $this->components->bulletList([
            'Use supervisor or pm2 to manage daemon workers: --daemon',
            'Monitor logs: tail -f storage/logs/laravel.log | grep translation',
            'Scale workers based on translation volume',
            'Ensure Redis/database is optimized for queue performance',
            'Monitor memory usage and adjust --memory as needed'
        ]);

        $this->newLine();
        $this->components->info('🔧 Queue Commands:');
        $this->table(['Command', 'Description'], [
            ['queue:work --queue=translations', 'Process translation jobs'],
            ['queue:failed', 'View failed translation jobs'], 
            ['queue:retry all', 'Retry all failed jobs'],
            ['queue:flush', 'Clear all queued jobs'],
            ['vig:translate:cache stats', 'View translation statistics']
        ]);

        $this->newLine();
    }
}
