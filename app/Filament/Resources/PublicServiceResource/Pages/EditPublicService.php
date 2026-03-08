<?php

namespace App\Filament\Resources\PublicServiceResource\Pages;

use App\Filament\Pages\BaseEditRecord;
use App\Filament\Resources\PublicServiceResource;
use App\Models\PublicServiceTranslation;

class EditPublicService extends BaseEditRecord
{
    protected static string $resource = PublicServiceResource::class;

    protected array $i18n = [];

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $locales = PublicServiceResource::getActiveLocales();
        $data['i18n'] = [];

        $translations = $this->record->translations()->get()->keyBy('locale');

        foreach ($locales as $loc) {
            $t = $translations->get($loc);
            $data['i18n'][$loc] = [
                'title' => $t?->title ?? '',
                'slug'  => $t?->slug ?? '',
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
        $locales = PublicServiceResource::getActiveLocales();

        foreach ($locales as $loc) {
            $payload = $this->i18n[$loc] ?? [];
            $title = trim((string) ($payload['title'] ?? ''));
            $slug  = trim((string) ($payload['slug'] ?? ''));

            if ($title === '' && $slug === '') {
                continue;
            }

            PublicServiceTranslation::updateOrCreate(
                ['public_service_id' => $this->record->id, 'locale' => $loc],
                ['title' => $title, 'slug' => $slug]
            );
        }
    }
}
