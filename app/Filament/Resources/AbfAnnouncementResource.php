<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbfAnnouncementResource\Pages;
use App\Models\AbfAnnouncement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbfAnnouncementResource extends Resource
{
    protected static ?string $model = AbfAnnouncement::class;

    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Nouveautés ABF';
    protected static ?string $navigationGroup = 'ABF';
    protected static ?int    $navigationSort  = 5;
    protected static ?string $modelLabel      = 'Nouveauté';
    protected static ?string $pluralModelLabel = 'Nouveautés ABF';

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRoleByName(['admin', 'super_admin']) ?? false;
    }

    // ─── Formulaire ───────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Contenu')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Forms\Components\RichEditor::make('body')
                        ->label('Contenu')
                        ->toolbarButtons([
                            'bold', 'italic', 'underline', 'strike',
                            'bulletList', 'orderedList',
                            'h2', 'h3',
                            'link',
                            'undo', 'redo',
                        ])
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Publication')
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true)
                        ->helperText('Désactiver pour masquer aux conseillers sans supprimer.'),

                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Date de publication')
                        ->helperText('Laisser vide pour publier immédiatement.')
                        ->native(false)
                        ->displayFormat('d/m/Y H:i')
                        ->nullable(),
                ]),
        ]);
    }

    // ─── Tableau ──────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->weight('bold')
                    ->limit(60),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publié le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Immédiatement')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index'  => Pages\ListAbfAnnouncements::route('/'),
            'create' => Pages\CreateAbfAnnouncement::route('/create'),
            'edit'   => Pages\EditAbfAnnouncement::route('/{record}/edit'),
        ];
    }
}
