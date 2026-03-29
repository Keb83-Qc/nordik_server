<?php

namespace App\Filament\Resources\QuoteTypeResource\Pages;

use App\Filament\Resources\QuoteTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuoteType extends CreateRecord
{
    protected static string $resource = QuoteTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
