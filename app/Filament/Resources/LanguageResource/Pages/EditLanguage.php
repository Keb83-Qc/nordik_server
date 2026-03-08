<?php

namespace App\Filament\Resources\LanguageResource\Pages;

use App\Filament\Resources\LanguageResource;
use App\Models\Language;
use App\Filament\Pages\BaseEditRecord;

class EditLanguage extends BaseEditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // si on coche "default", on force active
        if (!empty($data['is_default'])) {
            $data['is_active'] = true;
        }

        // si c'est déjà default, on n'autorise pas à désactiver
        if ($this->record instanceof Language && $this->record->is_default) {
            $data['is_active'] = true;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // si cette langue est default => toutes les autres deviennent non-default
        if ($this->record->is_default) {
            Language::where('id', '!=', $this->record->id)->update(['is_default' => false]);
        }
    }
}
