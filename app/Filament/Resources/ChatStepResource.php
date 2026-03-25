<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatStepResource\Pages;
use App\Models\ChatStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ChatStepResource extends Resource
{
    protected static ?string $model = ChatStep::class;

    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Site Web';
    protected static ?string $navigationLabel = 'Questions Chatbot';
    protected static ?string $modelLabel      = 'Question';
    protected static ?string $pluralModelLabel = 'Questions Chatbot';

    public static function getNavigationSort(): ?int
    {
        return 11;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identification')
                ->columns(3)
                ->schema([
                    Forms\Components\Select::make('chat_type')
                        ->label('Chatbot')
                        ->required()
                        ->options([
                            'auto'        => 'Auto',
                            'habitation'  => 'Habitation',
                            'bundle'      => 'Bundle (Auto + Habitation)',
                            'commercial'  => 'Commercial',
                        ])
                        ->default('auto')
                        ->columnSpan(1),

                    Forms\Components\TextInput::make('identifier')
                        ->label('Identifiant unique')
                        ->required()
                        ->unique(
                            table: ChatStep::class,
                            column: 'identifier',
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Forms\Get $get) => $rule->where('chat_type', $get('chat_type')),
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

                Tables\Columns\TextColumn::make('chat_type')
                    ->label('Chatbot')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'auto'       => 'Auto',
                        'habitation' => 'Habitation',
                        'bundle'     => 'Bundle',
                        'commercial' => 'Commercial',
                        default      => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
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
