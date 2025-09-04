<?php

namespace App\Services\Import\Mappers;

use App\Models\ImportMapping;
use Illuminate\Support\Str;

class MenuFieldMapper extends SmartFieldMapper
{
    protected array $menuSpecificPatterns;
    protected array $categoryMappings;
    protected array $allergenMappings;

    public function __construct()
    {
        parent::__construct();
        $this->initializeMenuPatterns();
        $this->initializeCategoryMappings();
        $this->initializeAllergenMappings();
    }

    public function getTargetFields(string $importType): array
    {
        return [
            'id' => ['type' => 'string', 'required' => false, 'description' => 'Unique menu item identifier'],
            'name' => ['type' => 'string', 'required' => true, 'description' => 'Menu item name'],
            'description' => ['type' => 'text', 'required' => false, 'description' => 'Menu item description'],
            'category' => ['type' => 'string', 'required' => false, 'description' => 'Menu category'],
            'subcategory' => ['type' => 'string', 'required' => false, 'description' => 'Menu subcategory'],
            'price' => ['type' => 'decimal', 'required' => true, 'description' => 'Menu item price'],
            'cost' => ['type' => 'decimal', 'required' => false, 'description' => 'Food cost per item'],
            'image_url' => ['type' => 'url', 'required' => false, 'description' => 'Menu item image URL'],
            'status' => ['type' => 'enum', 'required' => false, 'description' => 'Item status (active/inactive)'],
            'allergens' => ['type' => 'array', 'required' => false, 'description' => 'List of allergens'],
            'dietary_tags' => ['type' => 'array', 'required' => false, 'description' => 'Dietary restrictions/tags'],
            'nutritional_info' => ['type' => 'json', 'required' => false, 'description' => 'Nutritional information'],
            'preparation_time' => ['type' => 'integer', 'required' => false, 'description' => 'Prep time in minutes'],
            'cooking_time' => ['type' => 'integer', 'required' => false, 'description' => 'Cooking time in minutes'],
            'calories' => ['type' => 'integer', 'required' => false, 'description' => 'Calories per serving'],
            'protein' => ['type' => 'decimal', 'required' => false, 'description' => 'Protein content in grams'],
            'carbs' => ['type' => 'decimal', 'required' => false, 'description' => 'Carbohydrate content in grams'],
            'fat' => ['type' => 'decimal', 'required' => false, 'description' => 'Fat content in grams'],
            'fiber' => ['type' => 'decimal', 'required' => false, 'description' => 'Fiber content in grams'],
            'sugar' => ['type' => 'decimal', 'required' => false, 'description' => 'Sugar content in grams'],
            'sodium' => ['type' => 'decimal', 'required' => false, 'description' => 'Sodium content in mg'],
            'tags' => ['type' => 'array', 'required' => false, 'description' => 'Additional tags'],
            'portion_size' => ['type' => 'string', 'required' => false, 'description' => 'Portion size description'],
            'spice_level' => ['type' => 'enum', 'required' => false, 'description' => 'Spice level (mild/medium/hot)'],
            'temperature' => ['type' => 'enum', 'required' => false, 'description' => 'Serving temperature'],
            'availability' => ['type' => 'json', 'required' => false, 'description' => 'Availability schedule'],
            'seasonal' => ['type' => 'boolean', 'required' => false, 'description' => 'Is seasonal item'],
            'chef_special' => ['type' => 'boolean', 'required' => false, 'description' => 'Is chef special'],
            'popular' => ['type' => 'boolean', 'required' => false, 'description' => 'Is popular item'],
            'new_item' => ['type' => 'boolean', 'required' => false, 'description' => 'Is new menu item'],
            'pos_id' => ['type' => 'string', 'required' => false, 'description' => 'POS system ID'],
            'barcode' => ['type' => 'string', 'required' => false, 'description' => 'Item barcode'],
            'kitchen_notes' => ['type' => 'text', 'required' => false, 'description' => 'Kitchen preparation notes'],
            'modifier_groups' => ['type' => 'json', 'required' => false, 'description' => 'Available modifiers'],
            'size_variants' => ['type' => 'json', 'required' => false, 'description' => 'Size variants with prices']
        ];
    }

