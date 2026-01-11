<?php

namespace App\Infrastructure\Services;

use App\Application\Contracts\TransactionContract;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionService implements TransactionContract
{
    public function execute(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
