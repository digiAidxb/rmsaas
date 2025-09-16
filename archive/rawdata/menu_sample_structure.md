# Sample Restaurant Menu Structure Analysis

## Multi-Level Categories Structure

Based on the analysis requirements, here's the expected structure:

### Headers (Row 3):
- Code (SKU/Item Code)
- Food Name 
- Price
- Food Category (Level 1)
- Sub Category (Level 2)
- Modified Date
- Discontinue

### Sample Data Structure:
```
Code          | Food Name        | Price  | Food Category | Sub Category | Modified Date | Discontinue
AALO001       | AALO JIRA SADEKO | 400.00 | NEPALI FOOD   | VEG SNACKS   | 2025-01-01    | No
MOMO001       | CHICKEN MOMO     | 320.00 | NEPALI FOOD   | NON-VEG      | 2025-01-01    | No
PIZZA001      | MARGHERITA       | 850.00 | ITALIAN FOOD  | PIZZA        | 2025-01-01    | No
CURRY001      | DAL TADKA        | 280.00 | NEPALI FOOD   | CURRY        | 2025-01-01    | No
DRINKS001     | COCA COLA        | 120.00 | BEVERAGES     | COLD DRINKS  | 2025-01-01    | No
```

## Required System Enhancements:

### 1. Multi-Level Category Support:
- **Level 1**: Main Category (e.g., NEPALI FOOD, ITALIAN FOOD, BEVERAGES)
- **Level 2**: Sub Category (e.g., VEG SNACKS, NON-VEG, PIZZA, CURRY, COLD DRINKS)
- **Hierarchical Structure**: Categories should support parent-child relationships

### 2. Required Fields:
- **Code**: Unique identifier (SKU) - Essential for inventory tracking
- **Name**: Item name - Primary identification
- **Price**: Item cost - Financial data
- **Main Category**: Level 1 categorization  
- **Sub Category**: Level 2 categorization
- **Status**: Active/Inactive based on Discontinue field

### 3. Database Schema Requirements:
- Categories table with parent_id for hierarchy
- Menu items table with category_id and subcategory_id
- Support for unlimited category levels (recursive structure)

### 4. Import Workflow Sequence:
1. **Categories First**: Import and establish hierarchy
2. **Menu Items**: Link to established categories
3. **Inventory**: Base items for recipes
4. **Recipes**: Ingredient compositions  
5. **Sales Data**: Historical transactions (depends on menu items)

This structure supports complex restaurant hierarchies like:
- Main Category: NEPALI FOOD
  - Sub Category: VEG SNACKS
  - Sub Category: NON-VEG
  - Sub Category: CURRY
- Main Category: ITALIAN FOOD  
  - Sub Category: PIZZA
  - Sub Category: PASTA