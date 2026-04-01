<?php

namespace App\Filament\Resources\AbfRecommendationResource\Pages;

use App\Filament\Resources\AbfRecommendationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbfRecommendation extends EditRecord
{
    protected static string $resource = AbfRecommendationResource::class;

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
