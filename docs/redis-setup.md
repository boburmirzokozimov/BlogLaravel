# Redis Setup Guide

This document describes the Redis configuration for the BlogLaravel application.

---

## Overview

Redis is configured as a caching and session storage backend for the application. It provides fast, in-memory data storage for improved performance.

---

## Docker Configuration

### Redis Service

The Redis service is defined in `docker-compose.yml`:

```yaml
redis:
  image: redis:7-alpine
  container_name: blog-redis
  restart: unless-stopped
  command: redis-server --appendonly yes --requirepass "${REDIS_PASSWORD:-}"
  volumes:
    - redisdata:/data
  ports:
    - "6379:6379"
  networks:
    - blog-network
  healthcheck:
    test: ["CMD", "redis-cli", "ping"]
    interval: 10s
    timeout: 5s
    retries: 5
```

**Features:**
- Uses Redis 7 Alpine (lightweight)
- Persistence enabled with AOF (Append Only File)
- Optional password protection via environment variable
- Health checks for container monitoring
- Data persisted in named volume

---

## Laravel Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Cache Driver
CACHE_STORE=redis

# Redis Connection
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
```

**Variable Descriptions:**

| Variable | Description | Default |
|----------|-------------|---------|
| `CACHE_STORE` | Default cache driver | `redis` |
| `REDIS_CLIENT` | PHP Redis client (`phpredis` or `predis`) | `phpredis` |
| `REDIS_HOST` | Redis server hostname | `redis` (Docker service name) |
| `REDIS_PASSWORD` | Redis password (optional) | `null` |
| `REDIS_PORT` | Redis server port | `6379` |
| `REDIS_DB` | Default Redis database | `0` |
| `REDIS_CACHE_DB` | Cache-specific Redis database | `1` |

### PHP Extension

The `phpredis` extension is installed in both Docker containers:

- **php-fpm/Dockerfile**
- **php-cli/Dockerfile**

```dockerfile
# Install Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis
```

---

## Usage

### Starting Redis

```bash
# Start all services including Redis
docker-compose up -d

# Start only Redis
docker-compose up -d redis

# Check Redis status
docker-compose ps redis
```

### Testing Connection

Use the built-in Artisan command to test Redis connectivity:

```bash
# Via docker-compose
docker-compose exec php-cli php artisan redis:test

# Or if inside the container
php artisan redis:test
```

Expected output:
```
Testing Redis connection...
✓ Redis connection successful!
✓ SET operation successful: test:connection:1234567890
✓ GET operation successful: Hello from Laravel
✓ DEL operation successful

Redis Configuration:
+-----------+--------+
| Setting   | Value  |
+-----------+--------+
| Host      | redis  |
| Port      | 6379   |
| Database  | 0      |
| Client    | phpredis |
+-----------+--------+

✓ All Redis tests passed successfully!
```

### Using Redis in Laravel

#### Caching

```php
use Illuminate\Support\Facades\Cache;

// Store data in cache
Cache::put('key', 'value', 3600); // 1 hour

// Retrieve from cache
$value = Cache::get('key');

// Remember pattern
$users = Cache::remember('users', 3600, function () {
    return User::all();
});

// Cache tags (Redis/Memcached only)
Cache::tags(['users', 'admins'])->put('key', 'value', 3600);
Cache::tags(['users'])->flush();
```

#### Direct Redis Access

```php
use Illuminate\Support\Facades\Redis;

// Basic operations
Redis::set('name', 'John Doe');
$name = Redis::get('name');

// With expiration
Redis::setex('session:123', 3600, 'data');

// Lists
Redis::lpush('queue', 'job1');
Redis::rpop('queue');

// Hashes
Redis::hset('user:1', 'name', 'John');
Redis::hget('user:1', 'name');

// Sets
Redis::sadd('tags', 'php', 'laravel', 'redis');
Redis::smembers('tags');