    protected function calculateFieldMatchScore(string $sourceHeader, string $targetField, array $fieldConfig, array $sampleData): int
    {
        $score = parent::calculateFieldMatchScore($sourceHeader, $targetField, $fieldConfig, $sampleData);
        
        // Apply menu-specific scoring boosts
        $sourceHeaderLower = strtolower($sourceHeader);
        
        // Boost scores for menu-specific patterns
        if (isset($this->menuSpecificPatterns[$targetField])) {
            foreach ($this->menuSpecificPatterns[$targetField] as $pattern) {
                if (Str::contains($sourceHeaderLower, strtolower($pattern))) {
                    $score += 15; // Additional boost for menu-specific patterns
                    break;
                }
            }
        }
        
        // Special handling for common menu field variations
        switch ($targetField) {
            case 'name':
                if (in_array($sourceHeaderLower, ['item', 'menu_item', 'dish', 'product'])) {
                    $score += 20;
                }
                break;
                
            case 'price':
                if (in_array($sourceHeaderLower, ['cost', 'amount', 'charge', 'rate'])) {
                    $score += 20;
                }
                break;
                
            case 'category':
                if (in_array($sourceHeaderLower, ['type', 'group', 'section', 'department'])) {
                    $score += 20;
                }
                break;
                
            case 'description':
                if (in_array($sourceHeaderLower, ['desc', 'details', 'info', 'summary'])) {
                    $score += 20;
                }
                break;
        }
        
        // Analyze sample data for menu-specific patterns
        $sampleValues = $this->getSampleValuesForHeader($sourceHeader, $sampleData);
        $menuPatternScore = $this->analyzeMenuPatterns($sampleValues, $targetField);
        $score += $menuPatternScore;
        
        return min(100, $score);
    }

    protected function suggestTransformations(string $sourceHeader, string $targetField, array $sampleData): array
    {
        $transformations = parent::suggestTransformations($sourceHeader, $targetField, $sampleData);
        $sampleValues = $this->getSampleValuesForHeader($sourceHeader, $sampleData);
        
        // Menu-specific transformations
        switch ($targetField) {
            case 'name':
                if ($this->needsNameCleaning($sampleValues)) {
                    $transformations[] = ['function' => 'clean_menu_name'];
                }
                break;
                
            case 'category':
                if ($this->needsCategoryMapping($sampleValues)) {
                    $transformations[] = [
                        'function' => 'map_category',
                        'mapping' => $this->categoryMappings
                    ];
                }
                break;
                
            case 'price':
                if ($this->containsCurrency($sampleValues)) {
                    $transformations[] = ['function' => 'clean_price'];
                }
                break;
                
            case 'allergens':
                if ($this->containsAllergens($sampleValues)) {
                    $transformations[] = [
                        'function' => 'parse_allergens',
                        'mapping' => $this->allergenMappings
                    ];
                }
                break;
                
            case 'dietary_tags':
                if ($this->containsDietaryInfo($sampleValues)) {
                    $transformations[] = ['function' => 'parse_dietary_tags'];
                }
                break;
                
            case 'spice_level':
                if ($this->containsSpiceLevel($sampleValues)) {
                    $transformations[] = ['function' => 'normalize_spice_level'];
                }
                break;
                
            case 'nutritional_info':
                if ($this->containsNutritionalData($sampleValues)) {
                    $transformations[] = ['function' => 'parse_nutritional_info'];
                }
                break;
        }
        
        return $transformations;
    }

    protected function initializeMenuPatterns(): void
    {
        $this->menuSpecificPatterns = [
            'name' => ['item', 'dish', 'product', 'menu', 'food'],
            'description' => ['desc', 'details', 'ingredients', 'preparation'],
            'category' => ['cat', 'group', 'type', 'section', 'department'],
            'subcategory' => ['subcat', 'subgroup', 'subtype'],
            'price' => ['cost', 'amount', 'charge', 'rate', 'value'],
            'allergens' => ['allergy', 'allergen', 'contains', 'warning'],
            'dietary_tags' => ['diet', 'dietary', 'restriction', 'special'],
            'spice_level' => ['spice', 'heat', 'hot', 'mild', 'spicy'],
            'calories' => ['cal', 'energy', 'kcal'],
            'preparation_time' => ['prep', 'preparation', 'ready'],
            'cooking_time' => ['cook', 'cooking', 'bake', 'fry'],
            'portion_size' => ['portion', 'serving', 'size'],
            'seasonal' => ['season', 'limited', 'special'],
            'popular' => ['bestseller', 'favorite', 'top'],
            'chef_special' => ['chef', 'signature', 'special'],
            'new_item' => ['new', 'latest', 'recent']
        ];
    }

