<?php

namespace App\Filament\Resources\QuotePortalResource\Pages;

use App\Filament\Resources\QuotePortalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuotePortals extends ListRecords
{
    protected static string $resource = QuotePortalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nouveau portail'),
        ];
    }
}
