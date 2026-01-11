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
        $post = $this->postSubmit->execute([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'website_id' => $website->id,
        ]);

        return response()->json([
            'message' => 'Post created successfully',
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'website_id' => $post->website_id,
            ],
        ], 201);
    }
}
