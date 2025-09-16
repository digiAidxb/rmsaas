# 💾 Database Migration Guide: Windows MySQL → macOS MySQL

**Complete Database Transfer for RMSaaS Multi-Tenant System**  
**Source**: Windows 10 MySQL  
**Target**: MacBook Air M2 MySQL  
**Database Architecture**: Landlord/Tenant Multi-Database Setup

---

## 📋 **PRE-MIGRATION DATABASE ASSESSMENT**

### **Current Database Structure**
```sql
-- RMSaaS Multi-Tenant Database Architecture
├── rmsaas_landlord     # Central tenant registry & users
├── rmsaas_tenant       # Default tenant database schema
├── tenant_specific_*   # Individual tenant databases (if any)
└── testing databases   # Development/testing databases
```

### **Critical Data to Migrate**
- **Landlord Database**: Tenant registry, users, countries, subscription plans
- **Tenant Database**: Menu items, categories, inventory, import jobs, recipes
- **User Data**: Authentication data, roles, permissions
- **Import History**: All import jobs and mappings
- **Configuration Data**: Application settings, tenant configurations

---

## 🔍 **STEP 1: Windows Database Export**

### **1.1 Inventory Current Databases**
```bash
# On Windows 10 - Check existing databases
mysql -u root -p -e "SHOW DATABASES;"

# List RMSaaS specific databases
mysql -u root -p -e "SHOW DATABASES LIKE '%rmsaas%';"
mysql -u root -p -e "SHOW DATABASES LIKE '%tenant%';"
```

### **1.2 Export Database Schemas and Data**
```bash
# Navigate to project directory
cd C:\projects\cline\rmsaas

# Create backup directory
mkdir database_backup
cd database_backup

# Export Landlord Database (Complete)
mysqldump -u root -p --single-transaction --routines --triggers rmsaas_landlord > rmsaas_landlord_complete.sql

# Export Tenant Database (Complete)  
mysqldump -u root -p --single-transaction --routines --triggers rmsaas_tenant > rmsaas_tenant_complete.sql

# Export Schema Only (for reference)
mysqldump -u root -p --no-data rmsaas_landlord > rmsaas_landlord_schema.sql
mysqldump -u root -p --no-data rmsaas_tenant > rmsaas_tenant_schema.sql

# Export Data Only (for verification)
mysqldump -u root -p --no-create-info rmsaas_landlord > rmsaas_landlord_data.sql
mysqldump -u root -p --no-create-info rmsaas_tenant > rmsaas_tenant_data.sql
```

### **1.3 Export Specific Tenant Databases** (if any exist)
```bash
# List all tenant-specific databases
mysql -u root -p -e "SELECT CONCAT('mysqldump -u root -p --single-transaction --routines --triggers ', SCHEMA_NAME, ' > ', SCHEMA_NAME, '_backup.sql') AS export_command FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE '%tenant_%';"

# Execute the generated commands manually
# Example for specific tenant database:
# mysqldump -u root -p --single-transaction --routines --triggers tenant_restaurant1 > tenant_restaurant1_backup.sql
```

### **1.4 Export User Accounts and Privileges**
```bash
# Export MySQL user accounts (if custom users exist)
mysql -u root -p -e "SELECT CONCAT('CREATE USER ''', user, '''@''', host, ''' IDENTIFIED BY PASSWORD ''', password, ''';') FROM mysql.user WHERE user LIKE '%rmsaas%';" > mysql_users.sql

# Export user privileges
mysql -u root -p -e "SHOW GRANTS FOR 'your_rmsaas_user'@'localhost';" > user_privileges.sql
```

### **1.5 Validate Exports**
```bash
# Check file sizes (should not be 0 bytes)
dir *.sql

# Verify export integrity
mysql -u root -p -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'rmsaas_landlord';"
mysql -u root -p -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'rmsaas_tenant';"

# Count records in critical tables
mysql -u root -p rmsaas_landlord -e "SELECT 'tenants' as table_name, COUNT(*) as records FROM tenants UNION SELECT 'users', COUNT(*) FROM users;"
mysql -u root -p rmsaas_tenant -e "SELECT 'menu_items' as table_name, COUNT(*) as records FROM menu_items UNION SELECT 'categories', COUNT(*) FROM categories;"
```

---

