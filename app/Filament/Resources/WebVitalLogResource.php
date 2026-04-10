<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebVitalLogResource\Pages;
use App\Models\SystemLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class WebVitalLogResource extends Resource
{
    protected static ?string $model = SystemLog::class;
    protected static ?string $slug  = 'webvital-logs';

    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Web Vitals';
    protected static ?int    $navigationSort  = 4;

    public static function getNavigationGroup(): ?string
    {
        return 'Logs';
    }

    // Badge = nb de métriques "poor" (mauvaise perf) dans les dernières 24h
    public static function getNavigationBadge(): ?string
    {
        $count = SystemLog::where('message', 'like', '[WebVital]%')
            ->where('created_at', '>=', now()->subDay())
            ->whereJsonContains('context->rating', 'poor')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    // ── Formulaire de détail ───────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Grid::make(4)->schema([
                Forms\Components\TextInput::make('context.metric')
                    ->label('Métrique')
                    ->readOnly(),

                Forms\Components\TextInput::make('context.value')
                    ->label('Valeur (ms / score)')
                    ->readOnly(),

                Forms\Components\TextInput::make('context.rating')
                    ->label('Évaluation')
                    ->readOnly()
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'good'             => '✅ Bon',
                        'needs-improvement'=> '⚠️ À améliorer',
                        'poor'             => '🔴 Mauvais',
                        default            => $state ?? '—',
                    }),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Date')
                    ->readOnly(),
            ]),

            Forms\Components\TextInput::make('context.url')
                ->label('URL de la page')
                ->columnSpanFull()
                ->readOnly(),

            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('context.delta')
                    ->label('Delta')
                    ->readOnly(),

                Forms\Components\TextInput::make('context.nav_type')
                    ->label('Type de navigation')
                    ->readOnly(),
            ]),

            Forms\Components\Placeholder::make('context_display')
                ->label('Détails (JSON)')
                ->columnSpanFull()
                ->content(fn($record): \Illuminate\Support\HtmlString => self::renderCopyableJson($record)),
        ]);
    }

    // ── Table ──────────────────────────────────────────────────────────────
    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('message', 'like', '[WebVital]%'))
            ->columns([
                Tables\Columns\TextColumn::make('context.metric')
                    ->label('Métrique')
                    ->badge()
                    ->color(fn(?string $state): string => match (strtoupper((string) $state)) {
                        'LCP'  => 'info',
                        'INP'  => 'warning',
                        'CLS'  => 'success',
                        'FCP'  => 'primary',
                        'TTFB' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('context.rating')
                    ->label('Évaluation')
                    ->badge()
                    ->color(fn(?string $state): string => match ($state) {
                        'good'              => 'success',
                        'needs-improvement' => 'warning',
                        'poor'              => 'danger',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn(?string $state): string => match ($state) {
                        'good'              => '✅ Bon',
                        'needs-improvement' => '⚠️ À améliorer',
                        'poor'              => '🔴 Mauvais',
                        default             => $state ?? '—',
                    }),

                Tables\Columns\TextColumn::make('context.value')
                    ->label('Valeur')
                    ->formatStateUsing(fn($state) => $state !== null ? number_format((float) $state, 1) : '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('context.url')
                    ->label('Page')
                    ->limit(50)
                    ->tooltip(fn($record) => $record->context['url'] ?? null)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('context.nav_type')
                    ->label('Navigation')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordAction('view')
            ->filters([
                Tables\Filters\SelectFilter::make('rating')
                    ->label('Évaluation')
                    ->options([
                        'good'              => '✅ Bon',
                        'needs-improvement' => '⚠️ À améliorer',
                        'poor'              => '🔴 Mauvais',
                    ])
                    ->query(fn($query, $data) => $data['value']
                        ? $query->whereJsonContains('context->rating', $data['value'])
                        : $query),

                Tables\Filters\SelectFilter::make('metric')
                    ->label('Métrique')
                    ->options([
                        'LCP'  => 'LCP — Largest Contentful Paint',
                        'INP'  => 'INP — Interaction to Next Paint',
                        'CLS'  => 'CLS — Cumulative Layout Shift',
                        'FCP'  => 'FCP — First Contentful Paint',
                        'TTFB' => 'TTFB — Time to First Byte',
                    ])
                    ->query(fn($query, $data) => $data['value']
                        ? $query->whereJsonContains('context->metric', $data['value'])
                        : $query),

                Tables\Filters\Filter::make('today')
                    ->label("Aujourd'hui seulement")
                    ->query(fn($query) => $query->whereDate('created_at', today())),

                Tables\Filters\Filter::make('last_24h')
                    ->label('Dernières 24h')
                    ->query(fn($query) => $query->where('created_at', '>=', now()->subDay())),

                Tables\Filters\Filter::make('poor_only')
                    ->label('Mauvaises perf. seulement')
                    ->query(fn($query) => $query->whereJsonContains('context->rating', 'poor')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),

                Tables\Actions\Action::make('copier')
                    ->label('Copier')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->modalHeading('Copier le log Web Vital')
                    ->modalContent(fn($record): \Illuminate\Support\HtmlString => self::renderCopyModal($record))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Fermer'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('clear_webvitals')
                    ->label('Vider l\'historique')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Supprimer tous les logs Web Vitals ?')
                    ->modalDescription('Cette action est irréversible. Tous les logs Web Vitals seront supprimés.')
                    ->modalSubmitActionLabel('Oui, supprimer')
                    ->action(function (\Livewire\Component $livewire) {
                        $tab   = $livewire->activeTab ?? 'tous';
                        $query = SystemLog::where('message', 'like', '[WebVital]%');

                        if ($tab !== 'tous') {
                            $query->whereJsonContains('context->metric', strtoupper($tab));
                        }

                        $query->delete();

                        \Filament\Notifications\Notification::make()
                            ->title('Logs Web Vitals vidés avec succès')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWebVitalLogs::route('/'),
        ];
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }

    // ── Helpers rendus partagés ────────────────────────────────────────────

    private static function renderCopyModal($record): \Illuminate\Support\HtmlString
    {
        $copyId   = 'wvcopy-' . $record->id;
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
    }

    private static function renderCopyableJson($record): \Illuminate\Support\HtmlString
    {
        $copyId   = 'wvjson-' . $record->id;
        $jsonText = json_encode($record->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $fullText = $record->message . "\n\n" . $jsonText;

        return new \Illuminate\Support\HtmlString(
            '<div x-data="{copied: false}" style="width:100%">'
            . '<textarea id="' . $copyId . '" style="position:absolute;left:-9999px;width:1px;height:1px" readonly>' . htmlspecialchars($fullText) . '</textarea>'
            . '<div style="display:flex;justify-content:flex-end;margin-bottom:6px">'
            . '<button type="button"'
            . ' x-on:click="navigator.clipboard.writeText(document.getElementById(\'' . $copyId . '\').value).then(() => { copied=true; setTimeout(()=>copied=false,2500) })"'
            . ' style="display:inline-flex;align-items:center;gap:6px;padding:4px 12px;border-radius:7px;font-size:.78rem;font-weight:600;cursor:pointer;border:1px solid rgba(201,160,80,.45);background:rgba(201,160,80,.12);color:#c9a050">'
            . '<span x-show="!copied">📋 Copier</span>'
            . '<span x-show="copied" style="color:#22c55e">✓ Copié !</span>'
            . '</button>'
            . '</div>'
            . '<pre style="white-space:pre-wrap;font-family:monospace;font-size:.82rem;line-height:1.6;padding:1rem;border-radius:.5rem;overflow:auto;background:rgba(0,0,0,.04);border:1px solid rgba(0,0,0,.08)">'
            . htmlspecialchars($jsonText)
            . '</pre>'
            . '</div>'
        );
    }
}
