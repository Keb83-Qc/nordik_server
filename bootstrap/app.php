<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SetLocale;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (\Illuminate\Foundation\Configuration\Middleware $middleware) {
        $middleware->alias([
            'set-locale' => \App\Http\Middleware\SetLocale::class,
             'setlocale'  => \App\Http\Middleware\SetLocale::class,
        ]);

        // Applique les headers de sécurité + cache sur toutes les requêtes web
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\PublicPageCache::class,
            \App\Http\Middleware\FullPageCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // --- LOGIQUE DE CAPTURE AUTOMATIQUE ---
        $exceptions->reportable(function (Throwable $e) {
            try {
                $source = SystemLog::detectSource();

                // ── 404 / Modèle introuvable ────────────────────────────────────────
                // On log uniquement les 404 du site public (pas le panel admin — trop de bruit)
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
                    || $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
                ) {
                    if ($source === SystemLog::SOURCE_PUBLIC) {
                        SystemLog::record('warning', '[404] ' . request()->path(), [
                            'url'        => request()->fullUrl(),
                            'method'     => request()->method(),
                            'referer'    => request()->header('referer', ''),
                            'user_agent' => mb_substr(request()->userAgent() ?? '', 0, 200),
                        ], SystemLog::SOURCE_PUBLIC);
                    }
                    return; // Ne pas remonter davantage pour les 404
                }

                SystemLog::record('danger', 'Crash Automatique: ' . $e->getMessage(), [
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => mb_substr($e->getTraceAsString(), 0, 500),
                    'url'   => request()->fullUrl(),
                ], $source);

                // ── Notification email (max 1 par heure pour éviter le spam) ──
                $cacheKey = 'error_alert_sent_' . md5(get_class($e));
                if (!Cache::has($cacheKey)) {
                    Cache::put($cacheKey, true, 3600);

                    // Envoyer aux super_admins uniquement (pas à l'email de soumissions)
                    $recipients = \App\Models\User::role('super_admin')
                        ->pluck('email')
                        ->filter()
                        ->values()
                        ->all();

                    if (empty($recipients)) {
                        $recipients = array_filter([config('mail.from.address')]);
                    }

                    if (!empty($recipients)) {
                        Mail::raw(
                            "[VIP GPI] Erreur PHP détectée\n\n"
                            . "Message : " . $e->getMessage() . "\n"
                            . "Fichier  : " . $e->getFile() . ':' . $e->getLine() . "\n"
                            . "URL      : " . request()->fullUrl() . "\n\n"
                            . mb_substr($e->getTraceAsString(), 0, 800),
                            fn($m) => $m->to($recipients)->subject('[VIP GPI] 🚨 Erreur critique')
                        );
                    }
                }
            } catch (Throwable $dbError) {
                // Si la DB ou le mail est en panne, on évite le crash en boucle
            }
        });
    })
    ->create();
