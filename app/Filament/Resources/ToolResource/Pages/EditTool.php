<?php

namespace App\Filament\Resources\ToolResource\Pages;

use App\Filament\Resources\ToolResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditTool extends BaseEditRecord
{
    protected static string $resource = ToolResource::class;

    // Modifie le bouton Annuler du formulaire
    protected function getCancelFormAction(): Actions\Action
    {
        return parent::getCancelFormAction()
            ->label('Fermer');
    }
}
