<?php

namespace App\Foundation\UseCases;

interface TransactionContract
{
    public function execute(callable $callback): mixed;
}
