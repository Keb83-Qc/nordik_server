<?php

namespace App\Filament\Widgets;

use App\Models\Tool;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class DocumentsWidget extends BaseWidget
{
    protected static ?int $sort = 5; // Ordre d'affichage
    protected int | string | array $columnSpan = 2; // Prend 1 colonne sur 2
    protected static ?string $heading = '📂 Documents Utiles';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tool::query()
                    ->where('category', 'document')
                    ->whereNotNull('file_path')
                    ->where('file_path', '!=', '')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nom du fichier')
                    ->weight('bold')
                    ->icon('heroicon-o-document-text'),
            ])
            ->actions([
                // Action pour télécharger
                Tables\Actions\Action::make('download')
                    ->label('Télécharger')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(
                        fn(\App\Models\Tool $record) => $record->file_path
                            ? asset('storage/' . ltrim($record->file_path, '/'))
                            : ($record->external_url ?? '#')
                    )
                    ->openUrlInNewTab(),
            ])
            ->paginated(false); // Pas de pagination (liste simple)
    }
}
