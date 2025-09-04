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
        // Add POS integration fields to menu_items table
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('pos_item_id')->nullable()->after('id'); // External POS item ID
            $table->json('pos_metadata')->nullable()->after('pos_item_id'); // POS-specific data
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable()->after('pos_metadata');
            $table->string('pos_category_id')->nullable()->after('pos_system'); // POS category reference
            $table->json('pos_modifiers')->nullable()->after('pos_category_id'); // POS modifier options
            $table->decimal('pos_base_price', 8, 4)->nullable()->after('pos_modifiers'); // Price from POS
            $table->boolean('pos_sync_enabled')->default(true)->after('pos_base_price'); // Enable/disable sync
            $table->timestamp('last_pos_sync')->nullable()->after('pos_sync_enabled'); // Last sync timestamp
            
            // Indexes
            $table->index(['pos_item_id']);
            $table->index(['pos_system']);
            $table->index(['pos_category_id']);
            $table->index(['pos_sync_enabled']);
        });

        // Add POS integration fields to inventory_items table
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->string('pos_inventory_id')->nullable()->after('id'); // External POS inventory ID
            $table->json('pos_metadata')->nullable()->after('pos_inventory_id'); // POS-specific data
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable()->after('pos_metadata');
            $table->string('pos_supplier_id')->nullable()->after('pos_system'); // POS supplier reference
            $table->json('pos_unit_conversions')->nullable()->after('pos_supplier_id'); // Unit conversion rules
            $table->decimal('pos_current_stock', 10, 4)->nullable()->after('pos_unit_conversions'); // Current stock from POS
            $table->decimal('pos_cost_per_unit', 8, 4)->nullable()->after('pos_current_stock'); // Cost from POS
            $table->boolean('pos_sync_enabled')->default(true)->after('pos_cost_per_unit'); // Enable/disable sync
            $table->timestamp('last_pos_sync')->nullable()->after('pos_sync_enabled'); // Last sync timestamp
            $table->json('pos_tracking_settings')->nullable()->after('last_pos_sync'); // POS tracking preferences
            
            // Indexes
            $table->index(['pos_inventory_id']);
            $table->index(['pos_system']);
            $table->index(['pos_supplier_id']);
            $table->index(['pos_sync_enabled']);
        });

        // Add POS integration fields to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('pos_category_id')->nullable()->after('id'); // External POS category ID
            $table->json('pos_metadata')->nullable()->after('pos_category_id'); // POS-specific data
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable()->after('pos_metadata');
            $table->string('pos_parent_category_id')->nullable()->after('pos_system'); // POS parent category
            $table->json('pos_display_settings')->nullable()->after('pos_parent_category_id'); // POS display options
            $table->boolean('pos_visible')->default(true)->after('pos_display_settings'); // Visible in POS
            $table->integer('pos_sort_order')->nullable()->after('pos_visible'); // Display order in POS
            $table->boolean('pos_sync_enabled')->default(true)->after('pos_sort_order'); // Enable/disable sync
            $table->timestamp('last_pos_sync')->nullable()->after('pos_sync_enabled'); // Last sync timestamp
            
            // Indexes
            $table->index(['pos_category_id']);
            $table->index(['pos_system']);
            $table->index(['pos_parent_category_id']);
            $table->index(['pos_sync_enabled']);
        });

        // Add POS integration fields to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('pos_supplier_id')->nullable()->after('id'); // External POS supplier ID
            $table->json('pos_metadata')->nullable()->after('pos_supplier_id'); // POS-specific data
            $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable()->after('pos_metadata');
            $table->json('pos_integration_settings')->nullable()->after('pos_system'); // Integration preferences
            $table->string('pos_vendor_code')->nullable()->after('pos_integration_settings'); // Vendor code in POS
            $table->json('pos_payment_terms')->nullable()->after('pos_vendor_code'); // Payment terms from POS
            $table->boolean('pos_auto_ordering')->default(false)->after('pos_payment_terms'); // Auto-order integration
            $table->boolean('pos_sync_enabled')->default(true)->after('pos_auto_ordering'); // Enable/disable sync
            $table->timestamp('last_pos_sync')->nullable()->after('pos_sync_enabled'); // Last sync timestamp
            
            // Indexes
            $table->index(['pos_supplier_id']);
            $table->index(['pos_system']);
            $table->index(['pos_vendor_code']);
            $table->index(['pos_sync_enabled']);
        });

        // Add POS integration fields to orders table (if exists)
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('pos_order_id')->nullable()->after('id'); // External POS order ID
                $table->json('pos_metadata')->nullable()->after('pos_order_id'); // POS-specific data
                $table->enum('pos_system', ['square', 'toast', 'clover', 'lightspeed', 'touchbistro', 'resy', 'opentable', 'aloha', 'micros', 'generic'])->nullable()->after('pos_metadata');
                $table->string('pos_location_id')->nullable()->after('pos_system'); // POS location
                $table->string('pos_employee_id')->nullable()->after('pos_location_id'); // Employee who processed
                $table->json('pos_payment_details')->nullable()->after('pos_employee_id'); // Payment info from POS
                $table->timestamp('pos_order_time')->nullable()->after('pos_payment_details'); // Order time from POS
                $table->boolean('pos_sync_enabled')->default(true)->after('pos_order_time'); // Enable/disable sync
                $table->timestamp('last_pos_sync')->nullable()->after('pos_sync_enabled'); // Last sync timestamp
                
                // Indexes
                $table->index(['pos_order_id']);
                $table->index(['pos_system']);
                $table->index(['pos_location_id']);
                $table->index(['pos_employee_id']);
            });
        }

        // Add POS integration fields to tenants table for multi-location support
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('pos_systems')->nullable()->after('database'); // Enabled POS systems
            $table->json('pos_locations')->nullable()->after('pos_systems'); // POS location mappings
            $table->json('pos_api_credentials')->nullable()->after('pos_locations'); // Encrypted API credentials
            $table->json('pos_sync_settings')->nullable()->after('pos_api_credentials'); // Sync preferences
            $table->boolean('pos_integration_enabled')->default(false)->after('pos_sync_settings'); // Master POS toggle
            $table->timestamp('last_pos_sync')->nullable()->after('pos_integration_enabled'); // Last sync across all systems
            $table->enum('pos_sync_frequency', ['real_time', 'hourly', 'daily', 'manual'])->default('daily')->after('last_pos_sync'); // Sync frequency
            $table->json('pos_sync_status')->nullable()->after('pos_sync_frequency'); // Current sync status per system
            
            // Indexes
            $table->index(['pos_integration_enabled']);
            $table->index(['pos_sync_frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove POS fields from menu_items
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropIndex(['pos_item_id']);
            $table->dropIndex(['pos_system']);
            $table->dropIndex(['pos_category_id']);
            $table->dropIndex(['pos_sync_enabled']);
            $table->dropColumn([
                'pos_item_id', 'pos_metadata', 'pos_system', 'pos_category_id',
                'pos_modifiers', 'pos_base_price', 'pos_sync_enabled', 'last_pos_sync'
            ]);
        });

        // Remove POS fields from inventory_items
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->dropIndex(['pos_inventory_id']);
            $table->dropIndex(['pos_system']);
            $table->dropIndex(['pos_supplier_id']);
            $table->dropIndex(['pos_sync_enabled']);
            $table->dropColumn([
                'pos_inventory_id', 'pos_metadata', 'pos_system', 'pos_supplier_id',
                'pos_unit_conversions', 'pos_current_stock', 'pos_cost_per_unit',
                'pos_sync_enabled', 'last_pos_sync', 'pos_tracking_settings'
            ]);
        });

        // Remove POS fields from categories
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['pos_category_id']);
            $table->dropIndex(['pos_system']);
            $table->dropIndex(['pos_parent_category_id']);
            $table->dropIndex(['pos_sync_enabled']);
            $table->dropColumn([
                'pos_category_id', 'pos_metadata', 'pos_system', 'pos_parent_category_id',
                'pos_display_settings', 'pos_visible', 'pos_sort_order',
                'pos_sync_enabled', 'last_pos_sync'
            ]);
        });

        // Remove POS fields from suppliers
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['pos_supplier_id']);
            $table->dropIndex(['pos_system']);
            $table->dropIndex(['pos_vendor_code']);
            $table->dropIndex(['pos_sync_enabled']);
            $table->dropColumn([
                'pos_supplier_id', 'pos_metadata', 'pos_system', 'pos_integration_settings',
                'pos_vendor_code', 'pos_payment_terms', 'pos_auto_ordering',
                'pos_sync_enabled', 'last_pos_sync'
            ]);
        });

        // Remove POS fields from orders (if exists)
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex(['pos_order_id']);
                $table->dropIndex(['pos_system']);
                $table->dropIndex(['pos_location_id']);
                $table->dropIndex(['pos_employee_id']);
                $table->dropColumn([
                    'pos_order_id', 'pos_metadata', 'pos_system', 'pos_location_id',
                    'pos_employee_id', 'pos_payment_details', 'pos_order_time',
                    'pos_sync_enabled', 'last_pos_sync'
                ]);
            });
        }

        // Remove POS fields from tenants
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['pos_integration_enabled']);
            $table->dropIndex(['pos_sync_frequency']);
            $table->dropColumn([
                'pos_systems', 'pos_locations', 'pos_api_credentials', 'pos_sync_settings',
                'pos_integration_enabled', 'last_pos_sync', 'pos_sync_frequency', 'pos_sync_status'
            ]);
        });
    }
};
