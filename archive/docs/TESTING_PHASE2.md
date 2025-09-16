# 🧪 Phase 2 Testing Guide

## Quick Test Results Summary
✅ **Database Seeding**: 53 countries, 4 plans, 5 admins, 15 tenants, 300+ users
✅ **Multi-lingual Users**: Arabic, English, Chinese, Hindi, Spanish, French, German, Portuguese, Russian, Japanese
✅ **Tenant-User Isolation**: Each tenant has 20 users with balanced language distribution

## 🎯 Testing Methods Available

### Method 1: API Testing with curl/Postman (Recommended)
Test the authentication system using our built-in test routes:

#### 1. Test Basic Tenant Info
```bash
curl -H "Host: tenant1.localhost" http://localhost:8000/test/tenant-info
```

#### 2. View Tenant Users with Languages  
```bash
curl -H "Host: tenant1.localhost" http://localhost:8000/test/tenant-users
```

#### 3. Test Authentication with Multi-lingual Users
```bash
# Test Arabic Owner
curl -X POST -H "Host: tenant1.localhost" \
  -H "Content-Type: application/json" \
  -d '{"email":"owner@tenant1.localhost.com","password":"Restaurant@2025"}' \
  http://localhost:8000/test/login

# Test English Manager  
curl -X POST -H "Host: tenant1.localhost" \
  -H "Content-Type: application/json" \
  -d '{"email":"manager@tenant1.localhost.com","password":"Restaurant@2025"}' \
  http://localhost:8000/test/login

# Test Chinese Accountant
curl -X POST -H "Host: tenant1.localhost" \
  -H "Content-Type: application/json" \
  -d '{"email":"accountant@tenant1.localhost.com","password":"Restaurant@2025"}' \
  http://localhost:8000/test/login

# Test Hindi Operator
curl -X POST -H "Host: tenant1.localhost" \
  -H "Content-Type: application/json" \
  -d '{"email":"operator@tenant1.localhost.com","password":"Restaurant@2025"}' \
  http://localhost:8000/test/login
```

#### 4. View All Tenants and Sample Users
```bash
curl http://localhost:8000/test/all-tenants
```

### Method 2: Laravel Artisan Testing
Test directly through Laravel's command line:

#### Test Database Connection
```bash
php artisan tinker --execute="
echo 'Testing tenant user authentication:' . PHP_EOL;
use Illuminate\Support\Facades\DB;
\$user = DB::connection('landlord')->table('users')
    ->where('email', 'owner@tenant1.localhost.com')
    ->first(['name', 'email', 'preferred_language', 'role']);
print_r(\$user);
"
```

#### Test Multi-lingual Configuration
```bash
php artisan tinker --execute="
echo 'Available Languages:' . PHP_EOL;
print_r(config('app.available_locales'));
echo PHP_EOL . 'Default Locale: ' . app()->getLocale() . PHP_EOL;
"
```

### Method 3: Browser Testing (requires host configuration)

#### Setup Local Hosts (Optional)
Add to your `C:\Windows\System32\drivers\etc\hosts` file:
```
127.0.0.1 tenant1.localhost
127.0.0.1 tenant2.localhost  
127.0.0.1 test.localhost
127.0.0.1 gorkha.localhost
127.0.0.1 secure.localhost
```

Then visit: `http://tenant1.localhost:8000/test/tenant-info`

## 📊 Expected Test Results

### Tenant Info Response:
```json
{
  "current_tenant": {
    "id": 1,
    "name": "Demo Tenant 1", 
    "domain": "tenant1.localhost",
    "status": "pending"
  },
  "available_locales": {
    "en": {"name": "English", "native": "English"},
    "ar": {"name": "Arabic", "native": "العربية"},
    "zh": {"name": "Chinese", "native": "中文"},
    "hi": {"name": "Hindi", "native": "हिन्दी"}
  }
}
```

### Login Response:
```json
{
  "success": true,
  "user": {
    "name": "Ahmed Al-Rashid",
    "email": "owner@tenant1.localhost.com", 
    "role": "owner",
    "language": "ar",
    "language_name": "العربية"
  },
  "tenant": {
    "name": "Demo Tenant 1",
    "domain": "tenant1.localhost"  
  }
}
```

## 🔑 Test Credentials

**Default Password for all users**: `Restaurant@2025`

**Sample User Emails per Tenant**:
- **Owner (Arabic)**: owner@[domain].com
- **Manager (English)**: manager@[domain].com  
- **Accountant (Chinese)**: accountant@[domain].com
- **Operator (Hindi)**: operator@[domain].com

**Available Domains**: tenant1.localhost, tenant2.localhost, test.localhost, gorkha.localhost, secure.localhost

## ✅ Features to Verify

- [ ] Multi-tenant domain detection
- [ ] User authentication with tenant isolation
- [ ] Individual user language preferences (ar, en, zh, hi)
- [ ] Role-based user creation across tenants
- [ ] Database seeding with realistic data
- [ ] Session-based authentication with tenant guards
- [ ] Language preference persistence

## 🚀 Ready to Test!

Run these commands to start testing:

1. **Start Laravel Server**: `php artisan serve`
2. **Test API**: Use the curl commands above
3. **Verify Results**: Check the JSON responses match expected format

The system is ready with 300+ users across 15 tenants, each with proper language preferences as requested!