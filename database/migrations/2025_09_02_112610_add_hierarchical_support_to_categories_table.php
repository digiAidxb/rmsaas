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
        Schema::table('categories', function (Blueprint $table) {
            // Add hierarchical support
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->string('level')->default('main')->after('parent_id'); // main, sub, etc.
            $table->string('path')->nullable()->after('level'); // For quick hierarchy lookups
            $table->string('code')->unique()->nullable()->after('name'); // For menu.xls code field
            
            // Add foreign key constraint
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Add indexes for performance
            $table->index(['parent_id', 'sort_order']);
            $table->index(['level', 'is_active']);
            $table->index('path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['parent_id']);
            
            // Drop indexes
            $table->dropIndex(['parent_id', 'sort_order']);
            $table->dropIndex(['level', 'is_active']);
            $table->dropIndex(['path']);
            
            // Drop columns
            $table->dropColumn(['parent_id', 'level', 'path', 'code']);
        });
    }
};
