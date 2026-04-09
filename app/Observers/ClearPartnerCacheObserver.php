<?php

namespace App\Observers;

use App\Http\Middleware\FullPageCache;
use Illuminate\Support\Facades\Cache;

/**
 * Observer sur Partner.
 *
 * Quand un partenaire est créé, modifié ou supprimé :
 *   1. Vide le cache HTML de toutes les pages publiques (FullPageCache JSON)
 *   2. Oublie la clé Cache::remember('partners_visible') (TTL 1h)
 *
 * Sans ce fix, les nouvelles images de partenaires ne sont pas visibles
 * avant l'expiration naturelle du cache applicatif (1 heure).
 */
class ClearPartnerCacheObserver
{
    public function saved(mixed $model): void
    {
        $this->clearAll();
    }

    public function deleted(mixed $model): void
    {
        $this->clearAll();
    }

    private function clearAll(): void
    {
        // 1. Vider le cache HTML de pages (JSON dans storage/page-cache/)
        FullPageCache::clearAll();

        // 2. Oublier le cache applicatif des partenaires
        Cache::forget('partners_visible');
    }
}
