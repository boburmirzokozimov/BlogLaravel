<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\OAuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']); // no middleware
Route::post('/register', [AuthController::class, 'register']); // no middleware

Route::middleware(['auth:api'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/auth/redirect/{provider}', [OAuthController::class, 'redirect']);
Route::get('/auth/callback/{provider}', [OAuthController::class, 'callback']);
