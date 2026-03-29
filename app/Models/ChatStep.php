<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class ChatStep extends Model
{
    protected static function booted(): void
    {
        $clear = function (self $step) {
            // Invalide le cache par slug (nouvelle clé) ET par chat_type legacy
            if ($step->quoteType) {
                Cache::forget("chat_steps_{$step->quoteType->slug}");
            }
            // Rétrocompatibilité avec l'ancienne clé cache
            if ($step->chat_type) {
                Cache::forget("chat_steps_{$step->chat_type}");
            }
        };

        static::saved($clear);
        static::deleted($clear);
    }

    protected $fillable = [
        'identifier',
        'chat_type',        // Conservé pour rétrocompatibilité
        'quote_type_id',    // Nouvelle FK vers quote_types
        'question',
        'input_type',
        'options',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'question'  => 'array',
        'options'   => 'array',
        'is_active' => 'boolean',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function quoteType(): BelongsTo
    {
        return $this->belongsTo(QuoteType::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retourne la question dans la langue courante (ou français par défaut).
     */
    public function getQuestion(string $locale = null): string
    {
        $locale   = $locale ?? app()->getLocale();
        $question = $this->question;

        if (is_array($question)) {
            return $question[$locale] ?? $question['fr'] ?? '';
        }

        return (string) $question;
    }

    /**
     * Retourne le slug du type de quote (depuis la relation ou le champ legacy).
     */
    public function getTypeSlug(): string
    {
        return $this->quoteType?->slug ?? $this->chat_type ?? '';
    }
}
