<?php

namespace App\Filament\Resources\PartnerResource\Pages;

use App\Filament\Resources\PartnerResource;
use App\Filament\Pages\BaseEditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditPartner extends BaseEditRecord
{
    protected static string $resource = PartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // CETTE FONCTION S'ACTIVE JUSTE AVANT LA SAUVEGARDE D'UNE MODIFICATION
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si on a un logo et un nom
        if (isset($data['logo']) && isset($data['name'])) {
            $currentPath = $data['logo'];
            $nameSlug = Str::slug($data['name']);

            // Si le fichier ne contient pas déjà le bon nom (ex: on a changé le nom du partenaire)
            if (!str_contains($currentPath, $nameSlug)) {
                $extension = pathinfo($currentPath, PATHINFO_EXTENSION);
                $newFilename = $nameSlug . '-' . time() . '.' . $extension;
                $newPath = 'partners/' . $newFilename;

                if (Storage::disk('public')->exists($currentPath)) {
                    Storage::disk('public')->move($currentPath, $newPath);
                    $data['logo'] = $newPath; // On met à jour le chemin dans les données à sauvegarder
                }
            }
        }

        return $data;
    }
}
