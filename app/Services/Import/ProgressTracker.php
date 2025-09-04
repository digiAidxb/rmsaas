<?php

namespace App\Services\Import;

use App\Models\ImportJob;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Collection;

class ProgressTracker
{
    protected string $cachePrefix = 'import_progress_';
    protected int $cacheTtl = 3600; // 1 hour
    protected bool $useRedis = true;

    public function __construct()
    {
        $this->useRedis = config('cache.default') === 'redis';
    }

    /**
     * Initialize progress tracking for an import job
     */
    public function initializeProgress(ImportJob $importJob, array $config): void
    {
        $progressData = [
            'import_id' => $importJob->id,
            'status' => 'initialized',
            'total_records' => $config['total_rows'] ?? 0,
            'processed_records' => 0,
            'successful_records' => 0,
            'failed_records' => 0,
            'progress_percentage' => 0,
            'batch_size' => $config['batch_size'] ?? 1000,
            'total_batches' => $config['total_batches'] ?? 0,
            'completed_batches' => 0,
            'failed_batches' => 0,
            'current_batch' => 0,
            'estimated_completion' => null,
            'processing_speed' => 0, // records per second
            'started_at' => now(),
            'updated_at' => now(),
            'errors' => [],
            'warnings' => [],
            'phase' => 'parsing', // parsing, mapping, validating, importing
            'memory_usage' => memory_get_usage(true),
            'peak_memory_usage' => memory_get_peak_usage(true)
        ];

        $this->storeProgress($importJob->id, $progressData);
    }

    /**
     * Update import progress
     */
    public function updateProgress(int $importJobId, array $updates): void
    {
        $currentProgress = $this->getProgress($importJobId);
        
        if (!$currentProgress) {
            return;
        }

        // Merge updates with current progress
        $updatedProgress = array_merge($currentProgress, $updates);
        
        // Recalculate derived metrics
        $updatedProgress = $this->calculateDerivedMetrics($updatedProgress);
        
        // Update timestamp
        $updatedProgress['updated_at'] = now();
        
        // Store updated progress
        $this->storeProgress($importJobId, $updatedProgress);
        
        // Publish real-time update if using Redis
        if ($this->useRedis) {
            $this->publishProgressUpdate($importJobId, $updatedProgress);
        }
    }

    /**
     * Update batch progress
     */
    public function updateBatchProgress(int $importJobId, int $batchNumber, array $batchResult): void
    {
        $currentProgress = $this->getProgress($importJobId);
        
        if (!$currentProgress) {
            return;
        }

        // Update batch-specific metrics
        $updates = [
            'current_batch' => $batchNumber,
            'processed_records' => ($currentProgress['processed_records'] ?? 0) + ($batchResult['processed'] ?? 0),
            'successful_records' => ($currentProgress['successful_records'] ?? 0) + ($batchResult['successful'] ?? 0),
            'failed_records' => ($currentProgress['failed_records'] ?? 0) + ($batchResult['failed'] ?? 0),
        ];

        if ($batchResult['success'] ?? false) {
            $updates['completed_batches'] = ($currentProgress['completed_batches'] ?? 0) + 1;
        } else {
            $updates['failed_batches'] = ($currentProgress['failed_batches'] ?? 0) + 1;
        }

        // Add errors and warnings
        if (!empty($batchResult['errors'])) {
            $updates['errors'] = array_merge($currentProgress['errors'] ?? [], $batchResult['errors']);
        }

        if (!empty($batchResult['warnings'])) {
            $updates['warnings'] = array_merge($currentProgress['warnings'] ?? [], $batchResult['warnings']);
        }

        $this->updateProgress($importJobId, $updates);
    }

    /**
     * Update import phase
     */
    public function updatePhase(int $importJobId, string $phase, array $additionalData = []): void
    {
        $updates = array_merge(['phase' => $phase], $additionalData);
        $this->updateProgress($importJobId, $updates);
    }

