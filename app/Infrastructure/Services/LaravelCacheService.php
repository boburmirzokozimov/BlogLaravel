<?php

namespace App\Infrastructure\Services;

use App\Domain\Services\CacheService;
use Illuminate\Contracts\Cache\Repository;

final readonly class LaravelCacheService implements CacheService
{
    public function __construct(
        private Repository $cache
    ) {}

    public function put(string $key, mixed $value, \DateTimeInterface|\DateInterval|int|null $ttl = null): bool
    {
        return $this->cache->put($key, $value, $ttl);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->cache->get($key, $default);
    }

    public function forget(string $key): bool
    {
        return $this->cache->forget($key);
    }
}
