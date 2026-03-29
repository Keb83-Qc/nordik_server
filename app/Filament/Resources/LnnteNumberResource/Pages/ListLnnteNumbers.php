<?php

namespace App\Filament\Resources\LnnteNumberResource\Pages;

use App\Filament\Resources\LnnteNumberResource;
use App\Models\LnnteNumber;
use Filament\Resources\Pages\ListRecords;

class ListLnnteNumbers extends ListRecords
{
    protected static string $resource = LnnteNumberResource::class;

    /**
     * Onglets par lot d'import pour naviguer rapidement entre les versions CRTC.
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => ListRecords\Tab::make('Tous')
                ->badge(fn () => number_format(LnnteNumber::count(), 0, ',', ' ')),
        ];

        $batches = LnnteNumber::selectRaw('import_batch, COUNT(*) as total')
            ->whereNotNull('import_batch')
            ->groupBy('import_batch')
            ->orderBy('import_batch', 'desc')
            ->get();

        foreach ($batches as $batch) {
            $tabs[$batch->import_batch] = ListRecords\Tab::make($batch->import_batch)
                ->badge(number_format($batch->total, 0, ',', ' '))
                ->badgeColor('danger')
                ->modifyQueryUsing(fn ($query) => $query->where('import_batch', $batch->import_batch));
        }

        return $tabs;
    }
}
