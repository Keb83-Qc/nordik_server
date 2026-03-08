<?php

namespace App\Filament\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

abstract class BaseCreateRecord extends CreateRecord
{
    protected function getHeaderActions(): array
    {
        return array_values(array_filter([
            $this->backHeaderAction(),
            ...$this->getCustomHeaderActions(),
            $this->createHeaderAction(),
        ]));
    }

    protected function getFormActions(): array
    {
        return parent::getFormActions();
    }

    protected function getCustomHeaderActions(): array
    {
        return [];
    }

    protected function backHeaderAction(): Actions\Action
    {
        return Actions\Action::make('back')
            ->label('Retour')
            ->icon('heroicon-o-arrow-left')
            ->color('gray')
            ->url($this->getResource()::getUrl());
    }

    protected function createHeaderAction(): Actions\Action
    {
        return Actions\Action::make('create')
            ->label('Créer')
            ->icon('heroicon-o-check')
            ->color('primary')
            ->action('create');
    }
}
