<?php

namespace App\Application\Contracts;

interface CacheContract
{
    public function remember(string $key, int $seconds, callable $callback): mixed;
}
