<?php

namespace App\Services\Abf\DeathBudget;

/**
 * Génère un "patch" (dot paths) à appliquer au state Filament.
 *
 * - Les calculs sont faits via DeathBudgetCalculator (pur)
 * - Cette classe reste pure (aucun Get/Set)
 */
final class DeathBudgetAutofill
{
    public function __construct(private readonly DeathBudgetCalculator $calc)
    {
    }

    /**
     * Retourne un tableau de changements: ['payload.xxx' => value, ...]
     */
    public function changes(array $payload, bool $onlyIfEmpty): array
    {
        return array_merge(
            $this->deathBilanChanges($payload, $onlyIfEmpty),
            $this->derivedChanges($payload, $onlyIfEmpty),
        );
    }

    public function deathBilanChanges(array $payload, bool $onlyIfEmpty): array
    {
        $computed = $this->calc->computeDeathBilanFromInputs($payload);

        $out = [];
        foreach (['actif', 'passif'] as $side) {
            foreach ($computed[$side] as $key => $pair) {
                foreach (['client', 'spouse'] as $person) {
                    $current = data_get($payload, "death_bilan.{$side}.{$key}.{$person}");
                    $isEmpty = is_null($current) || $current === '';
                    if ($onlyIfEmpty && ! $isEmpty) {
                        continue;
                    }
                    $out["payload.death_bilan.{$side}.{$key}.{$person}"] = $pair[$person] ?? 0;
                }
            }
        }

        return $out;
    }

    public function derivedChanges(array $payload, bool $onlyIfEmpty): array
    {
        $computed = $this->calc->computeDeathBilanFromInputs($payload);
        $out = [];

        foreach (['client', 'spouse'] as $person) {
            // B1/B2: total actif/passif
            $actifTotal = $this->bilanSideTotal($payload, $computed, 'actif', $person);
            $passifTotal = $this->bilanSideTotal($payload, $computed, 'passif', $person);

            $targets = [
                "payload.death_budget.b.total_liquidities.{$person}" => $actifTotal,
                "payload.death_budget.b.total_immediate_needs.{$person}" => $passifTotal,
                "payload.death_budget.c.current_gross_monthly.{$person}" => $this->calc->estimatedCurrentGrossMonthlyIncome($payload, $person),
                "payload.death_budget.d.return_rate_percent.{$person}" => (float) config('abf.default_real_return_rate_percent', 5),
                "payload.death_budget.d.duration_years.{$person}" => (float) config('abf.default_income_replacement_years', 20),
            ];

            foreach ($targets as $path => $value) {
                $current = data_get($payload, str_replace('payload.', '', $path));
                $isEmpty = is_null($current) || $current === '';
                if ($onlyIfEmpty && ! $isEmpty) {
                    continue;
                }
                $out[$path] = round((float) $value, 2);
            }
        }

        return $out;
    }

    /**
     * Si le bilan est déjà saisi, on respecte le payload. Sinon, on utilise le computed.
     */
    private function bilanSideTotal(array $payload, array $computed, string $side, string $person): float
    {
        if ($this->hasAnyBilanValue($payload, $side, $person)) {
            return $this->calc->deathBilanTotal($payload, $side, $person);
        }

        $sum = 0.0;
        foreach (($computed[$side] ?? []) as $key => $pair) {
            $sum += (float) ($pair[$person] ?? 0);
        }
        return round($sum, 2);
    }

    private function hasAnyBilanValue(array $payload, string $side, string $person): bool
    {
        $node = (array) data_get($payload, "death_bilan.{$side}", []);
        foreach ($node as $row) {
            $v = $row[$person] ?? null;
            if ($v !== null && $v !== '' && is_numeric($v)) {
                return true;
            }
        }
        return false;
    }
}
