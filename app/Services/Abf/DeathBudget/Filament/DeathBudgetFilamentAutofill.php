<?php

namespace App\Services\Abf\DeathBudget\Filament;

use App\Services\Abf\DeathBudget\DeathBudgetAutofill;
use Filament\Forms\Get;
use Filament\Forms\Set;

/**
 * Adaptateur Filament (Get/Set) pour appliquer l'autofill.
 *
 * Toute la logique de calcul est dans les services "purs".
 */
final class DeathBudgetFilamentAutofill
{
    public function __construct(private readonly DeathBudgetAutofill $autofill)
    {
    }

    public function prefillAll(Get $get, Set $set, bool $onlyIfEmpty): void
    {
        $payload = (array) ($get('payload') ?? []);
        $changes = $this->autofill->changes($payload, $onlyIfEmpty);
        $this->apply($set, $changes);
    }

    public function apply(Set $set, array $changes): void
    {
        foreach ($changes as $path => $value) {
            $set($path, $value);
        }
    }
}
