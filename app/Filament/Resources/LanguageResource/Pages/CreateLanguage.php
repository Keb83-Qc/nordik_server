<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use App\Services\LangFilesManager;
use Filament\Resources\Pages\CreateRecord;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected function afterCreate(): void
    {
        $code = (string) ($this->record->code ?? '');
        if ($code !== '') {
            app(LangFilesManager::class)->ensureLocaleFromFrench($code);
        }
    }
}
