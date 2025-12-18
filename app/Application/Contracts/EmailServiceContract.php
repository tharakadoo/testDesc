<?php

namespace App\Application\Contracts;

interface EmailServiceContract
{
    public function send(array $data): bool;
}
