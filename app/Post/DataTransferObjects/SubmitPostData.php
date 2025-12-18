<?php

namespace App\Post\DataTransferObjects;

use Illuminate\Validation\ValidationException;

class SubmitPostData
{
    public function __construct(
        public string $title,
        public string $description,
        public int $website_id,
    ) {}

    /**
     * @throws ValidationException
     */
    public static function fromArray(array $data): self
    {
        if (empty($data['title'])) {
            throw ValidationException::withMessages(['title' => ['Title is required']]);
        }

        if (empty($data['description'])) {
            throw ValidationException::withMessages(['description' => ['Description is required']]);
        }

        if (empty($data['website_id'])) {
            throw ValidationException::withMessages(['website_id' => ['Website is required']]);
        }

        return new self(
            title: $data['title'],
            description: $data['description'],
            website_id: $data['website_id'],
        );
    }
}
