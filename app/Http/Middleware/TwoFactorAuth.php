<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user) {
            return $next($request);
        }

        // Ne s'applique qu'aux admins et super_admins
        if (! $user->hasRoleByName(['admin', 'super_admin'])) {
            return $next($request);
        }

        // Vérifier si le 2FA est activé globalement
        $enabled = Cache::remember('setting_two_factor_enabled', 300, function () {
            try {
                return \App\Models\Setting::where('key', 'two_factor_enabled')->value('value') === '1';
            } catch (\Throwable) {
                return false;
            }
        });

        if (! $enabled) {
            return $next($request);
        }

        // Exclure les routes 2FA elles-mêmes pour éviter une boucle infinie
        if ($request->routeIs('2fa.*')) {
            return $next($request);
        }

        // Si déjà vérifié dans cette session → laisser passer
        if ($request->session()->get('2fa_verified')) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas encore configuré son 2FA → page de setup
        if (! $user->two_factor_secret || ! $user->two_factor_confirmed_at) {
            return redirect()->route('2fa.setup');
        }

        // 2FA configuré mais pas encore vérifié cette session → vérification
        return redirect()->route('2fa.verify');
    }
}
