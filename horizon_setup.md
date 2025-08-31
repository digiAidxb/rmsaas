# Laravel Horizon Setup Notes

## Issue: Missing Extensions on Windows

Laravel Horizon requires PCNTL and POSIX extensions which are not available on Windows PHP installations.

## Solutions:

### Option 1: Use Laravel Pulse (Recommended for Windows)
```bash
composer require laravel/pulse
```
Laravel Pulse provides application performance monitoring without requiring PCNTL.

### Option 2: Use WSL/Docker
- Run Laravel Horizon in WSL or Docker where PCNTL is available
- Keep main application on Windows

### Option 3: Alternative Queue Monitoring
- Use database-based queue monitoring
- Implement custom queue status dashboard
- Use third-party queue monitoring tools

## Current Configuration

For now, we'll continue with database queue driver and implement basic queue monitoring through:
1. Built-in Laravel queue commands
2. Custom queue status endpoints
3. Database-based job tracking

## Queue Management Commands

```bash
# Start queue worker
php artisan queue:work

# Monitor failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## Next Steps

1. Create custom queue monitoring dashboard
2. Set up queue workers as Windows services
3. Implement job status tracking in database
4. Consider Docker setup for full Horizon functionality