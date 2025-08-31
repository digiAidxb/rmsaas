<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;

class SecurityLogger
{
    public static function logSecurityEvent(string $event, array $context = []): void
    {
        $tenant = Tenant::current();
        
        $securityContext = array_merge($context, [
            'tenant_id' => $tenant?->id,
            'tenant_domain' => $tenant?->domain,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]);

        Log::channel('security')->warning("Security Event: {$event}", $securityContext);
    }

    public static function logFailedLogin(string $email, string $reason = 'Invalid credentials'): void
    {
        self::logSecurityEvent('Failed Login Attempt', [
            'email' => $email,
            'reason' => $reason,
            'severity' => 'medium',
        ]);
    }

    public static function logSuspiciousActivity(string $activity, array $details = []): void
    {
        self::logSecurityEvent('Suspicious Activity', array_merge([
            'activity' => $activity,
            'severity' => 'high',
        ], $details));
    }

    public static function logDataAccess(string $resourceType, int $resourceId, string $action): void
    {
        self::logSecurityEvent('Data Access', [
            'resource_type' => $resourceType,
            'resource_id' => $resourceId,
            'action' => $action,
            'severity' => 'low',
        ]);
    }

    public static function logPermissionViolation(string $permission, string $resource = null): void
    {
        self::logSecurityEvent('Permission Violation', [
            'permission' => $permission,
            'resource' => $resource,
            'severity' => 'high',
        ]);
    }

    public static function logPasswordChange(): void
    {
        self::logSecurityEvent('Password Changed', [
            'severity' => 'medium',
        ]);
    }

    public static function logTwoFactorEnabled(): void
    {
        self::logSecurityEvent('Two Factor Authentication Enabled', [
            'severity' => 'low',
        ]);
    }

    public static function logTwoFactorDisabled(): void
    {
        self::logSecurityEvent('Two Factor Authentication Disabled', [
            'severity' => 'medium',
        ]);
    }
}