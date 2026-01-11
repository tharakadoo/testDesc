<?php

namespace App\Infrastructure\Email;

use App\Post\Contracts\EmailServiceContract;
use App\Mail\PostPublishedMail;
use Illuminate\Support\Facades\Mail;

class EmailService implements EmailServiceContract
{
    public function send(array $data): void
    {
        Mail::to($data['to'])->queue(new PostPublishedMail($data['post']));
    }
}
