<?php

namespace App\Filament\Resources\AbfRecommendationResource\Pages;

use App\Filament\Resources\AbfRecommendationResource;
use App\Models\AbfRecommendation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAbfRecommendation extends CreateRecord
{
    protected static string $resource = AbfRecommendationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Avant la création :
     *  - Génère la clé (slug camelCase) depuis le titre si elle est vide
     *  - Assigne sort_order = max de la catégorie + 1 (dernier de la catégorie)
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // .3 — Auto-slug depuis le titre
        if (empty($data['key']) && !empty($data['title'])) {
            $base = Str::camel(Str::slug($data['title'], ' '));
            $key  = $base;
            $i    = 2;
            while (AbfRecommendation::where('key', $key)->exists()) {
                $key = $base . $i++;
            }
            $data['key'] = $key;
        }

        // .4 — sort_order = dernier de la catégorie + 1
        $data['sort_order'] = AbfRecommendation::where('category', $data['category'])
            ->max('sort_order') + 1;

        return $data;
    }
}
