<?php

namespace VigStudio\VigAutoTranslations\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Translation\Services\GetGroupedTranslationsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use VigStudio\VigAutoTranslations\Jobs\ProcessTranslationChunk;

/**
 * Optimized bulk translation controller to handle large translation sets
 * without gateway timeouts on production environments
 */
class OptimizedBulkTranslationController extends BaseController
{
    /**
     * Start chunked bulk translation process with progress tracking
     * This method returns immediately and processes translations in background
     */
    public function startChunkedTranslation(Request $request, BaseHttpResponse $response)
    {
        $locale = $request->input('locale');
        $chunkSize = (int) $request->input('chunk_size', 25); // Default 25 items per chunk
        
        if (!$locale || $locale === 'en') {
            return $response
                ->setError()
                ->setMessage('Please select a valid target language (not English)');
        }

        // Validate chunk size
        if ($chunkSize < 1 || $chunkSize > 100) {
            $chunkSize = 25;
        }

        try {
            // Get all translations that need processing
            $groupedTranslationsService = new GetGroupedTranslationsService();
            $allTranslations = $groupedTranslationsService->handle();
            
            // Filter out already translated items to reduce processing load
            $translationsToProcess = $allTranslations->filter(function ($translation) use ($locale) {
                $existingTranslation = $this->getExistingTranslationValue(
                    $translation['group'], 
                    $translation['key'], 
                    $locale
                );
                
                // Only process if not already translated or translation is same as English
                return !$existingTranslation || $existingTranslation === $translation['value'];
            });

            $totalItems = $translationsToProcess->count();
            
            if ($totalItems === 0) {
                return $response->setMessage('All translations are already up to date!');
            }

            // Generate unique job batch ID for tracking
            $batchId = 'bulk_translation_' . $locale . '_' . time();
            
            // Initialize progress tracking in cache
            Cache::put("translation_progress_{$batchId}", [
                'total' => $totalItems,
                'processed' => 0,
                'translated' => 0,
                'skipped' => 0,
                'errors' => 0,
                'status' => 'queued',
                'locale' => $locale,
                'started_at' => now()->toISOString(),
                'chunk_size' => $chunkSize
            ], now()->addHours(2)); // Keep progress for 2 hours

            // Split translations into chunks for processing
            $chunks = $translationsToProcess->chunk($chunkSize);
            $chunkCount = $chunks->count();

            // Queue chunk processing jobs
            foreach ($chunks as $chunkIndex => $chunk) {
                ProcessTranslationChunk::dispatch(
                    $batchId,
                    $locale, 
                    $chunk->toArray(),
                    $chunkIndex + 1,
                    $chunkCount
                )->delay(now()->addSeconds($chunkIndex * 2)); // Stagger jobs to avoid API rate limits
            }

            return $response
                ->setData([
                    'batch_id' => $batchId,
                    'total_items' => $totalItems,
                    'chunk_count' => $chunkCount,
                    'chunk_size' => $chunkSize
                ])
                ->setMessage(sprintf(
                    'Bulk translation started! Processing %d translations in %d chunks. Check progress or wait for completion.',
                    $totalItems,
                    $chunkCount
                ));

        } catch (\Exception $e) {
            logger()->error('Failed to start chunked translation', [
                'locale' => $locale,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $response
                ->setError()
                ->setMessage('Failed to start translation process: ' . $e->getMessage());
        }
    }

    /**
     * Get progress of a chunked translation job
     */
    public function getTranslationProgress(Request $request, BaseHttpResponse $response)
    {
        $batchId = $request->input('batch_id');
        
        if (!$batchId) {
            return $response->setError()->setMessage('Batch ID is required');
        }

        $progress = Cache::get("translation_progress_{$batchId}");
        
        if (!$progress) {
            return $response->setError()->setMessage('Translation job not found or expired');
        }

        // Calculate completion percentage
        $completionPercentage = $progress['total'] > 0 
            ? round(($progress['processed'] / $progress['total']) * 100, 1)
            : 0;

        return $response->setData([
            'progress' => $progress,
            'completion_percentage' => $completionPercentage,
            'is_complete' => $progress['status'] === 'completed',
            'has_errors' => $progress['errors'] > 0
        ]);
    }

    /**
     * Process a small batch of translations immediately (for testing or small groups)
     * This is the fallback for when queue system is not available
     */
    public function processSmallBatch(Request $request, BaseHttpResponse $response)
    {
        $locale = $request->input('locale');
        $group = $request->input('group'); // Optional: specific group only
        $maxItems = min((int) $request->input('max_items', 20), 50); // Max 50 items
        
        if (!$locale || $locale === 'en') {
            return $response
                ->setError()
                ->setMessage('Please select a valid target language (not English)');
        }

        // Set reasonable time limit for small batches
        set_time_limit(120); // 2 minutes max
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        try {
            $groupedTranslationsService = new GetGroupedTranslationsService();
            $allTranslations = $groupedTranslationsService->handle();
            
            // Filter by group if specified
            if ($group) {
                $allTranslations = $allTranslations->filter(function ($translation) use ($group) {
                    return $translation['group'] === $group;
                });
            }

            // Take only the first N items to process
            $translationsToProcess = $allTranslations->take($maxItems);
            
            $processed = 0;
            $translated = 0;
            $skipped = 0;
            $errors = 0;
            
            foreach ($translationsToProcess as $translation) {
                try {
                    $key = $translation['key'];
                    $englishValue = $translation['value'];
                    $groupName = $translation['group'];
                    
                    // Check if already translated
                    $existingTranslation = $this->getExistingTranslationValue($groupName, $key, $locale);
                    
                    if ($existingTranslation && $existingTranslation !== $englishValue) {
                        $skipped++;
                        $processed++;
                        continue;
                    }
                    
                    // Translate with timeout protection
                    $enhancedManager = app(\VigStudio\VigAutoTranslations\EnhancedAutoTranslateManager::class);
                    $translatedValue = $enhancedManager->translate('en', $locale, $englishValue);
                    
                    if ($translatedValue && $translatedValue !== $englishValue) {
                        // Save translation
                        $manager = app(\Botble\Translation\Manager::class);
                        $manager->updateTranslation(
                            $locale,
                            str_replace('/', DIRECTORY_SEPARATOR, $groupName),
                            [$key => $translatedValue]
                        );
                        
                        $translated++;
                    } else {
                        $errors++;
                    }
                    
                    $processed++;
                    
                } catch (\Exception $e) {
                    logger()->warning('Translation error in small batch', [
                        'key' => $key ?? 'unknown',
                        'group' => $groupName ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                    $errors++;
                    $processed++;
                }

                // Break if we're taking too long (safety measure)
                if ((time() % 30) === 0) { // Check every ~30 operations
                    if (memory_get_usage() > 100 * 1024 * 1024) { // 100MB limit
                        break;
                    }
                }
            }

            $message = sprintf(
                'Small batch completed! Processed: %d, Translated: %d, Skipped: %d, Errors: %d',
                $processed,
                $translated, 
                $skipped,
                $errors
            );

            return $response
                ->setData([
                    'processed' => $processed,
                    'translated' => $translated,
                    'skipped' => $skipped,
                    'errors' => $errors
                ])
                ->setMessage($message);

        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage('Small batch translation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get estimated processing time and recommend approach
     */
    public function getEstimation(Request $request, BaseHttpResponse $response)
    {
        $locale = $request->input('locale');
        $group = $request->input('group'); // Optional
        
        if (!$locale) {
            return $response->setError()->setMessage('Locale is required');
        }

        try {
            $groupedTranslationsService = new GetGroupedTranslationsService();
            $allTranslations = $groupedTranslationsService->handle();
            
            if ($group) {
                $allTranslations = $allTranslations->filter(function ($translation) use ($group) {
                    return $translation['group'] === $group;
                });
            }

            // Count items that actually need translation
            $needsTranslation = $allTranslations->filter(function ($translation) use ($locale) {
                $existingTranslation = $this->getExistingTranslationValue(
                    $translation['group'], 
                    $translation['key'], 
                    $locale
                );
                return !$existingTranslation || $existingTranslation === $translation['value'];
            });

            $totalItems = $allTranslations->count();
            $itemsNeedingTranslation = $needsTranslation->count();
            
            // Estimate processing time (rough calculation)
            $avgTimePerItem = 2; // seconds (conservative estimate including API calls)
            $estimatedTimeSeconds = $itemsNeedingTranslation * $avgTimePerItem;
            $estimatedTimeMinutes = round($estimatedTimeSeconds / 60, 1);
            
            // Recommend approach based on size
            $recommendedApproach = $itemsNeedingTranslation <= 20 
                ? 'small_batch' 
                : ($itemsNeedingTranslation <= 100 ? 'medium_chunks' : 'large_chunks');
            
            $recommendedChunkSize = match($recommendedApproach) {
                'small_batch' => $itemsNeedingTranslation,
                'medium_chunks' => 25,
                'large_chunks' => 15, // Smaller chunks for very large sets
                default => 25
            };

            return $response->setData([
                'total_items' => $totalItems,
                'items_needing_translation' => $itemsNeedingTranslation,
                'items_already_translated' => $totalItems - $itemsNeedingTranslation,
                'estimated_time_seconds' => $estimatedTimeSeconds,
                'estimated_time_minutes' => $estimatedTimeMinutes,
                'recommended_approach' => $recommendedApproach,
                'recommended_chunk_size' => $recommendedChunkSize,
                'warnings' => $this->getPerformanceWarnings($itemsNeedingTranslation, $estimatedTimeMinutes)
            ]);

        } catch (\Exception $e) {
            return $response
                ->setError() 
                ->setMessage('Failed to get estimation: ' . $e->getMessage());
        }
    }

    /**
     * Get performance warnings based on translation size
     */
    private function getPerformanceWarnings(int $itemCount, float $estimatedMinutes): array
    {
        $warnings = [];
        
        if ($itemCount > 100) {
            $warnings[] = 'Large translation set detected. Consider using chunked processing to avoid timeouts.';
        }
        
        if ($estimatedMinutes > 5) {
            $warnings[] = 'Estimated processing time exceeds 5 minutes. Use background processing recommended.';
        }
        
        if ($itemCount > 500) {
            $warnings[] = 'Very large translation set. Consider translating by groups or during off-peak hours.';
        }

        return $warnings;
    }

    /**
     * Helper method to get existing translation value from files  
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
}
