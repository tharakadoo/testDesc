<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SubscriptionController;

Route::post('/websites/{website}/posts', [PostController::class, 'store']);
Route::post('/websites/{website}/subscribe', [SubscriptionController::class, 'store']);
