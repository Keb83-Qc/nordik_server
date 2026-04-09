<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Cast JSON chiffré avec rétrocompatibilité.
 *
 * - Écriture : chiffre toujours via Crypt::encrypt()
 * - Lecture  : essaie de déchiffrer → si échec (données legacy), décode le JSON brut
 *
 * Permet de migrer en douceur : les anciennes lignes non-chiffrées restent
 * lisibles ; toute nouvelle écriture chiffre automatiquement.
 */
class EncryptedJsonCast implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        // Tenter le déchiffrement (nouvelles données)
        try {
            return json_decode(Crypt::decrypt($value), true);
        } catch (\Throwable) {
            // Fallback legacy : JSON non chiffré
            try {
                $decoded = json_decode($value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            } catch (\Throwable) {
                return null;
            }
        }
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return Crypt::encrypt(json_encode($value));
    }
}
