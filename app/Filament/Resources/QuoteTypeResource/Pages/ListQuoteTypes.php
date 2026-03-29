<?php

namespace App\Filament\Resources\QuoteTypeResource\Pages;

use App\Filament\Resources\QuoteTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuoteTypes extends ListRecords
{
    protected static string $resource = QuoteTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
