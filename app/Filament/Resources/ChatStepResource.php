<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatStepResource\Pages;
use App\Models\ChatStep;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    Forms\Components\TextInput::make('identifier')
                        ->label('Identifiant unique')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->helperText('Ex: identity, email, phone — ne pas modifier après création')
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0)
                        ->columnSpan(1),
                ]),

            Forms\Components\Section::make('Question')
                ->schema([
                    Forms\Components\Textarea::make('question')
                        ->label('Texte de la question')
                        ->required()
                        ->rows(2)
                        ->maxLength(500),

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
                ->description('Pour les types select, radio et checkbox — une option par ligne sous format "valeur|Libellé"')
                ->schema([
                    Forms\Components\KeyValue::make('options')
                        ->label('Options')
                        ->keyLabel('Valeur (clé)')
                        ->valueLabel('Libellé affiché')
                        ->addButtonLabel('Ajouter une option')
                        ->reorderable(),
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

                Tables\Columns\TextColumn::make('identifier')
                    ->label('Identifiant')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('input_type')
                    ->label('Type')
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
                    ->colors([
                        'gray'    => fn ($state) => in_array($state, ['text', 'number']),
                        'info'    => fn ($state) => in_array($state, ['email', 'phone']),
                        'warning' => fn ($state) => in_array($state, ['select', 'radio', 'checkbox']),
                        'primary' => 'date',
                        'success' => 'consent',
                    ]),

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
