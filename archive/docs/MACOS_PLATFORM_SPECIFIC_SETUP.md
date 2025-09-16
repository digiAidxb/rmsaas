# 🍎 macOS Platform-Specific Setup for RMSaaS Multi-Tenant System

**Critical Platform Differences: Windows Commands → macOS Commands**  
**Focus**: Tenant subdomain support, services, and system-level configurations  
**Target**: Production-ready multi-tenant restaurant management system

---

## 🌐 **TENANT SUBDOMAIN SUPPORT - CRITICAL DIFFERENCE**

### **The Problem**
On Windows, you likely used:
- XAMPP/WAMP with virtual hosts
- Windows hosts file editing
- IIS or Apache configuration

### **macOS Solution: Laravel Valet (Recommended)**

**Install Laravel Valet:**
```bash
# Install Valet (best solution for Laravel multi-tenancy)
composer global require laravel/valet

# Add Composer global bin to PATH
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.zprofile
source ~/.zprofile

# Install Valet system services
valet install

# Navigate to your projects directory
cd ~/Development/projects

# Park the directory (enables *.test domains)
valet park

# Your project is now available at:
# Main app: http://rmsaas.test
# Tenant subdomains: http://tenant1.rmsaas.test, http://restaurant2.rmsaas.test, etc.
```

**Valet automatically handles:**
- ✅ Wildcard subdomain routing
- ✅ HTTPS certificates
- ✅ PHP version management
- ✅ Nginx configuration
- ✅ DNS resolution

---

## 🏠 **HOST FILE CONFIGURATION (Alternative Method)**

### **If not using Valet, manual hosts configuration:**

**Edit macOS hosts file:**
```bash
# Edit hosts file (requires admin privileges)
sudo nano /etc/hosts

# Add entries for your RMSaaS system:
127.0.0.1    rmsaas.local
127.0.0.1    tenant1.rmsaas.local
127.0.0.1    tenant2.rmsaas.local
127.0.0.1    restaurant1.rmsaas.local
127.0.0.1    demo.rmsaas.local
127.0.0.1    *.rmsaas.local    # This won't work - see solution below
```

**For wildcard subdomain support without Valet:**
```bash
# Install dnsmasq for wildcard subdomain support
brew install dnsmasq

# Configure dnsmasq
echo 'address=/.rmsaas.local/127.0.0.1' > /usr/local/etc/dnsmasq.conf

# Start dnsmasq service
sudo brew services start dnsmasq

# Configure macOS to use dnsmasq for .local domains
sudo mkdir -p /etc/resolver
echo 'nameserver 127.0.0.1' | sudo tee /etc/resolver/rmsaas.local

# Test configuration
ping tenant1.rmsaas.local    # Should resolve to 127.0.0.1
```

---

## ⚙️ **WEB SERVER CONFIGURATION**

### **Option 1: Laravel Valet (Recommended)**
```bash
# Valet handles everything automatically
valet park
valet secure rmsaas    # Enable HTTPS if needed
```

### **Option 2: Apache Configuration** (if you prefer Apache)
```bash
# Install Apache
brew install httpd

# Edit Apache configuration
sudo nano /usr/local/etc/httpd/httpd.conf

# Enable virtual hosts
# Uncomment: Include /usr/local/etc/httpd/extra/httpd-vhosts.conf

# Create virtual host file
sudo nano /usr/local/etc/httpd/extra/httpd-vhosts.conf
```

**Add to httpd-vhosts.conf:**
```apache
<VirtualHost *:80>
    DocumentRoot "/Users/yourusername/Development/projects/rmsaas/public"
    ServerName rmsaas.local
    ServerAlias *.rmsaas.local
    
    <Directory "/Users/yourusername/Development/projects/rmsaas/public">
        AllowOverride All
        Require all granted
    </Directory>
    
    # PHP configuration
    <FilesMatch \.php$>
        SetHandler "proxy:fcgi://127.0.0.1:9000"
    </FilesMatch>
</VirtualHost>
```

### **Option 3: Nginx Configuration**
```bash
# Install Nginx
brew install nginx

# Create site configuration
sudo nano /usr/local/etc/nginx/servers/rmsaas.conf
```

**Nginx configuration for RMSaaS:**
```nginx
server {
    listen 80;
    server_name rmsaas.local *.rmsaas.local;
    root /Users/yourusername/Development/projects/rmsaas/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

---

## 🐘 **PHP-FPM CONFIGURATION**

### **Windows vs macOS PHP Differences:**
```bash
# Install PHP-FPM (required for Nginx/Apache)
brew install php@8.2

