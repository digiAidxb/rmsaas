# 🗄️ Database Structure Documentation - Multi-Tenant Restaurant Management SaaS

> **Complete database architecture, schemas, relationships, and data flow patterns**

## 🏗️ **Database Architecture Overview**

### **Multi-Database Tenant Isolation Pattern**
```
┌─────────────────────────────────────────────────────────────┐
│                    LANDLORD DATABASE                        │
│                 (rmsaas_landlord)                          │
├─────────────────────────────────────────────────────────────┤
│  • tenants              • countries                        │
│  • users               • admin_users                       │
│  • subscription_plans  • system_settings                   │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       │ Manages & References
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    TENANT DATABASES                         │
│            (rmsaas_tenant1, rmsaas_tenant2...)             │
├─────────────────────────────────────────────────────────────┤
│  Each Tenant Gets Isolated Database:                       │
│                                                             │
│  • menu_categories     • sales                             │
│  • menu_items         • waste_records                      │
│  • inventory_items    • purchase_orders                    │
│  • suppliers          • daily_reconciliations             │
└─────────────────────────────────────────────────────────────┘
```

---

## 🗃️ **LANDLORD DATABASE Schema**

### **Connection Configuration**
```php
'landlord' => [
    'driver' => 'mysql',
    'host' => env('DB_HOST', '127.0.0.1'),
    'database' => env('LANDLORD_DB_DATABASE', 'rmsaas_landlord'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
]
```

---

### **1. 🏢 `tenants` Table - Central Tenant Registry**

**Purpose**: Master registry of all SaaS tenants with their database credentials and status

```sql
CREATE TABLE tenants (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Restaurant/Business Name
    domain VARCHAR(255) NOT NULL UNIQUE,           -- Subdomain (tenant1.app.com)
    database VARCHAR(255) NOT NULL UNIQUE,         -- Database name (rmsaas_tenant1)
    
    -- Database Credentials (Encrypted)
    db_username VARCHAR(255) NULL,                 -- Generated: tenant_domain_hash
    db_password TEXT NULL,                         -- Encrypted password
    db_host VARCHAR(255) DEFAULT '127.0.0.1',
    db_port INT DEFAULT 3306,
    
    -- Business Information
    contact_person VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    business_address TEXT NULL,
    city VARCHAR(255) NULL,
    country_id BIGINT UNSIGNED NULL,
    business_type ENUM('Fine Dining','Casual Dining','Fast Food','Cafe','Bar & Grill','Pizza','Asian','Italian','Mexican','Other'),
    
    -- Status Management
    status ENUM('pending','approved','suspended','cancelled') DEFAULT 'pending',
    approved_at TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Trial & Billing
    trial_ends_at TIMESTAMP NULL,
    
    -- Onboarding Process
    onboarding_status JSON NULL,                   -- Onboarding step progress
    onboarding_completed_at TIMESTAMP NULL,
    skip_onboarding BOOLEAN DEFAULT FALSE,
    
    -- System Settings
    settings JSON NULL,                            -- Tenant-specific configurations
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_domain (domain),
    INDEX idx_status (status),
    INDEX idx_country (country_id),
    
    -- Foreign Keys
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE SET NULL
);
```

**Key Features**:
- **Unique domain per tenant** (subdomain-based routing)
- **Encrypted database credentials** for security isolation
- **Onboarding progress tracking** with JSON step status
- **Business metadata** for customization and reporting

---

### **2. 👥 `users` Table - Cross-Tenant User Management**

