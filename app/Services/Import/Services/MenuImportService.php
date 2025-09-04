<?php

namespace App\Services\Import\Services;

use App\Models\ImportJob;
use App\Models\MenuItem;
use App\Models\Category;
use App\Services\Import\ImportService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class MenuImportService extends ImportService
{
    /**
     * Perform the actual menu import
     */
    protected function performImport(array $data, ImportJob $importJob): array
    {
        $results = [
            'successful' => 0,
            'failed' => 0,
            'processed' => 0,
            'details' => [],
            'created_items' => [],
            'updated_items' => [],
            'skipped_items' => []
        ];

        $rollbackData = [];

        try {
            DB::beginTransaction();

            foreach ($data as $index => $row) {
                try {
                    $results['processed']++;
                    
                    // Process menu item data
                    $menuItemData = $this->processMenuItemRow($row, $importJob);
                    
                    if (empty($menuItemData['name'])) {
                        $results['skipped_items'][] = [
                            'row' => $index + 1,
                            'reason' => 'Missing required field: name',
                            'data' => $row
                        ];
                        continue;
                    }

                    // Check if item already exists
                    $existingItem = MenuItem::where('name', $menuItemData['name'])
                                            ->orWhere('pos_item_id', $menuItemData['pos_item_id'])
                                            ->first();

                    if ($existingItem) {
                        // Update existing item
                        $oldData = $existingItem->toArray();
                        $existingItem->update($menuItemData);
                        
                        $results['successful']++;
                        $results['updated_items'][] = [
                            'id' => $existingItem->id,
                            'name' => $existingItem->name,
                            'row' => $index + 1
                        ];
                        
                        // Store rollback data
                        $rollbackData[] = [
                            'action' => 'update',
                            'model' => 'MenuItem',
                            'id' => $existingItem->id,
                            'old_data' => $oldData
                        ];
                    } else {
                        // Create new item
                        $newItem = MenuItem::create($menuItemData);
                        
                        $results['successful']++;
                        $results['created_items'][] = [
                            'id' => $newItem->id,
                            'name' => $newItem->name,
                            'row' => $index + 1
                        ];
                        
                        // Store rollback data
                        $rollbackData[] = [
                            'action' => 'create',
                            'model' => 'MenuItem',
                            'id' => $newItem->id
                        ];
                    }

                } catch (Exception $e) {
                    $results['failed']++;
                    $results['details'][] = [
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                        'data' => $row
                    ];
                    
                    Log::warning('Failed to import menu item', [
                        'row' => $index + 1,
                        'error' => $e->getMessage(),
                        'data' => $row
                    ]);
                }
            }

            // Store rollback data for potential rollback
            $importJob->update([
                'rollback_data' => $rollbackData,
                'new_items_created' => count($results['created_items']),
                'existing_items_updated' => count($results['updated_items'])
            ]);

            DB::commit();
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }

    /**
     * Process a single menu item row
     */
    protected function processMenuItemRow(array $row, ImportJob $importJob): array
    {
        $mappings = $importJob->field_mapping;
        $data = [];

        // Map basic fields
        $data['name'] = $this->getMappedValue($row, $mappings, 'name');
        $data['description'] = $this->getMappedValue($row, $mappings, 'description');
        $data['price'] = $this->cleanPrice($this->getMappedValue($row, $mappings, 'price'));
        $data['cost'] = $this->cleanPrice($this->getMappedValue($row, $mappings, 'cost'));
        
        // Category handling
        $categoryName = $this->getMappedValue($row, $mappings, 'category');
        if ($categoryName) {
            $category = $this->findOrCreateCategory($categoryName);
            $data['category_id'] = $category->id;
        }

        // Nutritional information
        $data['calories'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'calories'));
        $data['protein'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'protein'));
        $data['carbs'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'carbs'));
        $data['fat'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'fat'));
        $data['fiber'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'fiber'));
        $data['sugar'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'sugar'));
        $data['sodium'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'sodium'));

        // Operational fields
        $data['preparation_time'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'preparation_time'));
        $data['cooking_time'] = $this->cleanNumeric($this->getMappedValue($row, $mappings, 'cooking_time'));
        $data['portion_size'] = $this->getMappedValue($row, $mappings, 'portion_size');
        $data['spice_level'] = $this->normalizeSpiceLevel($this->getMappedValue($row, $mappings, 'spice_level'));
        
        // Boolean fields
        $data['is_available'] = $this->cleanBoolean($this->getMappedValue($row, $mappings, 'is_available'), true);
        $data['is_featured'] = $this->cleanBoolean($this->getMappedValue($row, $mappings, 'is_featured'), false);
        $data['is_seasonal'] = $this->cleanBoolean($this->getMappedValue($row, $mappings, 'is_seasonal'), false);
        $data['is_popular'] = $this->cleanBoolean($this->getMappedValue($row, $mappings, 'is_popular'), false);

        // Allergens and dietary tags (JSON arrays)
        $data['allergens'] = $this->processArrayField($this->getMappedValue($row, $mappings, 'allergens'));
        $data['dietary_tags'] = $this->processArrayField($this->getMappedValue($row, $mappings, 'dietary_tags'));

        // POS integration fields
        $data['pos_item_id'] = $this->getMappedValue($row, $mappings, 'pos_item_id');
        $data['pos_system'] = $importJob->pos_system;
        $data['pos_metadata'] = $this->buildPosMetadata($row, $mappings, $importJob);
        
        // Additional fields
        $data['sku'] = $this->getMappedValue($row, $mappings, 'sku');
        $data['barcode'] = $this->getMappedValue($row, $mappings, 'barcode');
        $data['notes'] = $this->getMappedValue($row, $mappings, 'notes');

        // Set defaults and clean up
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        return $data;
    }

    /**
     * Get mapped value from row data
     */
    protected function getMappedValue(array $row, array $mappings, string $targetField): mixed
    {
        foreach ($mappings as $sourceField => $mapping) {
            if (is_array($mapping) && isset($mapping['target_field']) && $mapping['target_field'] === $targetField) {
                $value = $row[$sourceField] ?? null;
                
                // Apply transformations if defined
                if (isset($mapping['transform']) && $mapping['transform']) {
                    $value = $this->applyTransformation($value, $mapping['transform']);
                }
                
                return $value;
            }
        }
        
        return null;
    }

    /**
     * Find or create category
     */
    protected function findOrCreateCategory(string $categoryName): Category
    {
        $normalizedName = $this->normalizeCategory($categoryName);
        
        $category = Category::where('name', $normalizedName)
                           ->orWhere('name', 'LIKE', '%' . $normalizedName . '%')
                           ->first();

        if (!$category) {
            $category = Category::create([
                'name' => $normalizedName,
                'description' => "Auto-created category from import",
                'sort_order' => Category::max('sort_order') + 1,
                'is_active' => true
            ]);
        }

        return $category;
    }

    /**
     * Normalize category names to standard restaurant categories
     */
    protected function normalizeCategory(string $category): string
    {
        $category = strtolower(trim($category));
        
        $categoryMappings = [
            'appetizers' => ['apps', 'starters', 'appetizer', 'small plates', 'tapas'],
            'mains' => ['main', 'entrees', 'entree', 'main course', 'main dishes', 'dinner'],
            'desserts' => ['dessert', 'sweets', 'pastries', 'ice cream'],
            'beverages' => ['drinks', 'beverage', 'drink'],
            'seafood' => ['fish', 'shellfish', 'salmon', 'tuna'],
            'poultry' => ['chicken', 'duck', 'turkey'],
            'vegetarian' => ['veggie', 'veg'],
            'salads' => ['salad'],
            'soups' => ['soup'],
            'sandwiches' => ['sandwich', 'burger', 'wrap']
        ];

        foreach ($categoryMappings as $standard => $alternatives) {
            if (in_array($category, $alternatives) || $category === $standard) {
                return ucfirst($standard);
            }
        }

        return ucwords($category);
    }

    /**
     * Normalize spice level
     */
    protected function normalizeSpiceLevel(mixed $spiceLevel): ?string
    {
        if (empty($spiceLevel)) {
            return null;
        }
        
        $spiceLevel = strtolower(trim($spiceLevel));
        
        if (in_array($spiceLevel, ['mild', 'medium', 'hot', 'extra hot'])) {
            return $spiceLevel;
        }
        
        // Map numeric values
        if (is_numeric($spiceLevel)) {
            $level = (int) $spiceLevel;
            if ($level <= 1) return 'mild';
            if ($level <= 2) return 'medium';
            if ($level <= 3) return 'hot';
            return 'extra hot';
        }
        
        // Map common terms
        $spiceMappings = [
            'mild' => ['1', '0', 'no spice', 'not spicy'],
            'medium' => ['2', 'moderate', 'some heat'],
            'hot' => ['3', 'spicy', 'very spicy'],
            'extra hot' => ['4', '5', 'extremely hot', 'ghost pepper']
        ];
        
        foreach ($spiceMappings as $level => $terms) {
            if (in_array($spiceLevel, $terms)) {
                return $level;
            }
        }
        
        return null;
    }

    /**
     * Clean price values
     */
    protected function cleanPrice(mixed $price): ?float
    {
        if (empty($price)) {
            return null;
        }
        
        // Remove currency symbols and clean
        $price = preg_replace('/[^\d.,]/', '', $price);
        $price = str_replace(',', '.', $price);
        
        return is_numeric($price) ? (float) $price : null;
    }

    /**
     * Clean numeric values
     */
    protected function cleanNumeric(mixed $value): ?float
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }
        
        return (float) $value;
    }

    /**
     * Clean boolean values
     */
    protected function cleanBoolean(mixed $value, bool $default = false): bool
    {
        if (empty($value)) {
            return $default;
        }
        
        $value = strtolower(trim($value));
        
        return in_array($value, ['true', '1', 'yes', 'y', 'on', 'active', 'available']);
    }

    /**
     * Process array fields (comma-separated values)
     */
    protected function processArrayField(mixed $value): ?array
    {
        if (empty($value)) {
            return null;
        }
        
        if (is_string($value)) {
            $items = array_map('trim', explode(',', $value));
            return array_filter($items);
        }
        
        if (is_array($value)) {
            return array_filter($value);
        }
        
        return null;
    }

    /**
     * Build POS metadata
     */
    protected function buildPosMetadata(array $row, array $mappings, ImportJob $importJob): array
    {
        $metadata = [
            'import_job_id' => $importJob->id,
            'imported_at' => now()->toDateTimeString(),
            'pos_system' => $importJob->pos_system,
            'original_data' => $row
        ];

        // Add POS-specific metadata fields
        $posFields = ['pos_category_id', 'pos_modifiers', 'pos_base_price', 'pos_variant_id'];
        foreach ($posFields as $field) {
            $value = $this->getMappedValue($row, $mappings, $field);
            if ($value !== null) {
                $metadata[$field] = $value;
            }
        }

        return $metadata;
    }

    /**
     * Apply transformation to value
     */
    protected function applyTransformation(mixed $value, string $transformation): mixed
    {
        switch ($transformation) {
            case 'uppercase':
                return strtoupper($value);
            case 'lowercase':
                return strtolower($value);
            case 'title_case':
                return ucwords($value);
            case 'trim':
                return trim($value);
            case 'remove_currency':
                return preg_replace('/[^\d.,]/', '', $value);
            default:
                return $value;
        }
    }

    /**
     * Perform rollback of menu import
     */
    protected function performRollback(array $rollbackData, ImportJob $importJob): void
    {
        foreach ($rollbackData as $item) {
            try {
                if ($item['model'] === 'MenuItem') {
                    $menuItem = MenuItem::find($item['id']);
                    
                    if ($menuItem) {
                        if ($item['action'] === 'create') {
                            // Delete created item
                            $menuItem->delete();
                        } elseif ($item['action'] === 'update' && isset($item['old_data'])) {
                            // Restore old data
                            $menuItem->update($item['old_data']);
                        }
                    }
                }
            } catch (Exception $e) {
                Log::warning('Failed to rollback menu item', [
                    'item' => $item,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}