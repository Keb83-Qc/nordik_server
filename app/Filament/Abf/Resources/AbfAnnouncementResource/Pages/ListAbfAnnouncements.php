<?php

namespace App\Filament\Abf\Resources\AbfAnnouncementResource\Pages;

use App\Filament\Abf\Resources\AbfAnnouncementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbfAnnouncements extends ListRecords
{
    protected static string $resource = AbfAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
