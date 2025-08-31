<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TenantRegistrationController extends Controller
{
    public function create()
    {
        $countries = Country::on('landlord')->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'currency_code']);

        return Inertia::render('TenantRegistration', [
            'countries' => $countries,
        ]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:255|unique:landlord.tenants,name',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|unique:landlord.users,email',
            'password' => 'required|string|min:8|confirmed',
            'country_id' => 'required|exists:landlord.countries,id',
            'city' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'business_address' => 'nullable|string|max:500',
            'business_type' => 'required|string|in:Fine Dining,Casual Dining,Fast Food,Cafe,Bar & Grill,Pizza,Asian,Italian,Mexican,Other',
            'captcha_verified' => 'required|accepted', // Simple captcha verification
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Additional cross-validation to prevent partial creation
        // Check on landlord database explicitly
        $existingTenant = Tenant::on('landlord')->where('name', $request->restaurant_name)->first();
        $existingUser = User::on('landlord')->where('email', $request->email)->first();
        
        if ($existingTenant) {
            return back()->withErrors(['restaurant_name' => 'A restaurant with this name already exists.'])->withInput();
        }
        
        if ($existingUser) {
            return back()->withErrors(['email' => 'This email address is already registered.'])->withInput();
        }

        // Additional validation: Check if email is associated with any tenant owner
        $existingOwner = User::on('landlord')
            ->where('email', $request->email)
            ->where('role', 'owner')
            ->first();
        
        if ($existingOwner) {
            return back()->withErrors(['email' => 'This email is already registered as a restaurant owner.'])->withInput();
        }

        try {
            // Get country details before transaction
            $country = Country::on('landlord')->find($request->country_id);
            $currencyCode = $country ? $country->currency_code : 'USD';

            DB::beginTransaction();

            // Generate unique subdomain
            $subdomain = $this->generateUniqueSubdomain($request->restaurant_name, $request->city);
            
            // Generate database name
            $databaseName = 'rmsaas_' . $subdomain;

            // Create tenant on landlord database
            $tenant = Tenant::on('landlord')->create([
                'name' => $request->restaurant_name,
                'domain' => $subdomain . '.rmsaas.local',
                'database' => $databaseName,
                'contact_person' => $request->contact_person,
                'phone' => $request->phone,
                'business_address' => $request->business_address,
                'city' => $request->city,
                'country_id' => $request->country_id,
                'business_type' => $request->business_type,
                'status' => 'pending',
                'is_active' => true,
                'trial_ends_at' => now()->addDays(30), // 30-day trial
                'settings' => json_encode([
                    'timezone' => 'UTC',
                    'currency' => $currencyCode,
                    'date_format' => 'Y-m-d',
                    'time_format' => '24h'
                ]),
            ]);

            // Create owner user on landlord database
            $user = User::on('landlord')->create([
                'name' => $request->contact_person,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'tenant_id' => $tenant->id,
                'role' => 'owner',
                'preferred_language' => 'en', // Default to English, can be changed later
                'email_verified_at' => now(), // Auto-verify for localhost testing
            ]);

            DB::commit();

            // Create tenant database outside the transaction to avoid lock timeouts
            try {
                $this->createTenantDatabase($tenant);
                \Log::info('Database created successfully for tenant: ' . $tenant->name);
            } catch (\Exception $e) {
                \Log::error('Database creation failed for tenant: ' . $tenant->name . ' - ' . $e->getMessage());
                // Database creation failed, but tenant record is created
            }

            // Send welcome email (to be implemented)
            // Mail::to($user->email)->send(new TenantWelcome($tenant, $user));

            return redirect()->route('tenant.registration.success', ['tenant' => $tenant->id])
                ->with('success', 'Registration successful! Your restaurant tenant has been created.');

        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }

    public function success($tenantId)
    {
        $tenant = Tenant::findOrFail($tenantId);
        
        return Inertia::render('TenantRegistrationSuccess', [
            'tenant' => [
                'name' => $tenant->name,
                'domain' => $tenant->domain,
                'login_url' => 'http://' . $tenant->domain . ':8000',
                'status' => $tenant->status,
            ],
        ]);
    }

    private function generateUniqueSubdomain($restaurantName, $city)
    {
        // Clean restaurant name
        $cleanName = Str::slug($restaurantName);
        
        // Get 3-letter city code
        $cityCode = strtolower(substr(preg_replace('/[^a-zA-Z]/', '', $city), 0, 3));
        
        // Create base subdomain
        $baseSubdomain = $cleanName . $cityCode;
        
        // Ensure uniqueness
        $counter = 1;
        $subdomain = $baseSubdomain;
        
        while (Tenant::on('landlord')->where('domain', 'like', $subdomain . '.%')->exists()) {
            $subdomain = $baseSubdomain . $counter;
            $counter++;
        }
        
        return $subdomain;
    }

    public function autoLogin($token)
    {
        try {
            $data = json_decode(base64_decode($token), true);
            
            if (!$data || !isset($data['user_id'], $data['tenant_id'], $data['expires'], $data['signature'])) {
                return redirect()->route('home')->withErrors(['error' => 'Invalid login token']);
            }
            
            // Check if token has expired
            if (time() > $data['expires']) {
                return redirect()->route('home')->withErrors(['error' => 'Login token has expired']);
            }
            
            // Verify signature
            $expectedSignature = hash_hmac('sha256', $data['user_id'] . $data['tenant_id'], config('app.key'));
            if (!hash_equals($expectedSignature, $data['signature'])) {
                return redirect()->route('home')->withErrors(['error' => 'Invalid login token signature']);
            }
            
            // Get tenant and user
            $tenant = Tenant::find($data['tenant_id']);
            $user = User::find($data['user_id']);
            
            if (!$tenant || !$user) {
                return redirect()->route('home')->withErrors(['error' => 'Invalid tenant or user']);
            }
            
            // Redirect to tenant auto-login endpoint
            $loginToken = base64_encode(json_encode([
                'user_id' => $user->id,
                'tenant_id' => $tenant->id,
                'expires' => time() + (30 * 60), // 30 minutes
                'signature' => hash_hmac('sha256', $user->id . $tenant->id, config('app.key'))
            ]));
            
            $redirectUrl = "http://{$tenant->domain}:8000/auto-login/{$loginToken}";
            
            return redirect()->away($redirectUrl);
            
        } catch (\Exception $e) {
            return redirect()->route('home')->withErrors(['error' => 'Auto-login failed: ' . $e->getMessage()]);
        }
    }

    private function createTenantDatabase(Tenant $tenant)
    {
        try {
            // Generate database credentials
            $credentials = $tenant->generateDatabaseCredentials();
            
            // Use a separate transaction for database credentials update
            DB::beginTransaction();
            $tenant->update([
                'db_username' => $credentials['username'],
                'db_password' => $credentials['password'],
                'db_host' => '127.0.0.1',
                'db_port' => 3306,
            ]);
            DB::commit();

            // Create database
            DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$tenant->database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Set tenant as current and run migrations
            $tenant->makeCurrent();
            \Artisan::call('migrate', ['--force' => true]);
            
            // Initialize onboarding
            $tenant->initializeOnboarding();
            
            return true;
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            throw new \Exception('Failed to create tenant database: ' . $e->getMessage());
        }
    }
}