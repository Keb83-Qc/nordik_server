<?php

namespace App\Filament\Resources\PublicServiceCategoryResource\Pages;

use App\Filament\Resources\PublicServiceCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicServiceCategories extends ListRecords
{
    protected static string $resource = PublicServiceCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
