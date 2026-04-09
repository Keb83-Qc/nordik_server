<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\QuotePortal;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'advisor_code',
        'type',             // Conservé pour rétrocompatibilité
        'quote_type_id',    // Nouvelle FK vers quote_types
        'portal_id',        // Portail d'origine (null = accès direct)
        'current_step',
        'data',
        'client_email',
        'client_phone',
        'is_phone_excluded',
    ];

    protected $casts = [
        // data: 'array' — le chiffrement nécessite une migration de colonne json→longtext
        // avant de pouvoir activer EncryptedJsonCast ici.
        'data'              => 'array',
        'is_phone_excluded' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function quoteType(): BelongsTo
    {
        return $this->belongsTo(QuoteType::class);
    }

    public function portal(): BelongsTo
    {
        return $this->belongsTo(QuotePortal::class, 'portal_id');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retourne le slug du type de quote (depuis la relation ou le champ legacy).
     */
    public function getTypeSlug(): string
    {
        return $this->quoteType?->slug ?? $this->type ?? '';
    }
}
