<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_jobs', function (Blueprint $table) {
            $table->id();
            
            // Job Identification
            $table->string('job_uuid')->unique();
            $table->string('job_name');
            $table->text('description')->nullable();
            
            // Import Source
            $table->enum('import_type', ['menu', 'inventory', 'recipes', 'sales', 'customers', 'employees', 'mixed']);
            $table->enum('source_type', ['file_upload', 'pos_api', 'manual_entry', 'scheduled_sync']);
            $table->string('source_file_path')->nullable(); // Original file location
            $table->string('processed_file_path')->nullable(); // Cleaned/processed file
            $table->string('original_filename')->nullable();
            $table->bigInteger('file_size_bytes')->nullable();
            $table->string('file_mime_type')->nullable();
            $table->string('file_hash')->nullable(); // For duplicate detection
            
            // POS System Integration
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable();
            $table->string('pos_location_id')->nullable(); // POS location/store ID
            $table->json('pos_metadata')->nullable(); // POS-specific configuration
            $table->string('pos_import_id')->nullable(); // External import reference
            
            // Processing Status
            $table->enum('status', ['pending', 'parsing', 'mapping', 'validating', 'importing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->integer('progress_percentage')->default(0);
            $table->integer('total_records')->default(0);
            $table->integer('processed_records')->default(0);
            $table->integer('successful_imports')->default(0);
            $table->integer('failed_imports')->default(0);
            $table->integer('skipped_records')->default(0);
            
            // Timing
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('processing_time_seconds')->nullable();
            $table->timestamp('scheduled_for')->nullable(); // For scheduled imports
            
            // Validation & Quality
            $table->json('validation_errors')->nullable(); // [{row: 5, field: 'price', error: 'Invalid format'}]
            $table->json('data_quality_score')->nullable(); // {completeness: 95, accuracy: 87, consistency: 92}
            $table->text('import_summary')->nullable(); // Human readable summary
            $table->json('field_mapping')->nullable(); // Column mapping configuration
            $table->boolean('has_headers')->default(true);
            $table->string('delimiter', 10)->default(','); // CSV delimiter
            $table->string('encoding', 50)->default('UTF-8');
            
            // User & Context
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('import_context')->default('onboarding'); // onboarding, routine, bulk_update
            $table->boolean('is_test_import')->default(false);
            $table->boolean('auto_approve')->default(false); // Skip manual review
            
            // Results & Analytics
            $table->json('import_results')->nullable(); // Detailed results by category
            $table->decimal('estimated_cost_impact', 12, 4)->nullable(); // Financial impact
            $table->decimal('estimated_time_savings', 8, 2)->nullable(); // Time saved vs manual entry
            $table->json('duplicate_detection_results')->nullable();
            $table->integer('new_items_created')->default(0);
            $table->integer('existing_items_updated')->default(0);
            
            // Error Handling & Recovery
            $table->text('error_message')->nullable();
            $table->json('error_details')->nullable(); // Full error context
            $table->integer('retry_count')->default(0);
            $table->integer('max_retries')->default(3);
            $table->timestamp('next_retry_at')->nullable();
            $table->enum('failure_reason', ['file_format', 'validation', 'mapping', 'system_error', 'timeout', 'cancelled'])->nullable();
            
            // Rollback & Cleanup
            $table->boolean('can_rollback')->default(true);
            $table->json('rollback_data')->nullable(); // Data needed for rollback
            $table->timestamp('rollback_deadline')->nullable(); // When rollback expires
            $table->boolean('cleanup_completed')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status']);
            $table->index(['import_type']);
            $table->index(['pos_system']);
            $table->index(['created_by_user_id']);
            $table->index(['import_context']);
            $table->index(['scheduled_for']);
            $table->index(['started_at']);
            $table->index(['completed_at']);
            $table->index(['file_hash']);
            $table->index(['progress_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_jobs');
    }
};
