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
    protected static ?string $navigationIcon  = 'heroicon-o-cpu-chip';
    protected static ?string $navigationLabel = 'Système';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Logs';
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

                // Niveau
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
                Tables\Filters\SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'public' => '🌐 Site public',
                        'admin'  => '🔧 Panel admin',
                        'cli'    => '⚙️ CLI / Queue',
                        'api'    => '🔌 API',
                    ]),

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
                    ->modalContent(function ($record): \Illuminate\Support\HtmlString {
                        $copyId   = 'logcopy-' . $record->id;
                        $jsonText = json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                        $fullText = $record->message . "\n\n" . $jsonText;

                        return new \Illuminate\Support\HtmlString(
                            '<div x-data="{copied: false}">'
                            . '<textarea id="' . $copyId . '" style="position:absolute;left:-9999px;width:1px;height:1px" readonly>' . htmlspecialchars($fullText) . '</textarea>'

                            . '<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">'
                            . '<span style="font-size:.82rem;font-weight:600;opacity:.7">Message + contexte JSON</span>'
                            . '<button type="button"'
                            . ' x-on:click="navigator.clipboard.writeText(document.getElementById(\'' . $copyId . '\').value).then(() => { copied=true; setTimeout(()=>copied=false,2500) })"'
                            . ' style="display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:8px;font-size:.8rem;font-weight:600;cursor:pointer;border:1px solid rgba(201,160,80,.45);background:rgba(201,160,80,.12);color:#c9a050;transition:background .15s"'
                            . ' x-on:mouseover="$el.style.background=\'rgba(201,160,80,.22)\'"'
                            . ' x-on:mouseout="$el.style.background=\'rgba(201,160,80,.12)\'">'
                            . '<span x-show="!copied">📋 Copier</span>'
                            . '<span x-show="copied" style="color:#22c55e">✓ Copié !</span>'
                            . '</button>'
                            . '</div>'

                            . '<pre style="white-space:pre-wrap;font-size:.82em;background:rgba(0,0,0,.04);padding:14px;border-radius:10px;overflow:auto;max-height:460px;line-height:1.55;margin:0;border:1px solid rgba(0,0,0,.07)">'
                            . htmlspecialchars($jsonText)
                            . '</pre>'
                            . '</div>'
                        );
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_logs')
                    ->label('Vider l\'historique système')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Vider l\'historique système')
                    ->modalDescription('Cette action est irréversible. Seuls les logs système de l\'onglet actif seront supprimés.')
                    ->modalSubmitActionLabel('Oui, supprimer')
                    ->action(function (\Livewire\Component $livewire) {
                        $tab = $livewire->activeTab ?? 'tous';

                        if ($tab === 'info') {
                            SystemLog::where('level', 'info')
                                ->where('message', 'not like', '[WebVital]%')
                                ->delete();
                        } elseif ($tab === 'update') {
                            SystemLog::where('level', 'update')->delete();
                        } elseif ($tab === 'error') {
                            SystemLog::where('level', 'error')->delete();
                        } elseif ($tab === 'fatal') {
                            SystemLog::where('level', 'fatal')->delete();
                        } else {
                            // Onglet "tous" : uniquement les logs système (exclut emails, connexions, webvitals)
                            SystemLog::where('source', 'not like', 'email_%')
                                ->whereNotIn('level', ['login', 'login_fail'])
                                ->where('message', 'not like', '[WebVital]%')
                                ->delete();
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Historique système vidé avec succès')
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