# Start PHP-FPM service
brew services start php@8.2

# PHP-FPM listens on port 9000 by default
# Configuration file location:
/usr/local/etc/php/8.2/php-fpm.d/www.conf
```

**Key PHP-FPM Settings for RMSaaS:**
```bash
# Edit PHP-FPM pool configuration
nano /usr/local/etc/php/8.2/php-fpm.d/www.conf

# Important settings for multi-tenant system:
user = _www
group = _www
listen = 127.0.0.1:9000
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

---

## 🗄️ **DATABASE SERVICE MANAGEMENT**

### **Windows vs macOS MySQL Service Commands:**

**Windows Commands (what you used):**
```bash
net start mysql
net stop mysql
```

**macOS Commands (what you need now):**
```bash
# Start MySQL service
brew services start mysql

# Stop MySQL service
brew services stop mysql

# Restart MySQL service
brew services restart mysql

# Check service status
brew services list | grep mysql

# Connect to MySQL
mysql -u root -p
```

**MySQL Configuration File Locations:**
```bash
# Windows: C:\ProgramData\MySQL\MySQL Server X.X\my.ini
# macOS: /usr/local/etc/my.cnf (or /etc/my.cnf)

# Edit MySQL configuration on macOS
nano /usr/local/etc/my.cnf
```

---

## 🔧 **SYSTEM SERVICE MANAGEMENT**

### **Windows Services → macOS Services Translation:**

**Windows Services Manager → macOS Homebrew Services:**
```bash
# List all services (Windows: services.msc)
brew services list

# Start service (Windows: net start servicename)
brew services start servicename

# Stop service (Windows: net stop servicename)
brew services stop servicename

# Restart service
brew services restart servicename

# Enable service at boot
brew services start servicename  # Already enables at boot

# Check service logs
tail -f /usr/local/var/log/servicename.log
```

---

## 📁 **FILE PATHS AND PERMISSIONS**

### **Critical Path Differences:**

**Windows Paths → macOS Paths:**
```bash
# Project location:
# Windows: C:\projects\cline\rmsaas
# macOS: ~/Development/projects/rmsaas (/Users/username/Development/projects/rmsaas)

# Laravel storage permissions:
# Windows: Usually automatic
# macOS: Requires explicit setting
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R $(whoami):_www storage bootstrap/cache

# Web server user:
# Windows: IUSR or IIS_IUSRS
# macOS: _www (for Apache/Nginx) or your user (for Valet)
```

### **Storage and Cache Permissions:**
```bash
# Set proper ownership for Laravel
cd ~/Development/projects/rmsaas
sudo chown -R $(whoami):_www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Verify permissions
ls -la storage/
ls -la bootstrap/cache/
```

---

## 🌐 **DNS AND NETWORKING**

### **Network Configuration Differences:**

**Flush DNS Cache:**
```bash
# Windows: ipconfig /flushdns
# macOS: 
sudo dscacheutil -flushcache
sudo killall -HUP mDNSResponder
```

**Check Network Interfaces:**
```bash
# Windows: ipconfig /all
# macOS:
ifconfig
# or
networksetup -listallhardwareports
```

**Test Domain Resolution:**
```bash
# Test if your tenant subdomains resolve
dig tenant1.rmsaas.local
nslookup tenant1.rmsaas.local

# Test HTTP response
curl -H "Host: tenant1.rmsaas.local" http://127.0.0.1
```

---

## 🔐 **SECURITY AND FIREWALL**

### **Firewall Configuration:**

**Allow HTTP/HTTPS traffic:**
```bash
# macOS Firewall settings (System Preferences → Security & Privacy → Firewall)
# Or via command line:
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --add /usr/local/bin/nginx
sudo /usr/libexec/ApplicationFirewall/socketfilterfw --add /usr/local/bin/httpd
```

**File Permissions Security:**
```bash
# Set secure file permissions
find ~/Development/projects/rmsaas -type f -exec chmod 644 {} \;
find ~/Development/projects/rmsaas -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
chmod 755 artisan
```

---

## 🚀 **DEVELOPMENT SERVER COMMANDS**

### **Windows vs macOS Development Server:**

**Laravel Built-in Server:**
```bash
# Works the same on both platforms
php artisan serve --host=0.0.0.0 --port=8000

# But for multi-tenant subdomains, use:
php artisan serve --host=0.0.0.0 --port=8000

# Test tenant access:
curl -H "Host: tenant1.rmsaas.local" http://localhost:8000
```

**Valet Development (macOS Only):**
```bash
# Start Valet services (if not running)
valet restart

# Check Valet status
valet status

# Test tenant subdomains
open http://rmsaas.test
open http://tenant1.rmsaas.test
```

