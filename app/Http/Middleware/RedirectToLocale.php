<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;

/**
 * Redirige les URLs sans préfixe locale vers leur version localisée.
 *
 * Ex: /about → /fr/about, /conseiller/jean → /fr/conseiller/jean
 *
 * Remplace ~30 routes legacy hardcodées dans web.php. Seules les URLs
 * listées dans $passthrough sont laissées telles quelles (routes techniques,
 * zones privées, etc.).
 */
class RedirectToLocale
{
    /**
     * URLs qui doivent rester hors locale (préfixes, exacts, ou regex).
     * Ne préfixe PAS ces chemins avec la locale.
     */
    private const PASSTHROUGH_PREFIXES = [
        '/',                  // racine — gérée par WelcomeController
        'admin',              // panel Filament
        'espace-conseiller',  // panel Filament
        'livewire',           // Livewire internals
        'filament',           // Filament internals
        '_debugbar',          // Debugbar
        '_ignition',          // Ignition
        'sanctum',            // Sanctum
        'up',                 // health check
        'sitemap.xml',
        'robots.txt',
        'favicon.ico',
        '2fa',                // 2FA (auth only)
        'switch-language',    // change de langue
        'log-js-error',
        'log-web-vitals',
        'abf',                // raccourci ABF
    ];

    public function handle(Request $request, Closure $next)
    {
        $path = trim($request->getPathInfo(), '/');

        // Racine ou chemin vide → laisser passer (WelcomeController)
        if ($path === '') {
            return $next($request);
        }

        $first = explode('/', $path)[0];

        // Si le premier segment est une locale active → laisser passer
        $activeCodes = Language::activeCodes();
        if (in_array($first, $activeCodes, true)) {
            return $next($request);
        }

        // Si c'est un chemin "technique" → laisser passer
        foreach (self::PASSTHROUGH_PREFIXES as $prefix) {
            if ($first === $prefix || str_starts_with($path, $prefix . '/')) {
                return $next($request);
            }
        }

        // URLs de conseiller avec intake/liste-bilan → laisser passer (routes advisorSlug)
        // Pattern: {slug}/intake/..., {slug}/liste-bilan/...
        if (preg_match('#^[a-z0-9\-]+/(intake|liste-bilan)(/|$)#', $path)) {
            return $next($request);
        }

        // Sinon → redirection 301 vers la version localisée
        $locale = session('locale') ?: Language::defaultCode() ?: 'fr';
        $query = $request->getQueryString();
        $target = "/{$locale}/{$path}" . ($query ? "?{$query}" : '');

        return redirect($target, 301);
    }
}
