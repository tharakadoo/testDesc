<?php

namespace App\Foundation\UseCases;

interface CacheContract
{
    public function remember(string $key, int $seconds, callable $callback): mixed;
}
