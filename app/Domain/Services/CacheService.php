<?php

namespace App\Domain\Services;

interface CacheService
{
    /**
     * Store an item in the cache.
     */
    public function put(string $key, mixed $value, \DateTimeInterface|\DateInterval|int|null $ttl = null): bool;

    /**
     * Retrieve an item from the cache by key.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Remove an item from the cache.
     */
    public function forget(string $key): bool;
}