**Purpose**: All users across all tenants (centralized authentication with tenant isolation)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tenant_id BIGINT UNSIGNED NOT NULL,            -- Tenant ownership
    
    -- Authentication
    email VARCHAR(255) NOT NULL,                   -- Must be unique per tenant
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    
    -- User Information
    name VARCHAR(255) NOT NULL,
    role ENUM('owner','manager','staff','accountant','auditor') NOT NULL,
    
    -- Multi-lingual Support
    preferred_language VARCHAR(5) DEFAULT 'en',    -- ISO language code
    timezone VARCHAR(50) DEFAULT 'UTC',
    
    -- Extended User Data
    employee_id VARCHAR(50) NULL,
    phone VARCHAR(20) NULL,
    date_of_birth DATE NULL,
    hire_date DATE NULL,
    department VARCHAR(100) NULL,
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    
    -- Security & Activity Tracking
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    failed_login_attempts INT DEFAULT 0,
    locked_until TIMESTAMP NULL,
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_email_per_tenant (tenant_id, email),
    INDEX idx_tenant (tenant_id),
    INDEX idx_role (role),
    INDEX idx_email (email),
    INDEX idx_preferred_language (preferred_language),
    INDEX idx_last_login (last_login_at),
    
    -- Foreign Keys
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

**Key Features**:
- **Tenant isolation** via `tenant_id` foreign key
- **Unique email per tenant** (same email can exist across tenants)
- **Multi-lingual preferences** with timezone support
- **Security tracking** (login attempts, IP addresses)
- **Role-based access control** with 5 distinct roles

---

### **3. 🌍 `countries` Table - Internationalization Support**

**Purpose**: Country data for multi-lingual, multi-currency SaaS operations

```sql
CREATE TABLE countries (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Country name in English
    code VARCHAR(2) NOT NULL UNIQUE,               -- ISO 2-letter code
    iso3 VARCHAR(3) NULL,                         -- ISO 3-letter code
    phone_code VARCHAR(10) NULL,                  -- +1, +44, etc.
    currency_code VARCHAR(3) NULL,                -- USD, EUR, GBP
    currency_symbol VARCHAR(10) NULL,             -- $, €, £
    tax_rate DECIMAL(5,2) DEFAULT 0.00,          -- Default tax rate
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_code (code),
    INDEX idx_currency (currency_code),
    INDEX idx_active (is_active)
);
```

**Sample Data**: 53 countries with complete currency and tax information

---

### **4. 👨‍💼 `admin_users` Table - System Administration**

**Purpose**: System-level administrators (separate from tenant users)

```sql
CREATE TABLE admin_users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    
    -- Admin Roles
    role ENUM('super_admin','admin','support','billing','developer') DEFAULT 'admin',
    permissions JSON NULL,                         -- Specific permission overrides
    
    -- Security
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    two_factor_secret VARCHAR(255) NULL,
    two_factor_recovery_codes TEXT NULL,
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_email (email),
    INDEX idx_role (role),
    INDEX idx_active (is_active)
);
```

---

### **5. 💳 `subscription_plans` Table - SaaS Pricing Tiers**

**Purpose**: Define pricing plans and feature limits for tenants

```sql
CREATE TABLE subscription_plans (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- "Starter", "Professional", etc.
    slug VARCHAR(255) NOT NULL UNIQUE,             -- "starter", "professional"
    description TEXT NULL,
    
    -- Pricing
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    billing_period ENUM('monthly','yearly','lifetime') DEFAULT 'monthly',
    currency VARCHAR(3) DEFAULT 'USD',
    
    -- Feature Limits
    max_users INT DEFAULT 5,
    max_locations INT DEFAULT 1,
    max_menu_items INT DEFAULT 100,
    max_storage_gb INT DEFAULT 1,
    
    -- Features (JSON for flexibility)
    features JSON NULL,                            -- {"analytics": true, "api_access": false}
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_popular BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_price (price)
);
```

---

## 🏪 **TENANT DATABASE Schema**

### **Connection Configuration (Dynamic)**
```php
// Runtime configuration per tenant
'tenant' => [
    'driver' => 'mysql',
    'host' => $tenant->db_host,              // Usually 127.0.0.1
    'database' => $tenant->database,         // rmsaas_tenant1, rmsaas_tenant2
    'username' => $tenant->db_username,      // tenant_domain_hash123
    'password' => $tenant->db_password,      // Encrypted, unique per tenant
]
```

**Security**: Each tenant gets isolated MySQL user with permissions ONLY to their database

---

### **1. 📂 `menu_categories` Table - Hierarchical Menu Organization**

