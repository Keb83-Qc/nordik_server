<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbfCaseAdminResource\Pages;
use App\Models\AbfCase;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbfCaseAdminResource extends Resource
{
    protected static ?string $model = AbfCase::class;

    protected static ?string $navigationIcon  = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Dossiers ABF';
    protected static ?string $modelLabel      = 'Dossier ABF';
    protected static ?string $pluralModelLabel = 'Dossiers ABF';
    protected static ?int    $navigationSort  = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion Clients';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    // Pas de create depuis cet espace — uniquement lecture/suppression
    public static function canCreate(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(AbfCase::query()->with('advisor'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),

                TextColumn::make('advisor.name')
                    ->label('Conseiller')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('advisor_code')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('client_last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '—')),

                TextColumn::make('client_first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? '—'),

                TextColumn::make('client_birth_date')
                    ->label('Date naissance')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'draft'     => 'gray',
                        'completed' => 'warning',
                        'signed'    => 'success',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'draft'     => 'Brouillon',
                        'completed' => 'Complété',
                        'signed'    => 'Signé',
                        default     => $state,
                    }),

                TextColumn::make('progress_percent')
                    ->label('Progression')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === null   => 'gray',
                        $state < 50       => 'danger',
                        $state < 90       => 'warning',
                        default           => 'success',
                    })
                    ->formatStateUsing(fn ($state) => $state !== null ? "{$state}%" : '—'),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'draft'     => 'Brouillon',
                        'completed' => 'Complété',
                        'signed'    => 'Signé',
                    ]),

                SelectFilter::make('advisor_code')
                    ->label('Code conseiller')
                    ->options(
                        AbfCase::query()
                            ->distinct()
                            ->orderBy('advisor_code')
                            ->whereNotNull('advisor_code')
                            ->pluck('advisor_code', 'advisor_code')
                            ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (AbfCase $record) => $record->getEditorUrlAttribute())
                    ->openUrlInNewTab(),

                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer ce dossier ABF ?')
                    ->modalDescription('Cette action est irréversible. Toutes les données du dossier seront supprimées définitivement.')
                    ->modalSubmitActionLabel('Oui, supprimer'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Supprimer les dossiers sélectionnés ?')
                        ->modalDescription('Cette action est irréversible.')
                        ->modalSubmitActionLabel('Oui, supprimer'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAllAbfCases::route('/'),
        ];
    }
}
