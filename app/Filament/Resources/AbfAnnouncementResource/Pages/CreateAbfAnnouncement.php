<?php

namespace App\Filament\Resources\AbfAnnouncementResource\Pages;

use App\Filament\Resources\AbfAnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbfAnnouncement extends CreateRecord
{
    protected static string $resource = AbfAnnouncementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
