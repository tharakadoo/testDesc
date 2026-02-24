<?php

namespace App\Foundation\IO\ExternalServices;

use App\Foundation\UseCases\CacheContract;
use Illuminate\Support\Facades\Cache;

final class LaravelCacheService implements CacheContract
{
    public function remember(string $key, int $seconds, callable $callback): mixed
    {
        return Cache::remember($key, $seconds, $callback);
    }
}
