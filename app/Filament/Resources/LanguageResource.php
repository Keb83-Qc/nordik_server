<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LanguageResource\Pages;
use App\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';
    protected static ?string $navigationLabel = 'Langues';

    public static function getNavigationGroup(): ?string
    {
        return 'GestionLangues';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Langue')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom')
                        ->required()
                        ->maxLength(50),

                    Forms\Components\TextInput::make('code')
                        ->label('Code')
                        ->helperText('Ex: fr, en, es, ht')
                        ->required()
                        ->maxLength(5)
                        ->rule('alpha')
                        ->unique(ignoreRecord: true)
                        ->dehydrateStateUsing(fn($state) => strtolower(trim($state))),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),

                    Forms\Components\Toggle::make('is_default')
                        ->label('Langue par défaut')
                        ->helperText('Une seule langue peut être par défaut.'),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('code')->label('Code')->searchable()->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Active')->boolean()->sortable(),
                Tables\Columns\IconColumn::make('is_default')->label('Par défaut')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->label('Ordre')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->label('MAJ')->since()->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Action "Définir par défaut" (propre et safe)
                Tables\Actions\Action::make('setDefault')
                    ->label('Définir par défaut')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->visible(fn(Language $record) => !$record->is_default)
                    ->action(function (Language $record) {
                        Language::query()->update(['is_default' => false]);
                        $record->update(['is_default' => true, 'is_active' => true]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                // Optionnel : afficher toujours la langue par défaut en premier
                return $query->orderByDesc('is_default')->orderBy('sort_order');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