```sql
CREATE TABLE menu_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(500) NULL,
    
    -- Hierarchy Support
    parent_id BIGINT UNSIGNED NULL,               -- Self-referencing for sub-categories
    sort_order INT DEFAULT 0,
    
    -- Availability
    is_active BOOLEAN DEFAULT TRUE,
    available_from TIME NULL,                     -- Daily availability window
    available_to TIME NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order),
    UNIQUE KEY unique_slug (slug),
    
    -- Foreign Keys
    FOREIGN KEY (parent_id) REFERENCES menu_categories(id) ON DELETE SET NULL
);
```

---

### **2. 🍕 `menu_items` Table - Menu Items with Variants**

```sql
CREATE TABLE menu_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    
    -- Item Details
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(500) NULL,
    
    -- Pricing
    price DECIMAL(8,2) NOT NULL,
    cost DECIMAL(8,2) NULL,                       -- Cost price for margin calculation
    
    -- Nutritional Information
    calories INT NULL,
    protein_g DECIMAL(5,2) NULL,
    carbs_g DECIMAL(5,2) NULL,
    fat_g DECIMAL(5,2) NULL,
    allergens JSON NULL,                          -- ["gluten", "dairy", "nuts"]
    
    -- Availability
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    available_from TIME NULL,
    available_to TIME NULL,
    
    -- Inventory Tracking
    track_inventory BOOLEAN DEFAULT FALSE,
    current_stock INT DEFAULT 0,
    low_stock_threshold INT DEFAULT 5,
    
    -- SEO & Ordering
    sort_order INT DEFAULT 0,
    preparation_time_minutes INT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_category (category_id),
    INDEX idx_active (is_active),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price),
    UNIQUE KEY unique_slug (slug),
    
    -- Foreign Keys
    FOREIGN KEY (category_id) REFERENCES menu_categories(id) ON DELETE CASCADE
);
```

---

### **3. 📦 `inventory_items` Table - Ingredient & Supply Management**

```sql
CREATE TABLE inventory_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    -- Item Identification
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL UNIQUE,             -- Stock Keeping Unit
    description TEXT NULL,
    
    -- Categorization
    category ENUM('ingredients','beverages','supplies','packaging','cleaning') DEFAULT 'ingredients',
    subcategory VARCHAR(100) NULL,
    
    -- Unit & Measurement
    unit VARCHAR(50) NOT NULL,                    -- kg, liters, pieces, boxes
    unit_cost DECIMAL(8,4) NOT NULL,             -- Cost per unit
    
    -- Stock Management
    current_stock DECIMAL(10,3) DEFAULT 0,
    minimum_stock DECIMAL(10,3) DEFAULT 0,       -- Reorder threshold
    maximum_stock DECIMAL(10,3) NULL,            -- Storage capacity
    
    -- Supplier Information
    primary_supplier_id BIGINT UNSIGNED NULL,
    supplier_sku VARCHAR(100) NULL,
    
    -- Storage & Safety
    storage_location VARCHAR(100) NULL,
    storage_temperature ENUM('frozen','refrigerated','room_temperature') NULL,
    expiry_days INT NULL,                         -- Days until expiry after receipt
    
    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_sku (sku),
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_current_stock (current_stock),
    INDEX idx_supplier (primary_supplier_id)
);
```

---

### **4. 🏪 `suppliers` Table - Vendor Management**

```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    -- Company Information
    company_name VARCHAR(255) NOT NULL,
    contact_person VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(20) NULL,
    
    -- Address
    address_line_1 VARCHAR(255) NULL,
    address_line_2 VARCHAR(255) NULL,
    city VARCHAR(100) NULL,
    state VARCHAR(100) NULL,
    postal_code VARCHAR(20) NULL,
    country VARCHAR(100) NULL,
    
    -- Business Terms
    payment_terms ENUM('net_15','net_30','net_45','net_60','cod') DEFAULT 'net_30',
    delivery_days VARCHAR(50) NULL,              -- "Mon,Tue,Wed"
    minimum_order DECIMAL(8,2) DEFAULT 0,
    
    -- Rating & Status
    rating DECIMAL(3,2) DEFAULT 0.00,           -- 0.00 to 5.00
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_active (is_active),
    INDEX idx_rating (rating)
);
```

