<?php

namespace App\Services\Abf;

use Illuminate\Support\Carbon;

/**
 * Estimation des prestations gouvernementales canadiennes / québécoises.
 *
 * Toutes les valeurs de retour sont en DOLLARS MENSUELS, sauf mention contraire.
 *
 * Sources (2025) :
 *   - QPP/RRQ : retraite-quebec.gouv.qc.ca
 *   - SV/OAS  : canada.ca/fr/services/prestations/pensionspubliques/rpc/securite-vieillesse
 */
final class GovernmentBenefitsEstimator
{
    // ── Constantes RRQ/QPP 2025 ───────────────────────────────────────────────

    /** Prestation de décès (forfaitaire one-time, plafond légal) */
    public const RRQ_DEATH_BENEFIT_MAX = 2_500.0;

    /** Rente mensuelle d'orphelin par enfant éligible (indexée QPP) */
    public const RRQ_ORPHAN_MONTHLY = 282.79;

    /** Rente de conjoint survivant — minimum mensuel garanti */
    public const RRQ_SURVIVOR_MIN_MONTHLY = 472.00;

    /** Rente de conjoint survivant — maximum mensuel (toutes catégories) */
    public const RRQ_SURVIVOR_MAX_MONTHLY = 1_173.58;

    /**
     * Taux d'estimation de la rente de retraite mensuelle à partir du revenu annuel.
     * Approximation : ~25 % du revenu annuel ÷ 12.
     * (Le calcul exact requiert le relevé de cotisation QPP du décédé.)
     */
    private const RRQ_RETIREMENT_RATE = 0.25;

    /** Part de la rente de retraite versée au conjoint survivant */
    private const RRQ_SURVIVOR_BASE_RATE = 0.375;

    // ── Constantes SV/OAS 2025 (indexées trimestriellement) ──────────────────

    public const OAS_MONTHLY_65_74  = 727.67;
    public const OAS_MONTHLY_75_PLUS = 800.44;
    public const OAS_ELIGIBLE_AGE   = 65;

    // ── RRQ : Rente de conjoint survivant ────────────────────────────────────

    /**
     * Estimation mensuelle de la rente de conjoint survivant QPP.
     *
     * Formule simplifiée (calcul minimum) :
     *   rente_retraite_estimée = revenu_annuel × 25 % ÷ 12
     *   rente_survivant = rente_retraite_estimée × 37,5 %
     *   résultat borné entre le minimum et le maximum QPP 2025
     *
     * Le calcul exact nécessite le relevé QPP officiel du décédé.
     * Cette estimation est volontairement conservatrice (plancher sécuritaire).
     *
     * @param  float  $deceasedAnnualIncome  Revenu annuel brut du décédé
     * @return float  Montant mensuel estimé
     */
    public function rrqSurvivorPension(float $deceasedAnnualIncome): float
    {
        if ($deceasedAnnualIncome <= 0) {
            return self::RRQ_SURVIVOR_MIN_MONTHLY;
        }

        $estimatedRetirementMonthly = ($deceasedAnnualIncome * self::RRQ_RETIREMENT_RATE) / 12.0;
        $survivorRente              = $estimatedRetirementMonthly * self::RRQ_SURVIVOR_BASE_RATE;

        return round(
            max(self::RRQ_SURVIVOR_MIN_MONTHLY, min($survivorRente, self::RRQ_SURVIVOR_MAX_MONTHLY)),
            2
        );
    }

    // ── RRQ : Rente d'orphelin ────────────────────────────────────────────────

    /**
     * Calcule la rente mensuelle TOTALE d'orphelin QPP pour tous les enfants éligibles.
     *
     * Éligibilité :
     *   - Moins de 18 ans, OU
     *   - Entre 18 et 24 ans avec `is_full_time_student = true`
     *
     * @param  array  $dependents  Tableau de personnes à charge (payload.dependents[])
     *                             Champs utilisés : birth_date, is_full_time_student
     * @return float  Montant mensuel total (nb_orphelins × 282,79 $)
     */
    public function rrqOrphanBenefit(array $dependents): float
    {
        $count = $this->countEligibleOrphans($dependents);

        return round($count * self::RRQ_ORPHAN_MONTHLY, 2);
    }

    /**
     * Retourne le nombre d'orphelins éligibles à la rente QPP.
     */
    public function countEligibleOrphans(array $dependents): int
    {
        $count = 0;

        foreach ($dependents as $dep) {
            $birthDate = $dep['birth_date'] ?? null;
            if (blank($birthDate)) {
                continue;
            }

            try {
                $age = Carbon::parse($birthDate)->age;
            } catch (\Throwable) {
                continue;
            }

            if ($age < 18) {
                $count++;
                continue;
            }

            // 18-24 ans aux études à temps plein
            if ($age < 25 && (bool) ($dep['is_full_time_student'] ?? false)) {
                $count++;
            }
        }

        return $count;
    }

