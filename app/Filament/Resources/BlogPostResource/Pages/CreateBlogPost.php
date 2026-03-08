<?php

namespace App\Filament\Resources\BlogPostResource\Pages;

use App\Filament\Pages\BaseCreateRecord;
use App\Filament\Resources\BlogPostResource;
use App\Models\BlogPost;
use App\Models\Language;

class CreateBlogPost extends BaseCreateRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $locales = Language::activeCodes();
        $fallback = Language::defaultCode();

        // Si ton form hydrate title_fr/title_en etc. on garde la compat
        foreach ($locales as $locale) {
            $titleKey = "title_{$locale}";
            $slugKey  = "slug_{$locale}";

            if (isset($data[$titleKey])) {
                $data[$titleKey] = (string) $data[$titleKey];
            }

            if (isset($data[$slugKey]) && $data[$slugKey] !== '') {
                $data[$slugKey] = BlogPost::makeSeoSlug((string) $data[$slugKey], $locale);
            } elseif (!empty($data[$titleKey])) {
                $data[$slugKey] = BlogPost::makeSeoSlug((string) $data[$titleKey], $locale);
            }
        }

        return $data;
    }
}
