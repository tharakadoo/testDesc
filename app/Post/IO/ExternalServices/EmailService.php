<?php

namespace App\Post\IO\ExternalServices;

use App\Post\UseCases\EmailServiceContract;
use Illuminate\Support\Facades\Mail;

class EmailService implements EmailServiceContract
{
    public function send(array $data): void
    {
        Mail::to($data['to'])->queue(new PostPublishedMail($data['post']));
    }
}
