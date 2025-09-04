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
        Schema::create('loss_analyses', function (Blueprint $table) {
            $table->id();
            
            // Analysis Identification
            $table->string('analysis_uuid')->unique();
            $table->string('analysis_name');
            $table->text('description')->nullable();
            
            // Time Period
            $table->date('analysis_date');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->enum('period_type', ['daily', 'weekly', 'monthly', 'quarterly', 'custom'])->default('daily');
            $table->integer('days_analyzed')->default(1);
            
            // Loss Categories & Classification
            $table->enum('primary_loss_type', ['waste', 'theft', 'spoilage', 'over_portioning', 'prep_error', 'supplier_shortage', 'system_error', 'unknown']);
            $table->json('loss_breakdown')->nullable(); // {waste: 45%, theft: 15%, spoilage: 30%, over_portioning: 10%}
            $table->decimal('total_loss_value', 12, 4)->default(0.0000); // Total $ loss
            $table->decimal('total_loss_percentage', 8, 4)->default(0.0000); // % of total inventory/sales
            
            // Item-Level Analysis
            $table->json('affected_items')->nullable(); // [{item_id, quantity_lost, value_lost, loss_type, confidence}]
            $table->json('high_loss_items')->nullable(); // Items with highest losses
            $table->json('item_loss_patterns')->nullable(); // Recurring patterns per item
            $table->integer('total_items_affected')->default(0);
            $table->integer('items_with_critical_loss')->default(0); // Loss > threshold
            
            // Financial Impact Analysis
            $table->decimal('direct_cost_impact', 12, 4)->default(0.0000); // Direct loss value
            $table->decimal('indirect_cost_impact', 12, 4)->default(0.0000); // Labor, opportunity cost
            $table->decimal('revenue_impact', 12, 4)->default(0.0000); // Lost revenue from out-of-stock
            $table->decimal('customer_impact_score', 8, 4)->default(0.0000); // Customer satisfaction impact
            $table->decimal('total_business_impact', 12, 4)->default(0.0000); // Combined impact
            
            // Root Cause Analysis
            $table->json('root_causes')->nullable(); // [{cause, confidence_score, impact_level, evidence}]
            $table->json('contributing_factors')->nullable(); // Secondary factors
            $table->text('analysis_methodology')->nullable(); // How analysis was performed
            $table->decimal('analysis_confidence_score', 5, 2)->default(0.00); // AI confidence (0-100)
            
            // Temporal & Environmental Factors
            $table->json('weather_correlation')->nullable(); // Weather impact on losses
            $table->json('seasonal_factors')->nullable(); // Seasonal patterns
            $table->json('day_of_week_patterns')->nullable(); // Weekly patterns
            $table->json('shift_correlation')->nullable(); // Which shifts have higher losses
            $table->json('staff_correlation')->nullable(); // Staff performance correlation
            
            // AI & Machine Learning Insights
            $table->json('ml_predictions')->nullable(); // Future loss predictions
            $table->json('anomaly_detection_results')->nullable(); // Unusual patterns detected
            $table->json('pattern_recognition')->nullable(); // Recurring patterns identified
            $table->json('correlation_analysis')->nullable(); // Correlations between variables
            $table->decimal('prediction_accuracy', 5, 2)->nullable(); // Historical prediction accuracy
            
            // Operational Context
            $table->json('operational_events')->nullable(); // Events during period (promotions, staff changes)
            $table->json('supplier_issues')->nullable(); // Supplier-related problems
            $table->json('equipment_issues')->nullable(); // Equipment failures affecting loss
            $table->json('inventory_turnover_impact')->nullable(); // How turnover affects loss
            
            // Loss Prevention Recommendations
            $table->json('immediate_actions')->nullable(); // Actions to take now
            $table->json('preventive_measures')->nullable(); // Long-term prevention strategies
            $table->json('process_improvements')->nullable(); // Operational improvements
            $table->json('training_recommendations')->nullable(); // Staff training needs
            $table->decimal('potential_savings', 12, 4)->default(0.0000); // Potential $ savings if addressed
            
            // Benchmarking & Comparisons
            $table->json('industry_benchmarks')->nullable(); // Compare to industry standards
            $table->json('historical_comparison')->nullable(); // Compare to previous periods
            $table->decimal('improvement_vs_last_period', 8, 4)->default(0.0000); // % improvement
            $table->json('peer_comparison')->nullable(); // Compare to similar restaurants
            $table->enum('performance_rating', ['excellent', 'good', 'average', 'below_average', 'poor'])->default('average');
            
            // Waste Stream Analysis
            $table->json('waste_stream_breakdown')->nullable(); // Pre-prep, post-prep, customer plate waste
            $table->json('recyclable_waste_recovery')->nullable(); // What could be recovered/donated
            $table->decimal('environmental_impact_score', 8, 4)->default(0.0000); // Environmental impact
            $table->json('sustainability_opportunities')->nullable(); // Sustainability improvements
            
            // Monitoring & Tracking
            $table->json('kpi_trends')->nullable(); // Key performance indicators over time
            $table->json('alert_triggers')->nullable(); // When to trigger alerts
            $table->boolean('requires_manager_attention')->default(false);
            $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            
            // Follow-up & Implementation
            $table->json('action_plan')->nullable(); // Detailed action plan
            $table->json('implementation_timeline')->nullable(); // When actions should be completed
            $table->json('success_metrics')->nullable(); // How to measure improvement
            $table->timestamp('next_review_date')->nullable();
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Analysis Quality & Validation
            $table->enum('analysis_status', ['draft', 'in_review', 'validated', 'implemented', 'closed'])->default('draft');
            $table->foreignId('validated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('validated_at')->nullable();
            $table->text('validation_notes')->nullable();
            $table->json('data_quality_score')->nullable(); // Quality of underlying data
            
            $table->timestamps();
            
            // Indexes
            $table->index(['analysis_date']);
            $table->index(['period_start_date', 'period_end_date']);
            $table->index(['primary_loss_type']);
            $table->index(['total_loss_value']);
            $table->index(['urgency_level']);
            $table->index(['analysis_status']);
            $table->index(['requires_manager_attention']);
            $table->index(['assigned_to_user_id']);
            $table->index(['validated_by_user_id']);
            $table->index(['performance_rating']);
            $table->index(['next_review_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loss_analyses');
    }
};
