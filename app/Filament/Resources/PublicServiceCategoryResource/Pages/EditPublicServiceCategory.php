<?php

namespace App\Filament\Resources\PublicServiceCategoryResource\Pages;

use App\Filament\Pages\BaseEditRecord;
use App\Filament\Resources\PublicServiceCategoryResource;
use App\Models\PublicServiceCategoryTranslation;

class EditPublicServiceCategory extends BaseEditRecord
{
    protected static string $resource = PublicServiceCategoryResource::class;

    protected array $i18n = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = PublicServiceCategoryResource::getActiveLocales();
        $data['i18n'] = [];

        $translations = $this->record->translations()->get()->keyBy('locale');

        foreach ($locales as $loc) {
            $t = $translations->get($loc);
            $data['i18n'][$loc] = [
                'name' => $t?->name ?? '',
                'slug' => $t?->slug ?? '',
            ];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->i18n = $data['i18n'] ?? [];
        unset($data['i18n']);

        return $data;
    }

    protected function afterSave(): void
    {
        $locales = PublicServiceCategoryResource::getActiveLocales();

        foreach ($locales as $loc) {
            $payload = $this->i18n[$loc] ?? [];
            $name = trim((string) ($payload['name'] ?? ''));
            $slug = trim((string) ($payload['slug'] ?? ''));

            if ($name === '' && $slug === '') {
                continue;
            }

            PublicServiceCategoryTranslation::updateOrCreate(
                ['public_service_category_id' => $this->record->id, 'locale' => $loc],
                ['name' => $name, 'slug' => $slug]
            );
        }
    }
}
