<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteType extends Model
{
    protected $fillable = [
        'slug',
        'label',
        'icon',
        'color',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'label'     => 'array',
        'is_active' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function chatSteps(): HasMany
    {
        return $this->hasMany(ChatStep::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function portals(): BelongsToMany
    {
        return $this->belongsToMany(QuotePortal::class, 'portal_quote_types', 'quote_type_id', 'portal_id')
                    ->withPivot(['sort_order', 'is_active'])
                    ->orderByPivot('sort_order');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retourne le libellé dans la langue courante (ou français par défaut).
     */
    public function getLabel(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $label  = $this->label;

        return $label[$locale] ?? $label['fr'] ?? $this->slug;
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