    /**
     * Mark import as completed
     */
    public function markCompleted(int $importJobId, array $finalStats = []): void
    {
        $updates = array_merge([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
            'phase' => 'completed'
        ], $finalStats);

        $this->updateProgress($importJobId, $updates);
    }

    /**
     * Mark import as failed
     */
    public function markFailed(int $importJobId, string $errorMessage, array $errorDetails = []): void
    {
        $currentProgress = $this->getProgress($importJobId);
        
        $updates = [
            'status' => 'failed',
            'phase' => 'failed',
            'error_message' => $errorMessage,
            'failed_at' => now(),
            'errors' => array_merge($currentProgress['errors'] ?? [], [[
                'message' => $errorMessage,
                'details' => $errorDetails,
                'timestamp' => now()
            ]])
        ];

        $this->updateProgress($importJobId, $updates);
    }

    /**
     * Get current progress for an import job
     */
    public function getProgress(int $importJobId): ?array
    {
        return Cache::get($this->cachePrefix . $importJobId);
    }

    /**
     * Get detailed progress with analytics
     */
    public function getDetailedProgress(int $importJobId): ?array
    {
        $progress = $this->getProgress($importJobId);
        
        if (!$progress) {
            return null;
        }

        // Add analytical insights
        $progress['analytics'] = $this->calculateAnalytics($progress);
        
        // Add recent error summary
        $progress['recent_errors'] = $this->getRecentErrors($progress['errors'] ?? [], 10);
        
        // Add performance metrics
        $progress['performance'] = $this->calculatePerformanceMetrics($progress);
        
        return $progress;
    }

    /**
     * Get progress for multiple import jobs
     */
    public function getMultipleProgress(array $importJobIds): array
    {
        $progressData = [];
        
        foreach ($importJobIds as $importJobId) {
            $progress = $this->getProgress($importJobId);
            if ($progress) {
                $progressData[$importJobId] = $progress;
            }
        }
        
        return $progressData;
    }

    /**
     * Get real-time progress stream (for WebSocket connections)
     */
    public function getProgressStream(int $importJobId): \Generator
    {
        if (!$this->useRedis) {
            throw new \Exception('Real-time progress streaming requires Redis');
        }

        $redis = Redis::connection();
        $channel = "import_progress_stream_{$importJobId}";
        
        // Subscribe to progress updates
        $redis->subscribe([$channel], function ($message, $channel) use ($importJobId) {
            yield json_decode($message, true);
        });
    }

    /**
     * Get progress history for an import job
     */
    public function getProgressHistory(int $importJobId, int $limit = 50): array
    {
        $historyKey = "import_history_{$importJobId}";
        
        if ($this->useRedis) {
            $history = Redis::lrange($historyKey, 0, $limit - 1);
            return array_map(fn($item) => json_decode($item, true), $history);
        }
        
        return Cache::get($historyKey, []);
    }

    /**
     * Clean up progress data for completed/failed imports
     */
    public function cleanup(int $importJobId, bool $keepHistory = true): void
    {
        if (!$keepHistory) {
            // Remove all progress data
            Cache::forget($this->cachePrefix . $importJobId);
            
            if ($this->useRedis) {
                Redis::del("import_history_{$importJobId}");
                Redis::del("import_batch_status_{$importJobId}");
            }
        } else {
            // Move current progress to history and remove real-time data
            $progress = $this->getProgress($importJobId);
            if ($progress) {
                $this->archiveProgress($importJobId, $progress);
            }
            
            Cache::forget($this->cachePrefix . $importJobId);
        }
    }

    /**
     * Get active import jobs with their progress
     */
    public function getActiveImports(): Collection
    {
        // This would require scanning cache keys or maintaining an active imports list
        // For now, return empty collection
        return collect([]);
    }

