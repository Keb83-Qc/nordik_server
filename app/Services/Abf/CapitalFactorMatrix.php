<?php

namespace App\Services\Abf;

/**
 * Matrice de facteurs de capitalisation (annuité mensuelle en fin de période).
 * Durées : 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60 ans
 * Taux   : 3 %, 4 %, 5 %, 6 %, 7 %, 8 %
 *
 * Source unique — injectée dans DeathBudgetCalculator, AbfCaseCalculator, etc.
 * Plus de duplication de la matrice dans 3 fichiers différents.
 */
final class CapitalFactorMatrix
{
    private array $matrix = [
        10 => [3 => 103.56, 4 => 98.77,  5 => 94.28,  6 => 90.07,  7 => 86.13,  8 => 82.42],
        15 => [3 => 144.81, 4 => 135.19, 5 => 126.46, 6 => 118.50, 7 => 111.26, 8 => 104.64],
        20 => [3 => 180.81, 4 => 165.02, 5 => 151.53, 6 => 139.58, 7 => 128.98, 8 => 119.55],
        25 => [3 => 210.88, 4 => 189.45, 5 => 171.06, 6 => 155.21, 7 => 141.49, 8 => 129.56],
        30 => [3 => 237.19, 4 => 209.46, 5 => 186.28, 6 => 166.79, 7 => 150.31, 8 => 136.28],
        35 => [3 => 259.84, 4 => 225.85, 5 => 198.14, 6 => 175.38, 7 => 156.53, 8 => 140.79],
        40 => [3 => 279.34, 4 => 239.27, 5 => 207.38, 6 => 181.75, 7 => 160.92, 8 => 143.82],
        45 => [3 => 296.13, 4 => 250.26, 5 => 214.59, 6 => 186.47, 7 => 164.01, 8 => 145.85],
        50 => [3 => 310.58, 4 => 259.26, 5 => 220.20, 6 => 189.97, 7 => 166.20, 8 => 147.22],
        55 => [3 => 323.02, 4 => 266.64, 5 => 224.57, 6 => 192.56, 7 => 167.74, 8 => 148.13],
        60 => [3 => 333.73, 4 => 272.68, 5 => 227.98, 6 => 194.49, 7 => 168.83, 8 => 148.75],
    ];

    /**
     * Retourne le facteur pour une durée et un taux donnés.
     *
     * - Durée + taux exacts dans la matrice → valeur précalculée
     * - Durée intermédiaire → interpolation linéaire entre les deux paliers encadrants
     * - Taux hors plage (< 3 % ou > 8 %) → formule analytique d'annuité mensuelle
     * - Durée > 60 ans → valeur du palier 60 (asymptote pratique)
     */
    public function lookup(int $duration, float $ratePercent): float
    {
        if ($duration <= 0) {
            return 0.0;
        }

        $rateKey = (int) round($ratePercent);

        // Taux hors plage → formule analytique
        if ($rateKey < 3 || $rateKey > 8) {
            return $this->analyticalFactor($duration, $ratePercent);
        }

        $durations = array_keys($this->matrix);

        // Correspondance exacte
        if (isset($this->matrix[$duration][$rateKey])) {
            return (float) $this->matrix[$duration][$rateKey];
        }

        // Durée inférieure au minimum → formule analytique
        if ($duration < min($durations)) {
            return $this->analyticalFactor($duration, $ratePercent);
        }

        // Durée supérieure au maximum → valeur asymptotique du dernier palier
        if ($duration > max($durations)) {
            return (float) ($this->matrix[max($durations)][$rateKey] ?? $this->analyticalFactor($duration, $ratePercent));
        }

        // Interpolation linéaire entre les deux paliers encadrants
        $lower = $this->floorDuration($duration, $durations);
        $upper = $this->ceilDuration($duration, $durations);

        if ($lower === $upper) {
            return (float) ($this->matrix[$lower][$rateKey] ?? $this->analyticalFactor($duration, $ratePercent));
        }

        $factorLower = (float) ($this->matrix[$lower][$rateKey] ?? $this->analyticalFactor($lower, $ratePercent));
        $factorUpper = (float) ($this->matrix[$upper][$rateKey] ?? $this->analyticalFactor($upper, $ratePercent));
        $ratio       = ($duration - $lower) / ($upper - $lower);

        return round($factorLower + $ratio * ($factorUpper - $factorLower), 4);
    }

    public function matrix(): array
    {
        return $this->matrix;
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function analyticalFactor(int $duration, float $ratePercent): float
    {
        // Annuité mensuelle en fin de période : a(n,i) = (1 - (1+i)^-n) / i
        $i = max(0.000001, $ratePercent / 100.0 / 12.0);
        $n = $duration * 12;

        return round((1.0 - (1.0 + $i) ** (-$n)) / $i, 4);
    }

    private function floorDuration(int $duration, array $durations): int
    {
        $result = min($durations);
        foreach ($durations as $d) {
            if ($d <= $duration) {
                $result = $d;
            }
        }

        return $result;
    }

    private function ceilDuration(int $duration, array $durations): int
    {
        $result = max($durations);
        foreach (array_reverse($durations) as $d) {
            if ($d >= $duration) {
                $result = $d;
            }
        }

        return $result;
    }
}
