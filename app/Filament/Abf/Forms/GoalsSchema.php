<?php

namespace App\Filament\Abf\Forms;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Get;

final class GoalsSchema
{
    public static function options(): array
    {
        return [
            'retirement' => 'Retraite',
            'buy_house' => 'Achat / changement de propriété',
            'kids_education' => 'Études des enfants',
            'debt_repayment' => 'Remboursement dettes',
            'insurance' => 'Optimisation assurances',
            'investments' => 'Stratégie placements',
            'business' => 'Projet entreprise',
            'travel' => 'Voyages / style de vie',
        ];
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function answerFields(): array
    {
        $fields = [];

        foreach (self::options() as $key => $label) {
            $fields[] = Textarea::make("payload.goals.answers.{$key}")
                ->label("{$label} — Réponse du client")
                ->rows(3)
                ->visible(fn(Get $get) => in_array($key, (array) ($get('payload.goals.selected') ?? []), true))
                ->required(fn(Get $get) => in_array($key, (array) ($get('payload.goals.selected') ?? []), true));
        }

        return $fields;
    }
}
