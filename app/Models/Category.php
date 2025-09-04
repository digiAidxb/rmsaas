<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'parent_id',
        'level',
        'path',
        'sort_order',
        'is_active',
        'pos_category_id',
        'pos_system',
        'pos_metadata'
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'pos_metadata' => 'array'
    ];

    protected $attributes = [
        'is_active' => true,
        'sort_order' => 0,
        'level' => 'main'
    ];

    /**
     * Hierarchical relationships
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function subcategories(): HasMany
    {
        return $this->children();
    }

    /**
     * Get all descendants (recursive)
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->with('descendants');
    }

    /**
     * Get all ancestors (path to root)
     */
    public function ancestors()
    {
        $ancestors = collect();
        $category = $this->parent;
        
        while ($category) {
            $ancestors->prepend($category);
            $category = $category->parent;
        }
        
        return $ancestors;
    }

    /**
     * Check if this category is a child of another
     */
    public function isChildOf(Category $category): bool
    {
        return $this->parent_id === $category->id;
    }

    /**
     * Check if this category is a parent of another
     */
    public function isParentOf(Category $category): bool
    {
        return $category->parent_id === $this->id;
    }

    /**
     * Get the menu items for the category
     */
    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }

    /**
     * Get menu items where this is the subcategory
     */
    public function menuItemsAsSubcategory(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'subcategory_id');
    }

    /**
     * Get full path name (e.g. "NEPALI FOOD > VEG SNACKS")
     */
    public function getFullNameAttribute(): string
    {
        if (!$this->parent) {
            return $this->name;
        }
        
        return $this->parent->full_name . ' > ' . $this->name;
    }

    /**
     * Update path when category is saved
     */
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($category) {
            $category->updatePath();
        });
    }

    /**
     * Update the path field for quick hierarchy lookups
     */
    private function updatePath()
    {
        if ($this->parent_id) {
            $parent = static::find($this->parent_id);
            $this->path = ($parent->path ?? $parent->id) . '/' . $this->id;
            $this->level = $parent->level === 'main' ? 'sub' : 'sub_' . (substr_count($parent->path ?? '', '/') + 1);
        } else {
            $this->path = (string) $this->id;
            $this->level = 'main';
        }
    }

    /**
     * Scope for main categories (no parent)
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope for subcategories (has parent)
     */
    public function scopeSub($query)
    {
        return $query->whereNotNull('parent_id');
    }
}