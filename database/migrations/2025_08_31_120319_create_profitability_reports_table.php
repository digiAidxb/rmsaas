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
        Schema::create('profitability_reports', function (Blueprint $table) {
            $table->id();
            
            // Report Identification
            $table->string('report_uuid')->unique();
            $table->string('report_name');
            $table->text('description')->nullable();
            $table->enum('report_type', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly', 'custom', 'real_time']);
            
            // Time Period
            $table->date('report_date');
            $table->date('period_start_date');
            $table->date('period_end_date');
            $table->integer('days_in_period')->default(1);
            $table->timestamp('generated_at');
            
            // Revenue Analysis
            $table->decimal('total_revenue', 15, 4)->default(0.0000); // Gross sales
            $table->decimal('net_revenue', 15, 4)->default(0.0000); // After returns/discounts
            $table->decimal('average_daily_revenue', 12, 4)->default(0.0000);
            $table->decimal('revenue_per_customer', 10, 4)->default(0.0000);
            $table->integer('total_transactions')->default(0);
            $table->integer('total_customers_served')->default(0);
            
            // Cost of Goods Sold (COGS)
            $table->decimal('total_cogs', 15, 4)->default(0.0000); // Total food/beverage costs
            $table->decimal('food_costs', 12, 4)->default(0.0000); // Food costs only
            $table->decimal('beverage_costs', 12, 4)->default(0.0000); // Beverage costs only
            $table->decimal('cogs_percentage', 8, 4)->default(0.0000); // COGS as % of revenue
            $table->decimal('target_cogs_percentage', 8, 4)->default(30.0000); // Target COGS %
            
            // Labor Costs
            $table->decimal('total_labor_cost', 12, 4)->default(0.0000); // All labor costs
            $table->decimal('kitchen_labor_cost', 12, 4)->default(0.0000); // Kitchen staff costs
            $table->decimal('service_labor_cost', 12, 4)->default(0.0000); // Front of house costs
            $table->decimal('management_labor_cost', 12, 4)->default(0.0000); // Management costs
            $table->decimal('labor_percentage', 8, 4)->default(0.0000); // Labor as % of revenue
            $table->decimal('target_labor_percentage', 8, 4)->default(28.0000); // Target labor %
            
            // Operating Expenses
            $table->decimal('rent_expenses', 12, 4)->default(0.0000);
            $table->decimal('utilities_expenses', 12, 4)->default(0.0000);
            $table->decimal('marketing_expenses', 12, 4)->default(0.0000);
            $table->decimal('insurance_expenses', 12, 4)->default(0.0000);
            $table->decimal('maintenance_expenses', 12, 4)->default(0.0000);
            $table->decimal('other_operating_expenses', 12, 4)->default(0.0000);
            $table->decimal('total_operating_expenses', 12, 4)->default(0.0000);
            
            // Profitability Metrics
            $table->decimal('gross_profit', 15, 4)->default(0.0000); // Revenue - COGS
            $table->decimal('gross_profit_margin', 8, 4)->default(0.0000); // GP as % of revenue
            $table->decimal('ebitda', 15, 4)->default(0.0000); // Earnings before interest, taxes, depreciation, amortization
            $table->decimal('ebitda_margin', 8, 4)->default(0.0000); // EBITDA as % of revenue
            $table->decimal('net_profit', 15, 4)->default(0.0000); // Bottom line profit
            $table->decimal('net_profit_margin', 8, 4)->default(0.0000); // Net profit as % of revenue
            
            // Menu Item Profitability
            $table->json('top_profit_items')->nullable(); // [{item_id, profit, margin, quantity_sold}]
            $table->json('lowest_profit_items')->nullable(); // Least profitable items
            $table->json('category_profitability')->nullable(); // Profit by menu category
            $table->json('menu_mix_optimization')->nullable(); // Suggested menu changes
            $table->decimal('menu_engineering_score', 8, 4)->default(0.0000); // Overall menu performance
            
            // Customer & Transaction Analysis
            $table->decimal('average_check_size', 10, 4)->default(0.0000);
            $table->decimal('customer_acquisition_cost', 10, 4)->default(0.0000);
            $table->decimal('customer_lifetime_value', 12, 4)->default(0.0000);
            $table->integer('repeat_customer_percentage', 5)->default(0); // % of repeat customers
            $table->json('peak_hours_analysis')->nullable(); // Profitability by hour
            
            // Operational Efficiency
            $table->decimal('sales_per_square_foot', 10, 4)->default(0.0000); // If sq ft data available
            $table->decimal('sales_per_seat', 10, 4)->default(0.0000); // Revenue per seat
            $table->decimal('table_turnover_rate', 6, 2)->default(0.00); // Average table turns
            $table->decimal('kitchen_efficiency_score', 8, 4)->default(0.0000); // Kitchen performance
            $table->decimal('service_efficiency_score', 8, 4)->default(0.0000); // Service performance
            
            // Waste & Loss Impact
            $table->decimal('waste_cost_impact', 12, 4)->default(0.0000); // Cost of waste
            $table->decimal('theft_loss_impact', 12, 4)->default(0.0000); // Estimated theft losses
            $table->decimal('inventory_shrinkage_cost', 12, 4)->default(0.0000); // Total shrinkage
            $table->decimal('potential_profit_recovery', 12, 4)->default(0.0000); // Recoverable losses
            
            // Competitive & Market Analysis
            $table->json('market_positioning')->nullable(); // How we compare to competitors
            $table->decimal('market_share_estimate', 8, 4)->nullable(); // Estimated market share
            $table->json('pricing_optimization')->nullable(); // Price adjustment recommendations
            $table->json('competitive_advantages')->nullable(); // Unique selling points
            
            // Seasonal & Trend Analysis
            $table->json('seasonal_adjustments')->nullable(); // Seasonal factors affecting profit
            $table->json('trend_analysis')->nullable(); // 30/60/90 day trends
            $table->decimal('year_over_year_growth', 8, 4)->default(0.0000); // YoY profit growth %
            $table->json('forecast_projections')->nullable(); // Future profit projections
            
            // Key Performance Indicators
            $table->json('kpi_dashboard')->nullable(); // Key metrics for quick view
            $table->enum('overall_performance', ['excellent', 'good', 'average', 'below_average', 'poor'])->default('average');
            $table->json('performance_alerts')->nullable(); // Areas needing attention
            $table->json('improvement_opportunities')->nullable(); // Specific recommendations
            
            // Financial Health Indicators
            $table->decimal('cash_flow_impact', 12, 4)->default(0.0000); // Impact on cash flow
            $table->decimal('break_even_point', 12, 4)->default(0.0000); // Daily break-even sales
            $table->decimal('profit_per_hour', 10, 4)->default(0.0000); // Profit per operating hour
            $table->integer('days_to_break_even')->nullable(); // If launching new location
            
            // Strategic Insights
            $table->json('strategic_recommendations')->nullable(); // High-level business strategy
            $table->json('cost_reduction_opportunities')->nullable(); // Where to cut costs
            $table->json('revenue_enhancement_opportunities')->nullable(); // How to increase revenue
            $table->decimal('roi_on_improvements', 8, 4)->default(0.0000); // Expected ROI on changes
            
            // Report Quality & Confidence
            $table->enum('report_status', ['draft', 'preliminary', 'final', 'archived'])->default('preliminary');
            $table->decimal('data_completeness_score', 5, 2)->default(0.00); // % of complete data
            $table->decimal('confidence_level', 5, 2)->default(0.00); // Confidence in accuracy
            $table->json('data_sources')->nullable(); // What data sources were used
            $table->text('methodology_notes')->nullable(); // How report was generated
            
            // Approval & Review
            $table->foreignId('generated_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->boolean('requires_action')->default(false);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['report_date']);
            $table->index(['period_start_date', 'period_end_date']);
            $table->index(['report_type']);
            $table->index(['overall_performance']);
            $table->index(['net_profit_margin']);
            $table->index(['gross_profit_margin']);
            $table->index(['generated_by_user_id']);
            $table->index(['reviewed_by_user_id']);
            $table->index(['report_status']);
            $table->index(['requires_action']);
            $table->index(['generated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profitability_reports');
    }
};