    // ── SV/OAS ───────────────────────────────────────────────────────────────

    /**
     * Retourne la prestation mensuelle de SV/OAS pour le survivant.
     *
     * Retourne 0 si le survivant a moins de 65 ans.
     * Le conseiller peut projeter manuellement le scénario à 65 ans.
     *
     * @param  string|null  $survivorBirthDate  Date de naissance du survivant (ISO ou parseable)
     * @return float  Montant mensuel (0 si < 65 ans)
     */
    public function oasBenefit(?string $survivorBirthDate): float
    {
        if (blank($survivorBirthDate)) {
            return 0.0;
        }

        try {
            $age = Carbon::parse($survivorBirthDate)->age;
        } catch (\Throwable) {
            return 0.0;
        }

        if ($age < self::OAS_ELIGIBLE_AGE) {
            return 0.0;
        }

        return $age >= 75 ? self::OAS_MONTHLY_75_PLUS : self::OAS_MONTHLY_65_74;
    }

    // ── Impôts au décès ───────────────────────────────────────────────────────

    /**
     * Estime les impôts imputables au décès (Québec 2025).
     *
     * Calcule l'impôt supplémentaire sur :
     *   1. REER/FERR présumé disposé (inclus en totalité dans le revenu de la dernière déclaration),
     *      sauf si transféré au conjoint survivant (roulement à imposition différée).
     *   2. Gains en capital sur actifs non enregistrés / résidence secondaire :
     *      - Première tranche ≤ 250 000 $ → inclusion à 50 %
     *      - Excédent > 250 000 $          → inclusion à 2/3 (règle post-2024)
     *
     * @param  QuebecTaxBrackets  $taxBrackets
     * @param  float  $annualIncome              Revenu d'emploi annuel du décédé
     * @param  float  $rrspValue                 Valeur totale REER/FERR
     * @param  float  $capitalGainsEstimate      Gains en capital bruts estimés
     * @param  bool   $rrspTransferredToSpouse   true → pas d'impôt immédiat sur le REER
     * @return float  Impôt total estimé (arrondi au dollar)
     */
    public function estimatedDeathTaxes(
        QuebecTaxBrackets $taxBrackets,
        float $annualIncome,
        float $rrspValue,
        float $capitalGainsEstimate,
        bool $rrspTransferredToSpouse = false,
    ): float {
        $taxableBase = $annualIncome;
        $totalTax    = 0.0;

        // 1. REER/FERR — disposition présumée
        if (! $rrspTransferredToSpouse && $rrspValue > 0.0) {
            $totalTax    += $taxBrackets->taxOnAdditionalIncome($taxableBase, $rrspValue);
            $taxableBase += $rrspValue;
        }

        // 2. Gains en capital (taux d'inclusion progressif post-2024)
        if ($capitalGainsEstimate > 0.0) {
            $inclusionBelow = min($capitalGainsEstimate, 250_000.0) * 0.50;
            $inclusionAbove = max(0.0, $capitalGainsEstimate - 250_000.0) * (2.0 / 3.0);
            $taxableGains   = $inclusionBelow + $inclusionAbove;
            $totalTax      += $taxBrackets->taxOnAdditionalIncome($taxableBase, $taxableGains);
        }

        return round($totalTax, 2);
    }

    // ── Helpers payload ───────────────────────────────────────────────────────

    /**
     * Construit la date de naissance ISO depuis les champs jour/mois/annee d'un payload.
     * Retourne null si les données sont incomplètes.
     */
    public function birthDateFromPayload(array $person): ?string
    {
        $jour  = $person['ddn_jour']   ?? $person['birth_day']   ?? null;
        $mois  = $person['ddn_mois']   ?? $person['birth_month'] ?? null;
        $annee = $person['ddn_annee']  ?? $person['birth_year']  ?? null;

        // Champ direct ISO (ex. : "1985-06-15")
        $direct = $person['birth_date'] ?? null;
        if (! blank($direct)) {
            return $direct;
        }

        if (! $jour || ! $mois || ! $annee) {
            return null;
        }

        $moisMap = [
            'Janvier' => '01', 'Février' => '02', 'Mars' => '03',
            'Avril'   => '04', 'Mai'     => '05', 'Juin' => '06',
            'Juillet' => '07', 'Août'    => '08', 'Septembre' => '09',
            'Octobre' => '10', 'Novembre'=> '11', 'Décembre'  => '12',
        ];

        $moisNum = is_numeric($mois) ? str_pad((string) $mois, 2, '0', STR_PAD_LEFT) : ($moisMap[$mois] ?? null);

        if (! $moisNum) {
            return null;
        }

        return $annee . '-' . $moisNum . '-' . str_pad((string) $jour, 2, '0', STR_PAD_LEFT);
    }
}
