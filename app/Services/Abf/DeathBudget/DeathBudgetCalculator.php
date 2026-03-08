<?php

namespace App\Services\Abf\DeathBudget;

/**
 * Calculateur "pur" pour le Budget au décès.
 *
 * Entrée: payload (array)
 * Sortie: valeurs calculées (float/array)
 *
 * Aucun couplage à Filament Get/Set.
 */
final class DeathBudgetCalculator
{
    /* ---------------------------------------------------------------------
     |  A — Bilan au décès (à partir des inputs)
     | --------------------------------------------------------------------- */

    public function computeDeathBilanFromInputs(array $payload): array
    {
        $assets = (array) ($payload['assets'] ?? []);
        $liabilities = (array) ($payload['liabilities'] ?? []);
        $insurances = (array) ($payload['insurances'] ?? []);

        $hasSpouse = (bool) ($payload['has_spouse'] ?? false);

        // Si pas de conjoint déclaré mais des lignes indiquent spouse/joint, on active le flag.
        if (! $hasSpouse) {
            foreach ([$assets, $liabilities, $insurances] as $list) {
                foreach ($list as $row) {
                    $owner = $row['owner'] ?? $row['insured'] ?? null;
                    if (in_array($owner, ['spouse', 'joint'], true)) {
                        $hasSpouse = true;
                        break 2;
                    }
                }
            }
        }

        $out = [
            'actif' => [
                'life_individual' => ['client' => 0.0, 'spouse' => 0.0],
                'group_insurance' => ['client' => 0.0, 'spouse' => 0.0],
                'mortgage_insurance' => ['client' => 0.0, 'spouse' => 0.0],
                'rrq_rpc' => ['client' => 0.0, 'spouse' => 0.0],
                'liquidities' => ['client' => 0.0, 'spouse' => 0.0],
                'rrsp' => ['client' => 0.0, 'spouse' => 0.0],
                'primary_residence' => ['client' => 0.0, 'spouse' => 0.0],
                'secondary_residence' => ['client' => 0.0, 'spouse' => 0.0],
                'other_assets' => ['client' => 0.0, 'spouse' => 0.0],
            ],
            'passif' => [
                'last_expenses' => ['client' => 0.0, 'spouse' => 0.0],
                'liquidation_fees' => ['client' => 0.0, 'spouse' => 0.0],
                'emergency_fund' => ['client' => 0.0, 'spouse' => 0.0],
                'education_fund' => ['client' => 0.0, 'spouse' => 0.0],
                'mortgage_balance' => ['client' => 0.0, 'spouse' => 0.0],
                'credit_card_balance' => ['client' => 0.0, 'spouse' => 0.0],
                'line_of_credit_balance' => ['client' => 0.0, 'spouse' => 0.0],
                'auto_loan_balance' => ['client' => 0.0, 'spouse' => 0.0],
                'taxes' => ['client' => 0.0, 'spouse' => 0.0],
                'charity' => ['client' => 0.0, 'spouse' => 0.0],
                'other_liabilities' => ['client' => 0.0, 'spouse' => 0.0],
            ],
        ];

        // RRQ / RPC (estimé)
        $out['actif']['rrq_rpc']['client'] = $this->estimateRrqRpcDeathBenefit($payload, 'client');
        $out['actif']['rrq_rpc']['spouse'] = $this->estimateRrqRpcDeathBenefit($payload, 'spouse');

        // Protections (rapides)
        foreach ($insurances as $row) {
            $type = $row['type'] ?? null;
            $coverage = (float) ($row['coverage'] ?? $row['amount'] ?? 0);
            if ($coverage <= 0) {
                continue;
            }

            $who = $row['insured'] ?? $row['owner'] ?? 'client';
            $dist = $this->distributeByOwner($who, $coverage, $hasSpouse);

            if ($type === 'life') {
                $out['actif']['life_individual']['client'] += $dist['client'];
                $out['actif']['life_individual']['spouse'] += $dist['spouse'];
            } elseif ($type === 'group') {
                $out['actif']['group_insurance']['client'] += $dist['client'];
                $out['actif']['group_insurance']['spouse'] += $dist['spouse'];
            } elseif (in_array($type, ['mortgage', 'mortgage_insurance'], true)) {
                $out['actif']['mortgage_insurance']['client'] += $dist['client'];
                $out['actif']['mortgage_insurance']['spouse'] += $dist['spouse'];
            }
        }

        // Détails protections (format PDF) -> merge pour assurance vie
        foreach (['client', 'spouse', 'children'] as $personKey) {
            $lifeRows = (array) data_get($payload, "protections_details.{$personKey}.life", []);
            foreach ($lifeRows as $row) {
                $amount = (float) ($row['death_capital'] ?? 0);
                if ($amount <= 0) {
                    continue;
                }

                $owner = match ($personKey) {
                    'spouse' => 'spouse',
                    'children' => 'client',
                    default => 'client',
                };

                $dist = $this->distributeByOwner($owner, $amount, $hasSpouse);
                $out['actif']['life_individual']['client'] += $dist['client'];
                $out['actif']['life_individual']['spouse'] += $dist['spouse'];
            }
        }

        foreach ($assets as $row) {
            $type = $row['type'] ?? 'other';
            $value = (float) ($row['value'] ?? $row['amount'] ?? 0);
            if ($value <= 0) {
                continue;
            }

            $owner = $row['owner'] ?? 'client';
            $dist = $this->distributeByOwner($owner, $value, $hasSpouse);
            $isLiquid = (bool) ($row['is_liquid'] ?? false);

            if ($type === 'cash' || $isLiquid) {
                $k = 'liquidities';
            } elseif ($type === 'rrsp') {
                $k = 'rrsp';
            } elseif ($type === 'home') {
                $k = 'primary_residence';
            } elseif (in_array($type, ['rental', 'second_home'], true)) {
                $k = 'secondary_residence';
            } else {
                $k = 'other_assets';
            }

            $out['actif'][$k]['client'] += $dist['client'];
            $out['actif'][$k]['spouse'] += $dist['spouse'];
        }

        foreach ($liabilities as $row) {
            $type = $row['type'] ?? 'other';
            $bal = (float) ($row['balance'] ?? $row['amount'] ?? 0);
            if ($bal <= 0) {
                continue;
            }

            $owner = $row['owner'] ?? 'client';
            $dist = $this->distributeByOwner($owner, $bal, $hasSpouse);

            if ($type === 'mortgage') {
                $k = 'mortgage_balance';
            } elseif ($type === 'credit') {
                $k = 'credit_card_balance';
            } elseif ($type === 'loc') {
                $k = 'line_of_credit_balance';
            } elseif ($type === 'loan' && $this->isAutoLoan($row)) {
                $k = 'auto_loan_balance';
            } elseif ($type === 'tax') {
                $k = 'taxes';
            } else {
                $k = 'other_liabilities';
            }

            $out['passif'][$k]['client'] += $dist['client'];
            $out['passif'][$k]['spouse'] += $dist['spouse'];
        }

        foreach (['actif', 'passif'] as $side) {
            foreach ($out[$side] as $k => $pair) {
                $out[$side][$k]['client'] = round((float) $pair['client'], 2);
                $out[$side][$k]['spouse'] = round((float) $pair['spouse'], 2);
            }
        }

        return $out;
    }

