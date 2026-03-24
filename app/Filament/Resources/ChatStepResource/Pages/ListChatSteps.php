<?php

namespace App\Filament\Resources\ChatStepResource\Pages;

use App\Filament\Resources\ChatStepResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatSteps extends ListRecords
{
    protected static string $resource = ChatStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
