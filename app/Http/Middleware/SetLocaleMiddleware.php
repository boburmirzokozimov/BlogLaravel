<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Restrict to the locales you actually have
        $supported = config('app.supported_locales', [config('app.locale')]);

        // Use Symfony’s language negotiation via Request::getPreferredLanguage()
        $preferred = $request->getPreferredLanguage($supported);

        // Fallback if nothing matched
        $locale = $preferred ?: config('app.fallback_locale', 'en');

        app()->setLocale($locale);

        return $next($request);
    }
}
