<?php

namespace App\Filament\Resources;

use App\Filament\Actions\DeeplTranslateAction;
use App\Filament\Resources\ChatStepResource\Pages;
use App\Models\ChatStep;
use App\Models\QuoteType;
use App\Services\DeeplTranslator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rules\Unique;

class ChatStepResource extends Resource
{
    protected static ?string $model = ChatStep::class;

    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationLabel = 'Questions Chatbot';
    protected static ?string $modelLabel      = 'Question';
    protected static ?string $pluralModelLabel = 'Questions Chatbot';

    public static function getNavigationGroup(): ?string
    {
        return 'Soumissions';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class, 3);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identification')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('quote_type_id')
                        ->label('Type de soumission')
                        ->required()
                        ->options(
                            fn () => QuoteType::orderBy('sort_order')
                                ->get()
                                ->mapWithKeys(fn (QuoteType $qt) => [
                                    $qt->id => $qt->getLabel('fr'),
                                ])
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('identifier')
                        ->label('Identifiant unique')
                        ->required()
                        ->unique(
                            table: ChatStep::class,
                            column: 'identifier',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Forms\Get $get) =>
                                $rule->where('quote_type_id', $get('quote_type_id')),
                        )
                        ->helperText('Ex: identity, email, phone — ne pas modifier après création')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Question (toutes langues)')
                ->headerActions([
                    DeeplTranslateAction::forField('question'),
                ])
                ->schema([
                    Forms\Components\Tabs::make('Traductions')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('🇫🇷 Français')
                                ->schema([
                                    Forms\Components\Textarea::make('question.fr')
                                        ->label('Question en français')
                                        ->required()
                                        ->rows(2)
                                        ->maxLength(500),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇬🇧 English')
                                ->schema([
                                    Forms\Components\Textarea::make('question.en')
                                        ->label('Question in English')
                                        ->rows(2)
                                        ->maxLength(500),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇪🇸 Español')
                                ->schema([
                                    Forms\Components\Textarea::make('question.es')
                                        ->label('Pregunta en español')
                                        ->rows(2)
                                        ->maxLength(500),
                                ]),
                            Forms\Components\Tabs\Tab::make('🇭🇹 Kreyòl')
                                ->schema([
                                    Forms\Components\Textarea::make('question.ht')
                                        ->label('Kesyon an kreyòl')
                                        ->rows(2)
                                        ->maxLength(500),
                                ]),
                        ]),

                    Forms\Components\Select::make('input_type')
                        ->label("Type de réponse")
                        ->required()
                        ->options([
                            'text'     => 'Texte libre',
                            'email'    => 'Email',
                            'phone'    => 'Téléphone',
                            'date'     => 'Date',
                            'select'   => 'Liste déroulante',
                            'radio'    => 'Choix unique (boutons)',
                            'checkbox' => 'Choix multiple',
                            'number'   => 'Nombre',
                            'consent'  => 'Consentement (oui/non)',
                        ])
                        ->default('text'),
                ]),

            Forms\Components\Section::make('Options de réponse')
                ->description('Pour les types select, radio et checkbox — clé: valeur interne, libellé: texte affiché')
                ->schema([
                    Forms\Components\KeyValue::make('options')
                        ->label('Options')
                        ->keyLabel('Valeur (clé)')
                        ->valueLabel('Libellé affiché')
                        ->addButtonLabel('Ajouter une option'),
                ])
                ->visible(fn (Forms\Get $get) => in_array($get('input_type'), ['select', 'radio', 'checkbox'])),

            Forms\Components\Section::make('Visibilité')
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Question active')
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

                Tables\Columns\TextColumn::make('quoteType.slug')
                    ->label('Chatbot')
                    ->badge()
                    ->formatStateUsing(fn (?string $state, ChatStep $record) => match ($state ?? $record->chat_type) {
                        'auto'       => 'Auto',
                        'habitation' => 'Habitation',
                        'bundle'     => 'Bundle',
                        'commercial' => 'Commercial',
                        default      => $state ?? $record->chat_type ?? '—',
                    })
                    ->color(fn (?string $state, ChatStep $record) => match ($state ?? $record->chat_type) {
                        'auto'       => 'info',
                        'habitation' => 'success',
                        'bundle'     => 'warning',
                        'commercial' => 'danger',
                        default      => 'gray',
                    }),

                Tables\Columns\TextColumn::make('identifier')
                    ->label('Identifiant')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Question (FR)')
                    ->formatStateUsing(fn ($state) => is_array($state) ? ($state['fr'] ?? '') : $state)
                    ->limit(60)
                    ->wrap(),

                // Indicateur de traductions complètes
                Tables\Columns\TextColumn::make('translations_status')
                    ->label('Traductions')
                    ->state(function (ChatStep $record): string {
                        $q      = $record->question ?? [];
                        $langs  = ['fr', 'en', 'es', 'ht'];
                        $filled = array_filter($langs, fn ($l) => !empty(trim($q[$l] ?? '')));
                        $count  = count($filled);
                        $total  = count($langs);
                        return "{$count}/{$total}";
                    })
                    ->badge()
                    ->color(function (ChatStep $record): string {
                        $q      = $record->question ?? [];
                        $langs  = ['fr', 'en', 'es', 'ht'];
                        $filled = array_filter($langs, fn ($l) => !empty(trim($q[$l] ?? '')));
                        return count($filled) === count($langs) ? 'success' : 'warning';
                    })
                    ->tooltip(function (ChatStep $record): string {
                        $q     = $record->question ?? [];
                        $langs = ['fr' => '🇫🇷', 'en' => '🇬🇧', 'es' => '🇪🇸', 'ht' => '🇭🇹'];
                        return implode('  ', array_map(
                            fn ($flag, $l) => $flag . ' ' . (!empty(trim($q[$l] ?? '')) ? '✅' : '🔴'),
                            $langs, array_keys($langs)
                        ));
                    }),

                Tables\Columns\TextColumn::make('input_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'text'     => 'Texte',
                        'email'    => 'Email',
                        'phone'    => 'Téléphone',
                        'date'     => 'Date',
                        'select'   => 'Liste',
                        'radio'    => 'Boutons',
                        'checkbox' => 'Cases',
                        'number'   => 'Nombre',
                        'consent'  => 'Consentement',
                        default    => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'email', 'phone'              => 'info',
                        'select', 'radio', 'checkbox' => 'warning',
                        'date'                        => 'primary',
                        'consent'                     => 'success',
                        default                       => 'gray',
                    }),

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
                    // ─── Traduction en lot ────────────────────────────────
                    Tables\Actions\BulkAction::make('translate_missing')
                        ->label('Traduire les manquants (DeepL)')
                        ->icon('heroicon-o-language')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Traduction DeepL en lot')
                        ->modalDescription('Traduit FR → EN, ES, HT pour toutes les questions sélectionnées. Seules les langues VIDES seront remplies.')
                        ->modalSubmitActionLabel('Lancer la traduction')
                        ->action(function (Collection $records) {
                            /** @var DeeplTranslator $deepl */
                            $deepl     = app(DeeplTranslator::class);
                            $added     = 0;
                            $errors    = 0;
                            $targets   = ['en' => 'EN', 'es' => 'ES', 'ht' => 'HT'];

                            foreach ($records as $record) {
                                /** @var ChatStep $record */
                                $q      = $record->question ?? [];
                                $frText = trim($q['fr'] ?? '');

                                if ($frText === '') continue;

                                $changed = false;

                                foreach ($targets as $lang => $deepLCode) {
                                    if (!empty(trim($q[$lang] ?? ''))) continue; // déjà traduit

                                    try {
                                        $q[$lang] = $deepl->translatePlain($frText, 'FR', $deepLCode);
                                        $added++;
                                        $changed = true;
                                    } catch (\Throwable $e) {
                                        $errors++;
                                    }
                                }

                                if ($changed) {
                                    $record->question = $q;
                                    $record->save(); // déclenche l'invalidation du cache
                                }
                            }

                            if ($errors > 0) {
                                Notification::make()
                                    ->warning()
                                    ->title("Traduction partielle : {$added} ajoutée(s), {$errors} échec(s)")
                                    ->body('Vérifiez votre clé DEEPL_API_KEY si les erreurs persistent.')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->success()
                                    ->title("{$added} traduction(s) ajoutée(s)")
                                    ->send();
                            }
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChatSteps::route('/'),
            'create' => Pages\CreateChatStep::route('/create'),
            'edit'   => Pages\EditChatStep::route('/{record}/edit'),
        ];
    }
}