## 📤 **STEP 2: Transfer Files to macOS**

### **2.1 Transfer Methods**
Choose the best method based on your setup:

**Option A: USB Drive Transfer**
```bash
# Copy all backup files to USB drive
# Ensure sufficient space (check total size first)
copy *.sql E:\  # Replace E: with your USB drive
```

**Option B: Cloud Storage**
```bash
# Upload to cloud service (Google Drive, Dropbox, etc.)
# Create compressed archive first
tar -czf rmsaas_database_backup.tar.gz *.sql
# Upload rmsaas_database_backup.tar.gz
```

**Option C: Network Transfer**
```bash
# Using SCP (if SSH is set up between machines)
scp *.sql username@macbook-ip:/Users/username/Downloads/
```

**Option D: Git Repository**
```bash
# Commit database files to private repository
git add database_backup/*.sql
git commit -m "Database backup for migration"
git push origin migration-backup
```

### **2.2 Verify Transfer on macOS**
```bash
# On MacBook - Verify files received
ls -la ~/Downloads/database_backup/  # or your transfer location
du -h ~/Downloads/database_backup/*  # Check file sizes
```

---

## 🍎 **STEP 3: macOS MySQL Setup**

### **3.1 Install MySQL on macOS**
```bash
# Install MySQL via Homebrew
brew install mysql

# Start MySQL service
brew services start mysql

# Secure MySQL installation
mysql_secure_installation
```

**MySQL Secure Installation Prompts:**
```
VALIDATE PASSWORD PLUGIN? [y/N]: y
Password validation policy: 2 (STRONG)
New root password: [enter secure password]
Remove anonymous users? [Y/n]: Y
Disallow root login remotely? [Y/n]: Y  
Remove test database? [Y/n]: Y
Reload privilege tables? [Y/n]: Y
```

### **3.2 Configure MySQL for RMSaaS**
```bash
# Connect to MySQL
mysql -u root -p

# Create databases
CREATE DATABASE rmsaas_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE rmsaas_tenant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create application user
CREATE USER 'rmsaas_user'@'localhost' IDENTIFIED BY 'your_secure_password';
CREATE USER 'rmsaas_user'@'%' IDENTIFIED BY 'your_secure_password';

# Grant privileges
GRANT ALL PRIVILEGES ON rmsaas_landlord.* TO 'rmsaas_user'@'localhost';
GRANT ALL PRIVILEGES ON rmsaas_tenant.* TO 'rmsaas_user'@'localhost';
GRANT ALL PRIVILEGES ON rmsaas_landlord.* TO 'rmsaas_user'@'%';
GRANT ALL PRIVILEGES ON rmsaas_tenant.* TO 'rmsaas_user'@'%';

# Grant global SELECT for Laravel migrations
GRANT SELECT ON *.* TO 'rmsaas_user'@'localhost';
GRANT SELECT ON *.* TO 'rmsaas_user'@'%';

FLUSH PRIVILEGES;

# Verify databases created
SHOW DATABASES;

# Exit MySQL
EXIT;
```

### **3.3 Test Connection**
```bash
# Test connection with new user
mysql -u rmsaas_user -p -e "SELECT 'Connection successful!' as status;"

# Test database access
mysql -u rmsaas_user -p rmsaas_landlord -e "SELECT DATABASE();"
mysql -u rmsaas_user -p rmsaas_tenant -e "SELECT DATABASE();"
```

---

## 📥 **STEP 4: Import Database Data**

### **4.1 Prepare Import Directory**
```bash
# Create working directory
mkdir -p ~/Development/database_migration
cd ~/Development/database_migration

# Move/copy backup files here
mv ~/Downloads/database_backup/* .

# OR extract if compressed
tar -xzf rmsaas_database_backup.tar.gz
```

### **4.2 Import Landlord Database**
```bash
# Import landlord database
mysql -u rmsaas_user -p rmsaas_landlord < rmsaas_landlord_complete.sql

# Verify import
mysql -u rmsaas_user -p rmsaas_landlord -e "SHOW TABLES;"

# Check record counts
mysql -u rmsaas_user -p rmsaas_landlord -e "
SELECT 'tenants' as table_name, COUNT(*) as records FROM tenants UNION ALL
SELECT 'users', COUNT(*) FROM users UNION ALL  
SELECT 'countries', COUNT(*) FROM countries UNION ALL
SELECT 'subscription_plans', COUNT(*) FROM subscription_plans;"
```

