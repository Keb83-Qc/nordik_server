<?php

namespace App\Filament\Resources\QuotePortalResource\Pages;

use App\Filament\Resources\QuotePortalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuotePortal extends CreateRecord
{
    protected static string $resource = QuotePortalResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
