<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Messagerie';

    public static function getNavigationGroup(): ?string
    {
        return 'Espace Conseiller';
    }

    // public static function shouldRegisterNavigation(): bool
    // {
    //     return auth()->user()?->can('view_any_message') ?? false;
    // }


    public static function getNavigationBadge(): ?string
    {
        $id = Filament::auth()->id();
        if (! $id) return null;

        $count = Message::query()
            ->where('receiver_id', $id)
            ->internal()
            ->where('is_read', false)
            ->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function getEloquentQuery(): Builder
    {
        $id = Filament::auth()->id();

        return parent::getEloquentQuery()
            ->when($id, fn(Builder $q) => $q->where('receiver_id', $id))
            ->internal()
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('sender_id')
                ->default(fn() => Filament::auth()->id()),

            Forms\Components\Select::make('receiver_id')
                ->label('Destinataire')
                ->options(
                    fn() => User::query()
                        ->where('id', '!=', Filament::auth()->id())
                        ->orderBy('first_name')
                        ->get()
                        ->mapWithKeys(fn(User $user) => [
                            $user->id => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                        ])
                        ->toArray()
                )
                ->searchable()
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('subject')
                ->label('Sujet')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),

            Forms\Components\RichEditor::make('body')
                ->label('Message')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.first_name')
                    ->label('De')
                    ->formatStateUsing(
                        fn($record) => $record->sender
                            ? trim($record->sender->first_name . ' ' . $record->sender->last_name)
                            : 'Système'
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user-circle'),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Sujet')
                    ->searchable()
                    ->limit(60)
                    ->weight(fn(Message $record): string => $record->is_read ? 'normal' : 'extra-bold')
                    ->color(fn(Message $record): string => $record->is_read ? 'gray' : 'primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reçu le')
                    ->dateTime('d M Y à H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('read')
                    ->label('Lire')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Lecture du message')
                    ->modalSubmitAction(false)
                    ->modalCancelAction(fn() => \Filament\Actions\StaticAction::make('close')->label('Fermer'))
                    ->form([
                        Forms\Components\TextInput::make('subject')->label('Sujet')->disabled(),
                        Forms\Components\RichEditor::make('body')->label('Message')->disabled(),
                    ])
                    ->mountUsing(function (Message $record, ComponentContainer $form) {
                        if (! $record->is_read) {
                            $record->update(['is_read' => true]);
                        }

                        $form->fill([
                            'subject' => $record->subject,
                            'body' => $record->body,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
        ];
    }
}
