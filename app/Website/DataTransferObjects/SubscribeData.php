<?php

namespace App\Website\DataTransferObjects;

use Illuminate\Validation\ValidationException;

class SubscribeData
{
    public function __construct(
        public string $email,
        public int $website_id,
    ) {}

    public static function fromArray(array $data): self
    {
        if (empty($data['email'])) {
            throw ValidationException::withMessages(['email' => ['Email is required']]);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages(['email' => ['Email must be valid']]);
        }

        if (empty($data['website_id'])) {
            throw ValidationException::withMessages(['website_id' => ['Website is required']]);
        }

        return new self(
            email: $data['email'],
            website_id: $data['website_id'],
        );
    }
}