---

### **5. 💰 `sales` Table - Transaction Recording**

```sql
CREATE TABLE sales (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    -- Transaction Details
    transaction_id VARCHAR(100) NOT NULL UNIQUE,
    pos_transaction_id VARCHAR(100) NULL,        -- External POS reference
    
    -- Financial
    subtotal DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    
    -- Items Sold (JSON for flexibility)
    items_sold JSON NOT NULL,                     -- [{"item_id":1,"quantity":2,"price":15.99}]
    
    -- Payment Information
    payment_method ENUM('cash','card','mobile','voucher','comp') DEFAULT 'card',
    payment_status ENUM('pending','completed','refunded','failed') DEFAULT 'completed',
    
    -- Timestamps
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    UNIQUE KEY unique_transaction (transaction_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_total_amount (total_amount),
    INDEX idx_payment_method (payment_method)
);
```

---

### **6. 🗑️ `waste_records` Table - Food Waste Tracking**

```sql
CREATE TABLE waste_records (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    
    -- Item Reference
    item_type ENUM('menu_item','inventory_item') NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,             -- References menu_items or inventory_items
    item_name VARCHAR(255) NOT NULL,              -- Snapshot for historical data
    
    -- Waste Details
    quantity DECIMAL(10,3) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    cost_per_unit DECIMAL(8,4) NOT NULL,
    total_cost DECIMAL(10,2) GENERATED ALWAYS AS (quantity * cost_per_unit) STORED,
    
    -- Waste Categorization
    waste_reason ENUM('expiry','spoilage','overproduction','contamination','customer_return','preparation_error','other') NOT NULL,
    waste_stage ENUM('preparation','cooking','serving','storage') NOT NULL,
    
    -- Additional Information
    notes TEXT NULL,
    photo_url VARCHAR(500) NULL,
    recorded_by_user_id BIGINT UNSIGNED NULL,     -- References landlord.users.id
    
    -- Prevention Tracking
    preventable BOOLEAN DEFAULT TRUE,
    prevention_action TEXT NULL,
    
    -- Timestamps
    waste_date DATE NOT NULL,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes
    INDEX idx_item_type_id (item_type, item_id),
    INDEX idx_waste_date (waste_date),
    INDEX idx_waste_reason (waste_reason),
    INDEX idx_total_cost (total_cost),
    INDEX idx_recorded_by (recorded_by_user_id)
);
```

---

## 🔗 **Database Relationships & Data Flow**

### **Cross-Database Relationships**
```
LANDLORD DATABASE                    TENANT DATABASES
┌─────────────┐                     ┌─────────────────┐
│   tenants   │────manages────────▶│  All Tables     │
│     id      │                     │                 │
└─────────────┘                     └─────────────────┘
┌─────────────┐                     ┌─────────────────┐
│    users    │────references─────▶│  waste_records  │
│ tenant_id   │                     │ recorded_by_... │
└─────────────┘                     └─────────────────┘
```

### **Intra-Database Relationships (Tenant)**
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│menu_        │    │menu_items   │    │sales        │
│categories   │───▶│category_id  │───▶│items_sold   │
└─────────────┘    └─────────────┘    │   (JSON)    │
                                      └─────────────┘
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│suppliers    │    │inventory_   │    │waste_       │
│     id      │───▶│items        │───▶│records      │
└─────────────┘    │supplier_id  │    │item_id      │
                   └─────────────┘    └─────────────┘
```

---

## 🔐 **Database Security & Isolation**

### **MySQL User Permissions (Per Tenant)**
```sql
-- Each tenant gets a unique MySQL user
CREATE USER 'tenant_domain_abc123'@'localhost' IDENTIFIED BY 'secure_random_password';
CREATE USER 'tenant_domain_abc123'@'%' IDENTIFIED BY 'secure_random_password';

-- Database-specific permissions only
GRANT ALL PRIVILEGES ON `rmsaas_tenant1`.* TO 'tenant_domain_abc123'@'localhost';
GRANT ALL PRIVILEGES ON `rmsaas_tenant1`.* TO 'tenant_domain_abc123'@'%';

