<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConnectionLogResource\Pages;
use App\Models\SystemLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConnectionLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;
    protected static ?string $slug  = 'connection-logs';

    protected static ?string $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Connexions';
    protected static ?int    $navigationSort  = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Logs';
    }

    // Badge = nb d'échecs de connexion des dernières 24h
    public static function getNavigationBadge(): ?string
    {
        $count = SystemLog::where('level', 'login_fail')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    // ── Formulaire de détail ───────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('level')
                    ->label('Résultat')
                    ->readOnly()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'login'      => '✓ Connexion réussie',
                        'login_fail' => '✗ Échec de connexion',
                        default      => $state ?? '—',
                    }),

                Forms\Components\TextInput::make('ip_address')
                    ->label('Adresse IP')
                    ->readOnly(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Date')
                    ->readOnly(),
            ]),

            Forms\Components\TextInput::make('message')
                ->label('Message')
                ->columnSpanFull()
                ->readOnly(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('context.email')
                    ->label('Courriel tenté')
                    ->readOnly(),

                Forms\Components\TextInput::make('context.user_agent')
                    ->label('Navigateur')
                    ->readOnly(),
            ]),

            Forms\Components\Textarea::make('context')
                ->label('Détails (JSON)')
                ->columnSpanFull()
                ->rows(10)
                ->readOnly()
                ->extraAttributes(['class' => 'font-mono'])
                ->afterStateHydrated(fn($component, $state) =>
                    $component->state(is_array($state)
                        ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                        : $state)
                ),
        ]);
    }

    // ── Table ──────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereIn('level', ['login', 'login_fail']))
            ->columns([
                Tables\Columns\TextColumn::make('level')
                    ->label('Résultat')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'login'      => 'success',
                        'login_fail' => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'login'      => '✓ Réussie',
                        'login_fail' => '✗ Échouée',
                        default      => $state ?? '—',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->message)
                    ->searchable(),

                Tables\Columns\TextColumn::make('context.email')
                    ->label('Courriel')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Utilisateur')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('context.user_agent')
                    ->label('Navigateur')
                    ->limit(35)
                    ->tooltip(fn($record) => $record->context['user_agent'] ?? null)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->filters([
                Tables\Filters\Filter::make('echecs_seulement')
                    ->label('Échecs seulement')
                    ->query(fn($query) => $query->where('level', 'login_fail')),

                Tables\Filters\Filter::make('today')
                    ->label("Aujourd'hui seulement")
                    ->query(fn($query) => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('last_24h')
                    ->label('Dernières 24h')
                    ->query(fn($query) => $query->where('created_at', '>=', now()->subDay())),

                Tables\Filters\Filter::make('last_7d')
                    ->label('7 derniers jours')
                    ->query(fn($query) => $query->where('created_at', '>=', now()->subDays(7))),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_connection_logs')
                    ->label('Vider les logs connexion')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer tous les logs de connexion ?')
                    ->modalDescription('Cette action est irréversible.')
                    ->modalSubmitActionLabel('Oui, tout supprimer')
                    ->action(function () {
                        SystemLog::whereIn('level', ['login', 'login_fail'])->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Logs de connexion vidés avec succès')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageConnectionLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
}
