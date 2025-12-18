<?php

namespace App\Infrastructure\Services;

use App\Mail\PostPublishedMail;
use App\Post\Contracts\EmailServiceContract;
use Illuminate\Support\Facades\Mail;

final class LaravelEmailService implements EmailServiceContract
{
    public function send(array $data): void
    {
        $to = $data['to'];
        $post = $data['post'];

        Mail::to($to)->queue(new PostPublishedMail($post));
    }
}
