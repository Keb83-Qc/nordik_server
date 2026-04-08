<?php

namespace App\Services;

use App\Services\Abf\CapitalFactorMatrix;
use App\Services\Abf\GovernmentBenefitsEstimator;
use App\Services\Abf\QuebecTaxBrackets;

class AbfCaseCalculator
{
    public function __construct(
        private readonly CapitalFactorMatrix       $matrix,
        private readonly GovernmentBenefitsEstimator $govBenefits,
        private readonly QuebecTaxBrackets          $taxBrackets,
    ) {}

    public function calculate(array $payload): array
    {
        $payload = $payload ?: [];

        return [
            'totals'       => $this->totals($payload),
            'progress'     => $this->progress($payload),
            'death_budget' => $this->deathBudget($payload),
            'disability'   => $this->disabilityNeeds($payload),
            'meta'         => [
                'computed_at'        => now()->toIso8601String(),
                'calculator_version' => 'abf-v3',
            ],
        ];
    }

    // ── Totaux actifs / passifs ───────────────────────────────────────────────

    private function totals(array $payload): array
    {
        $assets = (array) ($payload['assets'] ?? []);
        $liabs  = (array) ($payload['liabilities'] ?? []);

        $assetsTotal = collect($assets)->sum(fn ($a) => (float) ($a['value'] ?? 0));
        $liabsTotal  = collect($liabs)->sum(fn ($l) => (float) ($l['balance'] ?? 0));

        return [
            'assets_total'      => round($assetsTotal, 2),
            'liabilities_total' => round($liabsTotal, 2),
            'net_worth'         => round($assetsTotal - $liabsTotal, 2),
        ];
    }

    // ── Progression par section ───────────────────────────────────────────────

    private function progress(array $payload): array
    {
        $donePagesArr = (array) ($payload['navigation']['done_pages'] ?? []);

        $checks = [
            'dossier'        => fn () => true,
            'client'         => fn () => $this->hasText($payload, 'client.prenom') && $this->hasText($payload, 'client.nom'),
            'conjoint'       => fn () => $this->spouseOk($payload),
            'famille'        => fn () => true,
            'objectifs'      => fn () => $this->hasAnyText($payload, [
                'objectives_pdf.family', 'objectives_pdf.work', 'objectives_pdf.finances',
                'objectives_pdf.loisirs', 'objectives_pdf.retraite', 'objectives_pdf.priority_order',
            ]),
            'actifs'         => fn () => count((array) ($payload['actifs'] ?? [])) > 0,
            'passifs'        => fn () => true,
            'revenu_epargne' => fn () => count((array) ($payload['revenus'] ?? [])) > 0,
            'fonds_urgence'  => fn () => in_array('fonds-urgence', $donePagesArr),
            'maladie_grave'  => fn () => in_array('maladie-grave', $donePagesArr),
            'protections'    => fn () => $this->protectionsSectionOk($payload),
            'budget_deces'   => fn () => $this->deathBudgetSectionOk($payload),
            'resume'         => fn () => true,
        ];

        $done = [];
        foreach ($checks as $key => $fn) {
            $done[$key] = (bool) $fn();
        }

        $countDone = count(array_filter($done));
        $total     = count($done);

        return [
            'sections' => $done,
            'done'     => $countDone,
            'total'    => $total,
            'percent'  => $total ? (int) round(($countDone / $total) * 100) : 0,
        ];
    }

    private function spouseOk(array $payload): bool
    {
        if (! (bool) ($payload['has_spouse'] ?? false)) {
            return true;
        }

        return $this->hasText($payload, 'spouse.first_name') && $this->hasText($payload, 'spouse.last_name');
    }

    private function protectionsSectionOk(array $payload): bool
    {
        $quick = (array) ($payload['insurances'] ?? []);
        if ($this->repeaterItemsOk($quick, ['type', 'insured'])) {
            return true;
        }

        return $this->hasAnyProtectionDetail($payload, 'client')
            || $this->hasAnyProtectionDetail($payload, 'spouse')
            || $this->hasAnyProtectionDetail($payload, 'children');
    }

    private function hasAnyProtectionDetail(array $payload, string $key): bool
    {
        $base = (array) data_get($payload, "protections_details.{$key}", []);
        foreach (['life', 'disability', 'critical_illness'] as $type) {
            if (count((array) ($base[$type] ?? [])) > 0) {
                return true;
            }
        }

        return false;
    }

    private function deathBudgetSectionOk(array $payload): bool
    {
        return $this->hasNumeric($payload, 'death_budget.b.total_liquidities.client')
            || $this->hasNumeric($payload, 'death_budget.c.target_monthly_after_death.client')
            || $this->hasNumeric($payload, 'death_budget.e.additional_covered_amount.client');
    }

    private function repeaterItemsOk(array $items, array $requiredKeys): bool
    {
        if (count($items) === 0) {
            return true;
        }

        foreach ($items as $item) {
            foreach ($requiredKeys as $key) {
                if (! isset($item[$key]) || $item[$key] === '' || $item[$key] === null) {
                    return false;
                }
            }
        }

        return true;
    }

