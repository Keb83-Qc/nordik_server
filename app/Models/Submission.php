<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'advisor_code',
        'type',             // Conservé pour rétrocompatibilité
        'quote_type_id',    // Nouvelle FK vers quote_types
        'current_step',
        'data',
        'client_email',
        'client_phone',
        'is_phone_excluded',
    ];

    protected $casts = [
        'data'              => 'array',
        'is_phone_excluded' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function quoteType(): BelongsTo
    {
        return $this->belongsTo(QuoteType::class);
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
