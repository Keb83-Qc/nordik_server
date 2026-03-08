<?php

namespace App\Filament\Abf\Forms;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Carbon;

final class PersonSchema
{
    /**
     * ✅ Compat: tes Steps appellent encore pdfLikeFields().
     */
    public static function pdfLikeFields(string $person, bool $isSpouse = false): array
    {
        return self::schema($person, $isSpouse);
    }

    /**
     * Nouveau schema en “cards” (Sections Filament).
     */
    public static function schema(string $person, bool $isSpouse = false): array
    {
        $p = "payload.$person";
        $eligible = ['permanent_resident', 'temporary_resident'];

        $syncToSpouse = function (Get $get): bool {
            return (bool) $get('payload.has_spouse')
                && (bool) ($get('payload.spouse.same_contact_as_client') ?? true);
        };

        return [
            // CARD 1 — Infos personnelles
            Section::make('Infos personnelles')
                ->columns(12)
                ->schema([
                    TextInput::make("$p.last_name")->label('Nom')->required(! $isSpouse)->columnSpan(4),
                    TextInput::make("$p.first_name")->label('Prénom')->required(! $isSpouse)->columnSpan(4),
                    TextInput::make("$p.initials")->label('Init.')->maxLength(10)->columnSpan(2),
                    DatePicker::make("$p.birth_date")->label('Date de naissance')->columnSpan(2),

                    Select::make("$p.marital_status")
                        ->label('État civil')
                        ->options([
                            'single' => 'Célibataire',
                            'common_law' => 'Conjoint de fait',
                            'married' => 'Marié(e)',
                            'divorced' => 'Divorcé(e)',
                            'separated' => 'Séparé(e)',
                            'widowed' => 'Veuf(ve)',
                        ])
                        ->live()
                        ->afterStateUpdated(function (Set $set, Get $get, $state) use ($person) {
                            if ($person !== 'client') {
                                return;
                            }

                            $wantsSpouse = in_array((string) $state, ['common_law', 'married'], true);
                            $set('payload.has_spouse', $wantsSpouse);

                            if (! $wantsSpouse) {
                                return;
                            }

                            // Force état civil + sync contact conjoint par défaut
                            $set('payload.spouse.same_contact_as_client', true);
                            $set('payload.spouse.marital_status', (string) $state);
                            $set('payload.spouse.address', $get('payload.client.address'));
                            $set('payload.spouse.postal_code', $get('payload.client.postal_code'));
                            $set('payload.spouse.home_phone', $get('payload.client.home_phone'));
                        })
                        ->columnSpan(4),

                    Select::make("$p.smoker_status")
                        ->label('Fumeur / non-fumeur')
                        ->options([
                            'smoker' => 'Fumeur',
                            'non_smoker' => 'Non-fumeur',
                            'former_smoker' => 'Ancien fumeur',
                        ])
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) use ($p) {
                            if ($state !== 'smoker') {
                                $set("$p.smoker_since", null);
                            }
                        })
                        ->columnSpan(4),

                    TextInput::make("$p.smoker_since")
                        ->label('Depuis (année)')
                        ->placeholder('AAAA')
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue((int) now()->year)
                        ->visible(fn(Get $get) => (string) ($get("$p.smoker_status") ?? '') === 'smoker')
                        ->columnSpan(4),
                ]),

            // CARD 2 — Adresse & téléphone
            Section::make('Adresse & téléphone')
                ->columns(12)
                ->schema(array_values(array_filter([
                    // Toggle seulement pour le conjoint
                    $person === 'spouse'
                        ? Toggle::make('payload.spouse.same_contact_as_client')
                        ->label('Même adresse et tél. domicile que le client ?')
                        ->default(true)
                        ->live()
                        ->helperText("Si activé, l'adresse, le code postal et le tél. domicile seront synchronisés automatiquement.")
                        ->afterStateUpdated(function (Set $set, Get $get, $state) {
                            if (! $state) {
                                return;
                            }

                            $set('payload.spouse.address', $get('payload.client.address'));
                            $set('payload.spouse.postal_code', $get('payload.client.postal_code'));
                            $set('payload.spouse.home_phone', $get('payload.client.home_phone'));
                        })
                        ->columnSpanFull()
                        : null,

                    TextInput::make("$p.address")
                        ->label('Adresse')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) use ($person, $syncToSpouse) {
                            if ($person !== 'client') return;

                            if ($syncToSpouse($get)) {
                                $set('payload.spouse.address', $state);
                            }

                            self::syncMinorDependentsFromClient($set, $get);
                        })
                        ->columnSpan(8),

                    TextInput::make("$p.postal_code")
                        ->label('Code postal')
                        ->maxLength(10)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) use ($person, $syncToSpouse) {
                            if ($person !== 'client') return;

                            if ($syncToSpouse($get)) {
                                $set('payload.spouse.postal_code', $state);
                            }

                            self::syncMinorDependentsFromClient($set, $get);
                        })
                        ->columnSpan(4),

