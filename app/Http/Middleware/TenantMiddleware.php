<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        
        $tenant = Tenant::on('landlord')
            ->where('domain', $host)
            ->where('is_active', true)
            ->first();

        if (! $tenant) {
            abort(404, 'Tenant not found');
        }

        // Configure tenant database connection with specific credentials
        if ($tenant->db_username && $tenant->db_password) {
            config(['database.connections.tenant' => $tenant->getDatabaseConfig()]);
        } else {
            // Fallback for tenants without specific credentials (backwards compatibility)
            config(['database.connections.tenant' => [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $tenant->database,
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]]);
        }
        
        // Clear database connection cache
        \DB::purge('tenant');
        
        // Store current tenant for the application
        app()->instance('currentTenant', $tenant);

        $tenant->makeCurrent();

        return $next($request);
    }
}
