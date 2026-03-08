<?php

namespace App\Filament\Resources\PublicServiceCategoryResource\Pages;

use App\Filament\Resources\PublicServiceCategoryResource;
use App\Models\PublicServiceCategoryTranslation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreatePublicServiceCategory extends CreateRecord
{
    protected static string $resource = PublicServiceCategoryResource::class;

    protected array $i18n = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->i18n = $data['i18n'] ?? [];
        unset($data['i18n']);

        return $data;
    }

    protected function afterCreate(): void
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
