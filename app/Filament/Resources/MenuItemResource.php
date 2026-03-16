<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static ?string $navigationIcon  = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Menu Navigation';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identifiant')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('key')
                        ->label('Clé unique')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Ex: home, about, services — ne pas modifier après création')
                        ->columnSpan(1),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->options([
                            'link'          => 'Lien normal',
                            'mega_services' => 'Mega Menu Services',
                            'cta'           => 'Bouton CTA (or)',
                            'external'      => 'Lien externe',
                        ])
                        ->required()
                        ->default('link')
                        ->columnSpan(1),

                    Forms\Components\Select::make('target')
                        ->label('Ouverture')
                        ->options([
                            '_self'  => 'Même onglet',
                            '_blank' => 'Nouvel onglet',
                        ])
                        ->default('_self')
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Libellés (par langue)')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('label_fr')
                        ->label('🇫🇷 Français')
                        ->required(),
                    Forms\Components\TextInput::make('label_en')
                        ->label('🇬🇧 Anglais'),
                    Forms\Components\TextInput::make('label_es')
                        ->label('🇪🇸 Espagnol'),
                    Forms\Components\TextInput::make('label_ht')
                        ->label('🇭🇹 Créole haïtien'),
                ]),

            Forms\Components\Section::make('URL & Affichage')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('path')
                        ->label('Chemin (sans /locale/)')
                        ->helperText('Ex: home · about · contact · carrieres — laisser vide pour mega menu')
                        ->placeholder('home')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Visible dans le menu')
                        ->default(true)
                        ->columnSpan(2),
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

                Tables\Columns\TextColumn::make('key')
                    ->label('Clé')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('label_fr')
                    ->label('Libellé FR')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('label_en')
                    ->label('EN')
                    ->color('gray'),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'primary' => 'link',
                        'warning' => 'mega_services',
                        'success' => 'cta',
                        'info'    => 'external',
                    ])
                    ->formatStateUsing(fn(string $state) => match($state) {
                        'link'          => 'Lien',
                        'mega_services' => 'Mega Menu',
                        'cta'           => 'CTA',
                        'external'      => 'Externe',
                        default         => $state,
                    }),

                Tables\Columns\TextColumn::make('path')
                    ->label('Chemin')
                    ->color('gray')
                    ->placeholder('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn() => Cache::forget('menu_items_nav')),
                Tables\Actions\Action::make('toggle')
                    ->label(fn(MenuItem $r) => $r->is_active ? 'Masquer' : 'Afficher')
                    ->icon(fn(MenuItem $r) => $r->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->action(function (MenuItem $r) {
                        $r->update(['is_active' => !$r->is_active]);
                        Cache::forget('menu_items_nav');
                    }),
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
            'index'  => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit'   => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
