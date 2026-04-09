<?php

namespace App\Models;

use App\Casts\EncryptedJsonCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbfIntake extends Model
{
    protected $fillable = [
        'advisor_user_id',
        'token',
        'access_code',
        'client_first_name',
        'client_last_name',
        'client_email',
        'locale',
        'status',
        'abf_case_id',
        'payload',
        'expires_at',
    ];

    protected $casts = [
        'payload'    => EncryptedJsonCast::class,
        'expires_at' => 'datetime',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'advisor_user_id');
    }

    public function abfCase(): BelongsTo
    {
        return $this->belongsTo(AbfCase::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function getUrlAttribute(): string
    {
        return route('intake.show', [
            'advisorSlug' => $this->advisor->slug,
            'token'       => $this->token,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function clientFullName(): string
    {
        return trim(($this->client_first_name ?? '') . ' ' . ($this->client_last_name ?? ''));
    }
}
