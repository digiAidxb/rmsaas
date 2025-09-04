<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapping_name',
        'description',
        'mapping_uuid',
        'import_type',
        'pos_system',
        'pos_version',
        'file_format',
        'field_mappings',
        'default_values',
        'transformation_rules',
        'validation_rules',
        'sample_data',
        'field_analysis',
        'data_quality_metrics',
        'confidence_score',
        'has_headers',
        'header_row_number',
        'data_start_row',
        'delimiter',
        'quote_character',
        'escape_character',
        'encoding',
        'detected_columns',
        'suggested_mappings',
        'mapping_conflicts',
        'detection_accuracy',
        'category_mappings',
        'unit_conversions',
        'price_calculations',
        'inventory_linking',
        'is_template',
        'is_active',
        'usage_count',
        'last_used_at',
        'created_by_user_id',
        'version',
        'parent_mapping_id',
        'version_notes',
        'processing_hints',
        'expected_record_count',
        'batch_size',
        'parallel_processing',
        'error_handling_strategy',
        'max_errors_allowed',
        'error_recovery_rules',
        'average_success_rate',
        'total_records_processed',
        'total_successful_imports',
        'common_issues'
    ];

    protected $casts = [
        'field_mappings' => 'array',
        'default_values' => 'array',
        'transformation_rules' => 'array',
        'validation_rules' => 'array',
        'sample_data' => 'array',
        'field_analysis' => 'array',
        'data_quality_metrics' => 'array',
        'detected_columns' => 'array',
        'suggested_mappings' => 'array',
        'mapping_conflicts' => 'array',
        'category_mappings' => 'array',
        'unit_conversions' => 'array',
        'price_calculations' => 'array',
        'inventory_linking' => 'array',
        'version_notes' => 'array',
        'processing_hints' => 'array',
        'error_recovery_rules' => 'array',
        'common_issues' => 'array',
        'confidence_score' => 'integer',
        'header_row_number' => 'integer',
        'data_start_row' => 'integer',
        'usage_count' => 'integer',
        'expected_record_count' => 'integer',
        'batch_size' => 'integer',
        'max_errors_allowed' => 'integer',
        'total_records_processed' => 'integer',
        'total_successful_imports' => 'integer',
        'detection_accuracy' => 'decimal:2',
        'average_success_rate' => 'decimal:2',
        'has_headers' => 'boolean',
        'is_template' => 'boolean',
        'is_active' => 'boolean',
        'parallel_processing' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    protected $attributes = [
        'file_format' => 'csv',
        'confidence_score' => 0,
        'has_headers' => true,
        'header_row_number' => 1,
        'data_start_row' => 2,
        'delimiter' => ',',
        'quote_character' => '"',
        'escape_character' => '\\',
        'encoding' => 'UTF-8',
        'is_template' => false,
        'is_active' => true,
        'usage_count' => 0,
        'version' => '1.0',
        'batch_size' => 1000,
        'parallel_processing' => false,
        'error_handling_strategy' => 'skip',
        'max_errors_allowed' => 100,
        'total_records_processed' => 0,
        'total_successful_imports' => 0
    ];

    /**
     * Get the user who created this mapping
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the parent mapping (for versioning)
     */
    public function parentMapping(): BelongsTo
    {
        return $this->belongsTo(ImportMapping::class, 'parent_mapping_id');
    }

    /**
     * Get child mappings (versions based on this mapping)
     */
    public function childMappings(): HasMany
    {
        return $this->hasMany(ImportMapping::class, 'parent_mapping_id');
    }

    /**
     * Get import jobs that used this mapping
     */
    public function importJobs(): HasMany
    {
        return $this->hasMany(ImportJob::class, 'import_type', 'import_type')
                   ->where('pos_system', $this->pos_system);
    }

    /**
     * Check if mapping is a template
     */
    public function isTemplate(): bool
    {
        return $this->is_template;
    }

    /**
     * Check if mapping is active
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get success rate percentage
     */
    public function getSuccessRate(): float
    {
        if ($this->total_records_processed === 0) {
            return 0;
        }

        return round(($this->total_successful_imports / $this->total_records_processed) * 100, 2);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Update statistics after import
     */
    public function updateStatistics(int $recordsProcessed, int $successfulImports): void
    {
        $this->increment('total_records_processed', $recordsProcessed);
        $this->increment('total_successful_imports', $successfulImports);
        
        // Recalculate average success rate
        $this->update([
            'average_success_rate' => $this->getSuccessRate(),
            'last_used_at' => now()
        ]);
    }

    /**
     * Create a new version of this mapping
     */
    public function createVersion(array $updates = [], string $versionNote = ''): ImportMapping
    {
        $newVersion = $this->replicate();
        $newVersion->mapping_uuid = \Illuminate\Support\Str::uuid();
        $newVersion->parent_mapping_id = $this->id;
        $newVersion->version = $this->getNextVersion();
        $newVersion->version_notes = array_merge($this->version_notes ?? [], [
            'version' => $newVersion->version,
            'note' => $versionNote,
            'created_at' => now()->toDateTimeString()
        ]);
        $newVersion->usage_count = 0;
        $newVersion->total_records_processed = 0;
        $newVersion->total_successful_imports = 0;
        
        // Apply updates
        $newVersion->fill($updates);
        $newVersion->save();
        
        return $newVersion;
    }

    /**
     * Get next version number
     */
    protected function getNextVersion(): string
    {
        $versions = $this->childMappings()->pluck('version')->toArray();
        $versions[] = $this->version;
        
        $maxVersion = '1.0';
        foreach ($versions as $version) {
            if (version_compare($version, $maxVersion, '>')) {
                $maxVersion = $version;
            }
        }
        
        $parts = explode('.', $maxVersion);
        $parts[1] = (int)$parts[1] + 1;
        
        return implode('.', $parts);
    }

    /**
     * Get field mapping for specific source field
     */
    public function getFieldMapping(string $sourceField): ?array
    {
        return $this->field_mappings[$sourceField] ?? null;
    }

    /**
     * Set field mapping for source field
     */
    public function setFieldMapping(string $sourceField, array $mapping): void
    {
        $mappings = $this->field_mappings ?? [];
        $mappings[$sourceField] = $mapping;
        $this->update(['field_mappings' => $mappings]);
    }

    /**
     * Remove field mapping
     */
    public function removeFieldMapping(string $sourceField): void
    {
        $mappings = $this->field_mappings ?? [];
        unset($mappings[$sourceField]);
        $this->update(['field_mappings' => $mappings]);
    }

    /**
     * Validate field mappings
     */
    public function validateMappings(): array
    {
        $errors = [];
        $warnings = [];
        
        if (empty($this->field_mappings)) {
            $errors[] = 'No field mappings defined';
            return ['errors' => $errors, 'warnings' => $warnings, 'is_valid' => false];
        }
        
        // Add validation logic here
        foreach ($this->field_mappings as $source => $target) {
            if (empty($target['target_field'])) {
                $warnings[] = "No target field mapped for source field: {$source}";
            }
        }
        
        return [
            'errors' => $errors,
            'warnings' => $warnings,
            'is_valid' => empty($errors)
        ];
    }

    /**
     * Scope for active mappings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for template mappings
     */
    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    /**
     * Scope for specific import type
     */
    public function scopeForImportType($query, string $importType)
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

    /**
     * Scope for frequently used mappings
     */
    public function scopePopular($query, int $minUsage = 5)
    {
        return $query->where('usage_count', '>=', $minUsage)
                    ->orderBy('usage_count', 'desc');
    }

    /**
     * Scope for high confidence mappings
     */
    public function scopeHighConfidence($query, int $minConfidence = 80)
    {
        return $query->where('confidence_score', '>=', $minConfidence);
    }
}