<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Full-Page Cache — sauvegarde le HTML rendu dans storage/page-cache/
 *
 * Le fichier est ensuite servi directement par index.php AVANT de
 * booter Laravel (0 providers, 0 DB, 0 Blade = ~5ms au lieu de 1700ms).
 *
 * Exclut automatiquement :
 *   - Les requêtes non-GET
 *   - Les utilisateurs connectés (admin, conseiller, etc.)
 *   - Les pages avec session spéciale (consentement, etc.)
 *   - Les routes Filament (/admin, /abf, /conseiller)
 *   - Les routes login/logout/contact/quote
 */
class FullPageCache
{
    /** Routes exclues du cache (name patterns) */
    private const EXCLUDED_ROUTES = [
        'login*',
        'logout',
        'register*',
        'contact*',
        'quote.*',
        'consent.*',
        'abf.*',
        'access.*',
    ];

    /** Préfixes d'URI exclus */
    private const EXCLUDED_PREFIXES = [
        '/admin',
        '/espace-conseiller',
        '/abf',
        '/conseiller',
        '/livewire',
        '/switch-language',
    ];

    /** Durée du cache en secondes (10 min) */
    private const CACHE_TTL = 600;

    public function handle(Request $request, Closure $next): Response
    {
        // Seulement GET/HEAD
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        // Exclure les routes admin et spéciales
        $uri = $request->getPathInfo();
        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($uri, $prefix)) {
                return $next($request);
            }
        }

        // Exclure les routes nommées spéciales
        foreach (self::EXCLUDED_ROUTES as $pattern) {
            if ($request->routeIs($pattern)) {
                return $next($request);
            }
        }

        // Ne pas cacher si l'utilisateur est connecté
        if (auth()->check()) {
            return $next($request);
        }

        // Exécuter la requête normalement
        $response = $next($request);

        // Ne cacher que les 200 HTML
        if (
            $response->getStatusCode() === 200
            && str_contains($response->headers->get('Content-Type', ''), 'text/html')
        ) {
            $this->saveToCache($uri, $response);
        }

        return $response;
    }

    private function saveToCache(string $uri, Response $response): void
    {
        $cachePath = $this->getCachePath($uri);
        $cacheDir = dirname($cachePath);

        if (! is_dir($cacheDir)) {
            @mkdir($cacheDir, 0755, true);
        }

        // Sauvegarder le HTML + les headers essentiels
        $headers = [
            'Content-Type' => $response->headers->get('Content-Type'),
            'X-Frame-Options' => $response->headers->get('X-Frame-Options'),
            'X-Content-Type-Options' => $response->headers->get('X-Content-Type-Options'),
            'Referrer-Policy' => $response->headers->get('Referrer-Policy'),
            'Content-Security-Policy' => $response->headers->get('Content-Security-Policy'),
        ];

        $cacheData = json_encode([
            'headers' => array_filter($headers),
            'html' => $response->getContent(),
            'created_at' => time(),
        ]);

        @file_put_contents($cachePath, $cacheData, LOCK_EX);
    }

    public static function getCachePath(string $uri): string
    {
        // /fr/about → storage/page-cache/fr_about.json
        $key = trim($uri, '/');
        $key = $key === '' ? '_root' : str_replace('/', '_', $key);
        return storage_path('page-cache/' . $key . '.json');
    }

    /**
     * Servir depuis le cache (appelé AVANT Laravel boot dans index.php)
     * Retourne true si la page a été servie, false sinon.
     */
    public static function serveFromCache(string $uri, int $ttl = self::CACHE_TTL): bool
    {
        $key = trim($uri, '/');
        $key = $key === '' ? '_root' : str_replace('/', '_', $key);
        $cachePath = dirname(__DIR__, 3) . '/storage/page-cache/' . $key . '.json';

        if (! is_file($cachePath)) {
            return false;
        }

        // Vérifier le TTL
        $age = time() - filemtime($cachePath);
        if ($age > $ttl) {
            @unlink($cachePath);
            return false;
        }

        $data = @json_decode(file_get_contents($cachePath), true);
        if (! $data || empty($data['html'])) {
            @unlink($cachePath);
            return false;
        }

        // Envoyer les headers
        http_response_code(200);
        foreach ($data['headers'] ?? [] as $name => $value) {
            header("$name: $value");
        }
        header('X-Page-Cache: HIT (age=' . $age . 's)');
        header('Cache-Control: public, max-age=600, s-maxage=600, stale-while-revalidate=60');

        echo $data['html'];
        return true;
    }

    /**
     * Vider tout le cache de pages (à appeler après édition admin)
     */
    public static function clearAll(): void
    {
        $dir = storage_path('page-cache');
        if (is_dir($dir)) {
            foreach (glob($dir . '/*.json') as $file) {
                @unlink($file);
            }
        }
    }
}
