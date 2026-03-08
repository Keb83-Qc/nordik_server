<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Retour')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),

            Actions\Action::make('create')
                ->label('Sauvegarder')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('create'),
        ];
    }

    protected function getFormActions(): array
    {
        // ✅ enlève les boutons en bas
        return [];
    }
}
