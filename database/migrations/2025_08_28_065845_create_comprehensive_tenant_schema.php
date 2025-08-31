<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Tenant Database Schema
     * This creates all restaurant-specific tables for each tenant's database
     */
    public function up(): void
    {
        // Enhanced users table for tenant (restaurant staff)
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('staff'); // owner, manager, staff, accountant, auditor
            $table->json('permissions')->nullable();
            $table->string('employee_id')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->string('avatar_path')->nullable();
        });

        // Hierarchical categories for menu items
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            $table->index(['parent_id', 'sort_order']);
            $table->index(['is_active', 'sort_order']);
        });

        // Menu items with variants support
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('base_price', 10, 2);
            $table->string('sku')->nullable();
            $table->json('images')->nullable(); // Array of image paths
            $table->json('nutritional_info')->nullable(); // Calories, protein, etc.
            $table->json('allergens')->nullable(); // List of allergens
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('availability_schedule')->nullable(); // Days and times
            $table->integer('preparation_time')->nullable(); // Minutes
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['category_id', 'is_available']);
            $table->index(['is_featured', 'is_available']);
        });

        // Menu item variants (size, spice level, etc.)
        Schema::create('menu_item_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->string('type'); // size, spice_level, extras
            $table->string('name');
            $table->decimal('price_modifier', 8, 2)->default(0); // Add/subtract from base price
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['menu_item_id', 'type']);
        });

        // Unit types for inventory measurements
        Schema::create('unit_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Weight, Volume, Count
            $table->string('base_unit'); // kg, liter, piece
            $table->timestamps();
        });

        // Units for measuring inventory
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_type_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('symbol');
            $table->decimal('conversion_factor', 10, 4); // To base unit
            $table->boolean('is_base')->default(false);
            $table->timestamps();
            
            $table->index(['unit_type_id', 'is_base']);
        });

        // Suppliers management
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('tax_id')->nullable();
            $table->json('payment_terms')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'rating']);
        });

        // Inventory items master data
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('barcode')->nullable();
            $table->decimal('current_stock', 12, 3)->default(0);
            $table->decimal('minimum_stock', 12, 3)->default(0);
            $table->decimal('maximum_stock', 12, 3)->nullable();
            $table->decimal('reorder_point', 12, 3)->default(0);
            $table->decimal('last_purchase_price', 10, 2)->default(0);
            $table->decimal('average_cost', 10, 2)->default(0);
            $table->integer('shelf_life_days')->nullable();
            $table->enum('storage_type', ['dry', 'refrigerated', 'frozen'])->default('dry');
            $table->boolean('is_perishable')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['category_id', 'is_active']);
            $table->index(['supplier_id', 'is_active']);
            $table->index(['barcode']);
            $table->index(['current_stock', 'minimum_stock']);
        });

        // Recipes with ingredients
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')->constrained()->onDelete('cascade');
            $table->string('version', 10)->default('1.0');
            $table->text('instructions')->nullable();
            $table->decimal('yield_quantity', 8, 2); // How many portions
            $table->integer('preparation_time')->nullable(); // Minutes
            $table->integer('cooking_time')->nullable(); // Minutes
            $table->decimal('cost_per_serving', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['menu_item_id', 'is_active']);
            $table->unique(['menu_item_id', 'version']);
        });

        // Recipe ingredients
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 8, 3);
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->decimal('cost', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['recipe_id']);
            $table->index(['inventory_item_id']);
        });

        // Purchase orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->string('po_number')->unique();
            $table->enum('status', ['draft', 'sent', 'received', 'partial', 'cancelled'])->default('draft');
            $table->date('order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['supplier_id', 'status']);
            $table->index(['status', 'order_date']);
        });

        // Purchase order items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity_ordered', 12, 3);
            $table->decimal('quantity_received', 12, 3)->default(0);
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
            
            $table->index(['purchase_order_id']);
            $table->index(['inventory_item_id']);
        });

        // Stock movements (in/out transactions)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            $table->enum('type', ['in', 'out', 'adjustment', 'waste', 'transfer']);
            $table->enum('reason', ['purchase', 'sale', 'waste_spoilage', 'waste_theft', 'waste_preparation', 'adjustment', 'return']);
            $table->decimal('quantity', 12, 3);
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 12, 2)->nullable();
            $table->string('reference_type')->nullable(); // Purchase order, sale, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('batch_number')->nullable();
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'type', 'created_at']);
            $table->index(['type', 'reason', 'created_at']);
            $table->index(['reference_type', 'reference_id']);
        });

        // Waste tracking
        Schema::create('waste_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('recorded_by')->constrained('users')->onDelete('restrict');
            $table->enum('waste_type', ['spoilage', 'theft', 'preparation', 'customer_return', 'damage']);
            $table->decimal('quantity', 12, 3);
            $table->foreignId('unit_id')->constrained()->onDelete('restrict');
            $table->decimal('cost_impact', 10, 2);
            $table->text('reason')->nullable();
            $table->text('prevention_notes')->nullable();
            $table->json('photos')->nullable(); // Array of photo paths
            $table->date('discovery_date');
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'waste_type', 'discovery_date']);
            $table->index(['waste_type', 'discovery_date']);
        });

        // Sales data (imported from POS)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('menu_item_id')->nullable()->constrained()->onDelete('set null');
            $table->string('item_name'); // In case menu item is deleted
            $table->decimal('quantity', 8, 2);
            $table->decimal('unit_price', 8, 2);
            $table->decimal('total_price', 10, 2);
            $table->decimal('discount_amount', 8, 2)->default(0);
            $table->decimal('tax_amount', 8, 2)->default(0);
            $table->enum('status', ['completed', 'cancelled', 'refunded'])->default('completed');
            $table->enum('service_type', ['dine_in', 'takeaway', 'drive_through', 'delivery']);
            $table->json('modifiers')->nullable(); // Variants, extras
            $table->timestamp('sale_date');
            $table->string('pos_system')->nullable(); // Which POS system
            $table->json('pos_metadata')->nullable(); // Additional POS data
            $table->timestamps();
            
            $table->index(['menu_item_id', 'sale_date']);
            $table->index(['status', 'sale_date']);
            $table->index(['service_type', 'sale_date']);
            $table->index(['transaction_id']);
        });

        // Loss analysis results
        Schema::create('loss_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->onDelete('cascade');
            $table->date('analysis_date');
            $table->decimal('theoretical_usage', 12, 3); // Based on recipes and sales
            $table->decimal('actual_usage', 12, 3); // From stock movements
            $table->decimal('variance', 12, 3); // Difference
            $table->decimal('variance_percentage', 5, 2); // Percentage difference
            $table->decimal('cost_impact', 10, 2); // Financial impact
            $table->enum('variance_type', ['normal', 'high', 'critical']);
            $table->text('analysis_notes')->nullable();
            $table->json('recommendations')->nullable(); // AI recommendations
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'analysis_date']);
            $table->index(['variance_type', 'analysis_date']);
            $table->unique(['inventory_item_id', 'analysis_date']);
        });

        // Daily reconciliation
        Schema::create('daily_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->date('reconciliation_date');
            $table->foreignId('prepared_by')->constrained('users')->onDelete('restrict');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');
            $table->decimal('total_sales', 12, 2);
            $table->decimal('total_costs', 12, 2);
            $table->decimal('total_waste', 12, 2);
            $table->decimal('variance_amount', 12, 2);
            $table->decimal('variance_percentage', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            
            $table->index(['reconciliation_date', 'status']);
            $table->unique(['reconciliation_date']);
        });

        // Activity logs for audit trail
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['model_type', 'model_id']);
            $table->index(['action', 'created_at']);
        });

        // Data import history
        Schema::create('import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('import_type'); // menu, inventory, recipes, sales
            $table->enum('status', ['processing', 'completed', 'failed', 'partial']);
            $table->integer('total_records');
            $table->integer('processed_records');
            $table->integer('failed_records');
            $table->json('error_details')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'import_type', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_logs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('daily_reconciliations');
        Schema::dropIfExists('loss_analyses');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('waste_records');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('recipes');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('units');
        Schema::dropIfExists('unit_types');
        Schema::dropIfExists('menu_item_variants');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('categories');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'permissions', 'employee_id', 'phone', 'address',
                'hire_date', 'is_active', 'last_login_at', 'last_login_ip', 'avatar_path'
            ]);
        });
    }
};