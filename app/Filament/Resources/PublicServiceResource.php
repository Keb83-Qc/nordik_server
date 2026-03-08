<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicServiceResource\Pages;
use App\Models\PublicService;
use App\Models\PublicServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PublicServiceResource extends Resource
{
    protected static ?string $model = PublicService::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Services - Slugs';

    public static function getActiveLocales(): array
    {
        return DB::table('languages')
            ->where('is_active', 1)
            ->orderBy('sort_order')
            ->pluck('code')
            ->toArray();
    }

    public static function form(Form $form): Form
    {
        $locales = static::getActiveLocales();

        return $form->schema([
            Forms\Components\Section::make('Service')
                ->schema([
                    Forms\Components\Select::make('public_service_category_id')
                        ->label('Catégorie')
                        ->options(
                            PublicServiceCategory::query()
                                ->orderBy('sort_order')
                                ->pluck('id', 'id') // simple; si tu veux afficher le nom traduit, on peut l’améliorer
                                ->toArray()
                        )
                        ->searchable()
                        ->required(),

                    Forms\Components\Toggle::make('is_active')->default(true),

                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                ])->columns(3),

            Forms\Components\Section::make('Traductions (Titre + Slug)')
                ->schema([
                    Forms\Components\Tabs::make('i18n_tabs')
                        ->tabs(
                            collect($locales)->map(function (string $locale) {
                                return Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                    ->schema([
                                        Forms\Components\TextInput::make("i18n.$locale.title")
                                            ->label('Titre')
                                            ->required()
                                            ->maxLength(191),

                                        Forms\Components\TextInput::make("i18n.$locale.slug")
                                            ->label('Slug')
                                            ->required()
                                            ->maxLength(191),
                                    ])->columns(2);
                            })->toArray()
                        ),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        $locale = app()->getLocale();

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('public_service_category_id')
                    ->label('Catégorie ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('translations.title')
                    ->label('Titre')
                    ->getStateUsing(fn($record) => optional($record->translations->firstWhere('locale', $locale))->title)
                    ->searchable(),

                Tables\Columns\TextColumn::make('translations.slug')
                    ->label('Slug')
                    ->getStateUsing(fn($record) => optional($record->translations->firstWhere('locale', $locale))->slug)
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicServices::route('/'),
            'create' => Pages\CreatePublicService::route('/create'),
            'edit' => Pages\EditPublicService::route('/{record}/edit'),
        ];
    }
}
