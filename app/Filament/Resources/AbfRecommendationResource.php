<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbfRecommendationResource\Pages;
use App\Models\AbfRecommendation;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AbfRecommendationResource extends Resource
{
    protected static ?string $model = AbfRecommendation::class;

    protected static ?string $slug             = 'abf-recommandations';
    protected static ?string $navigationIcon   = 'heroicon-o-light-bulb';
    protected static ?string $navigationLabel  = 'Recommandations ABF';
    protected static ?string $modelLabel       = 'Recommandation';
    protected static ?string $pluralModelLabel = 'Recommandations ABF';

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion Clients';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function getCategoryOptions(): array
    {
        return [
            'deces'         => 'Décès',
            'invalidite'    => 'Invalidité',
            'maladie-grave' => 'Maladie grave',
            'fonds-urgence' => "Fonds d'urgence",
            'retraite'      => 'Retraite',
            'conseils'      => 'Conseils généraux',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('category')
                ->label('Catégorie')
                ->options(static::getCategoryOptions())
                ->required()
                ->live()
                ->columnSpanFull(),

            TextInput::make('title')
                ->label('Titre')
                ->helperText('Pour les conseils : titre de l\'accordéon. Pour les autres : libellé dans le menu Ajouter.')
                ->maxLength(500)
                ->required()
                ->live(debounce: 400)
                ->afterStateUpdated(function (?string $state, Set $set, Get $get): void {
                    // Générer la clé automatiquement seulement si elle est encore vide
                    if (blank($get('key')) && filled($state)) {
                        $set('key', Str::camel(Str::slug($state, ' ')));
                    }
                })
                ->columnSpanFull(),

            TextInput::make('key')
                ->label('Clé (slug unique)')
                ->helperText('Générée automatiquement depuis le titre (camelCase). Identifiant utilisé par le JS — ne pas modifier après création.')
                ->maxLength(100)
                ->readOnly()
                ->columnSpanFull(),

            Textarea::make('text')
                ->label('Texte de la recommandation')
                ->required()
                ->rows(5)
                ->columnSpanFull(),

            Toggle::make('checked_by_default')
                ->label('Coché par défaut (Conseils seulement)')
                ->visible(fn (Get $get) => $get('category') === 'conseils')
                ->columnSpanFull(),

            Toggle::make('is_active')
                ->label('Actif')
                ->default(true)
                ->inline(false),

            TextInput::make('sort_order')
                ->label('Ordre d\'affichage')
                ->numeric()
                ->default(0)
                ->minValue(0),

        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'deces'         => 'danger',
                        'invalidite'    => 'warning',
                        'maladie-grave' => 'info',
                        'fonds-urgence' => 'success',
                        'retraite'      => 'primary',
                        'conseils'      => 'gray',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => static::getCategoryOptions()[$state] ?? $state)
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => strlen($column->getState() ?? '') > 50 ? $column->getState() : null),

                TextColumn::make('text')
                    ->label('Texte')
                    ->limit(80)
                    ->color('gray')
                    ->tooltip(fn (TextColumn $column): ?string => strlen($column->getState() ?? '') > 80 ? $column->getState() : null),

                TextColumn::make('sort_order')
                    ->label('Ordre')
                    ->sortable()
                    ->alignCenter()
                    ->width('60px'),

                IconColumn::make('checked_by_default')
                    ->label('Défaut ✓')
                    ->boolean()
                    ->alignCenter()
                    ->width('70px'),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean()
                    ->alignCenter()
                    ->width('60px'),
            ])
            ->defaultSort('category')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options(static::getCategoryOptions()),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actifs seulement')
                    ->falseLabel('Inactifs seulement')
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer cette recommandation ?')
                    ->modalDescription('Cette action est irréversible. Les dossiers ABF déjà sauvegardés ne seront pas affectés.')
                    ->modalSubmitActionLabel('Oui, supprimer'),
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
            'index'  => Pages\ListAbfRecommendations::route('/'),
            'create' => Pages\CreateAbfRecommendation::route('/create'),
            'edit'   => Pages\EditAbfRecommendation::route('/{record}/edit'),
        ];
    }
}
