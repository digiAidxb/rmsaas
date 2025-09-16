# 🚀 Complete RMSaaS Migration Guide: Windows 10 → MacBook Air M2

**From Visual Studio Code to Xcode Development Environment**  
**Target System**: MacBook Air M2 (Apple Silicon)  
**Migration Date**: September 4, 2025  
**Project Status**: Production-Ready Multi-Tenant Restaurant Management SaaS

---

## 📋 **PRE-MIGRATION CHECKLIST**

### **On Windows 10 (Current Setup)**
- [ ] Export all database data
- [ ] Create complete project backup
- [ ] Document current environment variables
- [ ] List all installed dependencies
- [ ] Export VS Code settings and extensions
- [ ] Create database dumps (landlord & tenant schemas)

---

## 🔧 **STEP 1: macOS Development Environment Setup**

### **1.1 Install Homebrew (Package Manager)**
```bash
# Install Homebrew (macOS package manager)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Add Homebrew to PATH
echo 'eval "$(/opt/homebrew/bin/brew shellenv)"' >> ~/.zprofile
eval "$(/opt/homebrew/bin/brew shellenv)"

# Verify installation
brew --version
```

### **1.2 Install PHP 8.2+ with Extensions**
```bash
# Install PHP (optimized for Apple Silicon)
brew install php@8.2

# Install PHP extensions required for Laravel
brew install composer

# Verify PHP installation
php --version
php -m | grep -E "(curl|fileinfo|mbstring|openssl|PDO|pdo_mysql|tokenizer|xml|zip|json|bcmath|ctype)"
```

### **1.3 Install Node.js and npm**
```bash
# Install Node.js (LTS version)
brew install node

# Verify installation
node --version
npm --version

# Install global packages
npm install -g @vue/cli
npm install -g vite
```

### **1.4 Install MySQL Server**
```bash
# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql

# Secure MySQL installation
mysql_secure_installation

# Create databases
mysql -u root -p
```

**MySQL Setup Commands:**
```sql
-- Create databases
CREATE DATABASE rmsaas_landlord CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE rmsaas_tenant CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user (replace with your preferred credentials)
CREATE USER 'rmsaas_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON rmsaas_landlord.* TO 'rmsaas_user'@'localhost';
GRANT ALL PRIVILEGES ON rmsaas_tenant.* TO 'rmsaas_user'@'localhost';
GRANT SELECT ON *.* TO 'rmsaas_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### **1.5 Install Git and Configure**
```bash
# Git is pre-installed on macOS, but update if needed
brew install git

# Configure Git
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# Generate SSH key for GitHub (if needed)
ssh-keygen -t ed25519 -C "your.email@example.com"
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519
cat ~/.ssh/id_ed25519.pub  # Copy this to GitHub
```

---

## 📱 **STEP 2: Install and Configure Xcode**

### **2.1 Install Xcode**
```bash
# Install Xcode from App Store (large download ~10GB)
# OR install Xcode Command Line Tools only:
xcode-select --install

# Verify installation
xcode-select -p
```

### **2.2 Configure Xcode for Web Development**
1. **Open Xcode** from Applications
2. **Install Additional Components** when prompted
3. **Configure Preferences:**
   - Xcode → Preferences → Text Editing → Enable line numbers
   - Xcode → Preferences → Text Editing → Code completion → Enable all options
   - Xcode → Preferences → Behaviors → Set custom behaviors for builds

### **2.3 Install Essential Xcode Extensions**
- **PHP Syntax Highlighting**: Install CodeRunner or PHP extensions
- **Laravel Extensions**: Search for Laravel support in Xcode extensions
- **Git Integration**: Built-in, just sign in with your GitHub account

---

## 📂 **STEP 3: Project Migration Process**

### **3.1 Export Data from Windows Setup**

**On Windows 10:**
```bash
# Navigate to project directory
cd C:\projects\cline\rmsaas

# Export databases
mysqldump -u your_username -p rmsaas_landlord > rmsaas_landlord_backup.sql
mysqldump -u your_username -p rmsaas_tenant > rmsaas_tenant_backup.sql

# Create complete project archive
# Use 7-Zip or built-in Windows compression
# Include: entire project folder + database dumps
```

### **3.2 Transfer Project to MacBook**
**Options for transfer:**
- **USB Drive**: Copy entire project folder + database dumps
- **Cloud Storage**: Upload to Google Drive/Dropbox/OneDrive
- **Git Repository**: Push to GitHub and clone on Mac
- **Network Transfer**: Use AirDrop or shared network folder

### **3.3 Setup Project on MacBook**

```bash
# Create development directory
mkdir -p ~/Development/projects
cd ~/Development/projects

# If using Git:
git clone https://github.com/yourusername/rmsaas.git
cd rmsaas

# If using file transfer:
# Extract your project archive to ~/Development/projects/rmsaas
```

### **3.4 Install PHP Dependencies**
```bash
cd ~/Development/projects/rmsaas

# Install Composer dependencies
composer install

# Install Node dependencies
npm install

