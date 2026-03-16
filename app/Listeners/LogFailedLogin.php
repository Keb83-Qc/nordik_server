<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Cache;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email']
            ?? $event->credentials['username']
            ?? 'Inconnu';

        // Filament peut déclencher Failed plusieurs fois — déduplication 8 sec
        $cacheKey = 'login_fail_logged_' . md5($email) . '_' . request()->ip();
        if (Cache::has($cacheKey)) return;
        Cache::put($cacheKey, true, 8);

        $reason = $event->user === null
            ? 'Utilisateur introuvable'
            : 'Mot de passe incorrect';

        $interface = match ($event->guard) {
            'web'   => 'Portail Web',
            default => 'Admin Filament',
        };

        SystemLog::create([
            'level'      => 'login_fail',
            'message'    => "Tentative échouée ({$reason}) : {$email} via {$interface}",
            'user_id'    => $event->user?->id ?? null,
            'ip_address' => request()->ip(),
            'context'    => [
                'guard'      => $event->guard,
                'email'      => $email,
                'reason'     => $reason,
                'interface'  => $interface,
                'user_agent' => request()->userAgent() ?? 'Inconnu',
                'status'     => 'failed',
            ],
        ]);
    }
}
