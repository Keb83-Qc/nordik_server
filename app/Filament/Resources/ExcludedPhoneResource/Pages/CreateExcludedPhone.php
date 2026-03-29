<?php

namespace App\Filament\Resources\ExcludedPhoneResource\Pages;

use App\Filament\Resources\ExcludedPhoneResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExcludedPhone extends CreateRecord
{
    protected static string $resource = ExcludedPhoneResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * S'assure que added_by est toujours l'utilisateur connecté,
     * même si le champ hidden est absent du POST.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['added_by'] = auth()->id();
        return $data;
    }
}
