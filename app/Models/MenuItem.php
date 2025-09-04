<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'price',
        'cost',
        'category_id',
        'subcategory_id',
        'discontinued',
        'modified_date',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'sugar',
        'sodium',
        'preparation_time',
        'cooking_time',
        'portion_size',
        'spice_level',
        'is_available',
        'is_featured',
        'is_seasonal',
        'is_popular',
        'allergens',
        'dietary_tags',
        'pos_item_id',
        'pos_system',
        'pos_metadata',
        'sku',
        'barcode',
        'notes'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'category_id' => 'integer',
        'subcategory_id' => 'integer',
        'discontinued' => 'boolean',
        'modified_date' => 'datetime',
        'calories' => 'integer',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fat' => 'decimal:2',
        'fiber' => 'decimal:2',
        'sugar' => 'decimal:2',
        'sodium' => 'decimal:2',
        'preparation_time' => 'integer',
        'cooking_time' => 'integer',
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'is_seasonal' => 'boolean',
        'is_popular' => 'boolean',
        'allergens' => 'array',
        'dietary_tags' => 'array',
        'pos_metadata' => 'array'
    ];

    protected $attributes = [
        'is_available' => true,
        'is_featured' => false,
        'is_seasonal' => false,
        'is_popular' => false,
        'discontinued' => false
    ];

    /**
     * Get the main category that owns the menu item
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * Get the subcategory that owns the menu item
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    /**
     * Get full category path (e.g. "NEPALI FOOD > VEG SNACKS")
     */
    public function getFullCategoryAttribute(): string
    {
        $parts = [];
        
        if ($this->category) {
            $parts[] = $this->category->name;
        }
        
        if ($this->subcategory) {
            $parts[] = $this->subcategory->name;
        }
        
        return implode(' > ', $parts);
    }

    /**
     * Scope for active items (not discontinued)
     */
    public function scopeActive($query)
    {
        return $query->where('is_available', true)
                    ->where('discontinued', false);
    }

    /**
     * Scope for discontinued items
     */
    public function scopeDiscontinued($query)
    {
        return $query->where('discontinued', true);
    }

    /**
     * Check if item is available for ordering
     */
    public function getIsOrderableAttribute(): bool
    {
        return $this->is_available && !$this->discontinued;
    }
}