    public function estimateRrqRpcDeathBenefit(array $payload, string $person): float
    {
        if ($person === 'spouse' && ! (bool) ($payload['has_spouse'] ?? false)) {
            return 0.0;
        }

        $eligible = data_get($payload, "{$person}.rrq_rpc.eligible");

        if ($eligible === false || $eligible === 0 || $eligible === '0') {
            return 0.0;
        }

        if ($eligible === true || $eligible === 1 || $eligible === '1') {
            $manualAmount = (float) (data_get($payload, "{$person}.rrq_rpc.death_benefit_amount") ?? 0);
            if ($manualAmount > 0) {
                return round($manualAmount, 2);
            }

            return (float) config('abf.rrq_rpc_death_benefit_amount', 2500);
        }

        // Fallback auto minimal
        if (blank(data_get($payload, "{$person}.birth_date"))) {
            return 0.0;
        }

        if (! $this->personHasIncomeEvidence($payload, $person)) {
            return 0.0;
        }

        return (float) config('abf.rrq_rpc_death_benefit_amount', 2500);
    }

    /* ---------------------------------------------------------------------
     |  Totaux + dérivés B/C/D/E (à partir du payload)
     | --------------------------------------------------------------------- */

    public function deathBilanTotal(array $payload, string $side, string $person): float
    {
        $sum = 0.0;
        foreach ($this->deathBilanKeys($side) as $key) {
            $v = data_get($payload, "death_bilan.{$side}.{$key}.{$person}");
            $sum += is_numeric($v) ? (float) $v : 0.0;
        }
        return $sum;
    }

