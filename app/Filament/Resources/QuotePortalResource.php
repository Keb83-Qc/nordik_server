<?php

namespace App\Filament\Resources;

use App\Filament\Actions\DeeplTranslateAction;
use App\Filament\Concerns\HasTranslationTabs;
use App\Filament\Resources\QuotePortalResource\Pages;
use App\Models\QuotePortal;
use App\Models\QuoteType;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class QuotePortalResource extends Resource
{
    use HasTranslationTabs;
    protected static ?string $model = QuotePortal::class;

    protected static ?string $navigationIcon  = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Portails';
    protected static ?string $modelLabel      = 'Portail';
    protected static ?string $pluralModelLabel = 'Portails';

    public static function getNavigationGroup(): ?string
    {
        return 'Soumissions';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class, 2);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            // ─── Identification ───────────────────────────────────────────────
            Forms\Components\Section::make('Identification')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nom du portail')
                        ->required()
                        ->maxLength(100)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, $set) =>
                            $set('slug', \Illuminate\Support\Str::slug($state))
                        )
                        ->columnSpan(2),

                    Forms\Components\Select::make('type')
                        ->label('Type')
                        ->required()
                        ->options([
                            'internal' => '🏢 Interne',
                            'partner'  => '🤝 Partenaire',
                        ])
                        ->default('internal')
                        ->live()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->required()
                        ->unique(QuotePortal::class, 'slug', ignoreRecord: true)
                        ->helperText('Utilisé dans l\'URL — ex: "partenaire-abc" → /fr/p/partenaire-abc/quote')
                        ->columnSpan(2),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Portail actif')
                        ->default(true)
                        ->columnSpan(1),
                ]),

            // ─── Assignation conseiller (partenaires) ─────────────────────────
            Forms\Components\Section::make('Assignation du conseiller')
                ->description('Pour les portails partenaires seulement. Laissez vide pour utiliser la rotation automatique.')
                ->visible(fn (Get $get) => $get('type') === 'partner')
                ->schema([
                    Forms\Components\Select::make('advisor_code')
                        ->label('Conseiller fixe')
                        ->placeholder('— Rotation automatique —')
                        ->options(fn () => User::whereNotNull('advisor_code')
                            ->where('accepts_leads', true)
                            ->orderBy('first_name')
                            ->get()
                            ->mapWithKeys(fn (User $u) => [
                                $u->advisor_code => $u->first_name . ' ' . $u->last_name . ' (' . $u->advisor_code . ')',
                            ])
                        )
                        ->searchable()
                        ->nullable()
                        ->helperText('Si défini, toutes les soumissions de ce portail seront assignées à ce conseiller. Sinon, la rotation automatique s\'applique.'),
                ]),

            // ─── Branding ─────────────────────────────────────────────────────
            Forms\Components\Section::make('Branding & Couleurs')
                ->description('Personnalisation visuelle du portail')
                ->columns(2)
                ->schema([
                    Forms\Components\FileUpload::make('logo_path')
                        ->label('Logo')
                        ->image()
                        ->imagePreviewHeight('80')
                        ->maxSize(2048)
                        ->directory('portals/logos')
                        ->columnSpanFull()
                        ->helperText('Format recommandé : PNG transparent, max 2 Mo'),

                    Forms\Components\ColorPicker::make('primary_color')
                        ->label('Couleur principale')
                        ->default('#1a2e4a')
                        ->columnSpan(1),

                    Forms\Components\ColorPicker::make('secondary_color')
                        ->label('Couleur secondaire')
                        ->default('#e8b84b')
                        ->columnSpan(1),
                ]),

            // ─── Types de soumissions actifs ──────────────────────────────────
            Forms\Components\Section::make('Types de soumissions actifs')
                ->description('Choisir quels boutons de soumission sont disponibles dans ce portail')
                ->schema([
                    Forms\Components\CheckboxList::make('quoteTypes')
                        ->label('')
                        ->relationship(
                            name: 'quoteTypes',
                            titleAttribute: 'slug',
                            modifyQueryUsing: fn ($query) => $query->orderBy('sort_order'),
                        )
                        ->options(
                            fn () => QuoteType::orderBy('sort_order')
                                ->get()
                                ->mapWithKeys(fn (QuoteType $qt) => [
                                    $qt->id => $qt->getLabel('fr') . '  (' . $qt->slug . ')',
                                ])
                        )
                        ->columns(2)
                        ->gridDirection('row'),
                ]),

            // ─── Titre du consentement ────────────────────────────────────────
            Forms\Components\Section::make('Titre du consentement')
                ->description('Titre affiché en haut de la page de consentement')
                ->headerActions([
                    DeeplTranslateAction::forField('consent_title'),
                ])
                ->schema([
                    Forms\Components\Tabs::make('Langues titre')
                        ->tabs(self::translationTabs('consent_title', 'text', maxLength: 200)),
                ]),

            // ─── Texte du consentement ────────────────────────────────────────
            Forms\Components\Section::make('Texte de consentement')
                ->description('Texte affiché avant que le client commence sa soumission (HTML accepté)')
                ->headerActions([
                    DeeplTranslateAction::forField('consent_text', isHtml: true),
                ])
                ->schema([
                    Forms\Components\Tabs::make('Langues texte')
                        ->tabs(self::translationTabs(
                            'consent_text', 'textarea', rows: 10,
                            maxLength: 10000,
                            helperText: 'HTML accepté — ex: <p>...</p>, <strong>...</strong>'
                        )),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('type')
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->height(36)
                    ->defaultImageUrl(fn () => null)
                    ->extraImgAttributes(['style' => 'object-fit: contain;']),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'internal' => 'Interne',
                        'partner'  => 'Partenaire',
                        default    => $state,
                    })
                    ->color(fn ($state) => match ($state) {
                        'internal' => 'info',
                        'partner'  => 'warning',
                        default    => 'gray',
                    }),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('quoteTypes_count')
                    ->label('Types actifs')
                    ->counts('quoteTypes')
                    ->badge()
                    ->color('success'),

                Tables\Columns\ColorColumn::make('primary_color')
                    ->label('Couleur'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('copy_url')
                    ->label('URL')
                    ->icon('heroicon-o-link')
                    ->color('gray')
                    ->visible(fn (QuotePortal $record) => $record->type === 'partner')
                    ->modalHeading('URL du portail partenaire')
                    ->modalDescription('Partagez cette URL avec votre partenaire.')
                    ->modalContent(fn (QuotePortal $record) => new \Illuminate\Support\HtmlString(
                        '<div x-data="{ copied: false }" class="px-1 py-2">'
                        . '<div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg border border-gray-200">'
                        . '<code class="flex-1 text-sm break-all select-all text-gray-700">'
                        .     url('/fr/p/' . $record->slug . '/quote')
                        . '</code>'
                        . '<button type="button"'
                        . ' x-on:click="navigator.clipboard.writeText(\''
                        .     url('/fr/p/' . $record->slug . '/quote')
                        . '\').then(() => { copied = true; setTimeout(() => copied = false, 2500) })"'
                        . ' class="shrink-0 px-3 py-1.5 text-xs font-semibold rounded-md transition-colors"'
                        . ' :class="copied ? \'bg-green-500 text-white\' : \'bg-gray-800 text-white hover:bg-gray-700\'"'
                        . ' x-text="copied ? \'✓ Copié !\' : \'Copier\'"'
                        . '></button>'
                        . '</div>'
                        . '</div>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn (QuotePortal $record) => $record->type !== 'internal')
                    ->visible(fn (QuotePortal $record) => $record->type !== 'internal'),
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
            'index'  => Pages\ListQuotePortals::route('/'),
            'create' => Pages\CreateQuotePortal::route('/create'),
            'edit'   => Pages\EditQuotePortal::route('/{record}/edit'),
        ];
    }
}
