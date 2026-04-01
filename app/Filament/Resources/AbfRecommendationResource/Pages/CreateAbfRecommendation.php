<?php

namespace App\Filament\Resources\AbfRecommendationResource\Pages;

use App\Filament\Resources\AbfRecommendationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbfRecommendation extends CreateRecord
{
    protected static string $resource = AbfRecommendationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
