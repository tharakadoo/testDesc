<?php

namespace App\Infrastructure\Email;

use App\Application\Contracts\EmailServiceContract;
use App\Mail\PostPublishedMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailService implements EmailServiceContract
{
    public function send(array $data): bool
    {
        try {
            Mail::to($data['to'])->queue(new PostPublishedMail($data['post']));
            return true;
        } catch (\Exception $e) {
            Log::error('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }
}