---

## 📊 **ENVIRONMENT VARIABLES**

### **Windows Environment Variables → macOS Environment Variables:**

**Set System Environment Variables:**
```bash
# Windows: System Properties → Environment Variables
# macOS: Add to shell profile

# Add to ~/.zprofile (for zsh) or ~/.bash_profile (for bash)
echo 'export PATH="/usr/local/bin:$PATH"' >> ~/.zprofile
echo 'export PATH="$HOME/.composer/vendor/bin:$PATH"' >> ~/.zprofile
echo 'export DB_CONNECTION=mysql' >> ~/.zprofile
echo 'export DB_HOST=127.0.0.1' >> ~/.zprofile

# Reload shell
source ~/.zprofile
```

---

## 🧪 **TESTING MULTI-TENANT SETUP**

### **Complete Verification Script for macOS:**

```bash
#!/bin/bash
# Save as test_multitenancy.sh

echo "🧪 Testing RMSaaS Multi-Tenant Setup on macOS..."

PROJECT_DIR="$HOME/Development/projects/rmsaas"
cd "$PROJECT_DIR"

# Test 1: Check if main domain resolves
echo "🔍 Testing main domain..."
if curl -s -o /dev/null -w "%{http_code}" http://rmsaas.test | grep -q "200\|302"; then
    echo "✅ Main domain (rmsaas.test) accessible"
else
    echo "❌ Main domain not accessible"
fi

# Test 2: Check tenant subdomain
echo "🏢 Testing tenant subdomain..."
if curl -s -H "Host: tenant1.rmsaas.test" http://127.0.0.1:8000 -o /dev/null -w "%{http_code}" | grep -q "200\|302"; then
    echo "✅ Tenant subdomain working"
else
    echo "❌ Tenant subdomain not working"
fi

# Test 3: Check database connections
echo "🗄️ Testing database connections..."
if php artisan tinker --execute="DB::connection('landlord')->select('SELECT 1');" > /dev/null 2>&1; then
    echo "✅ Landlord database connection working"
else
    echo "❌ Landlord database connection failed"
fi

if php artisan tinker --execute="DB::connection('tenant')->select('SELECT 1');" > /dev/null 2>&1; then
    echo "✅ Tenant database connection working"
else
    echo "❌ Tenant database connection failed"
fi

# Test 4: Check required services
echo "🔧 Checking required services..."
if brew services list | grep mysql | grep -q started; then
    echo "✅ MySQL service running"
else
    echo "❌ MySQL service not running"
fi

if brew services list | grep php | grep -q started; then
    echo "✅ PHP service running"
else
    echo "⚠️ PHP service status unknown"
fi

echo "🎉 Multi-tenant setup verification complete!"
```

**Make it executable and run:**
```bash
chmod +x test_multitenancy.sh
./test_multitenancy.sh
```

---

## 📋 **MACOS-SPECIFIC SETUP CHECKLIST**

### **Essential macOS Configurations:**
- [ ] Laravel Valet installed and configured
- [ ] Wildcard subdomain support working (*.rmsaas.test)
- [ ] MySQL service started with brew services
- [ ] PHP-FPM configured and running
- [ ] File permissions set correctly (775 for storage)
- [ ] DNS resolution working for tenant subdomains
- [ ] .zprofile updated with correct PATH variables
- [ ] Firewall configured to allow web traffic
- [ ] Storage symlink created (php artisan storage:link)
- [ ] Multi-tenant verification script passes all tests

### **Key Differences Summary:**
```bash
# Windows → macOS Command Translation
net start mysql → brew services start mysql
ipconfig /flushdns → sudo dscacheutil -flushcache
services.msc → brew services list
System Properties → ~/.zprofile
C:\projects → ~/Development/projects
XAMPP/WAMP → Laravel Valet
Apache/IIS config → Valet handles automatically
```

---

## 🎉 **PLATFORM MIGRATION COMPLETE!**

Your RMSaaS multi-tenant system is now properly configured for macOS with:
- ✅ **Native subdomain support** via Laravel Valet
- ✅ **Proper service management** via Homebrew
- ✅ **Correct file permissions** for Laravel
- ✅ **macOS-optimized database** configuration
- ✅ **Professional development workflow** with native tools

**The tenant subdomain system will work seamlessly on macOS with these configurations!**

---

*macOS Platform-Specific Setup Guide Created: September 4, 2025*  
*🍎 Complete Windows → macOS Translation for RMSaaS Multi-Tenant System*  
*🌐 Tenant Subdomain Support Fully Configured*