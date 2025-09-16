# 🏗️ Laravel Multi-Tenant SaaS Foundation

> **A complete, production-ready multi-tenant SaaS foundation with domain-based tenant isolation, advanced authentication, and multi-lingual support.**

## 🎯 **Perfect For Any SaaS Project**

This foundation can be used for **any multi-tenant SaaS application** including:
- **Restaurant Management Systems**
- **CRM Platforms** 
- **E-commerce Multi-Vendor**
- **Property Management**
- **Educational Platforms**
- **Healthcare Systems**
- **Any domain-specific SaaS**

---

## 🚀 **Quick Start**

### **1. Clone & Install**
```bash
git clone <repository-url>
cd project-directory
composer install
npm install
```

### **2. Environment Setup**
```bash
cp .env.example .env
# Configure your database settings in .env
```

### **3. Database Setup**
```bash
# Create landlord database
php artisan migrate --database=landlord

# Seed with sample data  
php artisan db:seed
```

### **4. Test the System**
```bash
# Start server
php artisan serve

# Create test tenant
php artisan tenant:create-test

# Access via subdomain: http://tenant-domain.localhost:8000
```

---

## 🏗️ **Architecture Overview**

### **Database Architecture**
```
┌─────────────────┐     ┌─────────────────┐
│   LANDLORD DB   │────▶│   TENANT DB 1   │
│                 │     │   (tenant1)     │
│ • tenants       │     │ • menu_items    │
│ • users         │     │ • inventory     │
│ • countries     │     │ • sales         │
│ • admin_users   │     │ • (app data)    │
└─────────────────┘     └─────────────────┘
         │               ┌─────────────────┐
         └──────────────▶│   TENANT DB 2   │
                         │   (tenant2)     │
                         │ • menu_items    │
                         │ • inventory     │  
                         │ • sales         │
                         └─────────────────┘
```

### **Authentication Flow**
```
User Login → Domain Detection → Tenant Resolution → Guard Selection → User Validation → Tenant Verification
```

---

## 🔧 **Core Features**

### **🏢 Multi-Tenancy**
- ✅ **Domain-based tenant resolution** (`tenant1.yourapp.com`)
- ✅ **Separate databases per tenant** with isolation
- ✅ **Dynamic database connections** 
- ✅ **Tenant-specific MySQL users** with minimal permissions
- ✅ **Secure credential management** (encrypted)

### **🔐 Authentication & Security**
- ✅ **Multi-guard authentication** (`web`, `tenant`)
- ✅ **Custom user providers** with landlord DB queries
- ✅ **Tenant ownership validation**
- ✅ **Cross-database user management**
- ✅ **Secure session handling**

### **🌍 Multi-Lingual (10 Languages)**
- ✅ **Arabic, English, Chinese, Hindi, Spanish, French, German, Portuguese, Russian, Japanese**
- ✅ **RTL language support** (Arabic, Hebrew)
- ✅ **User-specific language preferences**
- ✅ **Cultural number/date formatting**
- ✅ **Easy translation management**

### **📊 Performance & Monitoring**
- ✅ **Laravel Pulse integration**
- ✅ **Comprehensive logging** (security, performance, errors)
- ✅ **Optimized database indexes**
- ✅ **Connection pooling**
- ✅ **Cache strategy with tenant prefixing**

---

## 🛠️ **Key Components**

### **Models**
- `Tenant` - Central tenant management
- `User` - Multi-tenant user model  
- `Country` - Internationalization support
- `AdminUser` - System administration

### **Authentication Components**
- `TenantGuard` - Custom authentication guard
- `TenantUserProvider` - Custom user provider  
- `TenantMiddleware` - Domain-based tenant resolution
- `LoginRequest` - Dynamic guard selection

### **Artisan Commands**
- `tenant:create` - Create new tenant
- `tenant:approve` - Approve pending tenant
- `tenant:list` - List all tenants  
- `tenant:create-test` - Create test tenant
- `tenant:seed-test-data` - Seed realistic test data
- **+10 more tenant management commands**

### **Middleware**
- `TenantMiddleware` - Tenant resolution & database switching
- `LanguageMiddleware` - User language preference handling
- `LoggingMiddleware` - Request/response logging

---

## 📋 **Database Schema**

### **Landlord Database Tables**
```sql
tenants              # Tenant management
users               # All users (cross-tenant)  
countries           # Internationalization
admin_users         # System administrators
subscription_plans  # SaaS pricing plans
```

### **Tenant Database Tables**
```sql
menu_categories     # Application-specific data
menu_items         
inventory_items
sales
waste_records
# ... (customize for your domain)
```

