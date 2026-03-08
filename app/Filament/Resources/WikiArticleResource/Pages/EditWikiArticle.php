<?php

namespace App\Filament\Resources\WikiArticleResource\Pages;

use App\Filament\Resources\WikiArticleResource;
use Filament\Actions;
use App\Filament\Pages\BaseEditRecord;

class EditWikiArticle extends BaseEditRecord
{
    protected static string $resource = WikiArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(), // Bouton pour voir l'article
            Actions\DeleteAction::make(),
        ];
    }
}
