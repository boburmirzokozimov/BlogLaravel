<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceJsonResponseMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Only force JSON for API routes, not admin routes
        if ($request->is('api/*') && !$request->is('admin/*')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
