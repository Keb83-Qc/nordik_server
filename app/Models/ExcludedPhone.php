<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Liste interne de numéros exclus du démarchage (inspirée de la LNNTE).
 *
 * Utilisation :
 *   ExcludedPhone::isExcluded('(418) 555-1234')  → true/false
 *   ExcludedPhone::normalize('(418) 555-1234')   → '14185551234'
 *
 * @property int         $id
 * @property string      $phone              Numéro original (affichage)
 * @property string      $phone_normalized   Chiffres uniquement, avec indicatif (recherche)
 * @property string      $reason
 * @property string|null $notes
 * @property int|null    $added_by
 * @property \Carbon\Carbon|null $expires_at
 * @property \Carbon\Carbon      $created_at
 * @property \Carbon\Carbon      $updated_at
 */
class ExcludedPhone extends Model
{
    protected $fillable = [
        'phone',
        'phone_normalized',
        'reason',
        'notes',
        'added_by',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // ─── Labels lisibles des raisons ─────────────────────────────────────────

    public const REASONS = [
        'client_request' => '🚫 Demande du client',
        'lnnte_official' => '📋 LNNTE officielle (CRTC)',
        'deceased'       => '🪦 Décédé',
        'competitor'     => '🏢 Concurrent',
        'do_not_disturb' => '🔕 Ne pas déranger',
        'internal'       => '🏠 Décision interne',
        'other'          => '❓ Autre',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /**
     * Exclut les entrées expirées (expires_at dans le passé).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    // ─── Normalisation ────────────────────────────────────────────────────────

    /**
     * Normalise un numéro de téléphone : garde uniquement les chiffres,
     * ajoute l'indicatif canadien/américain (1) si 10 chiffres.
     *
     * Exemples :
     *   "(418) 555-1234"   → "14185551234"
     *   "418-555-1234"     → "14185551234"
     *   "+1 418 555 1234"  → "14185551234"
     *   "5551234"          → "5551234"   (numéro trop court, retourné tel quel)
     */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        // Numéro nord-américain à 10 chiffres → ajouter indicatif 1
        if (strlen($digits) === 10) {
            $digits = '1' . $digits;
        }

        // Numéro avec indicatif déjà présent (11 chiffres commençant par 1)
        // → on garde tel quel

        return $digits;
    }

    /**
     * Vérifie si un numéro est dans la liste d'exclusion active.
     *
     * @param  string $phone  Numéro en n'importe quel format
     * @return bool
     */
    public static function isExcluded(string $phone): bool
    {
        $normalized = self::normalize($phone);

        if (empty($normalized)) {
            return false;
        }

        return self::active()
            ->where('phone_normalized', $normalized)
            ->exists();
    }

    /**
     * Retourne l'entrée d'exclusion pour un numéro, ou null si absent.
     */
    public static function findByPhone(string $phone): ?self
    {
        $normalized = self::normalize($phone);

        return self::active()
            ->where('phone_normalized', $normalized)
            ->first();
    }

    // ─── Boot — normalisation automatique à la sauvegarde ────────────────────

    protected static function booted(): void
    {
        static::saving(function (ExcludedPhone $record) {
            // Toujours recalculer le numéro normalisé
            $record->phone_normalized = self::normalize($record->phone);
        });
    }

    // ─── Accesseurs ───────────────────────────────────────────────────────────

    /**
     * Libellé lisible de la raison.
     */
    public function getReasonLabelAttribute(): string
    {
        return self::REASONS[$this->reason] ?? $this->reason;
    }

    /**
     * Indique si cette exclusion est expirée.
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Indique si cette exclusion est permanente (pas de date d'expiration).
     */
    public function getIsPermanentAttribute(): bool
    {
        return $this->expires_at === null;
    }
}
