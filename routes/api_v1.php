<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OAuthController;
use App\Http\Controllers\Api\V1\BlogPostController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']); // no middleware
Route::post('/register', [AuthController::class, 'register']); // no middleware
Route::get('/email/verify/{token}', [AuthController::class, 'verifyEmail'])
    ->name('api.v1.email.verify');

Route::middleware(['auth:api', 'verified'])->group(function () {
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

// Blog Posts (Protected endpoints - require authentication and email verification)
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('/blog-posts', [BlogPostController::class, 'store']);
    Route::put('/blog-posts/{id}', [BlogPostController::class, 'update']);
    Route::post('/blog-posts/{id}/publish', [BlogPostController::class, 'publish']);
    Route::post('/blog-posts/{id}/archive', [BlogPostController::class, 'archive']);
    Route::delete('/blog-posts/{id}', [BlogPostController::class, 'destroy']);
});

// Tags (Public endpoints)
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);
Route::get('/tags/slug/{slug}', [TagController::class, 'showBySlug']);

// Tags (Protected endpoints - require authentication and email verification)
Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::post('/tags', [TagController::class, 'store']);
    Route::put('/tags/{id}', [TagController::class, 'update']);
    Route::delete('/tags/{id}', [TagController::class, 'destroy']);
});
