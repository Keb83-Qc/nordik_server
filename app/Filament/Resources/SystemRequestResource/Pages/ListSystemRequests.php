<?php

namespace App\Filament\Resources\SystemRequestResource\Pages;

use App\Filament\Resources\SystemRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListSystemRequests extends ListRecords
{
    protected static string $resource = SystemRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