// Pub/Sub
Redis::publish('channel', 'message');
Redis::subscribe(['channel'], function ($message) {
    echo $message;
});
```

#### Session Storage

To use Redis for sessions, update `config/session.php`:

```php
'driver' => env('SESSION_DRIVER', 'redis'),
'connection' => env('SESSION_CONNECTION', 'default'),
```

And add to `.env`:

```env
SESSION_DRIVER=redis
SESSION_CONNECTION=default
```

#### Queue Driver

To use Redis for queues, update `.env`:

```env
QUEUE_CONNECTION=redis
```

Then dispatch jobs as normal:

```php
use App\Jobs\ProcessOrder;

ProcessOrder::dispatch($order);
```

---

## Monitoring

### Redis CLI

Access Redis CLI inside the container:

```bash
# Enter Redis container
docker-compose exec redis redis-cli

# Common commands
> PING                    # Test connection
> INFO                    # Server information
> DBSIZE                  # Number of keys
> KEYS *                  # List all keys (avoid in production)
> FLUSHDB                 # Clear current database
> FLUSHALL                # Clear all databases
> MONITOR                 # Watch all commands in real-time
```

### Laravel Horizon (Optional)

For advanced queue monitoring with Redis:

```bash
composer require laravel/horizon
php artisan horizon:install
php artisan horizon
```

---

## Performance Tips

1. **Use Cache Tags**: Group related cache entries for efficient clearing
2. **Set Expiration**: Always set TTL to prevent memory bloat
3. **Use Pipelines**: Batch multiple Redis commands
4. **Monitor Memory**: Use `INFO memory` in Redis CLI
5. **Use Appropriate Data Structures**: Choose the right Redis data type for your use case

### Pipelining Example

```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```

---

## Troubleshooting

### Connection Refused

**Error**: `Connection refused [tcp://redis:6379]`

**Solutions**:
1. Ensure Redis container is running: `docker-compose ps redis`
2. Check REDIS_HOST in .env matches service name
3. Restart containers: `docker-compose restart`

### Extension Not Found

**Error**: `Class 'Redis' not found`

**Solutions**:
1. Rebuild Docker images: `docker-compose build --no-cache`
2. Verify extension is enabled: `php -m | grep redis`
3. Check REDIS_CLIENT is set to `phpredis`

### Memory Exhausted

**Error**: Redis running out of memory

**Solutions**:
1. Set maxmemory policy in Redis config
2. Set appropriate TTL on cached items
3. Use `FLUSHDB` to clear old data
4. Increase Redis container memory limit

### Password Authentication

If you set a Redis password:

```bash
# In .env
REDIS_PASSWORD=your_secure_password

# In docker-compose.yml, command is already configured:
command: redis-server --appendonly yes --requirepass "${REDIS_PASSWORD:-}"
```

---

## Production Considerations

1. **Persistence**: AOF is enabled for data persistence
2. **Security**: Use password authentication in production
3. **Backup**: Redis data is in named volume `redisdata`
4. **Monitoring**: Set up monitoring and alerts
5. **Scaling**: Consider Redis Cluster for high availability
6. **Network**: Keep Redis on private network, not exposed publicly

### Backup Redis Data

```bash
# Manual backup
docker-compose exec redis redis-cli BGSAVE

# Copy RDB file
docker cp blog-redis:/data/dump.rdb ./backup/

# Restore
docker cp ./backup/dump.rdb blog-redis:/data/dump.rdb
docker-compose restart redis
```

---

## Additional Resources

- [Laravel Cache Documentation](https://laravel.com/docs/cache)
- [Laravel Redis Documentation](https://laravel.com/docs/redis)
- [Redis Official Documentation](https://redis.io/documentation)
- [Redis Commands Reference](https://redis.io/commands)
- [Redis Best Practices](https://redis.io/topics/best-practices)

---

## Summary

✅ Redis service configured in Docker  
✅ phpredis extension installed  
✅ Laravel cache driver set to Redis  
✅ Connection test command available  
✅ Persistent storage configured  
✅ Health checks enabled  

Your Redis setup is complete and ready to use!

