# Claude AI Knowledge Base - RMSaaS Development

**Purpose:** Critical lessons and best practices for future Claude Code sessions
**Updated:** September 16, 2025
**Context:** Laravel Multi-tenant SaaS Development

## 🧠 Critical Knowledge for Future Claude Sessions

### **Database Integrity & Multi-Tenant Operations**

#### 🚨 **Always Verify Before Database Operations:**
```php
// CRITICAL: Check tenant context before any database operation
$tenant = app('currentTenant');
if (!$tenant) {
    throw new Exception('No tenant context available');
}

// CRITICAL: Verify tenant database connection works
try {
    DB::connection('tenant')->getPdo();
} catch (\Exception $e) {
    Log::error('Tenant database connection failed', ['tenant' => $tenant->id, 'error' => $e->getMessage()]);
    throw new TenantDatabaseException('Cannot connect to tenant database');
}

// CRITICAL: Check if required tables exist before operations
$requiredTables = ['import_jobs', 'import_mappings', 'users'];
foreach ($requiredTables as $table) {
    if (!Schema::connection('tenant')->hasTable($table)) {
        throw new Exception("Required table '{$table}' missing in tenant database");
    }
}
```

#### 🔑 **Tenant Credential Management:**
- **Never assume auto-generated tenant credentials work**
- **Always test database access after tenant creation**
- **Use landlord credentials for tenant databases when auto-generated ones fail**
- **Update tenant records with working credentials:**

```php
// Fix tenant database access issues
$tenant->update([
    'db_username' => config('database.connections.mysql.username'),
    'db_password' => config('database.connections.mysql.password'),
    'db_host' => config('database.connections.mysql.host'),
    'db_port' => config('database.connections.mysql.port')
]);
```

### **Service Provider Registration (Laravel 11+)**

#### 🛠 **Critical Change in Laravel 11:**
Laravel 11 uses `bootstrap/providers.php` instead of `config/app.php` for provider registration.

```php
// File: bootstrap/providers.php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ImportServiceProvider::class,  // ✅ Always register custom providers here
];
```

#### ⚠️ **Service Binding Best Practices:**
- **Always check if concrete classes exist before binding**
- **Provide fallback implementations for missing services**
- **Use factory patterns for dynamic service selection**

```php
// ✅ Safe service binding with fallbacks
$this->app->bind('import.inventory', function ($app) {
    // Use existing service as fallback if specific service doesn't exist
    return new \App\Services\Import\Services\MenuImportService(
        $app->make('App\Services\Import\Parsers\ExcelParser'),
        $app->make('App\Services\Import\Mappers\SmartFieldMapper'),
        $app->make('App\Services\Import\Validators\ImportValidationEngine'),
        'inventory'
    );
});
```

### **File Processing & Performance**

#### ⏱️ **Timeout Prevention:**
- **Always set execution limits for file operations:**
```php
set_time_limit(300); // 5 minutes for file processing
ini_set('memory_limit', '512M'); // Increase memory for large files
```

- **Defer heavy operations to background:**
```php
// ❌ Don't do heavy processing in HTTP requests
$parser->parseEntireFile($file); // This can timeout

// ✅ Create job and defer processing
$importJob = ImportJob::create([...]);
dispatch(new ProcessImportJob($importJob))->onQueue('imports');
```

#### 📁 **Dynamic Parser Selection:**
**Never hardcode parser types.** Always detect file format dynamically:

```php
// ✅ Dynamic parser factory implementation
$this->app->singleton('FileParserFactory', function ($app) {
    return new class {
        public function createParser($mimeType, $extension = null) {
            $excelMimes = [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
                'application/excel'
            ];

            if (in_array($mimeType, $excelMimes)) {
                return app('App\Services\Import\Parsers\ExcelParser');
            }

            return app('App\Services\Import\Parsers\CsvParser'); // Default fallback
        }
    };
});
```

### **Vue.js & Frontend Development**

#### 🚫 **Reserved Keyword Issues:**
**Never use JavaScript reserved words as Vue props:**

```vue
<!-- ❌ NEVER do this -->
<script setup>
const props = defineProps({
    import: Object,  // 'import' is reserved in JS
    export: Object,  // 'export' is reserved in JS
    class: String,   // 'class' is reserved in JS
})
</script>

<!-- ✅ Always use descriptive, non-reserved names -->
<script setup>
const props = defineProps({
    importJob: Object,
    exportData: Object,
    cssClass: String,
})
</script>
```

#### 🎨 **Layout File Verification:**
**Always verify layout files exist before importing:**

```bash
# Check existing layouts before importing
ls resources/js/Layouts/
# AuthenticatedLayout.vue  GuestLayout.vue  OnboardingLayout.vue

# ❌ Don't assume layouts exist
import MainLayout from '@/Layouts/MainLayout.vue'  // May not exist

# ✅ Use existing layouts
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
```

#### 🔄 **Vite Development Server Management:**
- **Always restart Vite after major component changes**
- **Kill and restart if compilation errors persist**
- **Clear browser cache when component changes don't appear**

