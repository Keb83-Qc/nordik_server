<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Ajoute des headers Cache-Control sur les pages publiques (GET uniquement).
 * Permet aux navigateurs et proxies de mettre les pages en cache 10 minutes.
 */
class PublicPageCache
{
    // Pages qui NE doivent PAS être mises en cache
    private const NO_CACHE_ROUTES = [
        'login',
        'login.post',
        'logout',
        'register.ajax',
        'contact.send',
        'quote.*',
        'consent.*',
        'abf.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Seulement sur GET/HEAD, réponses 200, pages non-auth
        if (
            ! $request->isMethod('GET')
            || $response->getStatusCode() !== 200
            || auth()->check()
            || $this->isNoCacheRoute($request)
        ) {
            return $response;
        }

        $response->headers->set('Cache-Control', 'public, max-age=600, s-maxage=600, stale-while-revalidate=60');

        return $response;
    }

    private function isNoCacheRoute(Request $request): bool
    {
        foreach (self::NO_CACHE_ROUTES as $pattern) {
            if ($request->routeIs($pattern)) {
                return true;
            }
        }
        return false;
    }
}
