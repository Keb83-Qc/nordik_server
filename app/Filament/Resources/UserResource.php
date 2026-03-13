<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\Models\TeamTitle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Liste des conseillers';

    // ✅ Nom singulier (Create/Edit, titres, etc.)
    protected static ?string $modelLabel = 'Conseiller';

    // ✅ Nom pluriel (liste, breadcrumbs, titres)
    protected static ?string $pluralModelLabel = 'Conseillers';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        return method_exists($user, 'hasAnyRole')
            ? $user->hasAnyRole(['super_admin', 'admin'])
            : in_array((int) ($user->role_id ?? 0), [1, 2], true);
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }
    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }
    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion Conseillers';
    }

    private static function titleLabelFromJson(?array $name): string
    {
        $locale = app()->getLocale(); // 'fr' ou 'en'
        if (!$name) {
            return '';
        }

        return (string) ($name[$locale] ?? $name['fr'] ?? $name['en'] ?? '');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            // --- 1. IDENTITÉ ---
            Section::make('Identité')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('Prénom')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($get('last_name')) {
                                $set('slug', Str::slug($state . '-' . $get('last_name')));
                            }
                        }),

                    Forms\Components\TextInput::make('last_name')
                        ->label('Nom')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($set, $state, $get) {
                            if ($get('first_name')) {
                                $set('slug', Str::slug($get('first_name') . '-' . $state));
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->label('Permalien (URL)')
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->unique(ignoreRecord: true),
                ]),

            Forms\Components\Grid::make(3)
                ->schema([
                    // --- 2. GAUCHE : BIOGRAPHIE (2/3) ---
                    Forms\Components\Group::make()
                        ->columnSpan(2)
                        ->schema([
                            Section::make('Biographie')
                                ->icon('heroicon-o-document-text')
                                ->collapsed()           // replié par défaut
                                ->collapsible()         // rend la section repliable
                                ->schema([
                                    Tabs::make('BiographieTabs')
                                        ->tabs([
                                            Tabs\Tab::make('Français')->icon('heroicon-m-language')
                                                ->schema([
                                                    Forms\Components\RichEditor::make('bio_fr')
                                                        ->label('Biographie (FR)')
                                                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'h2', 'h3', 'undo', 'redo']),
                                                ]),
                                            Tabs\Tab::make('Anglais')->icon('heroicon-m-globe-alt')
                                                ->schema([
                                                    Forms\Components\RichEditor::make('bio_en')
                                                        ->label('Biography (EN)')
                                                        ->toolbarButtons(['bold', 'italic', 'link', 'bulletList', 'h2', 'h3', 'undo', 'redo']),
                                                ]),
                                        ]),
                                ]),
                        ]),

                    // --- 3. DROITE : PHOTO & INFOS (1/3) ---
                    Forms\Components\Group::make()
                        ->columnSpan(1)
                        ->schema([
                            Section::make('Photo de Profil')
                                ->description('Avatar, image carrée, recadrage automatique')
                                ->icon('heroicon-o-photo')
                                ->compact()
                                ->schema([
                                    Forms\Components\Placeholder::make('profile_card')
                                        ->label('')
                                        ->content(function (callable $get, ?User $record) {

                                            $user = $record;
                                            if (! $user) {
                                                return new HtmlString("<div class='text-sm text-slate-400'>Aucun utilisateur chargé.</div>");
                                            }

                                            // ✅ image du formulaire (live) si présente, sinon image_url du record
                                            $imageState = $get('image');

                                            $url = $user->image_url; // fallback

                                            // Si le state est un path (ex: team/xxx.jpg), on le transforme en URL
                                            if (is_string($imageState) && filled($imageState)) {
                                                // Si c'est déjà une URL, on la garde
                                                $url = str_starts_with($imageState, 'http')
                                                    ? $imageState
                                                    : asset('storage/' . $imageState);
                                            }

                                            $title = $user->title?->name ?? null;
                                            if (is_array($title)) {
                                                $locale = app()->getLocale();
                                                $title = $title[$locale] ?? $title['fr'] ?? $title['en'] ?? '';
                                            }

                                            $role = $user->role?->name ?? '';
                                            $city = $get('city') ?: $user->city;

                                            $fullName = trim(($get('first_name') ?: $user->first_name) . ' ' . ($get('last_name') ?: $user->last_name));

                                            return new HtmlString("
                                                <div class='rounded-2xl border border-white/10 bg-gradient-to-b from-white/5 to-white/0 p-5 shadow-sm'>
                                                    <div class='flex items-center gap-4'>
                                                        <div class='relative'>
                                                            <div class='h-20 w-20 rounded-full ring-2 ring-white/10 bg-white/5 overflow-hidden'>
                                                                <img src='{$url}' class='h-full w-full object-cover' onerror=\"this.src='/assets/img/VIP_Logo_Gold_Gradient10.png'\" />
                                                            </div>
                                                            <div class='absolute -bottom-1 -right-1 h-6 w-6 rounded-full bg-emerald-500 ring-4 ring-slate-950/60'></div>
                                                        </div>

                                                        <div class='min-w-0'>
                                                            <div class='text-base font-semibold text-white truncate'>{$fullName}</div>

                                                            " . (!empty($title) ? "<div class='text-sm text-slate-300 truncate'>{$title}</div>" : "") . "

                                                            <div class='mt-2 flex flex-wrap gap-2'>
                                                                " . (!empty($role) ? "<span class='inline-flex items-center rounded-full bg-white/5 px-2.5 py-1 text-xs text-slate-200 ring-1 ring-white/10'>{$role}</span>" : "") . "
                                                                " . (!empty($city) ? "<span class='inline-flex items-center rounded-full bg-white/5 px-2.5 py-1 text-xs text-slate-200 ring-1 ring-white/10'>📍 {$city}</span>" : "") . "
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class='mt-4 text-xs text-slate-400'>
                                                        Conseil: utilise une photo carrée (min 600×600). On recadre automatiquement en 1:1.
                                                    </div>
                                                </div>
                                            ");
                                        })
                                        ->dehydrated(false),

                                    Forms\Components\FileUpload::make('image')
                                        ->label('Photo')
                                        ->image()
                                        ->imageEditor()
                                        ->imageEditorAspectRatios(['1:1'])
                                        ->disk('public')
                                        ->directory('team')
                                        ->helperText('PNG/JPG/WEBP — carré recommandé (min 400×400)')
                                        ->getUploadedFileNameForStorageUsing(function ($file, $record): string {
                                            $name = ($record && filled($record->first_name))
                                                ? Str::slug($record->first_name . '-' . $record->last_name) . '-' . time()
                                                : (string) Str::uuid();
                                            return $name . '.' . ($file->guessExtension() ?: 'jpg');
                                        })
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Connexion')
                                ->schema([
                                    Forms\Components\TextInput::make('email')
                                        ->email()
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->prefixIcon('heroicon-m-envelope'),

                                    Forms\Components\TextInput::make('password')
                                        ->label('Mot de passe')
                                        ->password()
                                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                                        ->dehydrated(fn($state) => filled($state))
                                        ->required(fn(string $context): bool => $context === 'create'),

                                    // ✅ TITRE DU POSTE (team_titles.name = JSON)
                                    Forms\Components\Select::make('title_id')
                                        ->label('Titre du poste')
                                        ->options(function () {
                                            return TeamTitle::query()
                                                ->select(['id', 'name'])
                                                ->orderBy('id')
                                                ->get()
                                                ->mapWithKeys(function ($title) {
                                                    $label = self::titleLabelFromJson(is_array($title->name) ? $title->name : null);
                                                    return [$title->id => $label ?: ('Titre #' . $title->id)];
                                                })
                                                ->toArray();
                                        })
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    // Rôle système (Relation OK car name = string côté roles)
                                    Forms\Components\Select::make('role_id')
                                        ->relationship('role', 'name')
                                        ->label('Rôle')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    Forms\Components\TextInput::make('advisor_code')
                                        ->label('Code conseiller')
                                        ->maxLength(50),

                                    Forms\Components\Select::make('zoho_id')
                                        ->label('Compte Zoho People')
                                        ->options(
                                            fn() => Employee::query()
                                                ->select(['zoho_id', 'name', 'email', 'employee_number'])
                                                ->orderBy('name')
                                                ->get()
                                                ->mapWithKeys(fn($e) => [
                                                    $e->zoho_id => "{$e->name} — {$e->email}" . ($e->employee_number ? " ({$e->employee_number})" : ''),
                                                ])
                                                ->toArray()
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('Aucun lien')
                                        ->helperText("Choisis l'employé Zoho correspondant. (employees.zoho_id est synchronisé via Zoho People)")
                                        ->unique(table: 'users', column: 'zoho_id', ignoreRecord: true),
                                ]),
                        ]),
                ]),

            // --- 4. DETAILS SUPPLÉMENTAIRES (Replié) ---
            Section::make('Détails & Configuration')
                ->collapsed()
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Téléphone')
                            ->prefixIcon('heroicon-m-phone'),

                        Forms\Components\TextInput::make('city')
                            ->label('Ville')
                            ->prefixIcon('heroicon-m-map-pin'),

                        Forms\Components\TextInput::make('position')
                            ->label("Ordre d'affichage")
                            ->numeric()
                            ->default(999),

                        Forms\Components\Select::make('languages')
                            ->label('Langues parlées')
                            ->multiple()
                            ->options([
                                'fr' => 'Français',
                                'en' => 'Anglais',
                                'es' => 'Espagnol',
                            ])
                            ->searchable(),

                        Forms\Components\TextInput::make('booking_url')
                            ->label('Lien Calendly/Booking')
                            ->url()
                            ->prefixIcon('heroicon-m-calendar')
                            ->columnSpan(2),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn($query) => $query
                    ->whereNotIn('id', [0])
                    ->where('first_name', '!=', 'System')
                    ->where('first_name', '!=', 'Robot')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Photo')
                    ->circular()
                    ->defaultImageUrl(asset('assets/img/equipe/VIP_Logo_Gold_Gradient10.png')),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nom Complet')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['last_name'])
                    ->weight('bold')
                    ->wrap(false)
                    ->size('sm'),

                Tables\Columns\TextColumn::make('advisor_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->wrap(false)          // 👈 évite le retour à la ligne
                    ->size('sm'),

                Tables\Columns\IconColumn::make('employee.status')
                    ->label('Zoho')
                    ->boolean(fn($record) => ($record->employee?->status ?? '') === 'Active')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->toggleable()
                    ->alignCenter()
                    ->width('60px'),

                // ✅ Affichage du titre JSON en FR/EN
                Tables\Columns\TextColumn::make('title_display')
                    ->label('Titre du poste')
                    ->state(fn($record) => self::titleLabelFromJson(is_array($record->title?->name) ? $record->title->name : null))
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->wrap(false)
                    ->size('sm'),

                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rôle Système')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin' => 'warning',
                        'conseiller' => 'success',
                        default => 'gray',
                    })
                    ->toggleable(),

            ])
            ->defaultSort('position', 'asc')
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
