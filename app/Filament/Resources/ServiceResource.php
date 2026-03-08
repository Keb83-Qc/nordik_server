<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Language;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Services (Accueil)';
    protected static ?string $navigationGroup = 'Marketing';

    /**
     * Langues actives en DB (fallback fr/en)
     */
    protected static function activeLocales(): array
    {
        try {
            return Language::query()
                ->where('is_active', 1)
                ->orderByDesc('is_default')
                ->orderBy('sort_order')
                ->pluck('code')
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return ['fr', 'en'];
        }
    }

    protected static function localeLabel(string $locale): string
    {
        return match ($locale) {
            'fr' => 'Français',
            'en' => 'English',
            'ht' => 'Kreyòl',
            default => strtoupper($locale),
        };
    }

    public static function form(Form $form): Form
    {
        $locales = static::activeLocales();

        $tabs = [];
        foreach ($locales as $locale) {
            $tabs[] = Tabs\Tab::make(static::localeLabel($locale))
                ->schema([
                    TextInput::make("title__{$locale}")
                        ->label("Titre (" . strtoupper($locale) . ")")
                        ->required($locale === 'fr')
                        ->formatStateUsing(function (?Service $record) use ($locale) {
                            return $record?->getTranslation('title', $locale, false);
                        })
                        ->dehydrateStateUsing(function ($state, ?Service $record) use ($locale) {
                            // Filament en create: $record peut être null -> on retourne juste l’état
                            return $state;
                        })
                        ->afterStateHydrated(function (TextInput $component, ?Service $record) use ($locale) {
                            $component->state($record?->getTranslation('title', $locale, false));
                        })
                        ->saveRelationshipsUsing(function ($state, Service $record) use ($locale) {
                            $record->setTranslation('title', $locale, (string) $state);
                        }),

                    Textarea::make("description__{$locale}")
                        ->label("Description (" . strtoupper($locale) . ")")
                        ->rows(4)
                        ->formatStateUsing(fn(?Service $record) => $record?->getTranslation('description', $locale, false))
                        ->afterStateHydrated(function (Textarea $component, ?Service $record) use ($locale) {
                            $component->state($record?->getTranslation('description', $locale, false));
                        })
                        ->saveRelationshipsUsing(function ($state, Service $record) use ($locale) {
                            $record->setTranslation('description', $locale, $state ? (string) $state : null);
                        }),
                ]);
        }

        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                // GAUCHE
                Forms\Components\Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Forms\Components\Section::make('Texte (multi-langues)')
                            ->schema([
                                Tabs::make('Langues')->tabs($tabs),
                            ]),
                    ]),

                // DROITE
                Forms\Components\Section::make('Détails')
                    ->columnSpan(1)
                    ->schema([
                        Forms\Components\Placeholder::make('apercu')
                            ->label('Image actuelle')
                            ->content(fn($record) => $record?->image_url
                                ? new \Illuminate\Support\HtmlString("<img src='{$record->image_url}' style='width:100%;height:auto;border-radius:8px;border:1px solid #ddd;'>")
                                : 'Aucune image'),

                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('uploads/services')
                            ->label("Image (Home)")
                            ->dehydrated(fn($state) => filled($state)),

                        TextInput::make('link')
                            ->label('Lien')
                            ->helperText("Interne: /contact, /assurance/assurance-vie (SANS /fr). Externe: https://...")
                            ->dehydrateStateUsing(function ($state) {
                                $state = trim((string) $state);

                                // externe => on ne touche pas
                                if (preg_match('#^https?://#i', $state)) {
                                    return $state;
                                }

                                // interne
                                $state = '/' . ltrim($state, '/');

                                // retire locale au début si quelqu'un l'a mise
                                $state = preg_replace('#^/[a-zA-Z]{2,5}(?=/|$)#', '', $state);

                                // retire .php legacy
                                $state = preg_replace('#\.php$#i', '', $state);

                                // fallback safe
                                return $state === '' ? '/home' : $state;
                            }),

                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')->label('Image'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->formatStateUsing(function ($state, Service $record) {
                        $locale = app()->getLocale() ?: 'fr';
                        return $record->getTranslation('title', $locale, false)
                            ?: $record->getTranslation('title', 'fr', false)
                            ?: '';
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('description_fr')
                    ->label('Description (FR)')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('MAJ'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
