<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\Language;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Get;
use Filament\Forms\Set;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Articles de Blog';
    protected static ?string $recordTitleAttribute = 'title_fr';

    public static function getNavigationGroup(): ?string
    {
        return 'Marketing';
    }

    public static function form(Form $form): Form
    {
        $defaultLocale = Language::defaultCode() ?? config('app.fallback_locale', 'fr');

        $languages = Language::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'code')
            ->toArray();

        $categories = array_keys(BlogPost::CATEGORY_MAPPING);
        $catOptions = array_combine($categories, $categories);

        $authors = User::query()
            ->select(['first_name', 'last_name'])
            ->get()
            ->mapWithKeys(fn($user) => [
                trim("{$user->first_name} {$user->last_name}") => trim("{$user->first_name} {$user->last_name}")
            ])
            ->toArray();

        return $form->schema([
            Grid::make(3)->schema([

                Group::make()->schema([
                    Section::make()->schema([
                        Tabs::make('Langues')
                            ->tabs(function () use ($languages, $defaultLocale) {
                                $tabs = [];

                                foreach ($languages as $code => $label) {
                                    $schema = [];

                                    // ✅ Compteurs uniquement sur la langue source (FR par défaut)
                                    if ($code === $defaultLocale) {
                                        $schema[] = Section::make('Compteur (source)')
                                            ->schema([
                                                Placeholder::make('char_count_source_title')
                                                    ->label("Caractères titre (" . strtoupper($code) . ")")
                                                    ->content(function (Get $get) use ($code) {
                                                        $title = (string) $get("title.$code");
                                                        return mb_strlen(trim(strip_tags($title)), 'UTF-8');
                                                    }),

                                                Placeholder::make('char_count_source_content')
                                                    ->label("Caractères contenu (" . strtoupper($code) . ") approx (HTML retiré)")
                                                    ->content(function (Get $get) use ($code) {
                                                        $html = (string) $get("content.$code");
                                                        $text = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($html))));
                                                        return mb_strlen($text, 'UTF-8');
                                                    }),

                                                Placeholder::make('char_count_source_total')
                                                    ->label("Total caractères approx (" . strtoupper($code) . ")")
                                                    ->content(function (Get $get) use ($code) {
                                                        $title = (string) $get("title.$code");
                                                        $t = mb_strlen(trim(strip_tags($title)), 'UTF-8');

                                                        $html = (string) $get("content.$code");
                                                        $text = trim(preg_replace('/\s+/u', ' ', strip_tags(html_entity_decode($html))));
                                                        $c = mb_strlen($text, 'UTF-8');

                                                        return $t + $c;
                                                    }),
                                            ])
                                            ->collapsed();
                                    }

                                    $schema[] = TextInput::make("title.$code")
                                        ->label("Titre (" . strtoupper($code) . ")")
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) use ($code) {
                                            $current = (string) $get("slug.$code");
                                            if ($current === '') {
                                                $set("slug.$code", BlogPost::makeSeoSlug((string) $state, $code));
                                                return;
                                            }

                                            $normalized = BlogPost::makeSeoSlug($current, $code);
                                            if ($current === $normalized) {
                                                $set("slug.$code", BlogPost::makeSeoSlug((string) $state, $code));
                                            }
                                        })
                                        ->required($code === $defaultLocale);

                                    $schema[] = TextInput::make("slug.$code")
                                        ->label("Slug (" . strtoupper($code) . ")")
                                        ->helperText("SEO: sans accents, sans espaces, sans tirets en bout.")
                                        ->dehydrateStateUsing(fn($state) => BlogPost::makeSeoSlug((string) $state, $code))
                                        ->required($code === $defaultLocale);

                                    $schema[] = RichEditor::make("content.$code")
                                        ->label("Contenu (" . strtoupper($code) . ")")
                                        ->required($code === $defaultLocale)
                                        ->fileAttachmentsDirectory('blog-content')
                                        ->toolbarButtons(['bold', 'italic', 'link', 'h2', 'h3', 'bulletList', 'undo', 'redo']);

                                    $tabs[] = Tabs\Tab::make($label)
                                        ->icon($code === $defaultLocale ? 'heroicon-m-language' : 'heroicon-m-globe-alt')
                                        ->schema($schema);
                                }

                                return $tabs;
                            }),
                    ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Publication')->schema([
                        Select::make("category.$defaultLocale")
                            ->label('Catégorie (principale)')
                            ->options($catOptions)
                            ->required(),

                        Select::make('author')
                            ->label('Auteur')
                            ->options($authors)
                            ->default('VIP GPI')
                            ->searchable()
                            ->required(),

                        DateTimePicker::make('created_at')
                            ->label('Date')
                            ->default(now()),
                    ]),

                    Section::make('Image')->schema([
                        Placeholder::make('current_image_preview')
                            ->label('Aperçu actuel')
                            ->hidden(fn($operation) => $operation === 'create')
                            ->content(function ($record) {
                                if (!$record || blank($record->image)) {
                                    return null;
                                }

                                return new \Illuminate\Support\HtmlString(
                                    '<img src="' . e($record->image_url) . '"
                                        style="width:100%; max-height:200px; object-fit:cover; border-radius: 8px; border: 1px solid #ddd;"
                                        alt="Image actuelle">'
                                );
                            }),

                        FileUpload::make('image')
                            ->label('Changer l\'image')
                            ->image()
                            ->directory('blog')
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, callable $get) use ($defaultLocale): string {
                                $slug = (string) ($get("slug.$defaultLocale") ?? '');
                                if ($slug === '') {
                                    $slug = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                    $slug = BlogPost::makeSeoSlug($slug, $defaultLocale);
                                }

                                return $slug . '-' . time() . '.' . $file->getClientOriginalExtension();
                            })
                            ->columnSpanFull(),
                    ]),
                ])->columnSpan(['lg' => 1]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Aperçu')
                    ->width(80)->height(50)
                    ->square(false)
                    ->defaultImageUrl(asset('assets/img/default.jpg')),

                Tables\Columns\TextColumn::make('title_fr')
                    ->label('Article')
                    ->description(fn(BlogPost $record) => Str::limit(strip_tags((string) $record->category_fr), 30) . " — /" . $record->slug_fr)
                    ->searchable(query: fn($query, $search) => $query->where('title->fr', 'like', "%{$search}%"))
                    ->sortable(query: fn($query, $direction) => $query->orderBy('title->fr', $direction))
                    ->weight('bold')
                    ->wrap(),

                Tables\Columns\IconColumn::make('is_file_optimized')
                    ->label('État Image')
                    ->boolean()
                    ->getStateUsing(function ($record) {
                        if (empty($record->image)) return false;
                        if (str_starts_with($record->image, 'http') || str_starts_with($record->image, 'assets')) return true;

                        $slug = $record->getTranslation('slug', 'fr');
                        return str_starts_with($record->image, 'blog/') && str_contains($record->image, $slug);
                    })
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-exclamation-triangle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignCenter()
                    ->tooltip(fn($state) => $state ? 'Image bien rangée' : 'Image mal placée ou mal nommée'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('fix_image')
                    ->label('Ranger & Renommer')
                    ->icon('heroicon-m-folder-arrow-down')
                    ->color('danger')
                    ->button()
                    ->size('xs')
                    ->requiresConfirmation()
                    ->modalHeading('Déplacer et renommer l\'image ?')
                    ->modalDescription('L\'image sera déplacée dans le dossier "blog/" et renommée pour le SEO.')
                    ->hidden(function (BlogPost $record) {
                        if (empty($record->image)) return true;
                        if (str_starts_with($record->image, 'http') || str_starts_with($record->image, 'assets')) return true;

                        $slug = $record->getTranslation('slug', 'fr');
                        return str_starts_with($record->image, 'blog/') && str_contains($record->image, $slug);
                    })
                    ->action(function (BlogPost $record) {
                        $disk = \Illuminate\Support\Facades\Storage::disk('public');

                        if (!$disk->exists($record->image)) {
                            \Filament\Notifications\Notification::make()->title('Fichier introuvable')->danger()->send();
                            return;
                        }

                        $slug = $record->getTranslation('slug', 'fr');
                        $extension = pathinfo($record->image, PATHINFO_EXTENSION);
                        $newPath = 'blog/' . $slug . '-' . time() . '.' . $extension;

                        if ($disk->move($record->image, $newPath)) {
                            $record->update(['image' => $newPath]);
                            \Filament\Notifications\Notification::make()->title('Image rangée avec succès !')->success()->send();
                        }
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
