<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Models\Tenant;
use Symfony\Component\HttpFoundation\Response;

class LoggingMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        // Log request start
        $this->logRequest($request);
        
        $response = $next($request);
        
        // Log response and performance
        $this->logResponse($request, $response, $startTime);
        
        return $response;
    }

    private function logRequest(Request $request): void
    {
        $tenant = Tenant::current();
        
        Log::channel('tenant_daily')->info('Request started', [
            'tenant_id' => $tenant?->id,
            'tenant_domain' => $tenant?->domain,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function logResponse(Request $request, Response $response, float $startTime): void
    {
        $duration = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
        $tenant = Tenant::current();
        
        $context = [
            'tenant_id' => $tenant?->id,
            'tenant_domain' => $tenant?->domain,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'status_code' => $response->getStatusCode(),
            'duration_ms' => round($duration, 2),
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
        ];

        // Log to appropriate channels based on status code and performance
        if ($response->getStatusCode() >= 500) {
            Log::channel('system_errors')->error('Server error response', $context);
        } elseif ($response->getStatusCode() >= 400) {
            Log::channel('tenant_daily')->warning('Client error response', $context);
        } else {
            Log::channel('tenant_daily')->info('Request completed', $context);
        }

        // Log slow requests to performance channel
        if ($duration > 1000) { // Over 1 second
            Log::channel('performance')->warning('Slow request detected', $context);
        }
    }
}