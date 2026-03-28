<?php

namespace App\Filament\Abf\Resources\AbfParameterResource\Pages;

use App\Filament\Abf\Resources\AbfParameterResource;
use Filament\Resources\Pages\ListRecords;

class ListAbfParameters extends ListRecords
{
    protected static string $resource = AbfParameterResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