### **Inertia.js Best Practices**

#### 📡 **Response Consistency:**
**All tenant routes must return Inertia responses, not Blade views:**

```php
// ❌ Don't mix Blade with Inertia
public function show($id) {
    return view('tenant.imports.show', compact('import')); // Wrong!
}

// ✅ Always use Inertia responses in Inertia apps
public function show($id) {
    return inertia('Imports/Show', [
        'importJob' => $import->toArray()
    ]);
}
```

#### 🔗 **Route Naming Consistency:**
- **Use consistent route naming:** `imports.show` not `tenant.imports.show`
- **Verify route exists before redirecting:** `route('imports.show', $id)`

### **Performance Optimization Strategies**

#### 🚀 **Upload Optimization:**
1. **Increase execution time only for file operations**
2. **Use minimal POS detection (filename-based)**
3. **Store files immediately, process later**
4. **Return success response quickly**

```php
// ✅ Optimized upload handling
public function store(Request $request) {
    set_time_limit(300); // Only for this operation

    // Quick file storage
    $importJob = ImportJob::create([...]);

    // Simple filename-based detection (fast)
    $posSystem = $this->detectPosFromFilename($file->getClientOriginalName());

    // Defer heavy processing
    dispatch(new ProcessImportJob($importJob));

    return response()->json(['success' => true, 'redirect' => route('imports.show', $importJob->id)]);
}
```

#### 💾 **Memory Management:**
- **Increase memory limits for file operations**
- **Use streaming for large files**
- **Clean up temporary files after processing**

### **Error Handling Patterns**

#### 🛡️ **Comprehensive Error Handling:**
```php
try {
    // Risky operation
    $result = $service->processFile($file);
} catch (DatabaseException $e) {
    Log::error('Database error during import', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Database connection failed'], 500);
} catch (FileFormatException $e) {
    Log::warning('Unsupported file format', ['file' => $file->getClientOriginalName()]);
    return response()->json(['error' => 'Unsupported file format'], 422);
} catch (\Exception $e) {
    Log::error('Unexpected error during import', ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Import processing failed'], 500);
}
```

## 🔧 Development Workflow Best Practices

### **Pre-Development Checklist:**
1. ✅ Verify tenant database connectivity
2. ✅ Check service provider registrations
3. ✅ Confirm layout files exist
4. ✅ Test file upload limits
5. ✅ Verify required tables exist

### **During Development:**
1. ✅ Use factory patterns for dynamic services
2. ✅ Implement proper error handling
3. ✅ Test with actual tenant data
4. ✅ Avoid JavaScript reserved keywords
5. ✅ Keep performance in mind

### **Post-Development:**
1. ✅ Test multi-tenant scenarios
2. ✅ Verify all file formats work
3. ✅ Check error handling paths
4. ✅ Test with large files
5. ✅ Document architecture decisions

## 🚨 Critical Debugging Commands

```bash
# Check tenant database connectivity
php artisan tinker --execute="DB::connection('tenant')->getPdo();"

# List available layouts
ls resources/js/Layouts/

# Verify service provider registration
grep -r "ImportServiceProvider" bootstrap/providers.php

# Check import job status
php artisan tinker --execute="App\Models\ImportJob::latest()->first();"

# Clear all caches
php artisan config:clear && php artisan cache:clear

# Restart Vite properly
killall node && npm run dev
```

## 📚 Architecture Patterns to Follow

### **Multi-Tenant Service Pattern:**
```php
class TenantAwareService {
    protected function ensureTenantContext() {
        if (!app('currentTenant')) {
            throw new NoTenantException();
        }
    }

    protected function getTenantConnection() {
        $this->ensureTenantContext();
        return DB::connection('tenant');
    }
}
```

### **File Processing Pipeline Pattern:**
```php
class ImportPipeline {
    public function process($file) {
        return Pipeline::create()
            ->send($file)
            ->through([
                ValidateFileFormat::class,
                DetectFileType::class,
                CreateImportJob::class,
                QueueProcessing::class,
            ])
            ->thenReturn();
    }
}
```

### **Service Factory Pattern:**
```php
class ServiceFactory {
    public function createFor($type, $context = []) {
        $serviceClass = $this->resolveServiceClass($type);
        $dependencies = $this->resolveDependencies($serviceClass, $context);

        return new $serviceClass(...$dependencies);
    }
}
```

---

## 🎯 Key Reminders for Future Claude Sessions

1. **ALWAYS check tenant database connectivity before operations**
2. **NEVER use JavaScript reserved words in Vue props**
3. **ALWAYS verify layout files exist before importing**
4. **ALWAYS set execution limits for file operations**
5. **ALWAYS use Inertia responses in Inertia applications**
6. **ALWAYS register service providers in Laravel 11's bootstrap/providers.php**
7. **ALWAYS implement fallback strategies for missing services**
8. **ALWAYS test multi-tenant scenarios thoroughly**

These patterns and practices will prevent 90% of the issues encountered during this development phase.