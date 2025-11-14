<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => [
                    'en' => 'Unauthenticated',
                    'ru' => 'Необходима авторизация',
                ],
            ], 401);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => [
                    'en' => 'Please verify your email address to continue.',
                    'ru' => 'Пожалуйста, подтвердите ваш email адрес для продолжения.',
                ],
                'data' => [
                    'email_verified' => false,
                    'email' => $user->email,
                ],
            ], 403);
        }

        return $next($request);
    }
}
