<?php

namespace App\Filament\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

abstract class BaseEditRecord extends EditRecord
{
    protected function getHeaderActions(): array
    {
        return array_values(array_filter([
            $this->backHeaderAction(),
            ...$this->getCustomHeaderActions(),
            $this->saveHeaderAction(),
            $this->deleteHeaderAction(),
        ]));
    }

    // IMPORTANT: pas d’actions en bas
    protected function getFormActions(): array
    {
        return [];
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

    protected function saveHeaderAction(): Actions\Action
    {
        return Actions\Action::make('save')
            ->label('Sauvegarder')
            ->icon('heroicon-o-check')
            ->color('primary')
            ->action('save');
    }

    protected function deleteHeaderAction(): Actions\DeleteAction
    {
        return Actions\DeleteAction::make()
            ->label('Supprimer')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->successRedirectUrl($this->getResource()::getUrl());
    }
}
