<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicServiceCategoryResource\Pages;
use App\Models\PublicServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class PublicServiceCategoryResource extends Resource
{
    protected static ?string $model = PublicServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Services - Catégories';

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
            Forms\Components\Section::make('Catégorie')
                ->schema([
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->columns(2),

            Forms\Components\Section::make('Traductions (Nom + Slug)')
                ->schema([
                    Forms\Components\Tabs::make('i18n_tabs')
                        ->tabs(
                            collect($locales)->map(function (string $locale) {
                                return Forms\Components\Tabs\Tab::make(strtoupper($locale))
                                    ->schema([
                                        Forms\Components\TextInput::make("i18n.$locale.name")
                                            ->label('Nom')
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
                Tables\Columns\TextColumn::make("translations.name")
                    ->label('Nom')
                    ->getStateUsing(fn($record) => optional($record->translations->firstWhere('locale', $locale))->name)
                    ->searchable(),

                Tables\Columns\TextColumn::make("translations.slug")
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
            'index' => Pages\ListPublicServiceCategories::route('/'),
            'create' => Pages\CreatePublicServiceCategory::route('/create'),
            'edit' => Pages\EditPublicServiceCategory::route('/{record}/edit'),
        ];
    }
}
