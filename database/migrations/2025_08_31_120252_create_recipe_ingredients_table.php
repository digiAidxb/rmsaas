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
        Schema::create('recipe_ingredients', function (Blueprint $table) {
            $table->id();
            
            // Relationships
            $table->foreignId('recipe_id')->constrained('recipes')->onDelete('cascade');
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            
            // Ingredient Details
            $table->string('ingredient_name'); // Snapshot for historical data
            $table->decimal('quantity', 10, 4); // Amount needed for this recipe
            $table->string('unit', 50); // kg, grams, liters, pieces, etc.
            $table->decimal('unit_cost', 8, 4); // Cost per unit at time of recipe creation
            $table->decimal('total_cost', 10, 4); // Total cost for this ingredient in recipe
            
            // Recipe Instructions Specific
            $table->string('preparation_method')->nullable(); // "diced", "julienned", "minced"
            $table->text('notes')->nullable(); // Special instructions for this ingredient
            $table->boolean('is_optional')->default(false);
            $table->boolean('is_garnish')->default(false);
            
            // Nutritional Contribution (per unit)
            $table->decimal('calories_per_unit', 8, 3)->nullable();
            $table->decimal('protein_per_unit', 8, 3)->nullable();
            $table->decimal('carbs_per_unit', 8, 3)->nullable();
            $table->decimal('fat_per_unit', 8, 3)->nullable();
            
            // Ordering & Display
            $table->integer('sort_order')->default(0); // Order in recipe instructions
            $table->enum('ingredient_category', ['protein', 'vegetable', 'starch', 'dairy', 'spice', 'oil', 'sauce', 'garnish', 'other'])->default('other');
            
            // Scaling & Conversion
            $table->decimal('waste_factor', 5, 4)->default(0.0000); // Expected waste % for this ingredient
            $table->decimal('conversion_factor', 8, 4)->default(1.0000); // Unit conversion if needed
            $table->string('alternative_unit', 50)->nullable(); // Alternative measurement unit
            $table->decimal('alternative_quantity', 10, 4)->nullable(); // Quantity in alternative unit
            
            // Substitution Support
            $table->json('possible_substitutes')->nullable(); // [{"inventory_item_id": 123, "conversion_ratio": 1.2}]
            $table->text('substitution_notes')->nullable();
            
            // Quality & Freshness
            $table->integer('prep_minutes_before_use')->default(0); // How long before cooking to prep
            $table->boolean('requires_fresh')->default(false); // Must be prepped fresh
            $table->integer('max_prep_hours_ahead')->nullable(); // Max hours can be prepped in advance
            
            // POS Integration
            $table->string('pos_ingredient_id')->nullable(); // External POS ingredient ID
            $table->json('pos_modifiers')->nullable(); // POS-specific modifiers/options
            
            $table->timestamps();
            
            // Indexes
            $table->index(['recipe_id', 'sort_order']);
            $table->index(['inventory_item_id']);
            $table->index(['ingredient_category']);
            $table->index(['is_optional']);
            $table->index(['total_cost']);
            $table->index(['pos_ingredient_id']);
            
            // Unique constraint to prevent duplicate ingredients in same recipe
            $table->unique(['recipe_id', 'inventory_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipe_ingredients');
    }
};