    /**
     * Calculate processing speed and ETA
     */
    public function calculateETA(int $importJobId): ?array
    {
        $progress = $this->getProgress($importJobId);
        
        if (!$progress || !isset($progress['started_at'], $progress['processed_records'], $progress['total_records'])) {
            return null;
        }

        $elapsedSeconds = now()->diffInSeconds($progress['started_at']);
        $processedRecords = $progress['processed_records'];
        $totalRecords = $progress['total_records'];
        $remainingRecords = $totalRecords - $processedRecords;

        if ($elapsedSeconds <= 0 || $processedRecords <= 0) {
            return null;
        }

        $recordsPerSecond = $processedRecords / $elapsedSeconds;
        $estimatedSecondsRemaining = $remainingRecords / $recordsPerSecond;

        return [
            'records_per_second' => round($recordsPerSecond, 2),
            'estimated_seconds_remaining' => (int) $estimatedSecondsRemaining,
            'estimated_completion_time' => now()->addSeconds($estimatedSecondsRemaining),
            'elapsed_time' => $elapsedSeconds,
            'progress_rate' => ($processedRecords / $totalRecords) * 100
        ];
    }

    /**
     * Store progress data
     */
    protected function storeProgress(int $importJobId, array $progressData): void
    {
        Cache::put($this->cachePrefix . $importJobId, $progressData, $this->cacheTtl);
        
        // Store in history if using Redis
        if ($this->useRedis) {
            $this->addToHistory($importJobId, $progressData);
        }
    }

    /**
     * Calculate derived metrics from progress data
     */
    protected function calculateDerivedMetrics(array $progress): array
    {
        $totalRecords = $progress['total_records'] ?? 0;
        $processedRecords = $progress['processed_records'] ?? 0;
        
        // Calculate progress percentage
        if ($totalRecords > 0) {
            $progress['progress_percentage'] = round(($processedRecords / $totalRecords) * 100, 2);
        }
        
        // Calculate processing speed
        if (isset($progress['started_at'])) {
            $elapsedSeconds = now()->diffInSeconds($progress['started_at']);
            if ($elapsedSeconds > 0 && $processedRecords > 0) {
                $progress['processing_speed'] = round($processedRecords / $elapsedSeconds, 2);
                
                // Calculate ETA
                $remainingRecords = $totalRecords - $processedRecords;
                if ($remainingRecords > 0) {
                    $estimatedSecondsRemaining = $remainingRecords / $progress['processing_speed'];
                    $progress['estimated_completion'] = now()->addSeconds($estimatedSecondsRemaining);
                }
            }
        }
        
        // Update memory usage
        $progress['memory_usage'] = memory_get_usage(true);
        $progress['peak_memory_usage'] = max(
            $progress['peak_memory_usage'] ?? 0,
            memory_get_peak_usage(true)
        );
        
        return $progress;
    }

    /**
     * Publish progress update for real-time streaming
     */
    protected function publishProgressUpdate(int $importJobId, array $progressData): void
    {
        $channel = "import_progress_stream_{$importJobId}";
        Redis::publish($channel, json_encode($progressData));
    }

    /**
     * Add progress snapshot to history
     */
    protected function addToHistory(int $importJobId, array $progressData): void
    {
        $historyKey = "import_history_{$importJobId}";
        $historyItem = json_encode([
            'timestamp' => now(),
            'progress_percentage' => $progressData['progress_percentage'] ?? 0,
            'processed_records' => $progressData['processed_records'] ?? 0,
            'phase' => $progressData['phase'] ?? 'unknown',
            'processing_speed' => $progressData['processing_speed'] ?? 0,
            'memory_usage' => $progressData['memory_usage'] ?? 0
        ]);
        
        // Keep last 100 history items
        Redis::lpush($historyKey, $historyItem);
        Redis::ltrim($historyKey, 0, 99);
        Redis::expire($historyKey, 86400); // 24 hours
    }

    /**
     * Archive progress data for completed imports
     */
    protected function archiveProgress(int $importJobId, array $progressData): void
    {
        $archiveKey = "import_archive_{$importJobId}";
        Cache::put($archiveKey, $progressData, 86400 * 7); // Keep for 7 days
    }

