<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;

final class ProtectionsStep
{
    public static function make(): Step
    {
        return Step::make('Protections')
            ->id('protections')
            ->schema([
                Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])->schema([
                    self::protectionDetailsSection('client', 'Vous')
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 1,
                        ]),

                    Section::make('Détails protections — Votre conjoint')
                        ->visible(fn(Get $get) => (bool) $get('payload.has_spouse'))
                        ->columnSpan([
                            'default' => 1,
                            'xl' => 1,
                        ])
                        ->schema(self::protectionDetailsSectionSchema('spouse')),
                ]),

                self::protectionDetailsSection('children', 'Vos enfants'),

                Section::make('F — Protections recommandées')
                    ->schema([
                        Textarea::make('payload.recommendations.protections_recommandees')
                            ->label('Protections recommandées')
                            ->rows(6),
                    ]),
            ]);
    }

    private static function protectionDetailsSection(string $key, string $label): Section
    {
        return Section::make("Détails protections — {$label}")
            ->schema(self::protectionDetailsSectionSchema($key));
    }

    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    private static function protectionDetailsSectionSchema(string $personKey): array
    {
        $base = "payload.protections_details.{$personKey}";

        return [
            Repeater::make("{$base}.life")
                ->label('Assurance vie')
                ->collapsed()
                ->schema([
                    TextInput::make('provider')->label('Société émettrice'),
                    TextInput::make('subscription_year')->label('Année de souscription'),
                    TextInput::make('contract_type')->label('Type de contrat'),
                    TextInput::make('death_capital')->label('Capital-décès')->numeric()->prefix('$'),
                    TextInput::make('beneficiary')->label('Bénéficiaire'),
                    TextInput::make('annual_premium')->label('Prime annuelle')->numeric()->prefix('$'),
                    TextInput::make('dividend_participation')->label('Participations accumulées')->numeric()->prefix('$'),
                    TextInput::make('cash_value')->label('Valeur de rachat')->numeric()->prefix('$'),
                    TextInput::make('capitalization_fund')->label('Fonds de capitalisation')->numeric()->prefix('$'),
                ])
                ->columns(3),

            Repeater::make("{$base}.disability")
                ->label('Assurance invalidité')
                ->collapsed()
                ->schema([
                    TextInput::make('provider')->label('Société émettrice'),
                    TextInput::make('subscription_year')->label('Année de souscription'),
                    TextInput::make('premium')->label('Prime')->numeric()->prefix('$'),
                    TextInput::make('waiting_period')->label("Période d'attente"),
                    TextInput::make('monthly_income')->label('Revenu mensuel')->numeric()->prefix('$'),
                    TextInput::make('indexation_percent')->label("Pourcentage d'indexation")->numeric()->suffix('%'),
                    TextInput::make('protected_job_until_age')->label("Emploi protégé jusqu'à (ans)"),
                    Textarea::make('comments')->label('Commentaires')->rows(2)->columnSpanFull(),
                ])
                ->columns(3),

            Repeater::make("{$base}.critical_illness")
                ->label('Assurance maladie grave')
                ->collapsed()
                ->schema([
                    TextInput::make('provider')->label('Société émettrice'),
                    TextInput::make('subscription_year')->label('Année de souscription'),
                    TextInput::make('premium')->label('Prime')->numeric()->prefix('$'),
                    TextInput::make('insured_capital')->label('Capital assuré')->numeric()->prefix('$'),
                    TextInput::make('covered_illnesses_count')->label('Maladies couvertes (Nb)'),
                    Toggle::make('premium_refund')->label('Remboursement de prime ?')->default(false),
                    TextInput::make('premium_refund_percent')->label('Remboursement (%)')->numeric()->suffix('%')
                        ->visible(fn(Get $get) => (bool) ($get('premium_refund') ?? false)),
                ])
                ->columns(3),
        ];
    }
}
