<?php

use Illuminate\Support\Facades\Route;
use App\Post\IO\Http\Controllers\PostController;
use App\Website\IO\Http\Controllers\SubscriptionController;
use App\Website\IO\Http\Controllers\WebsiteController;

Route::get('/websites', [WebsiteController::class, 'index']);
Route::post('/websites/{website}/posts', [PostController::class, 'store']);
Route::post('/websites/{website}/subscribe', [SubscriptionController::class, 'store']);