    /**
     * Calculate analytics from progress data
     */
    protected function calculateAnalytics(array $progress): array
    {
        $analytics = [
            'efficiency' => 0,
            'error_rate' => 0,
            'quality_score' => 0,
            'performance_rating' => 'unknown'
        ];

        $totalRecords = $progress['total_records'] ?? 0;
        $processedRecords = $progress['processed_records'] ?? 0;
        $successfulRecords = $progress['successful_records'] ?? 0;
        $failedRecords = $progress['failed_records'] ?? 0;

        if ($processedRecords > 0) {
            // Calculate efficiency (successful vs total processed)
            $analytics['efficiency'] = round(($successfulRecords / $processedRecords) * 100, 2);
            
            // Calculate error rate
            $analytics['error_rate'] = round(($failedRecords / $processedRecords) * 100, 2);
            
            // Calculate overall quality score
            $analytics['quality_score'] = max(0, $analytics['efficiency'] - ($analytics['error_rate'] * 2));
            
            // Determine performance rating
            if ($analytics['quality_score'] >= 95) {
                $analytics['performance_rating'] = 'excellent';
            } elseif ($analytics['quality_score'] >= 85) {
                $analytics['performance_rating'] = 'good';
            } elseif ($analytics['quality_score'] >= 70) {
                $analytics['performance_rating'] = 'average';
            } else {
                $analytics['performance_rating'] = 'poor';
            }
        }

        return $analytics;
    }

    /**
     * Get recent errors summary
     */
    protected function getRecentErrors(array $errors, int $limit): array
    {
        $recentErrors = array_slice($errors, -$limit);
        
        // Group by error type/message for summary
        $errorSummary = [];
        foreach ($recentErrors as $error) {
            $key = $error['message'] ?? 'Unknown error';
            if (!isset($errorSummary[$key])) {
                $errorSummary[$key] = [
                    'message' => $key,
                    'count' => 0,
                    'first_occurrence' => $error['timestamp'] ?? null,
                    'last_occurrence' => $error['timestamp'] ?? null
                ];
            }
            
            $errorSummary[$key]['count']++;
            $errorSummary[$key]['last_occurrence'] = $error['timestamp'] ?? null;
        }

        return array_values($errorSummary);
    }

    /**
     * Calculate performance metrics
     */
    protected function calculatePerformanceMetrics(array $progress): array
    {
        return [
            'throughput' => $progress['processing_speed'] ?? 0,
            'memory_efficiency' => $this->calculateMemoryEfficiency($progress),
            'batch_success_rate' => $this->calculateBatchSuccessRate($progress),
            'avg_processing_time' => $this->calculateAverageProcessingTime($progress)
        ];
    }

    /**
     * Calculate memory efficiency
     */
    protected function calculateMemoryEfficiency(array $progress): float
    {
        $memoryUsage = $progress['memory_usage'] ?? 0;
        $processedRecords = $progress['processed_records'] ?? 1;
        
        return $processedRecords > 0 ? round($memoryUsage / $processedRecords, 2) : 0;
    }

    /**
     * Calculate batch success rate
     */
    protected function calculateBatchSuccessRate(array $progress): float
    {
        $completedBatches = $progress['completed_batches'] ?? 0;
        $failedBatches = $progress['failed_batches'] ?? 0;
        $totalBatches = $completedBatches + $failedBatches;
        
        return $totalBatches > 0 ? round(($completedBatches / $totalBatches) * 100, 2) : 0;
    }

    /**
     * Calculate average processing time per record
     */
    protected function calculateAverageProcessingTime(array $progress): float
    {
        if (!isset($progress['started_at']) || !($progress['processed_records'] ?? 0)) {
            return 0;
        }

        $elapsedSeconds = now()->diffInSeconds($progress['started_at']);
        $processedRecords = $progress['processed_records'];
        
        return round($elapsedSeconds / $processedRecords, 4); // seconds per record
    }
}