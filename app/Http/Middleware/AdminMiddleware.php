<?php

namespace App\Http\Middleware;

use App\Shared\Exceptions\Forbidden;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('admin.login');
        }

        if (!$request->user()->isAdmin()) {
            // For Inertia/web requests, redirect back with error message
            if ($request->header('X-Inertia')) {
                $redirect = $request->header('Referer')
                    ? redirect()->back()
                    : redirect()->route('admin.dashboard');

                return $redirect->withErrors([
                    'permission' => [
                        [
                            'en' => __('errors.do_not_have_permission', [], 'en'),
                            'ru' => __('errors.do_not_have_permission', [], 'ru'),
                        ],
                    ],
                ]);
            }

            // For API requests, throw exception (will be handled by exception handler)
            throw new Forbidden('errors.do_not_have_permission');
        }

        return $next($request);
    }
}
