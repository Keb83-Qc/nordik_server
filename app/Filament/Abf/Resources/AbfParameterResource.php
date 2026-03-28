<?php

namespace App\Filament\Abf\Resources;

use App\Filament\Abf\Resources\AbfParameterResource\Pages;
use App\Models\AbfParameter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AbfParameterResource extends Resource
{
    protected static ?string $model = AbfParameter::class;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Paramètres ABF';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $modelLabel      = 'Paramètre';
    protected static ?string $pluralModelLabel = 'Paramètres ABF';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('group')
                ->label('Groupe')
                ->options([
                    'hypotheses'    => 'Hypothèses générales',
                    'portefeuilles' => 'Rendements portefeuilles',
                    'deces'         => 'Décès',
                    'invalidite'    => 'Invalidité',
                    'maladie_grave' => 'Maladie grave',
                    'retraite'      => 'Retraite',
                    'fonds_urgence' => "Fonds d'urgence",
                    'rrq'           => 'RRQ / RPC',
                    'abf'           => 'ABF — Général',
                ])
                ->required()
                ->columnSpan(1),

            TextInput::make('key')
                ->label('Clé')
                ->required()
                ->maxLength(100)
                ->columnSpan(1),

            TextInput::make('label')
                ->label('Libellé')
                ->required()
                ->maxLength(200)
                ->columnSpan(2),

            Select::make('type')
                ->label('Type')
                ->options([
                    'number'  => 'Nombre',
                    'percent' => 'Pourcentage (%)',
                    'text'    => 'Texte',
                    'select'  => 'Sélection',
                    'boolean' => 'Oui / Non',
                ])
                ->required()
                ->columnSpan(1),

            TextInput::make('value')
                ->label('Valeur')
                ->required()
                ->columnSpan(1),

            Textarea::make('description')
                ->label('Description')
                ->rows(2)
                ->columnSpan(2),

            TextInput::make('sort_order')
                ->label('Ordre d\'affichage')
                ->numeric()
                ->default(0)
                ->columnSpan(1),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->label('Groupe')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'hypotheses'    => 'Hypothèses',
                        'portefeuilles' => 'Portefeuilles',
                        'deces'         => 'Décès',
                        'invalidite'    => 'Invalidité',
                        'maladie_grave' => 'Maladie grave',
                        'retraite'      => 'Retraite',
                        'fonds_urgence' => "Fonds d'urgence",
                        'rrq'           => 'RRQ / RPC',
                        'abf'           => 'ABF',
                        default         => $state,
                    })
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Clé')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('value')
                    ->label('Valeur')
                    ->badge()
                    ->color('success'),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('group')
            ->filters([
                SelectFilter::make('group')
                    ->label('Groupe')
                    ->options([
                        'hypotheses'    => 'Hypothèses générales',
                        'portefeuilles' => 'Rendements portefeuilles',
                        'deces'         => 'Décès',
                        'invalidite'    => 'Invalidité',
                        'maladie_grave' => 'Maladie grave',
                        'retraite'      => 'Retraite',
                        'fonds_urgence' => "Fonds d'urgence",
                        'rrq'           => 'RRQ / RPC',
                        'abf'           => 'ABF — Général',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbfParameters::route('/'),
            'edit'  => Pages\EditAbfParameter::route('/{record}/edit'),
        ];
    }
}
