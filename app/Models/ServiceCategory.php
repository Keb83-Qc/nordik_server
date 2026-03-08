<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    protected $fillable = ['code', 'is_active', 'sort_order'];

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceCategoryTranslation::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function translation(?string $locale = null): ?ServiceCategoryTranslation
    {
        $locale = $locale ?: app()->getLocale();
        return $this->translations->firstWhere('locale', $locale)
            ?? $this->translations->firstWhere('locale', config('app.fallback_locale', 'fr'))
            ?? $this->translations->first();
    }
}
