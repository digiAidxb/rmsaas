<?php

namespace App\Logging;

use Illuminate\Log\LogManager;
use Spatie\Multitenancy\Models\Tenant;

class TenantLogChannel
{
    /**
     * Create a custom Monolog instance for tenant-aware logging.
     */
    public function __invoke(array $config)
    {
        $tenant = Tenant::current();
        $tenantId = $tenant ? $tenant->id : 'unknown';
        
        return (new LogManager(app()))->build([
            'driver' => 'daily',
            'path' => storage_path("logs/tenant_{$tenantId}.log"),
            'level' => $config['level'] ?? 'debug',
            'days' => $config['days'] ?? 14,
            'replace_placeholders' => true,
        ]);
    }
}