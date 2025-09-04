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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            
            // Recipe Identification
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image_url')->nullable();
            
            // Recipe Classification
            $table->enum('type', ['menu_item', 'component', 'base_prep'])->default('menu_item');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->enum('category', ['appetizer', 'main', 'dessert', 'beverage', 'sauce', 'prep'])->nullable();
            
            // Yield & Portions
            $table->decimal('yield_quantity', 8, 3)->default(1.000); // How much this recipe makes
            $table->string('yield_unit', 50)->default('serving'); // serving, kg, liters, etc.
            $table->integer('servings')->default(1); // Number of servings this recipe makes
            
            // Timing
            $table->integer('prep_time_minutes')->nullable();
            $table->integer('cook_time_minutes')->nullable();
            $table->integer('total_time_minutes')->nullable();
            
            // Costing
            $table->decimal('ingredient_cost', 10, 4)->default(0.0000); // Total ingredient cost
            $table->decimal('labor_cost', 10, 4)->default(0.0000); // Labor cost per recipe
            $table->decimal('overhead_cost', 10, 4)->default(0.0000); // Overhead allocation
            $table->decimal('total_cost', 10, 4)->default(0.0000); // Total recipe cost
            $table->decimal('cost_per_serving', 10, 4)->default(0.0000); // Cost per individual serving
            
            // Nutritional Information (per serving)
            $table->decimal('calories_per_serving', 8, 2)->nullable();
            $table->decimal('protein_g', 8, 2)->nullable();
            $table->decimal('carbs_g', 8, 2)->nullable();
            $table->decimal('fat_g', 8, 2)->nullable();
            $table->decimal('fiber_g', 8, 2)->nullable();
            $table->decimal('sugar_g', 8, 2)->nullable();
            $table->decimal('sodium_mg', 8, 2)->nullable();
            
            // Allergens & Dietary
            $table->json('allergens')->nullable(); // ["gluten", "dairy", "nuts", "shellfish"]
            $table->json('dietary_tags')->nullable(); // ["vegetarian", "vegan", "gluten_free", "keto"]
            
            // Instructions & Notes
            $table->json('instructions')->nullable(); // [{"step": 1, "instruction": "...", "time": 5}]
            $table->text('chef_notes')->nullable();
            $table->text('storage_instructions')->nullable();
            $table->integer('shelf_life_hours')->nullable(); // How long recipe stays fresh
            
            // Recipe Management
            $table->string('version', 20)->default('1.0'); // Recipe version for tracking changes
            $table->timestamp('last_tested_at')->nullable();
            $table->enum('status', ['draft', 'testing', 'approved', 'archived'])->default('draft');
            $table->boolean('is_standardized')->default(false); // Approved for production
            
            // POS Integration
            $table->string('pos_recipe_id')->nullable(); // External POS system ID
            $table->json('pos_metadata')->nullable(); // Additional POS-specific data
            
            // Analytics
            $table->integer('times_prepared')->default(0); // Track usage
            $table->decimal('average_prep_time', 8, 2)->nullable(); // Actual vs estimated prep time
            $table->decimal('waste_percentage', 5, 2)->default(0.00); // Historical waste %
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type']);
            $table->index(['category']);
            $table->index(['status']);
            $table->index(['is_standardized']);
            $table->index(['total_cost']);
            $table->index(['cost_per_serving']);
            $table->index(['pos_recipe_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
