<?php

namespace App\Services\Abf;

/**
 * Taux marginaux combinés fédéral + Québec — 2025.
 *
 * Sources :
 *   - Fédéral : canada.ca — paliers 2025 indexés
 *   - Québec   : revenuquebec.ca — paliers 2025
 *
 * Utilisés pour estimer les impôts imputables au décès
 * (disposition présumée du REER, gains en capital, etc.).
 *
 * NOTE : Les crédits personnels de base réduisent l'impôt effectif.
 *        Ces calculs sont des approximations planchers pour la planification.
 */
final class QuebecTaxBrackets
{
    /**
     * Paliers combinés fédéral + QC 2025.
     * Format : [plafond du palier, taux marginal combiné]
     * Le dernier palier a PHP_INT_MAX comme plafond.
     */
    private array $brackets = [
        [17_905,      0.000],   // Sous le montant personnel de base → pratiquement 0 %
        [51_780,      0.274],   // 15 % fédéral + 14 % QC ≈ 27,4 %
        [57_375,      0.378],   // 20,5 % fédéral + 19 % QC ≈ 37,8 % (palier QC)
        [103_545,     0.378],
        [111_733,     0.453],   // 26 % fédéral + 24 % QC ≈ 45,3 % (palier QC)
        [114_750,     0.453],
        [154_906,     0.479],   // 29 % fédéral + 25,75 % QC ≈ 47,9 % (palier QC)
        [246_752,     0.479],
        [PHP_INT_MAX, 0.533],   // 33 % fédéral + 25,75 % QC ≈ 53,3 %
    ];

    /**
     * Retourne le taux marginal combiné pour un revenu annuel donné.
     */
    public function marginalRate(float $annualIncome): float
    {
        $previous = 0.0;

        foreach ($this->brackets as [$ceiling, $rate]) {
            if ($annualIncome <= $ceiling) {
                return $rate;
            }
            $previous = $ceiling;
        }

        return $this->brackets[array_key_last($this->brackets)][1];
    }

    /**
     * Estime l'impôt total sur un revenu annuel (somme progressive des paliers).
     */
    public function estimateTax(float $annualIncome): float
    {
        if ($annualIncome <= 0) {
            return 0.0;
        }

        $tax      = 0.0;
        $previous = 0.0;

        foreach ($this->brackets as [$ceiling, $rate]) {
            if ($annualIncome <= $previous) {
                break;
            }

            $tranche  = min($annualIncome, (float) $ceiling) - $previous;
            $tax     += $tranche * $rate;
            $previous = (float) $ceiling;
        }

        return round(max(0.0, $tax), 2);
    }

    /**
     * Calcule l'impôt additionnel dû sur un revenu supplémentaire,
     * en tenant compte du revenu déjà imposable (ex. : dernière déclaration du décédé).
     */
    public function taxOnAdditionalIncome(float $existingIncome, float $additionalIncome): float
    {
        if ($additionalIncome <= 0.0) {
            return 0.0;
        }

        $taxOnTotal = $this->estimateTax($existingIncome + $additionalIncome);
        $taxOnBase  = $this->estimateTax($existingIncome);

        return round(max(0.0, $taxOnTotal - $taxOnBase), 2);
    }
}
