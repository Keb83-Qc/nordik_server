<?php

namespace App\Filament\Resources\AbfAnnouncementResource\Pages;

use App\Filament\Resources\AbfAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbfAnnouncement extends EditRecord
{
    protected static string $resource = AbfAnnouncementResource::class;

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