### **4.3 Import Tenant Database**
```bash
# Import tenant database
mysql -u rmsaas_user -p rmsaas_tenant < rmsaas_tenant_complete.sql

# Verify import
mysql -u rmsaas_user -p rmsaas_tenant -e "SHOW TABLES;"

# Check record counts
mysql -u rmsaas_user -p rmsaas_tenant -e "
SELECT 'categories' as table_name, COUNT(*) as records FROM categories UNION ALL
SELECT 'menu_items', COUNT(*) FROM menu_items UNION ALL
SELECT 'import_jobs', COUNT(*) FROM import_jobs UNION ALL
SELECT 'recipes', COUNT(*) FROM recipes;"
```

### **4.4 Handle Import Errors** (if any)
```bash
# If you encounter charset issues:
mysql -u rmsaas_user -p rmsaas_landlord --default-character-set=utf8mb4 < rmsaas_landlord_complete.sql

# If you encounter foreign key issues:
mysql -u rmsaas_user -p -e "SET FOREIGN_KEY_CHECKS=0;"
mysql -u rmsaas_user -p rmsaas_landlord < rmsaas_landlord_complete.sql
mysql -u rmsaas_user -p rmsaas_tenant < rmsaas_tenant_complete.sql  
mysql -u rmsaas_user -p -e "SET FOREIGN_KEY_CHECKS=1;"

# For very large imports, use:
mysql -u rmsaas_user -p --max_allowed_packet=1G rmsaas_landlord < rmsaas_landlord_complete.sql
```

---

## ✅ **STEP 5: Database Verification**

### **5.1 Structure Verification**
```bash
# Compare table counts
echo "=== LANDLORD DATABASE TABLES ==="
mysql -u rmsaas_user -p rmsaas_landlord -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'rmsaas_landlord';"

echo "=== TENANT DATABASE TABLES ==="  
mysql -u rmsaas_user -p rmsaas_tenant -e "SELECT COUNT(*) as total_tables FROM information_schema.tables WHERE table_schema = 'rmsaas_tenant';"

# List all tables in each database
mysql -u rmsaas_user -p rmsaas_landlord -e "SHOW TABLES;"
mysql -u rmsaas_user -p rmsaas_tenant -e "SHOW TABLES;"
```

### **5.2 Data Integrity Verification**
```bash
# Create verification script
cat > verify_migration.sql << 'EOF'
-- Landlord Database Verification
USE rmsaas_landlord;
SELECT 'LANDLORD DB VERIFICATION' as status;
SELECT 'tenants' as table_name, COUNT(*) as records FROM tenants;
SELECT 'users' as table_name, COUNT(*) as records FROM users;
SELECT 'countries' as table_name, COUNT(*) as records FROM countries;

-- Tenant Database Verification  
USE rmsaas_tenant;
SELECT 'TENANT DB VERIFICATION' as status;
SELECT 'menu_items' as table_name, COUNT(*) as records FROM menu_items;
SELECT 'categories' as table_name, COUNT(*) as records FROM categories;
SELECT 'import_jobs' as table_name, COUNT(*) as records FROM import_jobs;
SELECT 'recipes' as table_name, COUNT(*) as records FROM recipes;
EOF

# Run verification
mysql -u rmsaas_user -p < verify_migration.sql
```

### **5.3 Foreign Key Relationships**
```bash
# Check foreign key constraints
mysql -u rmsaas_user -p rmsaas_tenant -e "
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_SCHEMA = 'rmsaas_tenant';"
```

### **5.4 Character Set and Collation**
```bash
# Verify character sets
mysql -u rmsaas_user -p -e "
SELECT SCHEMA_NAME, DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME IN ('rmsaas_landlord', 'rmsaas_tenant');"
```

---

## 🔧 **STEP 6: Laravel Application Configuration**

### **6.1 Update Environment Configuration**
```bash
# Navigate to Laravel project
cd ~/Development/projects/rmsaas

# Update .env file
nano .env
```

