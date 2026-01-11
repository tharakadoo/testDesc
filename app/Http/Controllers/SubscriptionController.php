<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website\Entities\Website;
use Illuminate\Http\JsonResponse;
use App\Website\UseCases\SubscribeUseCase;

class SubscriptionController extends Controller
{
    public function __construct(
        private SubscribeUseCase $subscribe,
    ) {}

    public function store(Request $request, Website $website): JsonResponse
    {
        $result = $this->subscribe->execute([
            'email' => $request->input('email'),
            'website_id' => $website->id,
        ]);

        return response()->json([
            'message' => 'Subscribed successfully',
            'subscription' => [
                'email' => $result['user']->email,
                'website_url' => $result['website']->url,
            ],
        ], 201);
    }
}