    // ── Budget au décès ───────────────────────────────────────────────────────

    public function deathBudget(array $payload): array
    {
        $db         = (array) ($payload['death_budget'] ?? []);
        $dependents = (array) ($payload['dependents']   ?? []);
        $hasSpouse  = (bool)  ($payload['has_spouse']    ?? false);

        // Legacy repeaters (rétrocompatibilité)
        $monthlyExpenses = collect((array) ($db['survivor_monthly_expenses'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount'] ?? 0));
        $oneTimeCosts = collect((array) ($db['one_time_costs'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount'] ?? 0));
        $monthlyIncome = collect((array) ($db['income_sources'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount_monthly'] ?? 0));

        $perPerson = [];

        foreach (['client', 'spouse'] as $person) {
            if ($person === 'spouse' && ! $hasSpouse) {
                continue;
            }

            // ── Section B ──────────────────────────────────────────────────
            $b1   = (float) data_get($db, "b.total_liquidities.{$person}", 0);
            $b2   = (float) data_get($db, "b.total_immediate_needs.{$person}", 0);
            $bNet = $b1 - $b2;

            // ── Section C ──────────────────────────────────────────────────
            $c1       = (float) data_get($db, "c.current_gross_monthly.{$person}", 0);
            $c2       = (float) data_get($db, "c.target_monthly_after_death.{$person}", 0);
            $c3_rrq   = (float) data_get($db, "c.rrq_rpc_survivor_rente.{$person}", 0);
            $c3_orph  = (float) data_get($db, "c.orphan_rente_monthly.{$person}", 0);
            $c3_sv    = (float) data_get($db, "c.sv_monthly.{$person}", 0);

            // Total des revenus gouvernementaux disponibles
            $totalGovIncome = $c3_rrq + $c3_orph + $c3_sv;
            $cGap           = max(0.0, $c2 - $totalGovIncome);

            // ── Section D ──────────────────────────────────────────────────
            $rate     = (float) data_get($db, "d.return_rate_percent.{$person}", 0);
            $duration = (int) round((float) data_get($db, "d.duration_years.{$person}", 0));
            $factor   = $this->matrix->lookup($duration, $rate);
            $capitalRequired = $factor * $cGap;

            // ── Section E ──────────────────────────────────────────────────
            $additional = max(0.0, $capitalRequired - $bNet);
            $covered    = (float) data_get($db, "e.additional_covered_amount.{$person}", 0);
            $remaining  = max(0.0, $additional - $covered);

            // ── Prestations gouvernementales estimées (pour le PDF) ────────
            $deceasedIncome = $c1 > 0 ? ($c1 * 12) : $this->annualIncomeFromPayload($payload, $person);
            $govEstimates   = $this->estimateGovernmentBenefits($payload, $person, $deceasedIncome, $dependents, $hasSpouse);

            $perPerson[$person] = [
                'b' => [
                    'total_liquidities'    => round($b1, 2),
                    'total_immediate_needs'=> round($b2, 2),
                    'net_liquidities'      => round($bNet, 2),
                ],
                'c' => [
                    'current_gross_monthly'      => round($c1, 2),
                    'target_monthly_after_death' => round($c2, 2),
                    'rrq_rpc_survivor_rente'     => round($c3_rrq, 2),
                    'orphan_rente_monthly'       => round($c3_orph, 2),
                    'sv_monthly'                 => round($c3_sv, 2),
                    'total_gov_income_monthly'   => round($totalGovIncome, 2),
                    'monthly_gap'                => round($cGap, 2),
                ],
                'd' => [
                    'return_rate_percent' => round($rate, 2),
                    'duration_years'      => $duration,
                    'factor'              => round($factor, 4),
                    'capital_required'    => round($capitalRequired, 2),
                ],
                'e' => [
                    'additional_need'      => round($additional, 2),
                    'covered_amount'       => round($covered, 2),
                    'remaining_uncovered'  => round($remaining, 2),
                ],
                'gov_estimates' => $govEstimates,
            ];
        }

        return [
            'legacy' => [
                'monthly_expenses_total' => round($monthlyExpenses, 2),
                'one_time_costs_total'   => round($oneTimeCosts, 2),
                'monthly_income_total'   => round($monthlyIncome, 2),
                'monthly_net'            => round($monthlyIncome - $monthlyExpenses, 2),
            ],
            'per_person'               => $perPerson,
            'total_additional_need'    => round(
                (float) data_get($perPerson, 'client.e.additional_need', 0) +
                (float) data_get($perPerson, 'spouse.e.additional_need', 0),
                2
            ),
            'total_remaining_uncovered' => round(
                (float) data_get($perPerson, 'client.e.remaining_uncovered', 0) +
                (float) data_get($perPerson, 'spouse.e.remaining_uncovered', 0),
                2
            ),
        ];
    }

    /**
     * Estime les prestations gouvernementales pour alimenter le PDF
     * quand les champs manuels C3 sont vides.
     */
    private function estimateGovernmentBenefits(
        array $payload,
        string $person,
        float $deceasedAnnualIncome,
        array $dependents,
        bool  $hasSpouse,
    ): array {
        // Survivant = la personne qui reste en vie quand $person décède
        $survivorKey = $person === 'client' ? 'conjoint' : 'client';
        $survivorBd  = $this->govBenefits->birthDateFromPayload(
            (array) ($payload[$survivorKey] ?? [])
        );

        // Rente de conjoint survivant (uniquement s'il y a un conjoint)
        $survivorRente = ($hasSpouse || $person === 'spouse')
            ? $this->govBenefits->rrqSurvivorPension($deceasedAnnualIncome)
            : 0.0;

        // Rente d'orphelin
        $orphanRente = $this->govBenefits->rrqOrphanBenefit($dependents);

        // SV du survivant
        $oasBenefit = $survivorBd ? $this->govBenefits->oasBenefit($survivorBd) : 0.0;

        // Impôts au décès
        $rrspValue = (float) data_get($payload, "death_bilan.actif.rrsp.{$person}", 0);
        if ($rrspValue <= 0) {
            // Fallback depuis les actifs
            foreach ((array) ($payload['assets'] ?? []) as $asset) {
                if (($asset['type'] ?? '') === 'rrsp') {
                    $owner = $asset['owner'] ?? 'client';
                    if ($owner === $person || $owner === 'joint') {
                        $rrspValue += (float) ($asset['value'] ?? 0);
                    }
                }
            }
        }

        $secondaryResidence = (float) data_get($payload, "death_bilan.actif.secondary_residence.{$person}", 0);
        $capitalGainsEstimate = $secondaryResidence * 0.30; // ~30 % de plus-value estimée

        $deathTaxes = $this->govBenefits->estimatedDeathTaxes(
            taxBrackets: $this->taxBrackets,
            annualIncome: $deceasedAnnualIncome,
            rrspValue: $rrspValue,
            capitalGainsEstimate: $capitalGainsEstimate,
            rrspTransferredToSpouse: $hasSpouse,
        );

        return [
            'rrq_survivor_rente_monthly' => round($survivorRente, 2),
            'rrq_orphan_rente_monthly'   => round($orphanRente, 2),
            'oas_monthly'                => round($oasBenefit, 2),
            'estimated_death_taxes'      => round($deathTaxes, 2),
            'eligible_orphans_count'     => $this->govBenefits->countEligibleOrphans($dependents),
        ];
    }

    // ── Invalidité ────────────────────────────────────────────────────────────

    private function disabilityNeeds(array $payload): array
    {
        $d = (array) ($payload['disability'] ?? []);

        $calcPerson = function (array $p): array {
            $income    = (float) ($p['avg_monthly_income'] ?? 0);
            $targetPct = (float) ($p['target_coverage_percent'] ?? 0.70);
            $need      = $income * $targetPct;

            $group  = (float) data_get($p, 'group.monthly_benefit', 0);
            $ind    = (float) data_get($p, 'individual.monthly_benefit', 0);
            $other  = (float) ($p['other_monthly_income'] ?? 0);
            $covered = $group + $ind + $other;
            $gap    = max(0.0, $need - $covered);

            return [
                'need_monthly'    => round($need, 2),
                'covered_monthly' => round($covered, 2),
                'gap_monthly'     => round($gap, 2),
            ];
        };

        return [
            'client' => $calcPerson((array) ($d['client'] ?? [])),
            'spouse' => $calcPerson((array) ($d['spouse'] ?? [])),
        ];
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Revenu annuel brut depuis le payload.
     * Prend le max entre annual_income et la somme des jobs, puis ajoute other_income_annual.
     * FIX : corrige le bug max() qui ignorait les jobs quand annual_income existait.
     */
    private function annualIncomeFromPayload(array $payload, string $person): float
    {
        $directAnnual = (float) (data_get($payload, "{$person}.annual_income") ?? 0);
        $otherAnnual  = (float) (data_get($payload, "{$person}.other_income_annual") ?? 0);
        $otherMonthly = (float) (data_get($payload, "{$person}.other_income_monthly") ?? 0);

        $jobsAnnual = 0.0;
        foreach ((array) (data_get($payload, "{$person}.jobs") ?? []) as $job) {
            $jobsAnnual += (float) ($job['annual_income'] ?? 0);
        }

        // Si annual_income est rempli, on s'en sert comme base principale.
        // Les jobs ne doublent pas le salaire — on prend le plus grand des deux.
        $baseAnnual = max($directAnnual, $jobsAnnual) + $otherAnnual + ($otherMonthly * 12.0);

        return round($baseAnnual, 2);
    }

    private function hasText(array $payload, string $path): bool
    {
        $v = data_get($payload, $path);

        return is_string($v) && trim($v) !== '';
    }

    private function hasAnyText(array $payload, array $paths): bool
    {
        foreach ($paths as $path) {
            if ($this->hasText($payload, $path)) {
                return true;
            }
        }

        return false;
    }

    private function hasNumeric(array $payload, string $path): bool
    {
        $v = data_get($payload, $path);

        return is_numeric($v);
    }
}
