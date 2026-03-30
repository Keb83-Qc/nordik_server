<?php

namespace App\Services;

class AbfCaseCalculator
{
    public function calculate(array $payload): array
    {
        $payload = $payload ?: [];

        return [
            'totals' => $this->totals($payload),
            'progress' => $this->progress($payload),
            'death_budget' => $this->deathBudget($payload),
            'disability' => $this->disabilityNeeds($payload),
            'meta' => [
                'computed_at' => now()->toIso8601String(),
                'calculator_version' => 'abf-v2',
            ],
        ];
    }

    private function totals(array $payload): array
    {
        $assets = (array) ($payload['assets'] ?? []);
        $liabs = (array) ($payload['liabilities'] ?? []);

        $assetsTotal = collect($assets)->sum(fn ($a) => (float) ($a['value'] ?? 0));
        $liabsTotal = collect($liabs)->sum(fn ($l) => (float) ($l['balance'] ?? 0));

        return [
            'assets_total' => round($assetsTotal, 2),
            'liabilities_total' => round($liabsTotal, 2),
            'net_worth' => round($assetsTotal - $liabsTotal, 2),
        ];
    }

    private function progress(array $payload): array
    {
        $checks = [
            'dossier' => fn () => true,
            'client' => fn () => $this->hasText($payload, 'client.prenom') && $this->hasText($payload, 'client.nom'),
            'conjoint' => fn () => $this->spouseOk($payload),
            'famille' => fn () => true,
            'objectifs' => fn () => $this->hasAnyText($payload, [
                'objectives_pdf.family',
                'objectives_pdf.work',
                'objectives_pdf.finances',
                'objectives_pdf.loisirs',
                'objectives_pdf.retraite',
                'objectives_pdf.priority_order',
            ]),
            'actifs' => fn () => $this->repeaterItemsOk((array) ($payload['assets'] ?? []), ['type', 'value']),
            'passifs' => fn () => $this->repeaterItemsOk((array) ($payload['liabilities'] ?? []), ['type', 'balance']),
            'protections' => fn () => $this->protectionsSectionOk($payload),
            'budget_deces' => fn () => $this->deathBudgetSectionOk($payload),
            'resume' => fn () => true,
        ];

        $done = [];
        foreach ($checks as $key => $fn) {
            $done[$key] = (bool) $fn();
        }

        $countDone = count(array_filter($done));
        $total = count($done);

        return [
            'sections' => $done,
            'done' => $countDone,
            'total' => $total,
            'percent' => $total ? (int) round(($countDone / $total) * 100) : 0,
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

    private function disabilityNeeds(array $payload): array
    {
        $d = (array) ($payload['disability'] ?? []);

        $calcPerson = function (array $p): array {
            $income = (float) ($p['avg_monthly_income'] ?? 0);
            $targetPct = (float) ($p['target_coverage_percent'] ?? 0.70);
            $need = $income * $targetPct;

            $group = (float) data_get($p, 'group.monthly_benefit', 0);
            $ind = (float) data_get($p, 'individual.monthly_benefit', 0);
            $other = (float) ($p['other_monthly_income'] ?? 0);

            $covered = $group + $ind + $other;
            $gap = max(0, $need - $covered);

            return [
                'need_monthly' => round($need, 2),
                'covered_monthly' => round($covered, 2),
                'gap_monthly' => round($gap, 2),
            ];
        };

        return [
            'client' => $calcPerson((array) ($d['client'] ?? [])),
            'spouse' => $calcPerson((array) ($d['spouse'] ?? [])),
        ];
    }

    public function deathBudget(array $payload): array
    {
        $db = (array) ($payload['death_budget'] ?? []);

        // Legacy repeaters (still supported)
        $monthlyExpenses = collect((array) ($db['survivor_monthly_expenses'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount'] ?? 0));
        $oneTimeCosts = collect((array) ($db['one_time_costs'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount'] ?? 0));
        $monthlyIncome = collect((array) ($db['income_sources'] ?? []))
            ->sum(fn ($r) => (float) ($r['amount_monthly'] ?? 0));

        $perPerson = [];
        foreach (['client', 'spouse'] as $person) {
            $b1 = (float) data_get($db, "b.total_liquidities.{$person}", 0);
            $b2 = (float) data_get($db, "b.total_immediate_needs.{$person}", 0);
            $bNet = $b1 - $b2;

            $c1 = (float) data_get($db, "c.current_gross_monthly.{$person}", 0);
            $c2 = (float) data_get($db, "c.target_monthly_after_death.{$person}", 0);
            $c3 = (float) data_get($db, "c.rrq_rpc_survivor_rente.{$person}", 0);
            $cGap = max(0, $c2 - $c3);

            $rate = (float) data_get($db, "d.return_rate_percent.{$person}", 0);
            $duration = (int) round((float) data_get($db, "d.duration_years.{$person}", 0));
            $factor = $this->capitalFactor($duration, $rate);
            $capitalRequired = $factor * $cGap;

            $additional = max(0, $capitalRequired - $bNet);
            $covered = (float) data_get($db, "e.additional_covered_amount.{$person}", 0);
            $remaining = max(0, $additional - $covered);

            $perPerson[$person] = [
                'b' => [
                    'total_liquidities' => round($b1, 2),
                    'total_immediate_needs' => round($b2, 2),
                    'net_liquidities' => round($bNet, 2),
                ],
                'c' => [
                    'current_gross_monthly' => round($c1, 2),
                    'target_monthly_after_death' => round($c2, 2),
                    'rrq_rpc_survivor_rente' => round($c3, 2),
                    'monthly_gap' => round($cGap, 2),
                ],
                'd' => [
                    'return_rate_percent' => round($rate, 2),
                    'duration_years' => $duration,
                    'factor' => round($factor, 2),
                    'capital_required' => round($capitalRequired, 2),
                ],
                'e' => [
                    'additional_need' => round($additional, 2),
                    'covered_amount' => round($covered, 2),
                    'remaining_uncovered' => round($remaining, 2),
                ],
            ];
        }

        return [
            'legacy' => [
                'monthly_expenses_total' => round($monthlyExpenses, 2),
                'one_time_costs_total' => round($oneTimeCosts, 2),
                'monthly_income_total' => round($monthlyIncome, 2),
                'monthly_net' => round($monthlyIncome - $monthlyExpenses, 2),
            ],
            'per_person' => $perPerson,
            'total_additional_need' => round(
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

    private function capitalFactor(int $duration, float $ratePercent): float
    {
        $matrix = [
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

        $r = (int) round($ratePercent);
        if (isset($matrix[$duration][$r])) {
            return (float) $matrix[$duration][$r];
        }

        if ($duration <= 0) {
            return 0.0;
        }

        // fallback annuity factor (monthly)
        $i = max(0.000001, $ratePercent / 100 / 12);
        $n = $duration * 12;
        return (1 - (1 + $i) ** (-$n)) / $i;
    }
}
