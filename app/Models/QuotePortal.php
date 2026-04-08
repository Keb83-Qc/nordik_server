<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\Submission;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class QuotePortal extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'type', 'is_active', 'primary_color', 'secondary_color', 'advisor_code'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('quote_portal');
    }

    protected $fillable = [
        'slug',
        'name',
        'type',
        'advisor_code',
        'cc_email',
        'logo_path',
        'primary_color',
        'secondary_color',
        'consent_title',
        'consent_text',
        'is_active',
    ];

    protected $casts = [
        'consent_title' => 'array',
        'consent_text'  => 'array',
        'is_active'     => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    /**
     * Conseiller fixe associé à ce portail (null = rotation automatique).
     */
    public function advisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_code', 'advisor_code');
    }

    /**
     * Soumissions reçues via ce portail.
     */
    public function submissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Submission::class, 'portal_id');
    }

    public function quoteTypes(): BelongsToMany
    {
        return $this->belongsToMany(QuoteType::class, 'portal_quote_types', 'portal_id', 'quote_type_id')
                    ->withPivot(['sort_order', 'is_active'])
                    ->orderByPivot('sort_order');
    }

    /**
     * Uniquement les types de quotes actifs pour ce portail.
     */
    public function activeQuoteTypes(): BelongsToMany
    {
        return $this->quoteTypes()
                    ->wherePivot('is_active', true)
                    ->where('quote_types.is_active', true);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isInternal(): bool
    {
        return $this->type === 'internal';
    }

    public function isPartner(): bool
    {
        return $this->type === 'partner';
    }

    /**
     * Retourne le titre de consentement dans la langue courante.
     */
    public function getConsentTitle(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $title  = $this->consent_title ?? [];

        return $title[$locale] ?? $title['fr'] ?? $this->name;
    }

    /**
     * Retourne le texte de consentement dans la langue courante.
     */
    public function getConsentText(string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $text   = $this->consent_text ?? [];

        return $text[$locale] ?? $text['fr'] ?? '';
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopePartners($query)
    {
        return $query->where('type', 'partner');
    }
}