**Update .env with macOS database settings:**
```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rmsaas_landlord
DB_USERNAME=rmsaas_user
DB_PASSWORD=your_secure_password

# Tenant Database  
LANDLORD_DB_DATABASE=rmsaas_landlord
TENANT_DB_DATABASE=rmsaas_tenant

# Database Connection Settings
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci

# MySQL Socket (macOS specific)
DB_SOCKET=/tmp/mysql.sock
```

### **6.2 Test Laravel Database Connection**
```bash
# Test database connections
php artisan tinker

# In Tinker console:
DB::connection('landlord')->select('SELECT 1 as test');
DB::connection('tenant')->select('SELECT 1 as test');  
exit
```

### **6.3 Run Laravel Migrations** (if needed)
```bash
# Check migration status
php artisan migrate:status

# Run any pending migrations
php artisan migrate --force

# Verify tenant database migrations
php artisan tenants:list  # If this command exists
```

---

## 🧪 **STEP 7: Application Testing**

### **7.1 Basic Functionality Test**
```bash
# Start development server
php artisan serve

# Test in browser:
# http://localhost:8000 - Main application
# Check database connectivity
# Test login/registration if applicable
```

### **7.2 Multi-Tenant Testing**
```bash
# Test tenant functionality
# Create test tenant if needed
php artisan tenant:create-test

# Test tenant login and database switching
# Verify tenant-specific data isolation
```

### **7.3 Data Verification Tests**
```bash
# Run Laravel tests
php artisan test

# Run specific database tests
php artisan test --filter=DatabaseTest
php artisan test --filter=TenantTest
```

---

## 📊 **STEP 8: Performance Optimization**

### **8.1 MySQL Configuration for macOS**
```bash
# Edit MySQL configuration
sudo nano /usr/local/etc/my.cnf
```

**Add performance optimizations:**
```ini
[mysqld]
# Performance optimizations for development
innodb_buffer_pool_size = 1G
innodb_log_file_size = 256M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Character set
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci

# Connection limits
max_connections = 200
max_user_connections = 180

# Query cache (if supported)
query_cache_type = 1
query_cache_size = 128M
```

### **8.2 Laravel Database Optimizations**
```bash
# Configure database connections in config/database.php
# Add connection pooling if needed
# Optimize query logging for development

# Clear and cache configurations
php artisan config:clear  
php artisan config:cache
```

---

## 🔄 **STEP 9: Backup Strategy on macOS**

### **9.1 Automated Backup Script**
```bash
# Create backup script
cat > ~/Development/scripts/backup_rmsaas_db.sh << 'EOF'
#!/bin/bash

# RMSaaS Database Backup Script for macOS
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$HOME/Development/database_backups"
mkdir -p $BACKUP_DIR

echo "🗄️ Starting RMSaaS Database Backup - $DATE"

# Backup landlord database
mysqldump -u rmsaas_user -p rmsaas_landlord > "$BACKUP_DIR/rmsaas_landlord_$DATE.sql"

# Backup tenant database  
mysqldump -u rmsaas_user -p rmsaas_tenant > "$BACKUP_DIR/rmsaas_tenant_$DATE.sql"

# Compress backups
tar -czf "$BACKUP_DIR/rmsaas_complete_backup_$DATE.tar.gz" "$BACKUP_DIR"/*.sql

echo "✅ Backup completed: rmsaas_complete_backup_$DATE.tar.gz"

# Clean up old backups (keep last 7 days)
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

EOF

# Make script executable
chmod +x ~/Development/scripts/backup_rmsaas_db.sh

# Test backup script
~/Development/scripts/backup_rmsaas_db.sh
```

### **9.2 Schedule Regular Backups**
```bash
# Create cron job for daily backups
crontab -e

# Add this line for daily backup at 2 AM:
0 2 * * * /Users/yourusername/Development/scripts/backup_rmsaas_db.sh >> /Users/yourusername/Development/logs/backup.log 2>&1
```

---

## 🚨 **TROUBLESHOOTING COMMON ISSUES**