---

## 🎛️ **Configuration**

### **Multi-Tenancy Config** (`config/multitenancy.php`)
```php
'tenant_database_connection_name' => 'tenant',
'landlord_database_connection_name' => 'landlord',
'tenant_model' => \App\Models\Tenant::class,
```

### **Authentication Config** (`config/auth.php`)
```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'tenant' => ['driver' => 'tenant-session', 'provider' => 'tenant-users'],
],
'providers' => [
    'tenant-users' => ['driver' => 'tenant-eloquent', 'model' => User::class],
],
```

---

## 🧪 **Testing & Development**

### **Create Test Environment**
```bash
# Create test tenant with sample data
php artisan tenant:create-test --approve

# Seed realistic data (300+ users across 15 tenants)
php artisan db:seed --class=TenantSeeder

# Test authentication
curl -H "Host: tenant1.localhost" http://localhost:8000/test/tenant-users
```

### **Sample Test Data**
- **15 tenants** with realistic restaurant names
- **300+ users** with multi-lingual preferences  
- **53 countries** with currency/tax data
- **4 subscription plans** (Starter, Professional, Enterprise, Custom)

---

## 🔄 **Customization for Your Domain**

### **1. Update Models & Migrations**
Replace restaurant-specific tables with your domain:
```bash
# Remove restaurant tables
database/migrations/*menu*
database/migrations/*inventory*
database/migrations/*waste*

# Add your domain tables
php artisan make:migration create_your_domain_tables
```

### **2. Update Seeders**
```bash
# Customize with your domain data
database/seeders/RestaurantTestDataSeeder.php → YourDomainSeeder.php
```

### **3. Frontend Customization**
```bash
# Update Vue components in resources/js/Pages/
# Customize dashboard and onboarding for your domain
```

### **4. Rename Application**
```bash
# Update in .env, config/app.php, package.json
APP_NAME="Your SaaS App"
```

---

## 🚀 **Production Deployment**

### **Database Setup**
```bash
# Create landlord database
CREATE DATABASE yourapp_landlord;

# Run landlord migrations
php artisan migrate --database=landlord

# Tenant databases are created automatically on registration
```

### **Environment Variables**
```bash
APP_ENV=production
DB_CONNECTION=landlord
LANDLORD_DB_DATABASE=yourapp_landlord
TENANT_DB_DATABASE=yourapp_tenant # Template name
```

### **Web Server Configuration**
```nginx
# Nginx - Wildcard subdomain support
server {
    server_name ~^(?<subdomain>.+)\.yourapp\.com$;
    # ... rest of config
}
```

---

## 📈 **Scalability Features**

### **Database Sharding Ready**
- Tenant databases can be distributed across multiple servers
- Connection configuration supports multiple hosts
- Ready for horizontal scaling

### **Performance Optimizations**
- Optimized database indexes
- Query result caching
- Connection pooling  
- Static asset optimization

### **Monitoring & Alerts**
- Laravel Pulse performance monitoring
- Comprehensive error logging
- Tenant-specific metrics
- Automated health checks

---

## 🔐 **Security Features**

### **Tenant Isolation**
- Database-level isolation
- Tenant-specific MySQL users
- No cross-tenant data access possible
- Encrypted credential storage

### **Authentication Security** 
- Multi-guard authentication
- Tenant ownership validation
- Secure session management
- CSRF protection

### **Data Protection**
- Input validation & sanitization
- SQL injection prevention
- XSS protection
- Secure file uploads

---

## 🎯 **Use Cases**

### **Restaurant Management (Current)**
- Multi-location restaurant chains
- Franchise management systems
- Food delivery platforms

### **Easily Adaptable To:**
- **CRM Systems** → Replace menu/inventory with leads/deals
- **E-commerce** → Replace with products/orders  
- **Education** → Replace with courses/students
- **Healthcare** → Replace with patients/appointments
- **Real Estate** → Replace with properties/clients

---

## 📞 **Support & Extensions**

### **Architecture Benefits**
- ✅ **Truly isolated tenants** (separate databases)
- ✅ **Infinite scalability** (add tenant servers)
- ✅ **Enterprise security** (tenant-specific credentials)  
- ✅ **Multi-lingual ready** (10 languages built-in)
- ✅ **Production tested** (comprehensive error handling)

### **Ready for Enterprise**
- Multi-server tenant distribution
- Advanced monitoring & alerting  
- Automated backup strategies
- Compliance reporting (GDPR, HIPAA ready)

---

**🎉 Perfect foundation for building your next multi-tenant SaaS application with enterprise-grade tenant isolation!**