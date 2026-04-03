<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\SystemLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;
    protected static ?string $slug  = 'email-logs';

    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Emails';
    protected static ?int    $navigationSort  = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Logs';
    }

    // Badge = nb d'emails dans les dernières 24h
    public static function getNavigationBadge(): ?string
    {
        $count = SystemLog::where('source', 'like', 'email_%')
            ->where('created_at', '>=', now()->subDay())
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    // ── Formulaire de détail ───────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('source')
                    ->label('Département')
                    ->readOnly()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'email_internal' => '📥 Soumissions internes',
                        'email_partner'  => '🏢 Soumissions partenaires',
                        'email_security' => '🔒 Sécurité & accès',
                        'email_abf'      => '📊 Profil financier',
                        'email_alert'    => '🔔 Alertes système',
                        'email_advisor'  => '👥 Liens conseillers',
                        default          => $state ?? '—',
                    }),

                Forms\Components\TextInput::make('level')
                    ->label('Niveau')
                    ->readOnly(),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Date d\'envoi')
                    ->readOnly(),
            ]),

            Forms\Components\TextInput::make('message')
                ->label('Sujet')
                ->columnSpanFull()
                ->readOnly(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('ctx_to')
                    ->label('Destinataire(s)')
                    ->readOnly()
                    ->afterStateHydrated(fn($component, $record) =>
                        $component->state($record?->context['to'] ?? '—')
                    ),

                Forms\Components\TextInput::make('ctx_subject')
                    ->label('Sujet détaillé')
                    ->readOnly()
                    ->afterStateHydrated(fn($component, $record) =>
                        $component->state($record?->context['subject'] ?? '—')
                    ),
            ]),

            Forms\Components\Textarea::make('ctx_json')
                ->label('Détails (JSON)')
                ->columnSpanFull()
                ->rows(8)
                ->readOnly()
                ->extraAttributes(['class' => 'font-mono'])
                ->afterStateHydrated(fn($component, $record) =>
                    $component->state(
                        json_encode($record?->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    )
                ),
        ]);
    }

    // ── Table ──────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('source', 'like', 'email_%'))
            ->columns([
                Tables\Columns\TextColumn::make('source')
                    ->label('Département')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'email_internal' => 'success',
                        'email_partner'  => 'info',
                        'email_security' => 'warning',
                        'email_abf'      => 'primary',
                        'email_alert'    => 'danger',
                        'email_advisor'  => 'success',
                        default          => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'email_internal' => '📥 Internes',
                        'email_partner'  => '🏢 Partenaires',
                        'email_security' => '🔒 Sécurité',
                        'email_abf'      => '📊 ABF',
                        'email_alert'    => '🔔 Alertes',
                        'email_advisor'  => '👥 Conseillers',
                        default          => $state ?? '—',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Sujet')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->message)
                    ->searchable(),

                Tables\Columns\TextColumn::make('destinataire')
                    ->label('Destinataire')
                    ->limit(40)
                    ->getStateUsing(fn($record) => $record->context['to'] ?? '—')
                    ->tooltip(fn($record) => $record->context['to'] ?? null),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date d\'envoi')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->label('Département')
                    ->options([
                        'email_internal' => '📥 Soumissions internes',
                        'email_partner'  => '🏢 Soumissions partenaires',
                        'email_security' => '🔒 Sécurité & accès',
                        'email_abf'      => '📊 Profil financier',
                        'email_alert'    => '🔔 Alertes système',
                        'email_advisor'  => '👥 Liens conseillers',
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
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_email_logs')
                    ->label('Vider les logs email')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer tous les logs email ?')
                    ->modalDescription('Cette action est irréversible.')
                    ->modalSubmitActionLabel('Oui, tout supprimer')
                    ->action(function () {
                        SystemLog::where('source', 'like', 'email_%')->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Logs email vidés avec succès')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEmailLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
}
