<?php

namespace App\Filament\Abf\Resources\AbfCaseAdminResource\Pages;

use App\Filament\Abf\Resources\AbfCaseAdminResource;
use Filament\Resources\Pages\ListRecords;

class ListAllAbfCases extends ListRecords
{
    protected static string $resource = AbfCaseAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
