<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_uuid',
        'job_name',
        'description',
        'import_type',
        'source_type',
        'source_file_path',
        'processed_file_path',
        'original_filename',
        'file_size_bytes',
        'file_mime_type',
        'file_hash',
        'pos_system',
        'pos_location_id',
        'pos_metadata',
        'pos_import_id',
        'status',
        'progress_percentage',
        'total_records',
        'processed_records',
        'successful_imports',
        'failed_imports',
        'skipped_records',
        'started_at',
        'completed_at',
        'processing_time_seconds',
        'scheduled_for',
        'validation_errors',
        'data_quality_score',
        'import_summary',
        'field_mapping',
        'has_headers',
        'delimiter',
        'encoding',
        'created_by_user_id',
        'import_context',
        'is_test_import',
        'auto_approve',
        'import_results',
        'estimated_cost_impact',
        'estimated_time_savings',
        'duplicate_detection_results',
        'new_items_created',
        'existing_items_updated',
        'error_message',
        'error_details',
        'retry_count',
        'max_retries',
        'next_retry_at',
        'failure_reason',
        'can_rollback',
        'rollback_data',
        'rollback_deadline',
        'cleanup_completed'
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'progress_percentage' => 'integer',
        'total_records' => 'integer',
        'processed_records' => 'integer',
        'successful_imports' => 'integer',
        'failed_imports' => 'integer',
        'skipped_records' => 'integer',
        'processing_time_seconds' => 'integer',
        'retry_count' => 'integer',
        'max_retries' => 'integer',
        'new_items_created' => 'integer',
        'existing_items_updated' => 'integer',
        'has_headers' => 'boolean',
        'is_test_import' => 'boolean',
        'auto_approve' => 'boolean',
        'can_rollback' => 'boolean',
        'cleanup_completed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'next_retry_at' => 'datetime',
        'rollback_deadline' => 'datetime',
        'pos_metadata' => 'array',
        'validation_errors' => 'array',
        'data_quality_score' => 'array',
        'field_mapping' => 'array',
        'import_results' => 'array',
        'duplicate_detection_results' => 'array',
        'error_details' => 'array',
        'rollback_data' => 'array',
        'estimated_cost_impact' => 'decimal:4',
        'estimated_time_savings' => 'decimal:2'
    ];

    protected $attributes = [
        'status' => 'pending',
        'progress_percentage' => 0,
        'total_records' => 0,
        'processed_records' => 0,
        'successful_imports' => 0,
        'failed_imports' => 0,
        'skipped_records' => 0,
        'retry_count' => 0,
        'max_retries' => 3,
        'new_items_created' => 0,
        'existing_items_updated' => 0,
        'has_headers' => true,
        'delimiter' => ',',
        'encoding' => 'UTF-8',
        'import_context' => 'onboarding',
        'is_test_import' => false,
        'auto_approve' => false,
        'can_rollback' => true,
        'cleanup_completed' => false
    ];

    /**
     * Get the user who created this import job
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the import mappings used for this job
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(ImportMapping::class, 'import_type', 'import_type')
                   ->where('pos_system', $this->pos_system);
    }

    /**
     * Check if import is in progress
     */
    public function isInProgress(): bool
    {
        return in_array($this->status, ['pending', 'parsing', 'mapping', 'validating', 'importing']);
    }

    /**
     * Check if import completed successfully
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if import failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancelled']);
    }

    /**
     * Check if import can be retried
     */
    public function canRetry(): bool
    {
        return $this->isFailed() && $this->retry_count < $this->max_retries;
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRate(): float
    {
        if ($this->total_records === 0) {
            return 0;
        }

        return round(($this->successful_imports / $this->total_records) * 100, 2);
    }

    /**
     * Get processing speed (records per second)
     */
    public function getProcessingSpeed(): float
    {
        if ($this->processing_time_seconds === 0 || $this->processed_records === 0) {
            return 0;
        }

        return round($this->processed_records / $this->processing_time_seconds, 2);
    }

    /**
     * Get estimated completion time for in-progress imports
     */
    public function getEstimatedCompletionTime(): ?\DateTime
    {
        if (!$this->isInProgress() || $this->processed_records === 0) {
            return null;
        }

        $speed = $this->getProcessingSpeed();
        if ($speed === 0) {
            return null;
        }

        $remainingRecords = $this->total_records - $this->processed_records;
        $estimatedSecondsRemaining = $remainingRecords / $speed;

        return now()->addSeconds($estimatedSecondsRemaining);
    }

    /**
     * Update progress
     */
    public function updateProgress(int $processedRecords): void
    {
        $progress = $this->total_records > 0 
            ? round(($processedRecords / $this->total_records) * 100, 2)
            : 0;

        $this->update([
            'processed_records' => $processedRecords,
            'progress_percentage' => $progress
        ]);
    }

    /**
     * Scope for active imports
     */
    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['pending', 'parsing', 'mapping', 'validating', 'importing']);
    }

    /**
     * Scope for completed imports
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for failed imports
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled']);
    }

    /**
     * Scope for specific import type
     */
    public function scopeOfType($query, string $importType)
    {
        return $query->where('import_type', $importType);
    }

    /**
     * Scope for specific POS system
     */
    public function scopeForPosSystem($query, string $posSystem)
    {
        return $query->where('pos_system', $posSystem);
    }
}