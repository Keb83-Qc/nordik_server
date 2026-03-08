<?php

namespace App\Filament\Abf\Resources\AbfCaseResource\Steps;

use App\Filament\Abf\Forms\PersonSchema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;

final class HouseholdStep
{
    public static function make(): Step
    {
        return Step::make('Client / Conjoint / Famille')
            ->id('client')
            ->schema([
                Toggle::make('payload.has_spouse')
                    ->label('Le client a un conjoint ?')
                    ->default(false)
                    ->live()
                    ->columnSpanFull(),

                Grid::make([
                    'default' => 1,
                    'xl' => 2,
                ])->schema([
                    Section::make('1 - Vous')
                        ->schema(PersonSchema::pdfLikeFields('client')),

                    Section::make('2 - Votre conjoint')
                        ->visible(fn(Get $get) => (bool) $get('payload.has_spouse'))
                        ->schema(PersonSchema::pdfLikeFields('spouse', isSpouse: true)),
                ]),

                Section::make('3 - Vos enfants ou personnes à charge')
                    ->schema([
                        Repeater::make('payload.dependents')
                            ->label('Enfants / personnes à charge')
                            ->defaultItems(0)
                            ->collapsed()
                            ->addActionLabel('Ajouter une personne')
                            ->schema([
                                Grid::make(12)->schema([
                                    TextInput::make('name')
                                        ->label('Prénom et nom')
                                        ->required()
                                        ->columnSpan(6),

                                    DatePicker::make('birth_date')
                                        ->label('Date de naissance')
                                        ->live()
                                        ->afterStateHydrated(function (Set $set, Get $get, $state) {
                                            // auto-fill si mineur + checkbox ON par défaut
                                            if (PersonSchema::isMinorDate($state)) {
                                                $set('same_contact_as_client', true);
                                                $set('address_phone', self::clientContactFromRepeater($get));
                                            }
                                        })
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            if (PersonSchema::isMinorDate($state)) {
                                                $set('same_contact_as_client', true);
                                                $set('address_phone', self::clientContactFromRepeater($get));
                                            }
                                        })
                                        ->columnSpan(3),

                                    Placeholder::make('age_display')
                                        ->label('Âge')
                                        ->content(function (Get $get) {
                                            $d = $get('birth_date');
                                            if (blank($d)) return '—';
                                            try {
                                                return Carbon::parse($d)->age . ' ans';
                                            } catch (\Throwable) {
                                                return '—';
                                            }
                                        })
                                        ->columnSpan(3),

                                    // ✅ La seule action demandée : checkbox “même adresse”
                                    Toggle::make('same_contact_as_client')
                                        ->label('Même adresse que le client ?')
                                        ->default(true)
                                        ->live()
                                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                            // On ne demande rien à saisir. Si ON -> on remplit le champ caché.
                                            if ($state) {
                                                $set('address_phone', self::clientContactFromRepeater($get));
                                            } else {
                                                $set('address_phone', null);
                                            }
                                        })
                                        ->columnSpan(4),

                                    // Champ caché (compat / PDF / calculs). Jamais affiché.
                                    Hidden::make('address_phone')
                                        ->dehydrated()
                                        ->default(null),

                                    Select::make('relationship')
                                        ->label('Lien')
                                        ->options([
                                            'child' => 'Enfant',
                                            'dependent' => 'Personne à charge',
                                            'other' => 'Autre',
                                        ])
                                        ->default('child')
                                        ->columnSpan(4),

                                    Select::make('financial_dependency')
                                        ->label('Dépendance financière')
                                        ->options([
                                            'full' => 'Totale',
                                            'partial' => 'Partielle',
                                            'none' => 'Aucune',
                                        ])
                                        ->default('full')
                                        ->columnSpan(4),
                                ]),
                            ]),
                    ]),
            ]);
    }

    /**
     * Dans un repeater item (payload.dependents.*.field),
     * remonter à payload.* = ../../../
     */
    private static function clientContactFromRepeater(Get $get): string
    {
        $address = trim((string) ($get('../../../client.address') ?? ''));
        $postal  = trim((string) ($get('../../../client.postal_code') ?? ''));
        $homeTel = trim((string) ($get('../../../client.home_phone') ?? ''));

        $out = $address;
        if ($postal !== '') $out .= " ($postal)";
        if ($homeTel !== '') $out .= " — Tél: {$homeTel}";
        return trim($out);
    }
}
