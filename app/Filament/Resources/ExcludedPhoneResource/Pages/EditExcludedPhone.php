<?php

namespace App\Filament\Resources\ExcludedPhoneResource\Pages;

use App\Filament\Resources\ExcludedPhoneResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExcludedPhone extends EditRecord
{
    protected static string $resource = ExcludedPhoneResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