    public function bNetLiquidities(array $payload, string $person): float
    {
        $b1 = (float) (data_get($payload, "death_budget.b.total_liquidities.{$person}") ?? 0);
        $b2 = (float) (data_get($payload, "death_budget.b.total_immediate_needs.{$person}") ?? 0);
        return round($b1 - $b2, 2);
    }

    public function cMonthlyGap(array $payload, string $person): float
    {
        if ($person === 'spouse' && ! (bool) ($payload['has_spouse'] ?? false)) {
            return 0.0;
        }

        $c2 = (float) (data_get($payload, "death_budget.c.target_monthly_after_death.{$person}") ?? 0);
        $c3 = (float) (data_get($payload, "death_budget.c.rrq_rpc_survivor_rente.{$person}") ?? 0);
        return round(max(0, $c2 - $c3), 2);
    }

    public function dCapitalFactor(array $payload, string $person): float
    {
        $duration = (int) round((float) (data_get($payload, "death_budget.d.duration_years.{$person}") ?? 0));
        $rate = (float) (data_get($payload, "death_budget.d.return_rate_percent.{$person}") ?? 0);

        if ($duration <= 0) {
            return 0.0;
        }

        return round($this->lookupCapitalFactor($duration, $rate), 2);
    }

    public function dCapitalRequired(array $payload, string $person): float
    {
        $factor = $this->dCapitalFactor($payload, $person);
        $d5 = $this->cMonthlyGap($payload, $person);
        return round($factor * $d5, 2);
    }

    public function eAdditionalNeed(array $payload, string $person): float
    {
        return round(max(0, $this->dCapitalRequired($payload, $person) - $this->bNetLiquidities($payload, $person)), 2);
    }

    public function estimatedCurrentGrossMonthlyIncome(array $payload, string $person): float
    {
        if ($person === 'spouse' && ! (bool) ($payload['has_spouse'] ?? false)) {
            return 0.0;
        }

        $annual = (float) (data_get($payload, "{$person}.annual_income") ?? 0);
        $otherAnnual = (float) (data_get($payload, "{$person}.other_income_annual") ?? 0);
        $otherMonthly = (float) (data_get($payload, "{$person}.other_income_monthly") ?? 0);

        $jobsAnnual = 0.0;
        foreach ((array) (data_get($payload, "{$person}.jobs") ?? []) as $row) {
            $jobsAnnual += (float) ($row['annual_income'] ?? 0);
        }

        $baseAnnual = max($annual, $jobsAnnual) + $otherAnnual;
        return round(($baseAnnual / 12) + $otherMonthly, 2);
    }

