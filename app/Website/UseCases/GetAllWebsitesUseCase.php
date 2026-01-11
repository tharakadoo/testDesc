<?php

namespace App\Website\UseCases;

use App\Website\Repositories\WebsiteRepositoryInterface;
use Illuminate\Support\Collection;

class GetAllWebsitesUseCase
{
    public function __construct(
        private WebsiteRepositoryInterface $website,
    ) {}

    public function execute(): Collection
    {
        return $this->website->all();
    }
}
