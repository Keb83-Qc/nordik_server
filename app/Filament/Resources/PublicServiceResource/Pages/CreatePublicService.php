<?php

namespace App\Filament\Resources\PublicServiceResource\Pages;

use App\Filament\Resources\PublicServiceResource;
use App\Models\PublicServiceTranslation;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicService extends CreateRecord
{
    protected static string $resource = PublicServiceResource::class;

    protected array $i18n = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->i18n = $data['i18n'] ?? [];
        unset($data['i18n']);

        return $data;
    }

    protected function afterCreate(): void
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
