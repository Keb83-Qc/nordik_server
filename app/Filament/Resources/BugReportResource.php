<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BugReportResource\Pages;
use App\Models\Message;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class BugReportResource extends Resource
{
    protected static ?string $model = Message::class;
    protected static ?string $slug = 'bug-reports'; // Évite conflit URL avec MessageResource
    protected static ?string $navigationIcon = 'heroicon-o-bug-ant';

    // ── Navigation dynamique selon le rôle ───────────────────────────────────
    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin'])
            ? 'Rapports conseillers'
            : 'Rapports & Suggestions';
    }

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()?->hasAnyRole(['admin', 'super_admin'])
            ? 'Gestion Conseillers'
            : 'Espace Conseiller';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class);
    }

    // Badge "En attente" visible seulement pour les admins
    public static function getNavigationBadge(): ?string
    {
        if (! auth()->user()?->hasAnyRole(['admin', 'super_admin'])) {
            return null;
        }

        $count = Cache::remember(
            'badge_bugreport_pending',
            300,
            fn() => Message::bugReport()->where('status', 'pending')->count()
        );

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    // ── Permissions ───────────────────────────────────────────────────────────
    public static function canViewAny(): bool
    {
        return auth()->check();
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        $user = auth()->user();
        if ($user?->hasAnyRole(['admin', 'super_admin'])) return true;
        // Conseillers peuvent supprimer leurs propres rapports en attente
        return $record->sender_id === $user?->id && ($record->status ?? 'pending') === 'pending';
    }

    // ── Query ─────────────────────────────────────────────────────────────────
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->bugReport()->latest();

        // Conseillers ne voient que leurs propres rapports
        if (! auth()->user()?->hasAnyRole(['admin', 'super_admin'])) {
            $query->where('sender_id', auth()->id());
        }

        return $query;
    }

    // ── Formulaire (création) ────────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category')
                    ->label('Type de rapport')
                    ->options([
                        'bug'         => '🐛 Bug / Erreur',
                        'suggestion'  => '💡 Suggestion',
                        'improvement' => '✨ Amélioration',
                    ])
                    ->required()
                    ->default('bug'),

                Forms\Components\Select::make('priority')
                    ->label('Priorité')
                    ->options([
                        'low'    => 'Basse',
                        'medium' => 'Moyenne',
                        'high'   => 'Élevée',
                    ])
                    ->required()
                    ->default('medium'),

                Forms\Components\TextInput::make('subject')
                    ->label('Titre')
                    ->placeholder('Décrivez le problème en une phrase…')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('body')
                    ->label('Description détaillée')
                    ->placeholder("Étapes pour reproduire, comportement attendu, ce qui s'est passé…")
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('url')
                    ->label('Page concernée (URL)')
                    ->url()
                    ->placeholder('https://vipgpi.ca/admin/...')
                    ->nullable()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    // ── Table ─────────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        $isAdmin = auth()->user()?->hasAnyRole(['admin', 'super_admin']);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data.category')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'bug'         => '🐛 Bug',
                        'suggestion'  => '💡 Suggestion',
                        'improvement' => '✨ Amélioration',
                        default       => 'Autre',
                    })
                    ->color(fn($state) => match ($state) {
                        'bug'         => 'danger',
                        'suggestion'  => 'info',
                        'improvement' => 'success',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('subject')
                    ->label('Titre')
                    ->limit(55)
                    ->searchable()
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn(?string $state) => match ($state ?? 'pending') {
                        'pending'     => 'En attente',
                        'in_progress' => 'En cours',
                        'resolved'    => 'Résolu',
                        'closed'      => 'Fermé',
                        default       => ucfirst($state ?? ''),
                    })
                    ->color(fn(?string $state) => match ($state ?? 'pending') {
                        'pending'     => 'warning',
                        'in_progress' => 'info',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sender.full_name')
                    ->label('Conseiller')
                    ->visible($isAdmin)
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('data.priority')
                    ->label('Priorité')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'low'    => '▼ Basse',
                        'medium' => '● Moyenne',
                        'high'   => '▲ Élevée',
                        default  => ucfirst($state ?? ''),
                    })
                    ->color(fn($state) => match ($state) {
                        'low'    => 'gray',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        default  => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Envoyé le')
                    ->dateTime('d M Y à H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('handled_at')
                    ->label('Traité le')
                    ->dateTime('d M Y à H:i')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction('voir')
            ->actions([
                // ── Voir (tous les rôles) ─────────────────────────────────
                Tables\Actions\Action::make('voir')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn(Message $record) => $record->subject)
                    ->modalSubmitAction(false)
                    ->modalCancelAction(
                        fn() => \Filament\Actions\StaticAction::make('close')->label('Fermer')
                    )
                    ->form([
                        Forms\Components\Select::make('category')
                            ->label('Type')
                            ->options([
                                'bug'         => '🐛 Bug / Erreur',
                                'suggestion'  => '💡 Suggestion',
                                'improvement' => '✨ Amélioration',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('priority')
                            ->label('Priorité')
                            ->options([
                                'low'    => 'Basse',
                                'medium' => 'Moyenne',
                                'high'   => 'Élevée',
                            ])
                            ->disabled(),

                        Forms\Components\TextInput::make('url')
                            ->label('Page concernée')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\RichEditor::make('body')
                            ->label('Description')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->mountUsing(function (Message $record, ComponentContainer $form) {
                        $form->fill([
                            'category' => $record->data['category'] ?? null,
                            'priority' => $record->data['priority'] ?? null,
                            'url'      => $record->data['url'] ?? null,
                            'body'     => $record->body,
                        ]);
                    }),

                // ── En cours — accusé de réception rapide (admin) ─────────
                Tables\Actions\Action::make('in_progress')
                    ->label('En cours')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(
                        fn(Message $record) =>
                            auth()->user()?->hasAnyRole(['admin', 'super_admin']) &&
                            ($record->status ?? 'pending') === 'pending'
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Marquer comme en cours ?')
                    ->action(function (Message $record) {
                        $record->update([
                            'status'     => 'in_progress',
                            'handled_by' => auth()->id(),
                            'handled_at' => now(),
                        ]);
                        Cache::forget('badge_bugreport_pending');
                        Notification::make()->info()->title('Statut mis à jour.')->send();
                    }),

                // ── Répondre + changer statut (admin) ────────────────────
                Tables\Actions\Action::make('respond')
                    ->label('Répondre')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->visible(fn() => auth()->user()?->hasAnyRole(['admin', 'super_admin']))
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('Nouveau statut')
                            ->options([
                                'in_progress' => 'En cours',
                                'resolved'    => 'Résolu ✓',
                                'closed'      => 'Fermé',
                            ])
                            ->required()
                            ->default('resolved'),

                        Forms\Components\Textarea::make('response')
                            ->label('Votre réponse au conseiller')
                            ->rows(5)
                            ->required()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data, Message $record) {
                        $record->update([
                            'status'     => $data['new_status'],
                            'handled_at' => now(),
                            'handled_by' => auth()->id(),
                            'is_read'    => true,
                            'data'       => array_merge($record->data ?? [], ['treated' => true]),
                        ]);

                        // Envoie un message interne de réponse au conseiller
                        if ($record->sender_id) {
                            Message::create([
                                'sender_id'   => auth()->id(),
                                'receiver_id' => $record->sender_id,
                                'subject'     => 'Réponse : ' . $record->subject,
                                'body'        => $data['response'],
                                'is_read'     => false,
                                'status'      => 'pending',
                                'data'        => [
                                    'type'        => 'bug_report_response',
                                    'original_id' => $record->id,
                                ],
                            ]);
                        }

                        Cache::forget('badge_bugreport_pending');

                        Notification::make()
                            ->success()
                            ->title('Réponse envoyée au conseiller.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Supprimer la sélection')
                        ->requiresConfirmation()
                        ->visible(fn() => auth()->user()?->hasAnyRole(['super_admin'])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBugReports::route('/'),
            'create' => Pages\CreateBugReport::route('/create'),
        ];
    }
}
