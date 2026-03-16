<?php

namespace App\Listeners;

use App\Models\SystemLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        $email = $event->credentials['email']
            ?? $event->credentials['username']
            ?? 'Inconnu';

        // On peut détecter la raison selon si l'user existe ou non
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
