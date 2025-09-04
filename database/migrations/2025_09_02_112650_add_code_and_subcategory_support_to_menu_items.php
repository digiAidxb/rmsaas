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
        Schema::table('menu_items', function (Blueprint $table) {
            // Add code field for POS system integration (like menu.xls)
            $table->string('code')->unique()->nullable()->after('id'); // SKU/Item code
            
            // Add subcategory support
            $table->unsignedBigInteger('subcategory_id')->nullable()->after('category_id');
            
            // Add status fields for menu.xls support
            $table->boolean('discontinued')->default(false)->after('is_active');
            $table->datetime('modified_date')->nullable()->after('discontinued');
            
            // Add foreign key constraint for subcategory
            $table->foreign('subcategory_id')->references('id')->on('categories')->onDelete('set null');
            
            // Add indexes
            $table->index('code');
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['is_active', 'discontinued']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['subcategory_id']);
            
            // Drop indexes
            $table->dropIndex(['code']);
            $table->dropIndex(['category_id', 'subcategory_id']);
            $table->dropIndex(['is_active', 'discontinued']);
            
            // Drop columns
            $table->dropColumn(['code', 'subcategory_id', 'discontinued', 'modified_date']);
        });
    }
};
