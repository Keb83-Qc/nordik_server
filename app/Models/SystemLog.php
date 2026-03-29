<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $fillable = ['level', 'message', 'context', 'source', 'user_id', 'ip_address'];

    protected $casts = [
        'context' => 'array',
    ];

    use Prunable;

    // ── Sources possibles ──────────────────────────────────────────────────────
    const SOURCE_PUBLIC = 'public';  // Site principal (visiteurs)
    const SOURCE_ADMIN  = 'admin';   // Panel Filament (conseillers/admins)
    const SOURCE_API    = 'api';     // Requêtes API / webhooks
    const SOURCE_CLI    = 'cli';     // Artisan / queues / crons

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Enregistre un log.
     * $source est auto-détectée si non fournie.
     */
    public static function record(string $level, string $message, array $context = [], ?string $source = null): void
    {
        try {
            self::create([
                'level'      => $level,
                'message'    => $message,
                'context'    => $context,
                'source'     => $source ?? self::detectSource(),
                'user_id'    => auth()->id() ?? null,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable) {
            // Si la DB est indisponible, on ne fait pas planter l'app
        }
    }

    /**
     * Détecte automatiquement la source depuis le contexte d'exécution.
     */
    public static function detectSource(): string
    {
        if (app()->runningInConsole()) {
            return self::SOURCE_CLI;
        }

        $path = request()->path();

        if (str_starts_with($path, 'admin')) {
            return self::SOURCE_ADMIN;
        }

        if (str_starts_with($path, 'api/')) {
            return self::SOURCE_API;
        }

        return self::SOURCE_PUBLIC;
    }

    public function prunable()
    {
        // Supprime automatiquement les logs vieux de plus de 30 jours
        return static::where('created_at', '<=', now()->subDays(30));
    }
}