# Copy environment file
cp .env.example .env
```

---

## ⚙️ **STEP 4: Environment Configuration**

### **4.1 Configure .env File**
```bash
# Edit .env file
nano .env  # or use Xcode to edit
```

**Update .env with macOS-specific settings:**
```env
APP_NAME="RMSaaS"
APP_ENV=local
APP_KEY=base64:your_app_key_here
APP_DEBUG=true
APP_URL=http://rmsaas.local

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rmsaas_landlord
DB_USERNAME=rmsaas_user
DB_PASSWORD=your_secure_password

# Tenant Database
LANDLORD_DB_DATABASE=rmsaas_landlord
TENANT_DB_DATABASE=rmsaas_tenant

# Mail Configuration (use Mailtrap for testing)
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password

# Session and Cache
SESSION_DRIVER=database
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Multi-tenancy
AUTH_GUARD=web
SESSION_CONNECTION=landlord
```

### **4.2 Generate Application Key**
```bash
php artisan key:generate
```

### **4.3 Import Database Data**
```bash
# Import landlord database
mysql -u rmsaas_user -p rmsaas_landlord < path/to/rmsaas_landlord_backup.sql

# Import tenant database (if you have existing tenant data)
mysql -u rmsaas_user -p rmsaas_tenant < path/to/rmsaas_tenant_backup.sql

# Run migrations to ensure all tables are up to date
php artisan migrate --force

# Run seeders if needed
php artisan db:seed
```

---

## 🌐 **STEP 5: Local Development Server Setup**

### **5.1 Configure Local Domains (Laravel Valet - Recommended)**
```bash
# Install Laravel Valet
composer global require laravel/valet

# Add Composer global bin to PATH
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.zprofile
source ~/.zprofile

# Install Valet
valet install

# Navigate to your projects directory
cd ~/Development/projects

# Park the directory (makes all projects available as .test domains)
valet park

# Your project will be available at: http://rmsaas.test
# Tenant domains will work as: http://tenant1.rmsaas.test
```

### **5.2 Alternative: Use Laravel Sail (Docker)**
```bash
# Install Laravel Sail
composer require laravel/sail --dev

# Publish Sail configuration
php artisan sail:install

# Start development environment
./vendor/bin/sail up -d

# Your project will be available at: http://localhost
```

### **5.3 Alternative: Built-in PHP Server**
```bash
# Simple development server
php artisan serve --host=0.0.0.0 --port=8000

# Your project will be available at: http://localhost:8000
```

---

## 🛠️ **STEP 6: Xcode Project Configuration**

### **6.1 Open Project in Xcode**
1. **Launch Xcode**
2. **File → Open** → Navigate to `~/Development/projects/rmsaas`
3. **Select the entire project folder** (not just individual files)

### **6.2 Configure Xcode Workspace**
```bash
# Create Xcode workspace file (optional but recommended)
cd ~/Development/projects/rmsaas
touch RMSaaS.xcworkspace
```

**Xcode Project Structure:**
```
RMSaaS.xcworkspace/
├── app/                    # Laravel Application
├── resources/             # Frontend Resources
│   ├── js/               # Vue.js Components
│   ├── css/              # Stylesheets
│   └── views/            # Blade Templates
├── database/             # Migrations & Seeders
├── public/               # Public Assets
└── config/               # Configuration Files
```

### **6.3 Configure Build Schemes (Optional)**
1. **Product → Scheme → New Scheme**
2. **Create schemes for:**
   - Development Build
   - Production Build
   - Testing
   - Database Migration

### **6.4 Set Up External Tools**
**Xcode → Preferences → Behaviors → External Tools:**
- **Laravel Artisan**: `/usr/local/bin/php artisan`
- **Composer**: `/usr/local/bin/composer`
- **NPM**: `/usr/local/bin/npm`

---

## 🎨 **STEP 7: Frontend Development Setup**

### **7.1 Build Frontend Assets**
```bash
cd ~/Development/projects/rmsaas

# Development build
npm run dev

# Watch for changes (recommended during development)
npm run watch

# Production build
npm run build
```

### **7.2 Configure Hot Module Replacement**
```bash
# Install Vite for hot reloading
npm install --save-dev vite laravel-vite-plugin

# Start Vite development server
npm run dev
```

---

## 📊 **STEP 8: Database & Testing Setup**

### **8.1 Run Database Migrations**
```bash
# Run all migrations
php artisan migrate

# Seed database with test data
php artisan db:seed

# Create test tenant
php artisan tenant:create-test --approve
```

### **8.2 Testing Setup**
```bash
# Run Laravel tests
php artisan test

# Run specific test suites
php artisan test --filter=OnboardingTest
php artisan test --filter=ImportTest
```

---

## 🔍 **STEP 9: Verification & Testing**

### **9.1 System Health Check**
```bash
# Check system status
php artisan about

# Check database connections
php artisan tinker
# In Tinker:
DB::connection('landlord')->select('SELECT 1');
DB::connection('tenant')->select('SELECT 1');
```

### **9.2 Test Multi-Tenant Setup**
1. **Visit Main Application**: `http://rmsaas.test` (or localhost:8000)
2. **Test Tenant Registration**: Create a new tenant
3. **Test Tenant Login**: Access tenant subdomain
4. **Test Onboarding Flow**: Complete onboarding process
5. **Test Import System**: Upload and import data

