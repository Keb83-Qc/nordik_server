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

            Forms\Components\Placeholder::make('context_display')
                ->label('Détails (JSON)')
                ->columnSpanFull()
                ->content(fn($record): \Illuminate\Support\HtmlString =>
                    new \Illuminate\Support\HtmlString(
                        '<pre style="font-family:monospace;font-size:.85rem;line-height:1.6;'
                        . 'padding:1rem;border-radius:.5rem;overflow:auto;white-space:pre-wrap;word-break:break-all;'
                        . 'background:rgba(0,0,0,.04);border:1px solid rgba(0,0,0,.08)">'
                        . htmlspecialchars(
                            json_encode(
                                $record?->context,
                                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                            ) ?? '—'
                        )
                        . '</pre>'
                    )
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

                Tables\Actions\Action::make('copier')
                    ->label('Copier')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->modalHeading('Copier le log')
                    ->modalContent(function ($record): \Illuminate\Support\HtmlString {
                        $copyId   = 'conncopy-' . $record->id;
                        $jsonText = json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $fullText = $record->message . "\n\n" . $jsonText;

                        return new \Illuminate\Support\HtmlString(
                            '<div x-data="{copied: false}">'
                            . '<textarea id="' . $copyId . '" style="position:absolute;left:-9999px;width:1px;height:1px" readonly>' . htmlspecialchars($fullText) . '</textarea>'
                            . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">'
                            . '<span style="font-size:.82rem;font-weight:600;opacity:.7">Message + contexte JSON</span>'
                            . '<button type="button"'
                            . ' x-on:click="navigator.clipboard.writeText(document.getElementById(\'' . $copyId . '\').value).then(() => { copied=true; setTimeout(()=>copied=false,2500) })"'
                            . ' style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:1px solid rgba(201,160,80,.45);background:rgba(201,160,80,.12);color:#c9a050">'
                            . '<span x-show="!copied">📋 Copier</span>'
                            . '<span x-show="copied" style="color:#22c55e">✓ Copié !</span>'
                            . '</button>'
                            . '</div>'
                            . '<pre style="white-space:pre-wrap;font-size:.82em;background:rgba(0,0,0,.04);padding:14px;border-radius:10px;overflow:auto;max-height:420px;line-height:1.55;margin:0;border:1px solid rgba(0,0,0,.07)">'
                            . htmlspecialchars($jsonText)
                            . '</pre>'
                            . '</div>'
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_connection_logs')
                    ->label('Vider l\'historique des connexions')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Vider l\'historique des connexions')
                    ->modalDescription('Cette action est irréversible. Seuls les logs de connexion de l\'onglet actif seront supprimés.')
                    ->modalSubmitActionLabel('Oui, supprimer')
                    ->action(function (\Livewire\Component $livewire) {
                        $tab = $livewire->activeTab ?? 'toutes';

                        if ($tab === 'reussies') {
                            SystemLog::where('level', 'login')->delete();
                        } elseif ($tab === 'echouees') {
                            SystemLog::where('level', 'login_fail')->delete();
                        } else {
                            SystemLog::whereIn('level', ['login', 'login_fail'])->delete();
                        }

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
