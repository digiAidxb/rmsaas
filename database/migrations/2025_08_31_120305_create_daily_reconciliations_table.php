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
        Schema::create('daily_reconciliations', function (Blueprint $table) {
            $table->id();
            
            // Reconciliation Period
            $table->date('reconciliation_date');
            $table->string('reconciliation_uuid')->unique();
            $table->enum('shift_period', ['morning', 'afternoon', 'evening', 'overnight', 'full_day'])->default('full_day');
            $table->time('period_start_time')->nullable();
            $table->time('period_end_time')->nullable();
            
            // Status & Processing
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'requires_review'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('processing_time_seconds')->nullable();
            
            // POS System Data
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable();
            $table->string('pos_location_id')->nullable();
            $table->json('pos_sales_data')->nullable(); // Raw POS sales data
            $table->json('pos_inventory_movements')->nullable(); // POS inventory transactions
            $table->timestamp('pos_data_retrieved_at')->nullable();
            $table->boolean('pos_data_complete')->default(false);
            
            // Sales Reconciliation
            $table->decimal('pos_total_sales', 12, 4)->default(0.0000); // Total sales from POS
            $table->decimal('system_total_sales', 12, 4)->default(0.0000); // Expected sales from system
            $table->decimal('sales_variance', 12, 4)->default(0.0000); // Difference
            $table->decimal('sales_variance_percentage', 8, 4)->default(0.0000); // % variance
            $table->integer('pos_transaction_count')->default(0);
            $table->integer('system_transaction_count')->default(0);
            
            // Inventory Reconciliation
            $table->json('inventory_discrepancies')->nullable(); // [{item_id, expected_qty, actual_qty, variance}]
            $table->decimal('total_inventory_variance_value', 12, 4)->default(0.0000); // $ impact of variances
            $table->integer('items_with_discrepancies')->default(0);
            $table->integer('total_inventory_items_checked')->default(0);
            $table->decimal('inventory_accuracy_percentage', 8, 4)->default(100.0000);
            
            // Cost Analysis
            $table->decimal('theoretical_food_cost', 12, 4)->default(0.0000); // What it should have cost
            $table->decimal('actual_food_cost', 12, 4)->default(0.0000); // What it actually cost
            $table->decimal('food_cost_variance', 12, 4)->default(0.0000); // Variance amount
            $table->decimal('food_cost_percentage', 8, 4)->default(0.0000); // Food cost %
            $table->decimal('target_food_cost_percentage', 8, 4)->default(30.0000); // Target %
            
            // Waste & Loss Tracking
            $table->json('waste_items')->nullable(); // [{item_id, quantity, reason, value}]
            $table->decimal('total_waste_value', 12, 4)->default(0.0000);
            $table->json('loss_categories')->nullable(); // {spoilage, spillage, theft, over_portioning}
            $table->decimal('shrinkage_percentage', 8, 4)->default(0.0000); // Total shrinkage %
            
            // Labor Cost Integration
            $table->decimal('labor_cost_for_period', 12, 4)->default(0.0000);
            $table->decimal('labor_hours_scheduled', 8, 2)->default(0.00);
            $table->decimal('labor_hours_actual', 8, 2)->default(0.00);
            $table->decimal('labor_cost_percentage', 8, 4)->default(0.0000); // Labor as % of sales
            
            // Profitability Analysis
            $table->decimal('gross_profit', 12, 4)->default(0.0000); // Sales - COGS
            $table->decimal('gross_profit_margin', 8, 4)->default(0.0000); // GP %
            $table->decimal('net_profit_after_labor', 12, 4)->default(0.0000); // GP - Labor
            $table->decimal('net_margin_percentage', 8, 4)->default(0.0000); // Net margin %
            
            // Menu Item Performance
            $table->json('top_selling_items')->nullable(); // [{item_id, quantity_sold, revenue}]
            $table->json('lowest_margin_items')->nullable(); // Items with lowest margins
            $table->json('highest_waste_items')->nullable(); // Items with most waste
            $table->json('menu_mix_analysis')->nullable(); // Category performance
            
            // Anomaly Detection & Alerts
            $table->json('detected_anomalies')->nullable(); // AI-detected unusual patterns
            $table->json('variance_alerts')->nullable(); // Variances exceeding thresholds
            $table->enum('alert_level', ['none', 'low', 'medium', 'high', 'critical'])->default('none');
            $table->json('recommended_actions')->nullable(); // AI suggestions for improvement
            
            // Compliance & Audit
            $table->json('compliance_checks')->nullable(); // Regulatory compliance status
            $table->boolean('requires_manager_review')->default(false);
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('manager_notes')->nullable();
            
            // Comparison & Trends
            $table->json('previous_period_comparison')->nullable(); // Compare to yesterday/last week
            $table->json('trend_analysis')->nullable(); // 7-day, 30-day trends
            $table->decimal('improvement_from_yesterday', 8, 4)->nullable(); // % improvement
            $table->json('seasonal_adjustments')->nullable(); // Seasonal factors
            
            // External Integrations
            $table->json('accounting_system_sync')->nullable(); // QuickBooks, Xero sync status
            $table->json('supplier_integration_data')->nullable(); // Supplier delivery confirmations
            $table->boolean('exported_to_accounting')->default(false);
            $table->timestamp('accounting_export_at')->nullable();
            
            // Performance Metrics
            $table->decimal('customer_satisfaction_score', 5, 2)->nullable(); // If integrated with reviews
            $table->integer('covers_served')->default(0); // Number of customers served
            $table->decimal('average_check_size', 8, 4)->default(0.0000); // Average transaction value
            $table->decimal('table_turnover_rate', 5, 2)->default(0.00); // Tables turned per period
            
            $table->timestamps();
            
            // Indexes
            $table->index(['reconciliation_date']);
            $table->index(['status']);
            $table->index(['shift_period']);
            $table->index(['pos_system']);
            $table->index(['alert_level']);
            $table->index(['requires_manager_review']);
            $table->index(['reviewed_by_user_id']);
            $table->index(['food_cost_percentage']);
            $table->index(['gross_profit_margin']);
            $table->index(['total_waste_value']);
            
            // Unique constraint
            $table->unique(['reconciliation_date', 'shift_period', 'pos_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reconciliations');
    }
};