    protected function initializeCategoryMappings(): void
    {
        $this->categoryMappings = [
            // Appetizers
            'appetizer' => ['appetizers', 'starters', 'apps', 'small plates', 'tapas', 'hors d\'oeuvres'],
            'starter' => ['appetizers', 'starters', 'apps'],
            
            // Main courses
            'main' => ['mains', 'entrees', 'main course', 'main dishes', 'dinner'],
            'entree' => ['entrees', 'mains', 'main course'],
            
            // Proteins
            'meat' => ['beef', 'pork', 'lamb', 'game'],
            'poultry' => ['chicken', 'duck', 'turkey', 'fowl'],
            'seafood' => ['fish', 'shellfish', 'salmon', 'tuna'],
            
            // Other categories
            'salad' => ['salads', 'greens', 'garden'],
            'soup' => ['soups', 'bisque', 'chowder', 'broth'],
            'pasta' => ['pastas', 'noodles', 'spaghetti'],
            'pizza' => ['pizzas', 'pies', 'flatbread'],
            'sandwich' => ['sandwiches', 'subs', 'wraps', 'burgers'],
            'dessert' => ['desserts', 'sweets', 'pastries', 'ice cream'],
            'beverage' => ['beverages', 'drinks', 'cocktails', 'wine', 'beer'],
            'side' => ['sides', 'side dishes', 'accompaniments']
        ];
    }

    protected function initializeAllergenMappings(): void
    {
        $this->allergenMappings = [
            'gluten' => ['wheat', 'barley', 'rye', 'flour'],
            'dairy' => ['milk', 'cheese', 'butter', 'cream', 'lactose'],
            'nuts' => ['peanuts', 'almonds', 'walnuts', 'cashews', 'pecans'],
            'shellfish' => ['shrimp', 'crab', 'lobster', 'oyster', 'clam'],
            'fish' => ['salmon', 'tuna', 'cod', 'seafood'],
            'eggs' => ['egg', 'mayonnaise'],
            'soy' => ['soybean', 'tofu', 'miso'],
            'sesame' => ['sesame seed', 'tahini'],
            'sulfites' => ['wine', 'dried fruit']
        ];
    }

    protected function analyzeMenuPatterns(array $sampleValues, string $targetField): int
    {
        if (empty($sampleValues)) {
            return 0;
        }
        
        $score = 0;
        
        switch ($targetField) {
            case 'price':
                $score += $this->analyzePricePatterns($sampleValues);
                break;
                
            case 'category':
                $score += $this->analyzeCategoryPatterns($sampleValues);
                break;
                
            case 'allergens':
                $score += $this->analyzeAllergenPatterns($sampleValues);
                break;
                
            case 'spice_level':
                $score += $this->analyzeSpiceLevelPatterns($sampleValues);
                break;
                
            case 'calories':
                $score += $this->analyzeCaloriePatterns($sampleValues);
                break;
        }
        
        return min(20, $score); // Cap at 20 points for pattern analysis
    }

    protected function analyzePricePatterns(array $values): int
    {
        $pricePatterns = 0;
        foreach ($values as $value) {
            if (is_numeric($value) && $value > 0 && $value < 1000) {
                $pricePatterns++;
            } elseif (preg_match('/^\$?\d+(\.\d{2})?$/', $value)) {
                $pricePatterns++;
            }
        }
        
        return (int) (($pricePatterns / count($values)) * 15);
    }

    protected function analyzeCategoryPatterns(array $values): int
    {
        $categoryMatches = 0;
        $knownCategories = array_merge(...array_values($this->categoryMappings));
        
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            if (in_array($lowerValue, $knownCategories)) {
                $categoryMatches++;
            }
        }
        
