<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LnnteNumberResource\Pages;
use App\Jobs\ImportLnnteFileJob;
use App\Models\LnnteNumber;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Resource en lecture seule pour consulter et gérer les imports LNNTE officiels.
 * Les numéros ne peuvent pas être ajoutés manuellement ici — uniquement via import fichier.
 */
class LnnteNumberResource extends Resource
{
    protected static ?string $model = LnnteNumber::class;

    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'LNNTE officielle (CRTC)';
    protected static ?string $modelLabel      = 'Numéro LNNTE';
    protected static ?string $pluralModelLabel = 'LNNTE officielle';

    public static function getNavigationGroup(): ?string
    {
        return 'Conformité';
    }

    public static function getNavigationSort(): ?int
    {
        return config('filament-navigation.sort.' . static::class, 2);
    }

    /**
     * Badge dans le menu : nombre total de numéros LNNTE.
     */
    public static function getNavigationBadge(): ?string
    {
        $count = LnnteNumber::count();
        return $count > 0 ? number_format($count, 0, ',', ' ') : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // ─── Pas de formulaire — resource en lecture seule ────────────────────────

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    // ─── Tableau ──────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('phone')
                    ->label('Numéro')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Numéro copié !')
                    ->icon('heroicon-m-phone-x-mark'),

                Tables\Columns\TextColumn::make('phone_normalized')
                    ->label('Normalisé')
                    ->badge()
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('import_batch')
                    ->label('Lot d\'import')
                    ->badge()
                    ->color('danger')
                    ->placeholder('—')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Importé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('import_batch')
                    ->label('Lot d\'import')
                    ->options(fn () => LnnteNumber::selectRaw('import_batch')
                        ->whereNotNull('import_batch')
                        ->distinct()
                        ->orderBy('import_batch', 'desc')
                        ->pluck('import_batch', 'import_batch')
                        ->toArray()
                    ),
            ])
            ->headerActions([
                // ─── Import nouveau fichier CRTC ──────────────────────────────
                Tables\Actions\Action::make('import_lnnte')
                    ->label('Importer un fichier CRTC')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('danger')
                    ->modalHeading('Import fichier LNNTE officiel (CRTC)')
                    ->modalDescription(
                        'Importez le fichier téléchargé depuis le portail CRTC (lnnte-dncl.gc.ca). ' .
                        'L\'import se fait en arrière-plan — vous recevrez une notification à la fin.'
                    )
                    ->form([
                        Forms\Components\FileUpload::make('lnnte_file')
                            ->label('Fichier LNNTE (.txt ou .csv)')
                            ->required()
                            ->acceptedFileTypes([
                                'text/plain', 'text/csv',
                                'application/csv', 'application/octet-stream',
                            ])
                            ->maxSize(102400) // 100 Mo max
                            ->storeFiles(false)
                            ->helperText(
                                'Format attendu : un numéro par ligne (ex: 4185551234) ' .
                                'ou CSV avec numéro en première colonne.'
                            ),

                        Forms\Components\TextInput::make('import_batch')
                            ->label('Nom du lot')
                            ->required()
                            ->default(fn () => 'Import ' . now()->format('Y-m') . ' CRTC')
                            ->placeholder('Ex: 2026-03 CRTC Québec')
                            ->helperText('Permet de retrouver et purger cet import plus tard.'),
                    ])
                    ->action(function (array $data): void {
                        $uploadedFile = $data['lnnte_file'];

                        if (! $uploadedFile) {
                            Notification::make()->warning()->title('Aucun fichier sélectionné')->send();
                            return;
                        }

                        $filename = 'lnnte/import_' . now()->format('YmdHis') . '_' . auth()->id() . '.txt';
                        Storage::put($filename, file_get_contents($uploadedFile->getRealPath()));

                        ImportLnnteFileJob::dispatch(
                            $filename,
                            $data['import_batch'] ?? '',
                            auth()->user(),
                        );

                        Notification::make()
                            ->success()
                            ->title('🚀 Import lancé en arrière-plan')
                            ->body('Vous recevrez une notification Filament à la fin de l\'import.')
                            ->send();
                    })
                    ->modalSubmitActionLabel('Lancer l\'import')
                    ->modalWidth('lg'),

                // ─── Purger un lot ────────────────────────────────────────────
                Tables\Actions\Action::make('purge_batch')
                    ->label('Purger un lot')
                    ->icon('heroicon-o-trash')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Purger un lot d\'import LNNTE')
                    ->modalDescription('Supprime tous les numéros du lot sélectionné. Cette action est irréversible.')
                    ->form([
                        Forms\Components\Select::make('batch')
                            ->label('Lot à purger')
                            ->required()
                            ->options(fn () => LnnteNumber::selectRaw('import_batch, COUNT(*) as total')
                                ->whereNotNull('import_batch')
                                ->groupBy('import_batch')
                                ->orderBy('import_batch', 'desc')
                                ->get()
                                ->mapWithKeys(fn ($row) => [
                                    $row->import_batch => $row->import_batch . ' (' . number_format($row->total, 0, ',', ' ') . ' numéros)',
                                ])
                                ->toArray()
                            ),
                    ])
                    ->action(function (array $data): void {
                        $deleted = LnnteNumber::where('import_batch', $data['batch'])->delete();

                        Notification::make()
                            ->success()
                            ->title("Lot « {$data['batch']} » purgé")
                            ->body(number_format($deleted, 0, ',', ' ') . ' numéro(s) supprimé(s).')
                            ->send();
                    }),
            ])
            ->actions([
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
            'index' => Pages\ListLnnteNumbers::route('/'),
        ];
    }
}
