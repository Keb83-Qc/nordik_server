<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Numéros inscrits sur la Liste Nationale de Numéros de
 * Télécommunication Exclus (LNNTE) officielle du CRTC.
 *
 * Cette table est en lecture seule depuis Filament.
 * Elle est peuplée exclusivement via ImportLnnteFileJob.
 *
 * Pour vérifier un numéro (interne + LNNTE) :
 *   ExcludedPhone::isExcluded('418-555-1234')
 *
 * @property int         $id
 * @property string      $phone
 * @property string      $phone_normalized
 * @property string|null $import_batch
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class LnnteNumber extends Model
{
    protected $table = 'lnnte_numbers';

    protected $fillable = [
        'phone',
        'phone_normalized',
        'import_batch',
    ];

    // ─── Normalisation (partagée avec ExcludedPhone) ──────────────────────────

    /**
     * Normalise un numéro : chiffres uniquement + indicatif CA/US si 10 chiffres.
     */
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10) {
            $digits = '1' . $digits;
        }

        return $digits;
    }

    /**
     * Vérifie si un numéro est dans la liste LNNTE officielle.
     */
    public static function isExcluded(string $phone): bool
    {
        $normalized = self::normalize($phone);

        if (empty($normalized)) {
            return false;
        }

        return self::where('phone_normalized', $normalized)->exists();
    }

    // ─── Statistiques ─────────────────────────────────────────────────────────

    /**
     * Liste des lots d'import disponibles avec leur nombre de numéros.
     */
    public static function importBatches(): \Illuminate\Support\Collection
    {
        return self::selectRaw('import_batch, COUNT(*) as total, MAX(created_at) as imported_at')
            ->groupBy('import_batch')
            ->orderByDesc('imported_at')
            ->get();
    }
}
