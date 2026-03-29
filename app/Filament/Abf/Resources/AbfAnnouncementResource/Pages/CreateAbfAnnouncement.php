<?php

namespace App\Filament\Abf\Resources\AbfAnnouncementResource\Pages;

use App\Filament\Abf\Resources\AbfAnnouncementResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbfAnnouncement extends CreateRecord
{
    protected static string $resource = AbfAnnouncementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
