<?php

namespace App\Filament\Resources\CareerPageResource\Pages;

use App\Filament\Resources\CareerPageResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditCareerPage extends BaseEditRecord
{
    protected static string $resource = CareerPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
