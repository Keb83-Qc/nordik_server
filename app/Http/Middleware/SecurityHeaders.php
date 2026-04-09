<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Empêche le clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Empêche le MIME sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Active la protection XSS du navigateur
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Contrôle les infos de référence envoyées
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Désactive les fonctionnalités sensibles du navigateur
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), serial=(), bluetooth=()');

        // Isole le contexte de navigation (protège contre Spectre / fenêtres cross-origin)
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // NOTE: Cross-Origin-Embedder-Policy intentionnellement absent —
        // require-corp bloquerait les ressources CDN (Bootstrap, Font Awesome, Google Fonts)
        // qui ne servent pas de header Cross-Origin-Resource-Policy.

        // Bloque Flash et PDF cross-domain (legacy, mais sécurité défense en profondeur)
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // HSTS: force HTTPS pendant 1 an (activer seulement en production avec HTTPS)
        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content Security Policy (adapté au projet: Bootstrap CDN, Font Awesome CDN, Google Fonts)
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https:",
            "frame-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
