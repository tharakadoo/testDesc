<?php

namespace App\Post\IO\ExternalServices;

use App\Post\UseCases\EmailServiceContract;
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
