<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Filament\Abf\Support\Money;
use App\Services\Abf\BalanceSheet;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;

final class AssetsLiabilitiesStep
{
    public static function make(): Step
    {
        return Step::make('Actifs & Passifs')
            ->id('actifs')
            ->schema([
                Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])
                    ->schema([
                        Section::make('Actifs')
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 1,
                            ])
                            ->schema([
                                Repeater::make('payload.assets')
                                    ->live()
                                    ->label('Actifs')
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->schema([
                                        Select::make('type')
                                            ->label('Type')
                                            ->required()
                                            ->options([
                                                'cash' => 'Liquidités',
                                                'tfsa' => 'CELI',
                                                'rrsp' => 'REER',
                                                'nonreg' => 'Non-enregistré',
                                                'home' => 'Résidence principale',
                                                'rental' => 'Résidence secondaire / immeuble',
                                                'vehicle' => 'Véhicule',
                                                'business' => 'Entreprise',
                                                'other' => 'Autre',
                                            ]),
                                        TextInput::make('description')->label('Description'),
                                        TextInput::make('value')
                                            ->label('Valeur')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->live(onBlur: true),
                                        Toggle::make('is_liquid')
                                            ->label('Actif liquide ?')
                                            ->default(false),
                                        Select::make('owner')
                                            ->label('Propriétaire')
                                            ->options([
                                                'client' => 'Client',
                                                'spouse' => 'Conjoint',
                                                'joint' => 'Commun',
                                            ])
                                            ->default('client'),
                                        TextInput::make('notes')
                                            ->label('Notes')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),

                                Section::make('Résumé — Actifs')
                                    ->columns(2)
                                    ->schema([
                                        Placeholder::make('assets_total_client')
                                            ->label('Client')
                                            ->content(fn(Get $get) => Money::dollars(self::sumAssets($get, 'client'))),

                                        Placeholder::make('assets_total_spouse')
                                            ->label('Conjoint')
                                            ->content(fn(Get $get) => Money::dollars(self::sumAssets($get, 'spouse'))),

                                        Placeholder::make('assets_total_joint')
                                            ->label('Commun')
                                            ->content(fn(Get $get) => Money::dollars(self::sumAssets($get, 'joint'))),

                                        Placeholder::make('assets_total_all')
                                            ->label('Total')
                                            ->content(fn(Get $get) => Money::dollars(self::sumAssets($get, null))),
                                    ]),
                            ]),

                        Section::make('Passifs')
                            ->columnSpan([
                                'default' => 1,
                                'xl' => 1,
                            ])
                            ->schema([
                                Repeater::make('payload.liabilities')
                                    ->live()
                                    ->label('Dettes / passifs')
                                    ->collapsed()
                                    ->defaultItems(0)
                                    ->schema([
                                        Select::make('type')
                                            ->label('Type')
                                            ->required()
                                            ->options([
                                                'mortgage' => 'Hypothèque',
                                                'loc' => 'Marge de crédit',
                                                'loan' => 'Prêt',
                                                'credit' => 'Carte de crédit',
                                                'student' => 'Prêt étudiant',
                                                'tax' => 'Impôts',
                                                'other' => 'Autre',
                                            ]),
                                        TextInput::make('creditor')->label('Créancier'),
                                        TextInput::make('balance')
                                            ->label('Solde')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->live(onBlur: true),
                                        TextInput::make('payment_monthly')
                                            ->label('Paiement mensuel')
                                            ->numeric()
                                            ->prefix('$'),
                                        TextInput::make('interest_rate')
                                            ->label('Taux (%)')
                                            ->numeric()
                                            ->suffix('%'),
                                        Select::make('owner')
                                            ->label('Responsable')
                                            ->options([
                                                'client' => 'Client',
                                                'spouse' => 'Conjoint',
                                                'joint' => 'Commun',
                                            ])
                                            ->default('client'),
                                        TextInput::make('notes')
                                            ->label('Notes')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(3),

                                Section::make('Résumé — Passifs')
                                    ->columns(2)
                                    ->schema([
                                        Placeholder::make('liabilities_total_client')
                                            ->label('Client')
                                            ->content(fn(Get $get) => Money::dollars(self::sumLiabilities($get, 'client'))),

                                        Placeholder::make('liabilities_total_spouse')
                                            ->label('Conjoint')
                                            ->content(fn(Get $get) => Money::dollars(self::sumLiabilities($get, 'spouse'))),

                                        Placeholder::make('liabilities_total_joint')
                                            ->label('Commun')
                                            ->content(fn(Get $get) => Money::dollars(self::sumLiabilities($get, 'joint'))),

                                        Placeholder::make('liabilities_total_all')
                                            ->label('Total passifs')
                                            ->content(fn(Get $get) => Money::dollars(self::sumLiabilities($get, null))),

                                        Placeholder::make('net_liquidity_all')
                                            ->label('Actif - Passif')
                                            ->content(fn(Get $get) => Money::dollars(
                                                self::sumAssets($get, null) - self::sumLiabilities($get, null)
                                            ))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    private static function sumAssets(Get $get, ?string $owner): float
    {
        /** @var BalanceSheet $svc */
        $svc = app(BalanceSheet::class);
        return (float) $svc->sumAssets((array) ($get('payload.assets') ?? []), $owner);
    }

    private static function sumLiabilities(Get $get, ?string $owner): float
    {
        /** @var BalanceSheet $svc */
        $svc = app(BalanceSheet::class);
        return (float) $svc->sumLiabilities((array) ($get('payload.liabilities') ?? []), $owner);
    }
}
