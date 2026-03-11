<?php

namespace App\Observers;

use App\Http\Middleware\FullPageCache;

/**
 * Observer générique qui invalide le cache HTML de page (FullPageCache)
 * dès qu'un modèle est sauvegardé ou supprimé.
 *
 * À enregistrer dans AppServiceProvider::boot() sur les modèles dont
 * le contenu apparaît sur des pages publiques mises en cache :
 *   BlogPost, Slide, HomepageStat, Service, Partner, Employee, etc.
 */
class ClearPageCacheObserver
{
    public function saved(mixed $model): void
    {
        FullPageCache::clearAll();
    }

    public function deleted(mixed $model): void
    {
        FullPageCache::clearAll();
    }
}
