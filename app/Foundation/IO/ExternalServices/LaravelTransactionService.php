<?php

namespace App\Foundation\IO\ExternalServices;

use App\Foundation\UseCases\TransactionContract;
use Illuminate\Support\Facades\DB;

final class LaravelTransactionService implements TransactionContract
{
    public function execute(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
