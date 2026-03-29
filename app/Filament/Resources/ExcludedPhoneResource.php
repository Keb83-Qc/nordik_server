<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExcludedPhoneResource\Pages;
use App\Models\ExcludedPhone;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ExcludedPhoneResource extends Resource
{
    protected static ?string $model = ExcludedPhone::class;

    protected static ?string $navigationIcon  = 'heroicon-o-phone-x-mark';
    protected static ?string $navigationLabel = 'Numéros exclus';
    protected static ?string $modelLabel      = 'Numéro exclu';
    protected static ?string $pluralModelLabel = 'Numéros exclus (LNNTE interne)';

    public static function getNavigationGroup(): ?string
    {
        return 'Conformité';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class, 1);
    }

    // Visible et accessible uniquement aux administrateurs
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    // ─── Formulaire ───────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Numéro de téléphone')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Numéro de téléphone')
                        ->required()
                        ->tel()
                        ->placeholder('ex: (418) 555-1234')
                        ->maxLength(30)
                        ->helperText('Saisissez le numéro dans n\'importe quel format — la normalisation est automatique.')
                        ->unique(
                            table: ExcludedPhone::class,
                            column: 'phone_normalized',
                            ignoreRecord: true,
                            modifyRuleUsing: fn ($rule, $state) => $rule->where(
                                'phone_normalized',
                                ExcludedPhone::normalize($state ?? '')
                            ),
                        )
                        ->columnSpan(1),

                    Forms\Components\Select::make('reason')
                        ->label('Raison')
                        ->required()
                        ->options(ExcludedPhone::REASONS)
                        ->default('client_request')
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Détails')
                ->columns(2)
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->placeholder('Contexte, nom du client, référence dossier...')
                        ->rows(3)
                        ->maxLength(1000)
                        ->columnSpan(1),

                    Forms\Components\DateTimePicker::make('expires_at')
                        ->label('Expiration')
                        ->helperText('Laisser vide pour une exclusion permanente.')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->minDate(now())
                        ->columnSpan(1),
                ]),

            // Champ caché — rempli automatiquement avec l'utilisateur connecté
            Forms\Components\Hidden::make('added_by')
                ->default(fn () => auth()->id()),

        ]);
    }

    // ─── Tableau ──────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([

                Tables\Columns\TextColumn::make('phone')
                    ->label('Numéro')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->copyMessage('Numéro copié !')
                    ->icon('heroicon-m-phone'),

                Tables\Columns\TextColumn::make('reason')
                    ->label('Raison')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string =>
                        ExcludedPhone::REASONS[$state] ?? $state
                    )
                    ->color(fn (string $state): string => match ($state) {
                        'lnnte_official'  => 'danger',
                        'deceased'        => 'gray',
                        'client_request'  => 'warning',
                        'do_not_disturb'  => 'warning',
                        'competitor'      => 'info',
                        'internal'        => 'primary',
                        default           => 'gray',
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(fn (ExcludedPhone $record): ?string => $record->notes)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('addedBy.first_name')
                    ->label('Ajouté par')
                    ->formatStateUsing(fn ($state, ExcludedPhone $record): string =>
                        $record->addedBy
                            ? $record->addedBy->first_name . ' ' . $record->addedBy->last_name
                            : '—'
                    )
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expiration')
                    ->dateTime('d/m/Y')
                    ->placeholder('Permanent ♾️')
                    ->color(fn (ExcludedPhone $record): string =>
                        $record->is_expired ? 'danger' : 'success'
                    ),

                // Badge actif / expiré
                Tables\Columns\IconColumn::make('status')
                    ->label('Statut')
                    ->state(fn (ExcludedPhone $record): bool => ! $record->is_expired)
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->tooltip(fn (ExcludedPhone $record): string =>
                        $record->is_expired ? 'Expiré' : 'Actif'
                    ),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ajouté le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                Tables\Filters\SelectFilter::make('reason')
                    ->label('Raison')
                    ->options(ExcludedPhone::REASONS),

                Tables\Filters\Filter::make('active_only')
                    ->label('Actifs seulement')
                    ->query(fn ($query) => $query->active())
                    ->default(),

                Tables\Filters\Filter::make('permanent')
                    ->label('Permanents seulement')
                    ->query(fn ($query) => $query->whereNull('expires_at')),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // ─── Pages ────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListExcludedPhones::route('/'),
            'create' => Pages\CreateExcludedPhone::route('/create'),
            'edit'   => Pages\EditExcludedPhone::route('/{record}/edit'),
        ];
    }
}
