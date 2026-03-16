<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $table = 'menu_items';

    public $translatable = ['label'];

    protected $fillable = [
        'key',
        'label',
        'path',
        'type',
        'target',
        'is_active',
        'sort_order',
        // accesseurs virtuels
        'label_fr', 'label_en', 'label_es', 'label_ht',
    ];

    protected $appends = ['label_fr', 'label_en', 'label_es', 'label_ht'];

    // ── Accesseurs traduction ──────────────────────────────────────────

    public function getLabelFrAttribute(): string
    {
        return $this->getTranslation('label', 'fr', false) ?? '';
    }
    public function setLabelFrAttribute(?string $value): void
    {
        $this->setTranslation('label', 'fr', $value ?? '');
    }

    public function getLabelEnAttribute(): string
    {
        return $this->getTranslation('label', 'en', false) ?? '';
    }
    public function setLabelEnAttribute(?string $value): void
    {
        $this->setTranslation('label', 'en', $value ?? '');
    }

    public function getLabelEsAttribute(): string
    {
        return $this->getTranslation('label', 'es', false) ?? '';
    }
    public function setLabelEsAttribute(?string $value): void
    {
        $this->setTranslation('label', 'es', $value ?? '');
    }

    public function getLabelHtAttribute(): string
    {
        return $this->getTranslation('label', 'ht', false) ?? '';
    }
    public function setLabelHtAttribute(?string $value): void
    {
        $this->setTranslation('label', 'ht', $value ?? '');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /** Retourne le label dans la locale courante, fallback fr */
    public function getLocalizedLabel(): string
    {
        $locale = app()->getLocale();
        return $this->getTranslation('label', $locale, false)
            ?: $this->getTranslation('label', 'fr', false)
            ?: $this->key;
    }

    /** Retourne l'URL complète avec préfixe locale */
    public function getLocalizedUrl(): string
    {
        $locale = app()->getLocale();

        if ($this->type === 'external') {
            return $this->path;
        }

        return '/' . $locale . '/' . ltrim($this->path, '/');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ── Auto-invalidation cache ───────────────────────────────────────

    protected static function booted(): void
    {
        $clear = function () {
            foreach (Language::activeCodes() as $locale) {
                \Illuminate\Support\Facades\Cache::forget("menu_items_nav_{$locale}");
            }
        };

        static::saved($clear);
        static::deleted($clear);
    }
}
