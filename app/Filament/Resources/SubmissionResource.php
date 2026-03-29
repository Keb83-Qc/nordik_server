<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
use App\Models\ChatStep;
use App\Models\Submission;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use App\Services\SubmissionMailer;

class SubmissionResource extends Resource
{
    protected static ?string $model = Submission::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?string $navigationLabel = 'Soumissions reçues';

    public static function getNavigationGroup(): ?string
    {
        return 'Soumissions';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class);
    }

    /**
     * Helper: lit les champs depuis data JSON
     * - supporte flat legacy: data[first_name]
     * - supporte bundle: data[common][first_name], data[auto][year], data[habitation][address]
     */
    private static function d(?Submission $record, string $key): mixed
    {
        if (!$record) {
            return null;
        }

        $data = $record->data ?? [];

        // flat legacy
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        // bundle nested (ordre: common -> auto -> habitation)
        if (isset($data['common']) && is_array($data['common']) && array_key_exists($key, $data['common'])) {
            return $data['common'][$key];
        }
        if (isset($data['auto']) && is_array($data['auto']) && array_key_exists($key, $data['auto'])) {
            return $data['auto'][$key];
        }
        if (isset($data['habitation']) && is_array($data['habitation']) && array_key_exists($key, $data['habitation'])) {
            return $data['habitation'][$key];
        }

        return null;
    }

    /** Identifiants déjà affichés dans les sections hardcodées (+ champs internes préfixés _) */
    private const KNOWN_FIELDS = [
        '_phone_excluded',
        'first_name','last_name','email','phone','age','profession','existing_products',
        'best_contact_time','gender','phone_is_cell','marital_status','employment_status',
        'education_level','industry','has_ia_products',
        'vehicle_year','year','vehicle_brand_name','brand','brand_id',
        'vehicle_model_name','model','model_id','usage','km_annuel','renewal_date',
        'address','license_number',
        'occupancy','property_type','hab_renewal_date','living_there','years_at_address',
        'units_in_building','contents_amount','electric_baseboard','supp_heating',
        'years_insured','years_with_insurer','current_insurer',
        'consent_profile','consent_marketing','marketing_email','consent_credit',
    ];

    /**
     * Retourne les steps DB actifs non hardcodés qui ont une réponse dans la soumission.
     */
    private static function extraSteps(Submission $record): \Illuminate\Support\Collection
    {
        if (!in_array($record->type, ['auto', 'habitation'], true)) {
            return collect();
        }

        $data = $record->data ?? [];

        return ChatStep::where('chat_type', $record->type)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(fn($step) =>
                !in_array($step->identifier, self::KNOWN_FIELDS, true)
                && isset($data[$step->identifier])
                && $data[$step->identifier] !== ''
            );
    }

    private static function typeLabel(?string $type): string
    {
        return match ($type) {
            'auto' => 'Auto',
            'habitation' => 'Habitation',
            'bundle' => 'Bundle',
            default => $type ?: '-',
        };
    }

    private static function typeColor(?string $type): string
    {
        return match ($type) {
            'auto' => 'primary',
            'habitation' => 'success',
            'bundle' => 'warning',
            default => 'gray',
        };
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->whereIn('type', ['auto', 'habitation', 'bundle']);

        $user = auth()->user();

        if ($user && ($user->isSuperAdmin() || $user->hasRole('admin'))) {
            return $query;
        }

        if ($user && !empty($user->advisor_code)) {
            return $query->where('advisor_code', $user->advisor_code);
        }

        return $query->whereRaw('1=0');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ── Alerte LNNTE ─────────────────────────────────────────────────────
            Forms\Components\Placeholder::make('lnnte_warning')
                ->label('')
                ->columnSpanFull()
                ->visible(fn (?Submission $record): bool => (bool) $record?->is_phone_excluded)
                ->content(function (?Submission $record): HtmlString {
                    if (! $record?->is_phone_excluded) return new HtmlString('');

                    $phone = self::d($record, 'phone') ?? 'inconnu';
                    $entry  = \App\Models\ExcludedPhone::findByPhone($phone);
                    $reason = $entry
                        ? (\App\Models\ExcludedPhone::REASONS[$entry->reason] ?? $entry->reason)
                        : 'Non précisé';
                    $notes = $entry?->notes
                        ? '<br><span style="font-size:12px;">Notes : ' . e($entry->notes) . '</span>'
                        : '';

                    return new HtmlString(
                        '<div style="background:#fff3cd;border:2px solid #ffc107;border-radius:10px;padding:14px 18px;margin-bottom:8px;">' .
                        '<div style="font-size:15px;font-weight:800;color:#7a4f00;margin-bottom:4px;">⚠️ Numéro exclu — LNNTE interne</div>' .
                        '<div style="font-size:13px;color:#664d03;line-height:1.6;">' .
                        'Le numéro <strong>' . e($phone) . '</strong> est dans votre liste d\'exclusion.<br>' .
                        '<strong>Ne pas contacter par téléphone</strong> sauf consentement explicite.<br>' .
                        '<span>Raison : ' . e($reason) . '</span>' . $notes .
                        '</div></div>'
                    );
                }),

            Forms\Components\Placeholder::make('fiche_titre')
                ->label('')
                ->columnSpanFull()
                ->content(function (?Submission $record) {
                    if (!$record) return null;

                    $first = self::d($record, 'first_name') ?? '';
                    $last  = self::d($record, 'last_name') ?? '';
                    $client = trim($first . ' ' . $last);
                    if ($client === '') $client = 'Client';

                    $type = self::typeLabel($record->type);

                    return new HtmlString(
                        '<div class="text-center border-b border-[#c9a050] pb-3 mb-6">' .
                            '<div class="text-2xl font-extrabold tracking-wide uppercase text-gray-900 dark:text-white">' .
                            'Fiche soumission : ' . e($client) .
                            '</div>' .
                            '<div class="mt-1 text-sm text-gray-600 dark:text-gray-300">' .
                            'Type : <span class="font-semibold text-gray-900 dark:text-white">' . e($type) . '</span>' .
                            '</div>' .
                            '</div>'
                    );
                }),

            // ── Informations Client ──────────────────────────────────────────────
            Forms\Components\Section::make('Informations Client')
                ->compact()
                ->schema([
                    Forms\Components\Placeholder::make('first_name')
                        ->label('Prénom')
                        ->content(fn($record) => self::d($record, 'first_name') ?? '-'),

                    Forms\Components\Placeholder::make('last_name')
                        ->label('Nom de famille')
                        ->content(fn($record) => self::d($record, 'last_name') ?? '-'),

                    Forms\Components\Placeholder::make('email')
                        ->label('Courriel')
                        ->content(fn($record) => self::d($record, 'email') ?? '-'),

                    Forms\Components\Placeholder::make('phone')
                        ->label('Téléphone')
                        ->content(fn($record) => self::d($record, 'phone') ?? '-'),

                    Forms\Components\Placeholder::make('age')
                        ->label('Âge')
                        ->content(function ($record) {
                            $age = self::d($record, 'age');
                            return $age !== null && $age !== '' ? ($age . ' ans') : '-';
                        }),

                    Forms\Components\Placeholder::make('profession')
                        ->label('Profession')
                        ->content(fn($record) => self::d($record, 'profession') ?? '-'),

                    Forms\Components\Placeholder::make('existing_products')
                        ->label('Produits (ass./placements)')
                        ->content(function ($record) {
                            $v = self::d($record, 'existing_products');
                            return match ($v) {
                                'assurance' => 'Assurances',
                                'placement' => 'Placements',
                                'both'      => 'Assurances et Placements',
                                'none'      => 'Aucun',
                                default     => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('best_contact_time')
                        ->label('Meilleur moment de contact')
                        ->content(function ($record) {
                            $v = self::d($record, 'best_contact_time');
                            return match ($v) {
                                'matin'          => 'Matin (8h - 12h)',
                                'apres_midi'     => 'Après-midi (12h - 17h)',
                                'soir'           => 'Soir (17h - 20h)',
                                'nimporte_quand' => "N'importe quand",
                                default          => $v ?: '-',
                            };
                        }),

                    // ── Champs spécifiques au profil habitation ──
                    Forms\Components\Placeholder::make('gender')
                        ->label('Genre')
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'gender');
                            return match ($v) {
                                'homme'      => 'Homme',
                                'femme'      => 'Femme',
                                'autre'      => 'Autre',
                                'prefer_not' => 'Préfère ne pas préciser',
                                default      => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('phone_is_cell')
                        ->label('Téléphone cellulaire ?')
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'phone_is_cell');
                            return match ($v) {
                                'yes'   => 'Oui',
                                'no'    => 'Non',
                                default => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('marital_status')
                        ->label('État civil')
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'marital_status');
                            return match ($v) {
                                'celibataire' => 'Célibataire',
                                'conjoint'    => 'Conjoint(e) de fait',
                                'marie'       => 'Marié(e)',
                                'autre'       => 'Autre',
                                default       => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('employment_status')
                        ->label('Statut professionnel')
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'employment_status');
                            return match ($v) {
                                'employe'                   => 'Employé(e)',
                                'travailleur_autonome',
                                'self'                      => 'Travailleur autonome',
                                'etudiant', 'student'       => 'Étudiant(e)',
                                'retraite', 'retired'       => 'Retraité(e)',
                                'sans_emploi', 'unemployed' => 'Sans emploi',
                                default                     => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('education_level')
                        ->label("Niveau d'éducation")
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'education_level');
                            return match ($v) {
                                'secondaire', 'highschool' => 'Secondaire',
                                'college'                  => 'Collège/Cégep',
                                'universite', 'university' => 'Université',
                                'autre', 'other'           => 'Autre',
                                default                    => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('industry')
                        ->label("Secteur d'activité")
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(fn($record) => self::d($record, 'industry') ?? '-'),

                    Forms\Components\Placeholder::make('has_ia_products')
                        ->label('Produits IA/VIP existants ?')
                        ->visible(fn($record) => in_array($record?->type, ['habitation', 'bundle'], true))
                        ->content(function ($record) {
                            $v = self::d($record, 'has_ia_products');
                            return match ($v) {
                                'yes'   => 'Oui',
                                'no'    => 'Non',
                                default => $v ?: '-',
                            };
                        }),
                ])
                ->columns(2),

            // ── Informations Véhicule ────────────────────────────────────────────
            Forms\Components\Section::make('Informations Véhicule')
                ->compact()
                ->visible(
                    fn(?Submission $record): bool =>
                    in_array($record?->type, ['auto', 'bundle'], true)
                )
                ->schema([
                    Forms\Components\Placeholder::make('vehicule_titre')
                        ->label('Véhicule sélectionné')
                        ->columnSpanFull()
                        ->content(function ($record) {
                            $year  = self::d($record, 'vehicle_year') ?? self::d($record, 'year') ?? '';
                            $brand = self::d($record, 'vehicle_brand_name') ?? self::d($record, 'brand') ?? '';
                            $model = self::d($record, 'vehicle_model_name') ?? self::d($record, 'model') ?? '';
                            $txt = trim($year . ' ' . $brand . ' ' . $model);
                            if ($txt === '') $txt = '-';

                            return new HtmlString(
                                '<span style="font-size:1.1rem;font-weight:bold;color:#c9a050;">' . e($txt) . '</span>'
                            );
                        }),

                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('usage')
                            ->label('Usage')
                            ->content(fn($record) => self::d($record, 'usage') ?? '-'),

                        Forms\Components\Placeholder::make('km_annuel')
                            ->label('KM Annuel')
                            ->content(fn($record) => self::d($record, 'km_annuel') ?? '-'),

                        Forms\Components\Placeholder::make('renewal_date')
                            ->label('Renouvellement')
                            ->content(fn($record) => self::d($record, 'renewal_date') ?? '-'),
                    ]),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('address_auto')
                            ->label('Adresse')
                            ->content(fn($record) => self::d($record, 'address') ?? '-'),

                        Forms\Components\Placeholder::make('license_number')
                            ->label('No. de permis')
                            ->content(function ($record) {
                                $v = self::d($record, 'license_number');
                                return ($v && $v !== 'not_provided') ? $v : 'Non fourni';
                            }),
                    ]),
                ]),

            // ── Informations Habitation ──────────────────────────────────────────
            Forms\Components\Section::make('Informations Habitation')
                ->compact()
                ->visible(
                    fn(?Submission $record): bool =>
                    in_array($record?->type, ['habitation', 'bundle'], true)
                )
                ->schema([
                    Forms\Components\Placeholder::make('occupancy')
                        ->label('Occupation')
                        ->content(fn($record) => self::d($record, 'occupancy') ?? '-'),

                    Forms\Components\Placeholder::make('property_type')
                        ->label('Type de propriété')
                        ->content(fn($record) => self::d($record, 'property_type') ?? '-'),

                    Forms\Components\Placeholder::make('address_home')
                        ->label('Adresse du bien')
                        ->columnSpanFull()
                        ->content(fn($record) => self::d($record, 'address') ?? '-'),

                    Forms\Components\Placeholder::make('hab_renewal_date')
                        ->label('Renouvellement habitation')
                        ->content(fn($record) => self::d($record, 'hab_renewal_date') ?? '-'),

                    Forms\Components\Placeholder::make('living_there')
                        ->label('Réside à cette adresse ?')
                        ->content(function ($record) {
                            $v = self::d($record, 'living_there');
                            return match ($v) {
                                'yes'   => 'Oui',
                                'no'    => 'Non',
                                default => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('years_at_address')
                        ->label("Années à l'adresse")
                        ->content(function ($record) {
                            $v = self::d($record, 'years_at_address');
                            return $v !== null && $v !== '' ? ($v . ' an(s)') : '-';
                        }),

                    Forms\Components\Placeholder::make('units_in_building')
                        ->label("Unités dans l'immeuble")
                        ->content(fn($record) => self::d($record, 'units_in_building') ?? '-'),

                    Forms\Components\Placeholder::make('contents_amount')
                        ->label('Valeur des biens assurés')
                        ->content(function ($record) {
                            $v = self::d($record, 'contents_amount');
                            return $v !== null && $v !== '' ? number_format((float)$v, 0, ',', ' ') . ' $' : '-';
                        }),

                    Forms\Components\Placeholder::make('electric_baseboard')
                        ->label('Plinthes élect. (chauffage principal) ?')
                        ->content(function ($record) {
                            $v = self::d($record, 'electric_baseboard');
                            return match ($v) {
                                'yes'   => 'Oui',
                                'no'    => 'Non',
                                default => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('supp_heating')
                        ->label("Chauffage d'appoint ?")
                        ->content(function ($record) {
                            $v = self::d($record, 'supp_heating');
                            return match ($v) {
                                'yes'   => 'Oui',
                                'no'    => 'Non',
                                default => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('years_insured')
                        ->label("Années d'assurance habitation")
                        ->content(function ($record) {
                            $v = self::d($record, 'years_insured');
                            return match ($v) {
                                '0'       => '0 an',
                                '1_2'     => '1-2 ans',
                                '3_5'     => '3-5 ans',
                                '6_10'    => '6-10 ans',
                                '11_plus' => '11 ans et plus',
                                default   => $v ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('years_with_insurer')
                        ->label("Années avec l'assureur actuel")
                        ->content(function ($record) {
                            $v = self::d($record, 'years_with_insurer');
                            return $v !== null && $v !== '' ? ($v . ' an(s)') : '-';
                        }),

                    Forms\Components\Placeholder::make('current_insurer')
                        ->label('Assureur actuel')
                        ->content(fn($record) => self::d($record, 'current_insurer') ?? '-'),
                ])
                ->columns(2),

            // ── Consentements ────────────────────────────────────────────────────
            Forms\Components\Section::make('Consentements')
                ->compact()
                ->schema([
                    Forms\Components\Placeholder::make('consent_profile')
                        ->label('Profilage')
                        ->content(function ($record) {
                            return match (self::d($record, 'consent_profile')) {
                                'accept' => '✅ Accepté',
                                'refuse' => '❌ Refusé',
                                default  => self::d($record, 'consent_profile') ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('consent_marketing')
                        ->label('Communications marketing')
                        ->content(function ($record) {
                            return match (self::d($record, 'consent_marketing')) {
                                'accept' => '✅ Accepté',
                                'refuse' => '❌ Refusé',
                                default  => self::d($record, 'consent_marketing') ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('marketing_email')
                        ->label('Marketing par courriel')
                        ->content(function ($record) {
                            return match (self::d($record, 'marketing_email')) {
                                'yes'   => '✅ Oui',
                                'no'    => '❌ Non',
                                default => self::d($record, 'marketing_email') ?: '-',
                            };
                        }),

                    Forms\Components\Placeholder::make('consent_credit')
                        ->label('Vérification de crédit')
                        ->content(function ($record) {
                            return match (self::d($record, 'consent_credit')) {
                                'yes'   => '✅ Autorisé',
                                'no'    => '❌ Refusé',
                                default => self::d($record, 'consent_credit') ?: '-',
                            };
                        }),
                ])
                ->columns(2),

            // ── Champs additionnels (steps Filament non hardcodés) ───────────────
            Forms\Components\Section::make('Champs additionnels')
                ->compact()
                ->visible(fn(?Submission $record): bool =>
                    $record !== null && self::extraSteps($record)->isNotEmpty()
                )
                ->schema([
                    Forms\Components\Placeholder::make('extra_fields_html')
                        ->label('')
                        ->columnSpanFull()
                        ->content(function (?Submission $record): HtmlString {
                            if (!$record) return new HtmlString('');

                            $data  = $record->data ?? [];
                            $steps = self::extraSteps($record);

                            $html = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:8px 24px;">';
                            foreach ($steps as $step) {
                                $label = is_array($step->question)
                                    ? ($step->question['fr'] ?? $step->identifier)
                                    : ($step->question ?? $step->identifier);
                                $value = e($data[$step->identifier] ?? '-');
                                $html .= '<div style="padding:6px 0;border-bottom:1px solid #f0f0f0;">'
                                    . '<span style="font-weight:600;color:#374151;">' . e($label) . ' :</span> '
                                    . '<span style="color:#111827;">' . $value . '</span>'
                                    . '</div>';
                            }
                            $html .= '</div>';

                            return new HtmlString($html);
                        }),
                ])
                ->columns(1),

            // ── Suivi ────────────────────────────────────────────────────────────
            Forms\Components\Section::make('Suivi')
                ->compact()
                ->schema([
                    Forms\Components\Placeholder::make('advisor')
                        ->label('Conseiller lié')
                        ->content(fn($record) => $record->advisor_code ?? 'Aucun'),

                    Forms\Components\Placeholder::make('created_at')
                        ->label('Date de réception')
                        ->content(fn($record) => $record->created_at?->format('d/m/Y H:i') ?? '-'),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->formatStateUsing(fn($state) => self::typeLabel($state))
                    ->color(fn($state) => self::typeColor($state)),

                Tables\Columns\IconColumn::make('is_phone_excluded')
                    ->label('LNNTE')
                    ->boolean()
                    ->trueIcon('heroicon-o-phone-x-mark')
                    ->falseIcon('heroicon-o-phone')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->tooltip(fn (Submission $record): string =>
                        $record->is_phone_excluded
                            ? '⚠️ Numéro dans la liste d\'exclusion LNNTE interne'
                            : 'Numéro libre'
                    ),

                Tables\Columns\TextColumn::make('client')
                    ->label('Client')
                    ->getStateUsing(function ($record) {
                        $first = self::d($record, 'first_name') ?? '';
                        $last  = self::d($record, 'last_name') ?? '';
                        $email = self::d($record, 'email') ?? '';
                        $phone = self::d($record, 'phone') ?? '';

                        $name = trim($first . ' ' . $last);
                        if ($name === '') $name = 'Client';

                        return $name . ($email ? " • $email" : '') . ($phone ? " • $phone" : '');
                    })
                    ->searchable() // recherche globale standard (on garde simple ici)
                    ->wrap()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('vehicule')
                    ->label('Véhicule')
                    ->visible(
                        fn(?Submission $record): bool =>
                        in_array($record?->type, ['auto', 'bundle'], true)
                    )
                    ->getStateUsing(fn($record) => trim(
                        (self::d($record, 'vehicle_year') ?? self::d($record, 'year') ?? '-') . ' ' .
                            (self::d($record, 'vehicle_brand_name') ?? self::d($record, 'brand') ?? '-') . ' ' .
                            (self::d($record, 'vehicle_model_name') ?? self::d($record, 'model') ?? '')
                    ))
                    ->badge()
                    ->color('primary')
                    ->wrap(),

                Tables\Columns\TextColumn::make('advisor_code')
                    ->label('Conseiller')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')->label('Du'),
                        Forms\Components\DatePicker::make('until')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),

                Tables\Filters\TernaryFilter::make('is_phone_excluded')
                    ->label('Numéros exclus LNNTE')
                    ->placeholder('Tous')
                    ->trueLabel('Exclus seulement ⚠️')
                    ->falseLabel('Non exclus ✅'),

                Tables\Filters\SelectFilter::make('advisor_code')
                    ->label('Conseiller')
                    ->options(
                        fn() => \Illuminate\Support\Facades\Cache::remember('filter_advisor_codes', 300, fn() =>
                            User::query()
                                ->whereNotNull('advisor_code')
                                ->orderBy('first_name')
                                ->select(['first_name', 'last_name', 'advisor_code'])
                                ->get()
                                ->mapWithKeys(fn($u) => [$u->advisor_code => "{$u->first_name} {$u->last_name} ({$u->advisor_code})"])
                                ->toArray()
                        )
                    )
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'auto' => 'Auto',
                        'habitation' => 'Habitation',
                        'bundle' => 'Bundle',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Détails de la soumission')
                    ->modalWidth('4xl')
                    ->modalSubmitAction(false),

                Tables\Actions\Action::make('resend_email')
                    ->label('Renvoyer email')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Renvoyer l’email de soumission ?')
                    ->modalDescription("Ça renverra exactement le même email que celui envoyé automatiquement après la soumission.")
                    ->action(function (Submission $record) {
                        try {
                            SubmissionMailer::sendSubmissionEmail($record);

                            Notification::make()
                                ->title('Email renvoyé')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title("Erreur d'envoi")
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSubmissions::route('/'),
        ];
    }
}
