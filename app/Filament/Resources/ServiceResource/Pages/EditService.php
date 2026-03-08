<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditService extends EditRecord
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

            Actions\Action::make('save')
                ->label('Sauvegarder')
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('save'),

            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->successRedirectUrl($this->getResource()::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
