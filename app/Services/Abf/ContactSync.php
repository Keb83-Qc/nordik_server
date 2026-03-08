<?php

namespace App\Services\Abf;

use Illuminate\Support\Carbon;

final class ContactSync
{
    public static function isMinor(mixed $birthDate): bool
    {
        if (blank($birthDate)) {
            return false;
        }

        try {
            return Carbon::parse($birthDate)->age < 18;
        } catch (\Throwable) {
            return false;
        }
    }

    public static function clientHomeContact(array $payload): string
    {
        $address = trim((string) data_get($payload, 'client.address', ''));
        $postal  = trim((string) data_get($payload, 'client.postal_code', ''));
        $homeTel = trim((string) data_get($payload, 'client.home_phone', ''));

        $out = $address;
        if ($postal !== '') {
            $out .= " ($postal)";
        }
        if ($homeTel !== '') {
            $out .= " — Tél: $homeTel";
        }

        return trim($out);
    }

    /**
     * Met à jour les personnes à charge mineures (si same_contact_as_client != false).
     * Retourne le payload modifié (pur).
     */
    public static function syncMinorDependents(array $payload): array
    {
        $deps = (array) data_get($payload, 'dependents', []);
        $contact = self::clientHomeContact($payload);

        foreach ($deps as $i => $d) {
            $birth = $d['birth_date'] ?? null;

            if (! self::isMinor($birth)) {
                continue;
            }

            // par défaut = true, sauf si l’utilisateur a explicitement mis false
            $sync = $d['same_contact_as_client'] ?? true;

            if ($sync) {
                $deps[$i]['same_contact_as_client'] = true;
                $deps[$i]['address_phone'] = $contact;
            }
        }

        data_set($payload, 'dependents', $deps);

        return $payload;
    }
}
