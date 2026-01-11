<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Website\UseCases\GetAllWebsitesUseCase;

class WebsiteController extends Controller
{
    public function __construct(
        private GetAllWebsitesUseCase $getAllWebsites,
    ) {}

    public function index(): JsonResponse
    {
        $websites = $this->getAllWebsites->execute();

        return response()->json([
            'websites' => $websites->map(fn($website) => [
                'id' => $website->id,
                'url' => $website->url,
            ]),
        ]);
    }
}
