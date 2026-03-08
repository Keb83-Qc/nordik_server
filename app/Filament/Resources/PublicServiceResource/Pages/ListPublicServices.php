<?php

namespace App\Filament\Resources\PublicServiceResource\Pages;

use App\Filament\Resources\PublicServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicServices extends ListRecords
{
    protected static string $resource = PublicServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
