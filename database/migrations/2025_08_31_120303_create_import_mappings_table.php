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
        Schema::create('import_mappings', function (Blueprint $table) {
            $table->id();
            
            // Mapping Identification
            $table->string('mapping_name');
            $table->text('description')->nullable();
            $table->string('mapping_uuid')->unique();
            
            // Source Context
            $table->enum('import_type', ['menu', 'inventory', 'recipes', 'sales', 'customers', 'employees', 'mixed']);
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic']);
            $table->string('pos_version')->nullable(); // POS system version for compatibility
            $table->string('file_format', 50)->default('csv'); // csv, xlsx, json, xml
            
            // Field Mappings
            $table->json('field_mappings'); // {source_field: target_field, transformations, validation_rules}
            $table->json('default_values')->nullable(); // Default values for missing fields
            $table->json('transformation_rules')->nullable(); // Data transformation logic
            $table->json('validation_rules')->nullable(); // Field-specific validation
            
            // Sample Data & Analysis
            $table->json('sample_data')->nullable(); // Sample rows for preview
            $table->json('field_analysis')->nullable(); // Data type detection, patterns
            $table->json('data_quality_metrics')->nullable(); // Completeness, consistency scores
            $table->integer('confidence_score')->default(0); // AI confidence in mapping (0-100)
            
            // Header & Format Detection
            $table->boolean('has_headers')->default(true);
            $table->integer('header_row_number')->default(1);
            $table->integer('data_start_row')->default(2);
            $table->string('delimiter', 10)->default(',');
            $table->string('quote_character', 10)->default('"');
            $table->string('escape_character', 10)->default('\\');
            $table->string('encoding', 50)->default('UTF-8');
            
            // Smart Detection Results
            $table->json('detected_columns')->nullable(); // Auto-detected column structure
            $table->json('suggested_mappings')->nullable(); // AI-suggested field mappings
            $table->json('mapping_conflicts')->nullable(); // Potential mapping issues
            $table->decimal('detection_accuracy', 5, 2)->nullable(); // Accuracy of auto-detection
            
            // Business Logic Mappings
            $table->json('category_mappings')->nullable(); // POS categories -> system categories
            $table->json('unit_conversions')->nullable(); // Unit standardization rules
            $table->json('price_calculations')->nullable(); // Pricing logic and tax handling
            $table->json('inventory_linking')->nullable(); // How to link to existing inventory
            
            // Template & Reusability
            $table->boolean('is_template')->default(false); // Reusable template
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0); // How many times used
            $table->timestamp('last_used_at')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Versioning & History
            $table->string('version', 20)->default('1.0');
            $table->foreignId('parent_mapping_id')->nullable()->constrained('import_mappings')->onDelete('set null');
            $table->json('version_notes')->nullable(); // Change log
            
            // Performance & Optimization
            $table->json('processing_hints')->nullable(); // Performance optimization hints
            $table->integer('expected_record_count')->nullable();
            $table->integer('batch_size')->default(1000); // Records per batch
            $table->boolean('parallel_processing')->default(false);
            
            // Error Handling
            $table->enum('error_handling_strategy', ['skip', 'fail', 'default', 'prompt'])->default('skip');
            $table->integer('max_errors_allowed')->default(100);
            $table->json('error_recovery_rules')->nullable(); // How to handle specific errors
            
            // Success Metrics
            $table->decimal('average_success_rate', 5, 2)->nullable(); // Historical success %
            $table->integer('total_records_processed')->default(0);
            $table->integer('total_successful_imports')->default(0);
            $table->json('common_issues')->nullable(); // Frequently encountered problems
            
            $table->timestamps();
            
            // Indexes
            $table->index(['import_type']);
            $table->index(['pos_system']);
            $table->index(['is_template']);
            $table->index(['is_active']);
            $table->index(['created_by_user_id']);
            $table->index(['last_used_at']);
            $table->index(['usage_count']);
            $table->index(['confidence_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_mappings');
    }
};
