<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\V1\BlogPostController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']); // no middleware
Route::post('/register', [AuthController::class, 'register']); // no middleware

Route::middleware(['api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/auth/redirect/{provider}', [OAuthController::class, 'redirect']);
Route::get('/auth/callback/{provider}', [OAuthController::class, 'callback']);

// Blog Posts (Public endpoints)
Route::get('/blog-posts', [BlogPostController::class, 'index']);
Route::get('/blog-posts/{id}', [BlogPostController::class, 'show']);
Route::get('/blog-posts/slug/{slug}', [BlogPostController::class, 'showBySlug']);

// Blog Posts (Protected endpoints - require authentication)
Route::middleware(['api'])->group(function () {
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{id}', [BlogPostController::class, 'update']);
    Route::post('/blog-posts/{id}/publish', [BlogPostController::class, 'publish']);
    Route::post('/blog-posts/{id}/archive', [BlogPostController::class, 'archive']);
    Route::delete('/blog-posts/{id}', [BlogPostController::class, 'destroy']);
});