                    TextInput::make("$p.home_phone")
                        ->label('Tél. domicile')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Set $set, Get $get, $state) use ($person, $syncToSpouse) {
                            if ($person !== 'client') return;

                            if ($syncToSpouse($get)) {
                                $set('payload.spouse.home_phone', $state);
                            }

                            self::syncMinorDependentsFromClient($set, $get);
                        })
                        ->columnSpan(4),

                    TextInput::make("$p.cell_phone")->label('Tél. cellulaire')->columnSpan(4),
                    TextInput::make("$p.email")->label('Courriel')->email()->columnSpan(4),
                ]))),

            // CARD 3 — Citoyenneté / CELI
            Section::make('Citoyenneté / CELI')
                ->columns(12)
                ->schema([
                    Select::make("$p.citizenship_status")
                        ->label('Citoyenneté / statut au Canada')
                        ->options([
                            'canadian_citizen' => 'Citoyen(ne) canadien(ne)',
                            'permanent_resident' => 'Résident(e) permanent(e)',
                            'temporary_resident' => 'Résident(e) temporaire / permis',
                            'other' => 'Autre',
                        ])
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) use ($p, $eligible) {
                            if (! in_array((string) $state, $eligible, true)) {
                                $set("$p.has_sin", null);
                                $set("$p.work_in_canada_since", null);
                            }
                        })
                        ->columnSpan(6),

                    Toggle::make("$p.has_sin")
                        ->label('Avez-vous un NAS ?')
                        ->inline(false)
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) use ($p) {
                            if (! $state) {
                                $set("$p.work_in_canada_since", null);
                            }
                        })
                        ->visible(fn(Get $get) => in_array((string) ($get("$p.citizenship_status") ?? ''), $eligible, true))
                        ->columnSpan(3),

                    DatePicker::make("$p.work_in_canada_since")
                        ->label('Travaille au Canada depuis le')
                        ->helperText('Point de départ pour estimer les droits CELI (approx.).')
                        ->visible(
                            fn(Get $get) =>
                            in_array((string) ($get("$p.citizenship_status") ?? ''), $eligible, true)
                                && (bool) $get("$p.has_sin")
                        )
                        ->required(
                            fn(Get $get) =>
                            in_array((string) ($get("$p.citizenship_status") ?? ''), $eligible, true)
                                && (bool) $get("$p.has_sin")
                        )
                        ->columnSpan(3),
                ]),

            // CARD 4 — Emploi & revenus
            Section::make('Emploi & revenus')
                ->columns(12)
                ->schema([
                    TextInput::make("$p.jobs.0.employer")->label('Employeur')->columnSpan(6),
                    DatePicker::make("$p.employment_since")->label('Depuis le')->columnSpan(3),
                    TextInput::make("$p.jobs.0.occupation")->label('Fonction')->columnSpan(3),

                    TextInput::make("$p.work_address")->label('Adresse (travail)')->columnSpan(8),
                    TextInput::make("$p.work_phone")->label('Tél. bureau')->columnSpan(2),
                    TextInput::make("$p.fax")->label('Télécopieur')->columnSpan(2),

                    TextInput::make("$p.jobs.0.annual_income")->label("Revenu d'emploi")->numeric()->prefix('$')->columnSpan(4),
                    TextInput::make("$p.other_income_annual")->label('Autres revenus (annuels)')->numeric()->prefix('$')->columnSpan(4),
                    TextInput::make("$p.other_income_monthly")->label('Autres revenus (mensuels)')->numeric()->prefix('$')->columnSpan(4),
                ]),

            // CARD 5 — RRQ/RPC & légal
            Section::make('RRQ/RPC & légal')
                ->columns(12)
                ->schema([
                    Toggle::make("$p.rrq_rpc.eligible")
                        ->label('Admissible aux prestations de décès RRQ/RPC ?')
                        ->inline(false)
                        ->columnSpan(4),

                    TextInput::make("$p.rrq_rpc.death_benefit_amount")
                        ->label('Montant RRQ/RPC (optionnel)')
                        ->numeric()
                        ->prefix('$')
                        ->visible(fn(Get $get) => (bool) $get("$p.rrq_rpc.eligible"))
                        ->columnSpan(4),

                    Grid::make(12)->schema([
                        Toggle::make("$p.legal.will_exists")->label('Testament ?')->inline(false)->columnSpan(4),
                        Toggle::make("$p.legal.mandate_incapacity_exists")->label("Mandat d’inaptitude (QC) ?")->inline(false)->columnSpan(4),
                        Toggle::make("$p.legal.power_of_attorney_exists")->label('Procuration (hors QC) ?')->inline(false)->columnSpan(4),
                    ])->columnSpanFull(),
                ])
                ->collapsible(),
        ];
    }

    /** Construit une string “adresse + cp + tel domicile” pour remplir address_phone */
    private static function clientContactString(Get $get): string
    {
        $address = trim((string) $get('payload.client.address'));
        $postal  = trim((string) $get('payload.client.postal_code'));
        $homeTel = trim((string) $get('payload.client.home_phone'));

        $out = $address;
        if ($postal !== '') {
            $out .= " ($postal)";
        }
        if ($homeTel !== '') {
            $out .= " — Tél: {$homeTel}";
        }
        return trim($out);
    }

    public static function isMinorDate(mixed $birthDate): bool
    {
        if (blank($birthDate)) return false;

        try {
            return Carbon::parse($birthDate)->age < 18;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Met à jour payload.dependents[*].address_phone pour les mineurs
     * si same_contact_as_client n’est pas explicitement false.
     */
    private static function syncMinorDependentsFromClient(Set $set, Get $get): void
    {
        $deps = (array) ($get('payload.dependents') ?? []);
        if ($deps === []) return;

        $contact = self::clientContactString($get);

        foreach ($deps as $i => $d) {
            $birth = $d['birth_date'] ?? null;
            if (! self::isMinorDate($birth)) {
                continue;
            }

            $sync = $d['same_contact_as_client'] ?? true;
            if (! $sync) {
                continue;
            }

            $deps[$i]['same_contact_as_client'] = true;
            $deps[$i]['address_phone'] = $contact;
        }

        $set('payload.dependents', $deps);
    }
}
