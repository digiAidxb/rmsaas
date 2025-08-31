<?php

namespace App\Tasks;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Spatie\Multitenancy\Contracts\IsTenant;
use Illuminate\Support\Facades\DB;

class SwitchTenantDatabaseTask implements SwitchTenantTask
{
    public function makeCurrent(IsTenant $tenant): void
    {
        // Configure the tenant database connection
        $tenantConnectionName = config('multitenancy.tenant_database_connection_name');
        
        // Get tenant-specific database config
        if (method_exists($tenant, 'getDatabaseConfig') && $tenant->db_username && $tenant->db_password) {
            $databaseConfig = $tenant->getDatabaseConfig();
        } else {
            $databaseConfig = [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $tenant->database,
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ];
        }
        
        // Set the tenant connection config
        config(["database.connections.{$tenantConnectionName}" => $databaseConfig]);
        
        // Purge the connection to ensure fresh config
        DB::purge($tenantConnectionName);
        
        // Switch the default database connection to tenant
        config(['database.default' => $tenantConnectionName]);
        
        // Clear any default connection cache
        DB::purge();
    }

    public function forgetCurrent(): void
    {
        // Switch back to landlord connection
        $landlordConnectionName = config('multitenancy.landlord_database_connection_name', 'landlord');
        config(['database.default' => $landlordConnectionName]);
        DB::purge();
    }
}
