<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Cache;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Filament déclenche Login sur 2 requêtes distinctes (Livewire + redirection)
        // On déduplique via un verrou cache de 8 secondes par user + IP
        $cacheKey = 'login_logged_' . $user->id . '_' . request()->ip();
        if (Cache::has($cacheKey)) return;
        Cache::put($cacheKey, true, 8);

        // Mise à jour du dernier login sans toucher updated_at
        $user->withoutTimestamps(fn () => $user->update(['last_login' => now()]));

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
