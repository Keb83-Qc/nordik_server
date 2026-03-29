<?php

namespace App\Filament\Resources\ExcludedPhoneResource\Pages;

use App\Filament\Resources\ExcludedPhoneResource;
use App\Models\ExcludedPhone;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListExcludedPhones extends ListRecords
{
    protected static string $resource = ExcludedPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // ─── Vérifier un numéro rapidement ───────────────────────────────
            Actions\Action::make('check_number')
                ->label('Vérifier un numéro')
                ->icon('heroicon-o-magnifying-glass')
                ->color('gray')
                ->form([
                    TextInput::make('phone')
                        ->label('Numéro à vérifier')
                        ->required()
                        ->tel()
                        ->placeholder('ex: (418) 555-1234')
                        ->helperText('Le format n\'a pas d\'importance.'),
                ])
                ->action(function (array $data): void {
                    $phone  = $data['phone'];
                    $entry  = ExcludedPhone::findByPhone($phone);

                    if ($entry) {
                        $reason = ExcludedPhone::REASONS[$entry->reason] ?? $entry->reason;
                        $since  = $entry->created_at->format('d/m/Y');

                        Notification::make()
                            ->danger()
                            ->title('🚫 Numéro exclu')
                            ->body("**{$phone}** est dans la liste depuis le {$since}.\n\nRaison : {$reason}" .
                                ($entry->notes ? "\n\nNotes : {$entry->notes}" : ''))
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->success()
                            ->title('✅ Numéro libre')
                            ->body("**{$phone}** n'est pas dans la liste d'exclusion.")
                            ->send();
                    }
                })
                ->modalSubmitActionLabel('Vérifier')
                ->modalWidth('sm'),

            // ─── Import en lot ────────────────────────────────────────────────
            Actions\Action::make('import_bulk')
                ->label('Import en lot')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\Select::make('reason')
                        ->label('Raison')
                        ->required()
                        ->options(ExcludedPhone::REASONS)
                        ->default('lnnte_official'),

                    \Filament\Forms\Components\Textarea::make('phones')
                        ->label('Numéros (un par ligne)')
                        ->required()
                        ->rows(8)
                        ->placeholder("4185551234\n4185556789\n(418) 555-0001\n...")
                        ->helperText('Collez une liste de numéros — un par ligne. Le format n\'a pas d\'importance.'),

                    \Filament\Forms\Components\Textarea::make('notes')
                        ->label('Notes (optionnel)')
                        ->rows(2)
                        ->placeholder('Ex: Import LNNTE mars 2026'),
                ])
                ->action(function (array $data): void {
                    $lines   = preg_split('/[\r\n]+/', trim($data['phones']));
                    $added   = 0;
                    $skipped = 0;

                    foreach ($lines as $line) {
                        $line = trim($line);
                        if ($line === '') continue;

                        $normalized = ExcludedPhone::normalize($line);
                        if (empty($normalized)) { $skipped++; continue; }

                        // Ignore les doublons silencieusement
                        $exists = ExcludedPhone::where('phone_normalized', $normalized)->exists();
                        if ($exists) { $skipped++; continue; }

                        ExcludedPhone::create([
                            'phone'    => $line,
                            'reason'   => $data['reason'],
                            'notes'    => $data['notes'] ?? null,
                            'added_by' => auth()->id(),
                        ]);

                        $added++;
                    }

                    Notification::make()
                        ->success()
                        ->title("{$added} numéro(s) importé(s)" . ($skipped > 0 ? ", {$skipped} ignoré(s) (doublons)" : ''))
                        ->send();
                })
                ->modalSubmitActionLabel('Importer')
                ->modalHeading('Import en lot de numéros exclus'),

            Actions\CreateAction::make()
                ->label('Ajouter un numéro'),
        ];
    }
}
