<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SystemLogResource\Pages;
use App\Models\SystemLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SystemLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Logs Système';

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class);
    }

    // Badge de navigation = nb de logs "danger" des dernières 24h
    public static function getNavigationBadge(): ?string
    {
        $count = SystemLog::where('level', 'danger')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)->schema([
                    Forms\Components\TextInput::make('level')
                        ->label('Niveau')
                        ->readOnly(),
                    Forms\Components\TextInput::make('source')
                        ->label('Source')
                        ->readOnly(),
                    Forms\Components\DateTimePicker::make('created_at')
                        ->label('Date')
                        ->readOnly(),
                ]),

                Forms\Components\Textarea::make('message')
                    ->label('Message')
                    ->columnSpanFull()
                    ->readOnly(),

                Forms\Components\Textarea::make('context')
                    ->label('Détails techniques (JSON complet)')
                    ->columnSpanFull()
                    ->rows(15)
                    ->readOnly()
                    ->extraAttributes(['class' => 'font-mono'])
                    ->formatStateUsing(fn($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),

                Forms\Components\TextInput::make('ip_address')
                    ->label('Adresse IP')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Source (public / admin / cli / api)
                Tables\Columns\TextColumn::make('source')
                    ->label('Source')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'public' => 'info',
                        'admin'  => 'gray',
                        'cli'    => 'gray',
                        'api'    => 'warning',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'public' => '🌐 Site',
                        'admin'  => '🔧 Admin',
                        'cli'    => '⚙️ CLI',
                        'api'    => '🔌 API',
                        default  => $state ?? '—',
                    })
                    ->sortable(),

                // Niveau (danger / warning / info / login / etc.)
                Tables\Columns\TextColumn::make('level')
                    ->label('Niveau')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'danger', 'fatal', 'login_fail' => 'danger',
                        'warning', 'error'              => 'warning',
                        'info'                          => 'info',
                        'login', 'update', 'success'    => 'success',
                        default                         => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'login'      => '✓ Connexion',
                        'login_fail' => '✗ Échec login',
                        'danger'     => '🔴 Critique',
                        'warning'    => '⚠ Avertissement',
                        'info'       => 'ℹ Info',
                        default      => strtoupper($state),
                    }),

                // Message
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->message)
                    ->searchable(),

                // URL (depuis context)
                Tables\Columns\TextColumn::make('context.url')
                    ->label('URL')
                    ->limit(40)
                    ->tooltip(fn($record) => $record->context['url'] ?? null)
                    ->toggleable(),

                // Référant (depuis context)
                Tables\Columns\TextColumn::make('context.referer')
                    ->label('Référant')
                    ->limit(35)
                    ->tooltip(fn($record) => $record->context['referer'] ?? null)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.full_name')
                    ->label('Utilisateur')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('context.user_agent')
                    ->label('Navigateur')
                    ->limit(40)
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
                // Filtre par source
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'public' => '🌐 Site public',
                        'admin'  => '🔧 Panel admin',
                        'cli'    => '⚙️ CLI / Queue',
                        'api'    => '🔌 API',
                    ]),

                // Filtre par niveau
                Tables\Filters\SelectFilter::make('level')
                    ->label('Niveau')
                    ->options([
                        'danger'     => '🔴 Critique (crash)',
                        'warning'    => '⚠ Avertissement',
                        'info'       => 'ℹ Info',
                        'login'      => '✓ Connexion réussie',
                        'login_fail' => '✗ Échec de connexion',
                        'update'     => 'Mise à jour',
                    ]),

                // Filtre par période
                Tables\Filters\Filter::make('today')
                    ->label("Aujourd'hui seulement")
                    ->query(fn($query) => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('last_24h')
                    ->label('Dernières 24h')
                    ->query(fn($query) => $query->where('created_at', '>=', now()->subDay())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('voir_json')
                    ->label('JSON')
                    ->icon('heroicon-o-code-bracket')
                    ->modalHeading('Détails Techniques')
                    ->modalContent(fn($record) => new \Illuminate\Support\HtmlString(
                        '<pre style="white-space: pre-wrap; font-size: 0.85em; background: #f3f4f6; padding: 10px; border-radius: 8px;">' .
                            json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) .
                            '</pre>'
                    ))
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_logs')
                    ->label('Vider l\'historique')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer tous les logs ?')
                    ->modalDescription('Cette action est irréversible. Tout l\'historique système sera effacé.')
                    ->modalSubmitActionLabel('Oui, tout supprimer')
                    ->action(function () {
                        SystemLog::truncate();

                        \Filament\Notifications\Notification::make()
                            ->title('Historique vidé avec succès')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageSystemLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
}
