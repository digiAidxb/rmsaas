# Redis Setup Instructions

## Install Redis on Windows

1. **Download Redis for Windows:**
   - Download from: https://github.com/microsoftarchive/redis/releases
   - Or use Windows Subsystem for Linux (WSL)
   - Alternative: Use Docker

2. **Using Docker (Recommended):**
   ```bash
   docker run --name redis-rmsaas -p 6379:6379 -d redis:alpine
   ```

3. **Using WSL:**
   ```bash
   sudo apt update
   sudo apt install redis-server
   sudo service redis-server start
   ```

4. **Test Connection:**
   ```bash
   redis-cli ping
   # Should return: PONG
   ```

## After Redis Installation

Update `.env` file:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

Then run:
```bash
php artisan config:clear
php artisan cache:clear
```

## Current Status

Redis is not currently installed on this system. The application is configured to fall back to database storage for:
- Sessions
- Cache
- Queues

This is acceptable for development but Redis should be installed for production performance.