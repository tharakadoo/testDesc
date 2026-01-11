<?php

namespace App\Application\Contracts;

interface TransactionContract
{
    public function execute(callable $callback): mixed;
}
