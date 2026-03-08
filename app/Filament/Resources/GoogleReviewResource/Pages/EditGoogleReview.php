<?php

namespace App\Filament\Resources\GoogleReviewResource\Pages;

use App\Filament\Resources\GoogleReviewResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditGoogleReview extends BaseEditRecord
{
    protected static string $resource = GoogleReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
