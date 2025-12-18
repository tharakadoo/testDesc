<?php

namespace App\Post\Contracts;

interface EmailServiceContract
{
    /**
     * Send an email with given data.
     *
     * @param array $data ['to' => string, 'post' => \App\Post\Entities\Post]
     */
    public function send(array $data): void;
}