    public function renderCapitalFactorTableHtml(): string
    {
        $matrix = $this->capitalFactorMatrix();
        $rates = [3, 4, 5, 6, 7, 8];

        $html = '<div style="overflow:auto"><table style="width:100%;border-collapse:collapse;font-size:12px">';
        $html .= '<thead><tr><th style="border:1px solid #d1d5db;padding:6px">Durée</th>';
        foreach ($rates as $r) {
            $html .= '<th style="border:1px solid #d1d5db;padding:6px">' . number_format($r, 2, ',', ' ') . ' %</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($matrix as $duration => $cols) {
            $html .= '<tr>';
            $html .= '<td style="border:1px solid #d1d5db;padding:6px;font-weight:600">' . $duration . '</td>';
            foreach ($rates as $r) {
                $html .= '<td style="border:1px solid #d1d5db;padding:6px;text-align:right">' . number_format((float) $cols[$r], 2, ',', ' ') . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /* ---------------------------------------------------------------------
     |  Internals
     | --------------------------------------------------------------------- */

    private function personHasIncomeEvidence(array $payload, string $person): bool
    {
        $directIncome = (float) (data_get($payload, "{$person}.annual_income") ?? 0);
        $otherAnnual = (float) (data_get($payload, "{$person}.other_income_annual") ?? 0);
        if ($directIncome > 0 || $otherAnnual > 0) {
            return true;
        }

        $jobsIncome = 0.0;
        foreach ((array) (data_get($payload, "{$person}.jobs") ?? []) as $job) {
            $jobsIncome += (float) ($job['annual_income'] ?? 0);
        }
        return $jobsIncome > 0;
    }

    private function distributeByOwner(?string $owner, float $amount, bool $hasSpouse): array
    {
        $owner = $owner ?: 'client';

        if ($owner === 'spouse') {
            return ['client' => 0.0, 'spouse' => $amount];
        }

        if (in_array($owner, ['joint', 'child'], true)) {
            if ($owner === 'child') {
                return ['client' => $amount, 'spouse' => 0.0];
            }

            return $hasSpouse
                ? ['client' => $amount / 2, 'spouse' => $amount / 2]
                : ['client' => $amount, 'spouse' => 0.0];
        }

        return ['client' => $amount, 'spouse' => 0.0];
    }

    private function isAutoLoan(array $row): bool
    {
        $hay = strtolower(trim(($row['name'] ?? '') . ' ' . ($row['creditor'] ?? '') . ' ' . ($row['notes'] ?? '') . ' ' . ($row['description'] ?? '')));
        if ($hay === '') {
            return false;
        }

        foreach (['auto', 'voiture', 'véhicule', 'vehicule', 'car', 'vehicle', 'prêt auto', 'pret auto'] as $kw) {
            if (str_contains($hay, $kw)) {
                return true;
            }
        }

        return false;
    }

    private function deathBilanKeys(string $side): array
    {
        return match ($side) {
            'actif' => [
                'life_individual',
                'group_insurance',
                'mortgage_insurance',
                'rrq_rpc',
                'liquidities',
                'rrsp',
                'primary_residence',
                'secondary_residence',
                'other_assets',
            ],
            'passif' => [
                'last_expenses',
                'liquidation_fees',
                'emergency_fund',
                'education_fund',
                'mortgage_balance',
                'credit_card_balance',
                'line_of_credit_balance',
                'auto_loan_balance',
                'taxes',
                'charity',
                'other_liabilities',
            ],
            default => [],
        };
    }

    private function capitalFactorMatrix(): array
    {
        return [
            10 => [3 => 103.56, 4 => 98.77, 5 => 94.28, 6 => 90.07, 7 => 86.13, 8 => 82.42],
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
    }

    private function lookupCapitalFactor(int $duration, float $ratePercent): float
    {
        $matrix = $this->capitalFactorMatrix();
        $rate = (int) round($ratePercent);

        if (isset($matrix[$duration][$rate])) {
            return (float) $matrix[$duration][$rate];
        }

        // Fallback annuité immédiate
        $r = max(0.000001, $ratePercent / 100 / 12);
        $n = $duration * 12;
        return (1 - (1 + $r) ** (-$n)) / $r;
    }
}