### **Issue 1: Character Encoding Problems**
```bash
# Symptoms: Garbled text, special characters not displaying
# Solution:
mysql -u rmsaas_user -p -e "ALTER DATABASE rmsaas_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u rmsaas_user -p -e "ALTER DATABASE rmsaas_tenant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# For specific tables:
mysql -u rmsaas_user -p rmsaas_tenant -e "ALTER TABLE table_name CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### **Issue 2: Foreign Key Constraint Failures**
```bash
# Temporary disable foreign key checks during import
mysql -u rmsaas_user -p -e "SET FOREIGN_KEY_CHECKS=0;"
# Re-import data
mysql -u rmsaas_user -p -e "SET FOREIGN_KEY_CHECKS=1;"
```

### **Issue 3: Large Import Timeouts**
```bash
# Increase MySQL timeouts
mysql -u rmsaas_user -p -e "SET GLOBAL max_allowed_packet=1073741824;"
mysql -u rmsaas_user -p -e "SET GLOBAL wait_timeout=28800;"
mysql -u rmsaas_user -p -e "SET GLOBAL interactive_timeout=28800;"
```

### **Issue 4: Permission Denied Errors**
```bash
# Check MySQL process ownership
ps aux | grep mysql

# Fix file permissions if needed
sudo chown -R mysql:mysql /usr/local/var/mysql/

# Restart MySQL
brew services restart mysql
```

### **Issue 5: Laravel Connection Issues**
```bash
# Clear Laravel configuration cache
php artisan config:clear

# Test database connection in Tinker
php artisan tinker
DB::connection()->getPdo();
```

---

## ✅ **MIGRATION COMPLETION CHECKLIST**

### **Database Migration:**
- [ ] All Windows databases exported successfully
- [ ] MySQL installed and configured on macOS
- [ ] Databases created on macOS MySQL
- [ ] User accounts and privileges configured
- [ ] All database data imported successfully
- [ ] Foreign key relationships intact
- [ ] Character sets and collations correct

### **Application Configuration:**
- [ ] Laravel .env file updated
- [ ] Database connections tested
- [ ] Migrations status verified
- [ ] Application starts without errors
- [ ] Multi-tenant functionality working

### **Data Verification:**
- [ ] All tables present in both databases
- [ ] Record counts match original databases
- [ ] Foreign key constraints working
- [ ] Data integrity maintained
- [ ] No character encoding issues

### **Performance & Backup:**
- [ ] MySQL performance optimized
- [ ] Automated backup script created
- [ ] Regular backup schedule configured
- [ ] Test restore procedure verified

---

## 📈 **POST-MIGRATION OPTIMIZATION**

### **Database Monitoring**
```bash
# Install database monitoring tools
brew install mytop                    # Real-time MySQL monitoring
brew install --cask sequel-pro       # GUI database management
brew install --cask tableplus        # Modern database client

# Create monitoring script
cat > ~/Development/scripts/db_monitor.sh << 'EOF'  
#!/bin/bash
echo "=== MySQL Status ==="
brew services list | grep mysql

echo "=== Database Sizes ==="
mysql -u rmsaas_user -p -e "
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema IN ('rmsaas_landlord', 'rmsaas_tenant')
GROUP BY table_schema;"

echo "=== Connection Status ==="  
mysql -u rmsaas_user -p -e "SHOW STATUS LIKE 'Connections';"
EOF

chmod +x ~/Development/scripts/db_monitor.sh
```

---

## 🎉 **DATABASE MIGRATION COMPLETE!**

Your RMSaaS databases have been successfully migrated from Windows MySQL to macOS MySQL!

**Migration Summary:**
- ✅ **Complete Data Transfer**: All databases and data migrated
- ✅ **Multi-Tenant Architecture**: Landlord/tenant separation maintained  
- ✅ **Data Integrity**: Foreign keys and relationships preserved
- ✅ **Performance Optimized**: MySQL configured for optimal performance
- ✅ **Backup Strategy**: Automated backup system implemented
- ✅ **Laravel Integration**: Application fully configured for macOS

**Key Benefits:**
- **Better Performance**: macOS MySQL optimized for Apple Silicon
- **Improved Development**: Native UNIX environment
- **Enhanced Security**: Better file permissions and access control
- **Professional Tools**: Superior database management applications
- **Backup Reliability**: Automated Time Machine integration

**Your RMSaaS multi-tenant database system is now running natively on macOS with enterprise-grade reliability!** 🚀

---

*Database Migration Guide Completed: September 4, 2025*  
*💾 Multi-Tenant Database Successfully Migrated to macOS*  
*🍎 Optimized for Apple Silicon MacBook Air M2*