        return (int) (($categoryMatches / count($values)) * 10);
    }

    protected function analyzeAllergenPatterns(array $values): int
    {
        $allergenMatches = 0;
        $knownAllergens = array_merge(...array_values($this->allergenMappings));
        
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            foreach ($knownAllergens as $allergen) {
                if (Str::contains($lowerValue, $allergen)) {
                    $allergenMatches++;
                    break;
                }
            }
        }
        
        return (int) (($allergenMatches / count($values)) * 8);
    }

    protected function analyzeSpiceLevelPatterns(array $values): int
    {
        $spiceLevels = ['mild', 'medium', 'hot', 'spicy', 'no spice', 'extra hot'];
        $spiceMatches = 0;
        
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            if (in_array($lowerValue, $spiceLevels)) {
                $spiceMatches++;
            }
        }
        
        return (int) (($spiceMatches / count($values)) * 10);
    }

    protected function analyzeCaloriePatterns(array $values): int
    {
        $calorieMatches = 0;
        foreach ($values as $value) {
            if (is_numeric($value) && $value >= 50 && $value <= 2000) {
                $calorieMatches++;
            }
        }
        
        return (int) (($calorieMatches / count($values)) * 12);
    }

    // Helper methods for transformation detection
    protected function needsNameCleaning(array $values): bool
    {
        foreach ($values as $value) {
            if (preg_match('/[^\w\s\-\']/', $value) || strlen($value) > 100) {
                return true;
            }
        }
        return false;
    }

    protected function needsCategoryMapping(array $values): bool
    {
        $needsMapping = false;
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            foreach ($this->categoryMappings as $standard => $variations) {
                if (in_array($lowerValue, $variations) && $lowerValue !== $standard) {
                    $needsMapping = true;
                    break 2;
                }
            }
        }
        return $needsMapping;
    }

    protected function containsAllergens(array $values): bool
    {
        $knownAllergens = array_merge(...array_values($this->allergenMappings));
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            foreach ($knownAllergens as $allergen) {
                if (Str::contains($lowerValue, $allergen)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function containsDietaryInfo(array $values): bool
    {
        $dietaryKeywords = ['vegan', 'vegetarian', 'gluten-free', 'keto', 'paleo', 'dairy-free', 'sugar-free'];
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            foreach ($dietaryKeywords as $keyword) {
                if (Str::contains($lowerValue, $keyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function containsSpiceLevel(array $values): bool
    {
        $spiceKeywords = ['mild', 'medium', 'hot', 'spicy', 'heat', 'chili'];
        foreach ($values as $value) {
            $lowerValue = strtolower($value);
            foreach ($spiceKeywords as $keyword) {
                if (Str::contains($lowerValue, $keyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function containsNutritionalData(array $values): bool
    {
        foreach ($values as $value) {
            if (is_numeric($value) && $value > 0 && $value < 5000) {
                return true; // Likely nutritional values
            }
        }
        return false;
    }

    /**
     * Get menu-specific validation rules
     */
    public function getMenuValidationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'category' => ['string', 'max:100'],
            'description' => ['string', 'max:1000'],
            'calories' => ['integer', 'min:0', 'max:5000'],
            'preparation_time' => ['integer', 'min:0', 'max:480'], // 8 hours max
            'cooking_time' => ['integer', 'min:0', 'max:480'],
            'spice_level' => ['in:none,mild,medium,hot,extra_hot'],
            'status' => ['in:active,inactive,seasonal,discontinued']
        ];
    }

    /**
     * Get suggested default values for menu import
     */
    public function getMenuDefaultValues(): array
    {
        return [
            'status' => 'active',
            'spice_level' => 'none',
            'seasonal' => false,
            'chef_special' => false,
            'popular' => false,
            'new_item' => false,
            'preparation_time' => 15, // 15 minutes default
            'portion_size' => 'regular'
        ];
    }

    /**
     * Generate menu item slug from name
     */
    protected function generateSlug(string $name): string
    {
        return Str::slug($name);
    }

    /**
     * Clean menu item name
     */
    protected function cleanMenuName(string $name): string
    {
        // Remove special characters but keep apostrophes and hyphens
        $cleaned = preg_replace('/[^\w\s\-\']/', '', $name);
        
        // Trim and convert to title case
        return Str::title(trim($cleaned));
    }

    /**
     * Map category variations to standard categories
     */
    protected function mapCategory(string $category): string
    {
        $lowerCategory = strtolower(trim($category));
        
        foreach ($this->categoryMappings as $standard => $variations) {
            if (in_array($lowerCategory, $variations)) {
                return $standard;
            }
        }
        
        return $category; // Return original if no mapping found
    }

    /**
     * Parse allergens from text
     */
    protected function parseAllergens(string $allergenText): array
    {
        $allergens = [];
        $lowerText = strtolower($allergenText);
        
        foreach ($this->allergenMappings as $allergen => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($lowerText, $keyword)) {
                    $allergens[] = $allergen;
                    break;
                }
            }
        }
        
        return array_unique($allergens);
    }
}