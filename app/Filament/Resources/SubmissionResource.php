<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubmissionResource\Pages;
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
    protected static ?string $navigationLabel = 'Soumissions';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion Clients';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class);
    }

    public static function canViewAny(): bool
    {
        return auth()->check();
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
                                'both' => 'Assurances et Placements',
                                'none' => 'Aucun',
                                default => $v ?: '-',
                            };
                        }),
                ])
                ->columns(2),

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
                ]),

            Forms\Components\Section::make('Informations Habitation')
                ->compact()
                ->visible(
                    fn(?Submission $record): bool =>
                    in_array($record?->type, ['habitation', 'bundle'], true)
                )
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('occupancy')
                            ->label('Occupation')
                            ->content(fn($record) => self::d($record, 'occupancy') ?? '-'),

                        Forms\Components\Placeholder::make('property_type')
                            ->label('Type de propriété')
                            ->content(fn($record) => self::d($record, 'property_type') ?? '-'),

                        Forms\Components\Placeholder::make('address_home')
                            ->label('Adresse')
                            ->content(fn($record) => self::d($record, 'address') ?? '-'),

                        Forms\Components\Placeholder::make('living_there')
                            ->label('Vivez-vous à cette adresse ?')
                            ->content(fn($record) => self::d($record, 'living_there') ?? '-'),
                    ]),
                ]),

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

                Tables\Filters\SelectFilter::make('advisor_code')
                    ->label('Conseiller')
                    ->options(
                        fn() => User::query()
                            ->whereNotNull('advisor_code')
                            ->orderBy('first_name')
                            ->get()
                            ->mapWithKeys(fn($u) => [$u->advisor_code => "{$u->first_name} {$u->last_name} ({$u->advisor_code})"])
                            ->toArray()
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
