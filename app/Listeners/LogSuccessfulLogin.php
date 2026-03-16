<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    // Filament peut déclencher Login plusieurs fois par requête (multi-guard)
    // Ce flag statique évite les doublons dans les logs
    private static bool $fired = false;

    public function handle(Login $event): void
    {
        if (self::$fired) return;
        self::$fired = true;

        $user = $event->user;

        // Mise à jour du dernier login sans toucher updated_at
        $user->withoutTimestamps(fn () => $user->update(['last_login' => now()]));

        // Label lisible du guard/interface
        $interface = match ($event->guard) {
            'web'   => 'Portail Web',
            default => 'Admin Filament',
        };

        SystemLog::create([
            'level'      => 'login',
            'message'    => "Connexion de {$user->full_name} via {$interface}",
            'user_id'    => $user->id,
            'ip_address' => request()->ip(),
            'context'    => [
                'guard'      => $event->guard,
                'email'      => $user->email,
                'role'       => $user->roles->first()?->name ?? 'N/A',
                'remember'   => $event->remember,
                'user_agent' => request()->userAgent() ?? 'Inconnu',
                'interface'  => $interface,
            ],
        ]);
    }
}