-- Global permissions for Laravel migrations
GRANT SELECT ON *.* TO 'tenant_domain_abc123'@'localhost';
GRANT SELECT ON *.* TO 'tenant_domain_abc123'@'%';

FLUSH PRIVILEGES;
```

### **Data Isolation Guarantees**
- ✅ **Physical Isolation**: Each tenant has separate database
- ✅ **Access Control**: Tenant-specific MySQL users with minimal permissions
- ✅ **Credential Security**: Encrypted database passwords in landlord DB
- ✅ **Application Logic**: All queries automatically scoped to tenant database

---

## 📊 **Sample Data Volume**

### **Landlord Database Sample Data**
| Table | Records | Description |
|-------|---------|-------------|
| `countries` | 53 | Complete country, currency, tax data |
| `subscription_plans` | 4 | Starter, Professional, Enterprise, Custom |
| `admin_users` | 5 | System administrators with different roles |
| `tenants` | 15 | Sample restaurant tenants with realistic names |
| `users` | 300+ | Multi-lingual users across all tenants |

### **Per-Tenant Database Sample Data**
| Table | Records | Description |
|-------|---------|-------------|
| `menu_categories` | 4-8 | Appetizers, Main Courses, Desserts, Beverages |
| `menu_items` | 20-50 | Complete menu with pricing and descriptions |
| `inventory_items` | 50-100 | Ingredients, supplies, beverages |
| `suppliers` | 5-10 | Vendor information with contact details |
| `sales` | 100-500 | Historical transaction data |
| `waste_records` | 50-200 | Food waste tracking with cost analysis |

---

## 🚀 **Migration Strategy**

### **Database Creation Flow**
```
1. Tenant Registration
   ├── Create record in landlord.tenants
   ├── Generate unique database name
   └── Create empty tenant database (with root privileges)

2. Tenant Approval  
   ├── Generate tenant-specific MySQL user
   ├── Grant database permissions
   ├── Run tenant database migrations
   └── Seed initial data

3. Runtime Operations
   ├── Domain-based tenant resolution
   ├── Dynamic database connection setup
   └── Tenant-scoped queries
```

### **Migration Files**
```
database/migrations/
├── 2025_08_27_225136_create_tenants_table.php
├── 2025_08_28_065752_create_comprehensive_landlord_schema.php
└── 2025_08_28_065845_create_comprehensive_tenant_schema.php
```

---

## 📈 **Scalability Considerations**

### **Horizontal Scaling Ready**
- **Database Sharding**: Distribute tenant databases across multiple servers
- **Connection Pooling**: Efficient connection management per tenant
- **Read Replicas**: Scale read operations with replica databases
- **Backup Strategy**: Per-tenant backup and restore capabilities

### **Performance Optimizations**
- **Optimized Indexes**: All foreign keys and query columns indexed
- **JSON Column Usage**: Flexible schema evolution with JSON fields
- **Cached Connections**: Connection pooling and persistent connections
- **Query Optimization**: N+1 prevention with proper eager loading

---

## 🛠️ **Development & Debugging**

### **Useful Database Queries**
```sql
-- Check tenant database status
SELECT t.name, t.domain, t.database, t.status, 
       COUNT(u.id) as user_count
FROM tenants t 
LEFT JOIN users u ON t.id = u.tenant_id 
GROUP BY t.id;

-- Find users by language preference
SELECT preferred_language, COUNT(*) as user_count 
FROM users 
GROUP BY preferred_language 
ORDER BY user_count DESC;

-- Check tenant database permissions
SHOW GRANTS FOR 'tenant_domain_abc123'@'localhost';
```

### **Connection Testing**
```php
// Test landlord connection
DB::connection('landlord')->select('SELECT 1');

// Test tenant connection (after tenant resolution)
$tenant->makeCurrent();
DB::connection('tenant')->select('SELECT 1');
```

---

**📊 This comprehensive database structure supports true multi-tenant SaaS architecture with enterprise-grade security, scalability, and data isolation!**