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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->decimal('cost', 8, 2)->nullable();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            
            // Nutritional information
            $table->integer('calories')->nullable();
            $table->decimal('protein', 5, 2)->nullable();
            $table->decimal('carbs', 5, 2)->nullable();
            $table->decimal('fat', 5, 2)->nullable();
            $table->decimal('fiber', 5, 2)->nullable();
            $table->decimal('sugar', 5, 2)->nullable();
            $table->decimal('sodium', 8, 2)->nullable();
            
            // Operational fields
            $table->integer('preparation_time')->nullable();
            $table->integer('cooking_time')->nullable();
            $table->string('portion_size')->nullable();
            $table->string('spice_level')->nullable();
            
            // Boolean flags
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_seasonal')->default(false);
            $table->boolean('is_popular')->default(false);
            
            // JSON arrays
            $table->json('allergens')->nullable();
            $table->json('dietary_tags')->nullable();
            
            // POS integration
            $table->string('pos_item_id')->nullable();
            $table->string('pos_system')->nullable();
            $table->json('pos_metadata')->nullable();
            
            // Additional fields
            $table->string('sku')->nullable();
            $table->string('barcode')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['is_available', 'is_featured']);
            $table->index(['category_id', 'is_available']);
            $table->index('pos_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
