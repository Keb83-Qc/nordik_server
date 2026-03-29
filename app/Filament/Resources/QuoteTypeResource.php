<?php

namespace App\Filament\Resources;

use App\Filament\Actions\DeeplTranslateAction;
use App\Filament\Resources\QuoteTypeResource\Pages;
use App\Models\QuoteType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteTypeResource extends Resource
{
    protected static ?string $model = QuoteType::class;

    protected static ?string $navigationIcon  = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Types de soumissions';
    protected static ?string $modelLabel      = 'Type de soumission';
    protected static ?string $pluralModelLabel = 'Types de soumissions';

    public static function getNavigationGroup(): ?string
    {
        return 'Soumissions';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class, 1);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identification')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('slug')
                        ->label('Identifiant unique (slug)')
                        ->required()
                        ->unique(QuoteType::class, 'slug', ignoreRecord: true)
                        ->helperText('Ex: auto, habitation, bundle — minuscules, pas d\'espaces')
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Libellés (toutes langues)')
                ->description('Le libellé affiché sur les boutons de sélection')
                ->headerActions([
                    DeeplTranslateAction::forField('label'),
                ])
                ->schema([
                    Forms\Components\Tabs::make('Traductions')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('🇫🇷 Français')
                                ->schema([
                                    Forms\Components\TextInput::make('label.fr')
                                        ->label('Libellé en français')
                                        ->required()
                                        ->maxLength(100),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                ->schema([
                                    Forms\Components\TextInput::make('label.en')
                                        ->label('Label in English')
                                        ->maxLength(100),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇪🇸 Español')
                                ->schema([
                                    Forms\Components\TextInput::make('label.es')
                                        ->label('Etiqueta en español')
                                        ->maxLength(100),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇭🇹 Kreyòl')
                                ->schema([
                                    Forms\Components\TextInput::make('label.ht')
                                        ->label('Etikèt an kreyòl')
                                        ->maxLength(100),
                                ]),
                        ]),
                ]),

            Forms\Components\Section::make('Apparence')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('icon')
                        ->label('Icône Heroicon')
                        ->placeholder('heroicon-o-truck')
                        ->helperText('Nom de l\'icône Heroicons (ex: heroicon-o-home, heroicon-o-truck)')
                        ->columnSpan(1),

                    Forms\Components\Select::make('color')
                        ->label('Couleur du badge')
                        ->options([
                            'info'    => '🔵 Bleu (info)',
                            'success' => '🟢 Vert (success)',
                            'warning' => '🟠 Orange (warning)',
                            'danger'  => '🔴 Rouge (danger)',
                            'gray'    => '⚫ Gris (gray)',
                            'primary' => '🟣 Primaire',
                        ])
                        ->default('info')
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Visibilité')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Type actif')
                        ->helperText('Un type inactif n\'apparaîtra dans aucun portail')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width(50),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Icône')
                    ->formatStateUsing(fn ($state) => $state ?? '—')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('label')
                    ->label('Libellé (FR)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? ($state['fr'] ?? '—') : $state)
                    ->searchable(query: fn ($query, $search) => $query->whereRaw(
                        "JSON_UNQUOTE(JSON_EXTRACT(label, '$.fr')) LIKE ?",
                        ["%{$search}%"]
                    ))
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('color')
                    ->label('Couleur')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'info'    => 'Bleu',
                        'success' => 'Vert',
                        'warning' => 'Orange',
                        'danger'  => 'Rouge',
                        'gray'    => 'Gris',
                        'primary' => 'Primaire',
                        default   => $state ?? '—',
                    })
                    ->color(fn ($state) => $state ?? 'gray'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListQuoteTypes::route('/'),
            'create' => Pages\CreateQuoteType::route('/create'),
            'edit'   => Pages\EditQuoteType::route('/{record}/edit'),
        ];
    }
}
