<?php

namespace App\Services\Abf;

use Illuminate\Support\Carbon;

final class TfsaEstimator
{
    public function __construct(private readonly TfsaAnnualLimits $limits)
    {
    }

    /**
     * CELI théorique max (approximation) :
     * - Somme des plafonds annuels depuis l'année admissible
     * - Ne tient PAS compte des cotisations passées ni retraits
     */
    public function maxContributionRoom(array $payload, string $person, ?int $currentYear = null): float
    {
        if ($person === 'spouse' && ! (bool) ($payload['has_spouse'] ?? false)) {
            return 0.0;
        }

        $startYear = $this->eligibilityStartYear($payload, $person, $currentYear);
        if (! $startYear) {
            return 0.0;
        }

        $currentYear ??= (int) now()->year;
        $limits = $this->limits->byYear();

        $sum = 0.0;
        for ($year = $startYear; $year <= $currentYear; $year++) {
            $sum += (float) ($limits[$year] ?? 0);
        }

        return round($sum, 2);
    }

    /**
     * Détermine l'année de début approximative d'accumulation CELI.
     * Règle simplifiée :
     * - 18 ans et +
     * - citoyen canadien => dès 18 ans
     * - résident permanent / temporaire => depuis la date "travaille au Canada depuis" (si NAS), sinon non calculable
     * - min. 2009 (début du CELI)
     */
    public function eligibilityStartYear(array $payload, string $person, ?int $currentYear = null): ?int
    {
        $currentYear ??= (int) now()->year;

        $birthYear = $this->yearFromDate(data_get($payload, "{$person}.birth_date"));
        if (! $birthYear) {
            return null;
        }

        $yearTurning18 = $birthYear + 18;
        $citizenship = (string) data_get($payload, "{$person}.citizenship_status", '');

        if ($citizenship === 'canadian_citizen') {
            $startYear = max(2009, $yearTurning18);
            return $startYear <= $currentYear ? $startYear : null;
        }

        if (in_array($citizenship, ['permanent_resident', 'temporary_resident'], true)) {
            $hasSin = data_get($payload, "{$person}.has_sin");
            if ($hasSin === false || $hasSin === 0 || $hasSin === '0') {
                return null;
            }

            $workYear =
                $this->yearFromDate(data_get($payload, "{$person}.work_in_canada_since"))
                // compat anciens dossiers
                ?? $this->yearFromDate(data_get($payload, "{$person}.tax_residency.canadian_tax_resident_since"))
                ?? $this->yearFromDate(data_get($payload, "{$person}.in_canada_since"));

            if (! $workYear) {
                return null;
            }

            $startYear = max(2009, $yearTurning18, $workYear);
            return $startYear <= $currentYear ? $startYear : null;
        }

        return null;
    }

    private function yearFromDate(mixed $value): ?int
    {
        if (blank($value)) {
            return null;
        }

        try {
            return (int) Carbon::parse($value)->year;
        } catch (\Throwable) {
            return null;
        }
    }
}
