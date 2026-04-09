<?php

namespace App\Observers;

use App\Http\Middleware\FullPageCache;
use App\Models\Language;
use Illuminate\Support\Facades\Cache;

/**
 * Observer sur MenuItem.
 *
 * Quand un item de menu est créé, modifié ou supprimé :
 *   1. Vide le cache HTML de toutes les pages publiques (FullPageCache JSON)
 *   2. Vide les clés Cache::remember() du menu dans tous les locales actives
 *
 * Sans cet observer, les changements de menu dans l'admin ne sont pas
 * visibles avant l'expiration naturelle du cache (30 min).
 */
class ClearMenuCacheObserver
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

        // 2. Vider les clés Cache::remember() du menu pour chaque locale
        foreach (Language::activeCodes() as $locale) {
            Cache::forget("menu_items_nav_{$locale}");
            Cache::forget("menu_services_{$locale}");
        }
    }
}
