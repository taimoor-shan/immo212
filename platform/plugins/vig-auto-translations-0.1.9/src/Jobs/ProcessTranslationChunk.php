<?php

namespace VigStudio\VigAutoTranslations\Jobs;

use Botble\Translation\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager;

/**
 * Background job for processing translation chunks
 * Prevents gateway timeouts by processing translations asynchronously
 */
class ProcessTranslationChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes per chunk
    public $tries = 3; // Retry failed chunks up to 3 times
    public $maxExceptions = 2; // Allow 2 exceptions before marking as failed

    protected string $batchId;
    protected string $locale;
    protected array $translationsChunk;
    protected int $chunkNumber;
    protected int $totalChunks;

    /**
     * Create a new job instance
     */
    public function __construct(
        string $batchId,
        string $locale,
        array $translationsChunk,
        int $chunkNumber,
        int $totalChunks
    ) {
        $this->batchId = $batchId;
        $this->locale = $locale;
        $this->translationsChunk = $translationsChunk;
        $this->chunkNumber = $chunkNumber;
        $this->totalChunks = $totalChunks;

        // Set queue parameters
        $this->onQueue('translations'); // Use dedicated queue for translations
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        Log::info("Processing translation chunk {$this->chunkNumber}/{$this->totalChunks}", [
            'batch_id' => $this->batchId,
            'locale' => $this->locale,
            'chunk_size' => count($this->translationsChunk)
        ]);

        try {
            // Get current progress
            $progress = Cache::get("translation_progress_{$this->batchId}") ?: [
                'total' => 0,
                'processed' => 0,
                'translated' => 0,
                'skipped' => 0,
                'errors' => 0,
                'status' => 'processing'
            ];

            // Update status to processing
            $progress['status'] = 'processing';
            Cache::put("translation_progress_{$this->batchId}", $progress, now()->addHours(2));

            // Process each translation in the chunk
            $chunkTranslated = 0;
            $chunkSkipped = 0;
            $chunkErrors = 0;
            $chunkProcessed = 0;

            $enhancedManager = app(EnhancedAutoTranslateManager::class);
            $manager = app(Manager::class);

            foreach ($this->translationsChunk as $translation) {
                try {
                    $key = $translation['key'];
                    $englishValue = $translation['value'];
                    $groupName = $translation['group'];

                    // Check if already translated to avoid unnecessary API calls
                    $existingTranslation = $this->getExistingTranslationValue($groupName, $key, $this->locale);
                    
                    if ($existingTranslation && $existingTranslation !== $englishValue) {
                        $chunkSkipped++;
                        $chunkProcessed++;
                        continue;
                    }

                    // Perform translation with timeout protection
                    $translatedValue = $this->translateWithRetry($enhancedManager, $englishValue);

                    if ($translatedValue && $translatedValue !== $englishValue) {
                        // Save translation directly to files
                        $manager->updateTranslation(
                            $this->locale,
                            str_replace('/', DIRECTORY_SEPARATOR, $groupName),
                            [$key => $translatedValue]
                        );

                        $chunkTranslated++;
                        
                        Log::debug("Translated: {$englishValue} → {$translatedValue}", [
                            'batch_id' => $this->batchId,
                            'group' => $groupName,
                            'key' => $key
                        ]);
                    } else {
                        $chunkErrors++;
                        Log::warning("Translation failed", [
                            'batch_id' => $this->batchId,
                            'group' => $groupName,
                            'key' => $key,
                            'value' => $englishValue
                        ]);
                    }

                    $chunkProcessed++;

                } catch (\Exception $e) {
                    $chunkErrors++;
                    $chunkProcessed++;
                    
                    Log::error("Translation error in chunk processing", [
                        'batch_id' => $this->batchId,
                        'chunk_number' => $this->chunkNumber,
                        'key' => $key ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }

                // Add small delay to avoid overwhelming the API
                if (count($this->translationsChunk) > 10) {
                    usleep(100000); // 0.1 second delay for larger chunks
                }
            }

            // Update progress atomically
            $this->updateProgress($chunkProcessed, $chunkTranslated, $chunkSkipped, $chunkErrors);

            Log::info("Chunk {$this->chunkNumber}/{$this->totalChunks} completed", [
                'batch_id' => $this->batchId,
                'processed' => $chunkProcessed,
                'translated' => $chunkTranslated,
                'skipped' => $chunkSkipped,
                'errors' => $chunkErrors
            ]);

        } catch (\Exception $e) {
            Log::error("Critical error processing translation chunk", [
                'batch_id' => $this->batchId,
                'chunk_number' => $this->chunkNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Update progress with chunk error
            $this->updateProgress(count($this->translationsChunk), 0, 0, count($this->translationsChunk));
            
            throw $e; // Re-throw to trigger retry mechanism
        }
    }

    /**
     * Translate with retry mechanism for API failures
     */
    protected function translateWithRetry(EnhancedAutoTranslateManager $manager, string $value, int $maxRetries = 2): ?string
    {
        $attempt = 0;
        
        while ($attempt < $maxRetries) {
            try {
                $result = $manager->translate('en', $this->locale, $value);
                
                if ($result && $result !== $value) {
                    return $result;
                }
                
                // If translation returned same value, don't retry
                if ($result === $value) {
                    return null;
                }
                
            } catch (\Exception $e) {
                $attempt++;
                
                if ($attempt >= $maxRetries) {
                    Log::warning("Translation failed after {$maxRetries} attempts", [
                        'batch_id' => $this->batchId,
                        'value' => substr($value, 0, 100),
                        'error' => $e->getMessage()
                    ]);
                    return null;
                }
                
                // Exponential backoff
                sleep(pow(2, $attempt));
            }
        }
        
        return null;
    }

    /**
     * Update progress in cache atomically
     */
    protected function updateProgress(int $processed, int $translated, int $skipped, int $errors): void
    {
        $lockKey = "translation_lock_{$this->batchId}";
        
        // Simple lock mechanism using cache
        $lockAcquired = Cache::add($lockKey, true, 30); // 30 second lock
        
        if (!$lockAcquired) {
            // Wait for lock and try again
            sleep(1);
            $lockAcquired = Cache::add($lockKey, true, 30);
        }
        
        if ($lockAcquired) {
            try {
                $progress = Cache::get("translation_progress_{$this->batchId}") ?: [
                    'total' => count($this->translationsChunk) * $this->totalChunks,
                    'processed' => 0,
                    'translated' => 0,
                    'skipped' => 0,
                    'errors' => 0,
                    'status' => 'processing',
                    'completed_chunks' => 0
                ];
                
                // Update counters
                $progress['processed'] += $processed;
                $progress['translated'] += $translated;
                $progress['skipped'] += $skipped;
                $progress['errors'] += $errors;
                $progress['completed_chunks'] = ($progress['completed_chunks'] ?? 0) + 1;
                
                // Check if all chunks are complete
                if ($progress['completed_chunks'] >= $this->totalChunks) {
                    $progress['status'] = 'completed';
                    $progress['completed_at'] = now()->toISOString();
                }
                
                Cache::put("translation_progress_{$this->batchId}", $progress, now()->addHours(2));
                
            } finally {
                Cache::forget($lockKey);
            }
        }
    }

    /**
     * Get existing translation value from files
     */
    protected function getExistingTranslationValue(string $group, string $key, string $locale): ?string
    {
        try {
            $translationKey = Str::of($group)
                ->replaceLast(DIRECTORY_SEPARATOR, '::')
                ->append(".{$key}")
                ->toString();
            
            $translation = trans($translationKey, [], $locale);
            
            // Return null if translation is the same as the key (not found)
            return $translation === $translationKey ? null : $translation;
            
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Translation chunk job failed permanently", [
            'batch_id' => $this->batchId,
            'chunk_number' => $this->chunkNumber,
            'exception' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Mark progress as failed
        $progress = Cache::get("translation_progress_{$this->batchId}");
        if ($progress) {
            $progress['status'] = 'failed';
            $progress['error'] = $exception->getMessage();
            $progress['failed_at'] = now()->toISOString();
            Cache::put("translation_progress_{$this->batchId}", $progress, now()->addHours(2));
        }
    }
}
