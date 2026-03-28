<?php

namespace App\Filament\Abf\Resources\AbfParameterResource\Pages;

use App\Filament\Abf\Resources\AbfParameterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbfParameter extends EditRecord
{
    protected static string $resource = AbfParameterResource::class;

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
