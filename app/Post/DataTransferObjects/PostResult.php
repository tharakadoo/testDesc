<?php

namespace App\Post\DataTransferObjects;

final readonly class PostResult
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public int $website_id,
    ) {}
}
