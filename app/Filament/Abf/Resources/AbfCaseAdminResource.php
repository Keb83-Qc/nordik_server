<?php

namespace App\Filament\Abf\Resources;

use App\Filament\Abf\Resources\AbfCaseAdminResource\Pages;
use App\Models\AbfCase;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbfCaseAdminResource extends Resource
{
    protected static ?string $model = AbfCase::class;

    protected static ?string $navigationIcon  = 'heroicon-o-folder-open';
    protected static ?string $navigationLabel = 'Tous les dossiers ABF';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $modelLabel      = 'Dossier ABF';
    protected static ?string $pluralModelLabel = 'Tous les dossiers ABF';

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
                TextColumn::make('advisor.full_name')
                    ->label('Conseiller')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),

                TextColumn::make('client_last_name')
                    ->label('Nom client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '—')),

                TextColumn::make('client_first_name')
                    ->label('Prénom client')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ?? '—'),

                TextColumn::make('updated_at')
                    ->label('Dernière modif.')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label('Ouvrir')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (AbfCase $record) => $record->editor_url)
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
