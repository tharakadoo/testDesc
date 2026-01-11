<?php

namespace App\Infrastructure\Services;

use App\Application\Contracts\CacheContract;
use Illuminate\Support\Facades\Cache;

final class LaravelCacheService implements CacheContract
{
    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        return Cache::remember($key, $seconds, $callback);
    }
}
