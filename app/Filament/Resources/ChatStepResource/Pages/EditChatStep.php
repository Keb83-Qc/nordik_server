<?php

namespace App\Filament\Resources\ChatStepResource\Pages;

use App\Filament\Resources\ChatStepResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatStep extends EditRecord
{
    protected static string $resource = ChatStepResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
