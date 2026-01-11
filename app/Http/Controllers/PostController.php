<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Website\Entities\Website;
use Illuminate\Http\JsonResponse;
use App\Post\UseCases\PostSubmitUseCase;

class PostController extends Controller
{
    public function __construct(
        private PostSubmitUseCase $postSubmit,
    ) {}

    public function store(Request $request, Website $website): JsonResponse
    {
        $result = $this->postSubmit->execute([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'website_id' => $website->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => [
                'id' => $result->id,
                'title' => $result->title,
                'description' => $result->description,
                'website_id' => $result->website_id,
            ],
        ], 201);
    }
}
