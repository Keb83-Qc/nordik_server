<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ──────────────────────────────────────────────────────────
// FULL-PAGE CACHE — Sert le HTML en ~5ms au lieu de ~1700ms
// Bypass complet de Laravel pour les pages publiques cachées.
// ──────────────────────────────────────────────────────────
if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'GET'
    && !isset($_COOKIE[session_name()])  // Pas de session active → visiteur anonyme
    && !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin')
    && !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/abf')
    && !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/conseiller')
    && !str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/livewire')
) {
    $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?'); // ignore query string
    $cacheKey = trim($uri, '/');
    $cacheKey = $cacheKey === '' ? '_root' : str_replace('/', '_', $cacheKey);
    $cacheFile = __DIR__ . '/../storage/page-cache/' . $cacheKey . '.json';

    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 600) {
        $data = @json_decode(file_get_contents($cacheFile), true);
        if (!empty($data['html'])) {
            http_response_code(200);
            foreach ($data['headers'] ?? [] as $name => $value) {
                header("$name: $value");
            }
            header('X-Page-Cache: HIT');
            header('Cache-Control: public, max-age=600, s-maxage=600');
            echo $data['html'];
            exit; // ← 0 Laravel, 0 DB, 0 provider, ~5ms
        }
    }
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