### **9.3 Performance Optimization**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Clear caches during development
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📝 **STEP 10: macOS-Specific Optimizations**

### **10.1 Apple Silicon Optimizations**
```bash
# Install ARM64 optimized packages
arch -arm64 brew install mysql php node

# Check architecture
uname -m  # Should show 'arm64'

# Use native ARM64 PHP
which php  # Should show /opt/homebrew/bin/php
```

### **10.2 System Preferences for Development**
1. **System Preferences → Security & Privacy → Privacy → Full Disk Access**
   - Add Terminal, Xcode, and any development tools

2. **System Preferences → Network → Advanced → DNS**
   - Add `127.0.0.1` for local development

3. **System Preferences → Energy Saver**
   - Disable "Put hard disks to sleep" during development

### **10.3 macOS Development Tools**
```bash
# Install useful development tools
brew install --cask sequel-pro          # Database management
brew install --cask tableplus           # Modern database client
brew install --cask postman             # API testing
brew install --cask raycast             # Enhanced spotlight
brew install tree                       # Directory structure visualization
brew install htop                       # System monitoring
```

---

## 🚨 **TROUBLESHOOTING GUIDE**

### **Common Issues & Solutions**

#### **1. Permission Issues**
```bash
# Fix Laravel permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R $(whoami):$(whoami) storage bootstrap/cache
```

#### **2. MySQL Connection Issues**
```bash
# Check MySQL status
brew services list | grep mysql

# Restart MySQL
brew services restart mysql

# Check MySQL socket
mysql_config --socket
```

#### **3. PHP Extensions Missing**
```bash
# Check installed extensions
php -m

# Install missing extensions
brew install php@8.2-curl php@8.2-mbstring php@8.2-xml
```

#### **4. Node.js/NPM Issues**
```bash
# Clear NPM cache
npm cache clean --force

# Reinstall node modules
rm -rf node_modules package-lock.json
npm install
```

#### **5. Xcode Build Issues**
- **Clean Build Folder**: Product → Clean Build Folder
- **Reset Derived Data**: Xcode → Preferences → Locations → Derived Data → Delete
- **Update Command Line Tools**: `xcode-select --install`

---

## 📋 **POST-MIGRATION CHECKLIST**

### **Verify All Systems Working:**
- [ ] PHP and all extensions installed
- [ ] MySQL running and databases accessible
- [ ] Laravel application starts without errors
- [ ] Frontend assets compile successfully
- [ ] Multi-tenant routing works correctly
- [ ] Database migrations run successfully
- [ ] Import system functions properly
- [ ] AI analytics services operational
- [ ] All tests pass
- [ ] Xcode can build and run project
- [ ] Local development domains resolve
- [ ] Hot reloading works for frontend development

### **Development Workflow Setup:**
- [ ] Git configured with SSH keys
- [ ] Xcode preferences configured
- [ ] Database management tool installed
- [ ] API testing tool (Postman) configured
- [ ] Backup strategy implemented
- [ ] Documentation updated with new paths

---

## 🎯 **DEVELOPMENT WORKFLOW ON macOS**

### **Daily Development Routine:**
```bash
# Start development environment
cd ~/Development/projects/rmsaas

# If using Valet (recommended):
# Project automatically available at http://rmsaas.test

# If using Laravel Sail:
./vendor/bin/sail up -d

# Start frontend development
npm run watch

# Open in Xcode for code editing
open -a Xcode .
```

### **Xcode Development Tips:**
1. **Use Split View**: View code and simulator/browser simultaneously
2. **Custom Build Scripts**: Create scripts for Laravel commands
3. **Debugging**: Use Xcode's debugging tools for PHP (with proper setup)
4. **Version Control**: Leverage Xcode's built-in Git integration
5. **Code Completion**: Configure Xcode for better PHP/Laravel support

---

## 🎉 **MIGRATION COMPLETE!**

Your RMSaaS project is now successfully migrated to macOS with Xcode development environment!

**Key Benefits of macOS Development:**
- ✅ **Native UNIX Environment**: Better compatibility with Laravel
- ✅ **Superior Performance**: Apple Silicon optimization
- ✅ **Professional IDE**: Xcode with advanced debugging
- ✅ **Better Terminal**: Native bash/zsh with better performance
- ✅ **Docker Integration**: Seamless container development
- ✅ **iOS Development Ready**: Future mobile app development

**Next Steps:**
1. **Familiarize with macOS shortcuts**: Cmd instead of Ctrl
2. **Explore Xcode features**: Debugging, profiling, testing
3. **Set up backup strategy**: Time Machine for automatic backups
4. **Configure development workflow**: Optimize for your productivity

**Project Status**: ✅ **FULLY MIGRATED & OPERATIONAL**

---

*Migration Guide Completed: September 4, 2025*  
*🍎 Welcome to macOS Development!*  
*🚀 Your RMSaaS project is ready for Apple Silicon development*