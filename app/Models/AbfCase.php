<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class AbfCase extends Model
{
    use LogsActivity;

    protected $table = 'abf_cases';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['client_first_name', 'client_last_name', 'status', 'advisor_user_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('abf_case');
    }

    protected $fillable = [
        'advisor_user_id',
        'advisor_code',
        'client_first_name',
        'client_last_name',
        'client_birth_date',
        'slug',
        'status',
        'payload',
        'results',
        'completed_at',
        'signed_at',
    ];

    protected $casts = [
        'payload'          => 'encrypted:array',
        'results'          => 'array',
        'client_birth_date'=> 'date',
        'completed_at'     => 'datetime',
        'signed_at'        => 'datetime',
    ];

    // ── Route model binding ───────────────────────────────────────────────
    // Accepte deux formats : "nouveau-{id}" ou "{slug}"

    public function getRouteKeyName(): string
    {
        return 'slug_or_id';  // fictif — on override resolveRouteBinding
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        // Format "nouveau-{id}" → résolution par ID
        if (preg_match('/^nouveau-(\d+)$/', $value, $m)) {
            return static::where('id', $m[1])->firstOrFail();
        }

        // Sinon résolution par slug (scopé à l'advisor connecté)
        return static::where('slug', $value)
            ->where('advisor_user_id', auth()->id())
            ->firstOrFail();
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    public function getProgressPercentAttribute(): ?int
    {
        $p = $this->results['progress']['percent'] ?? null;
        return $p === null ? null : (int) $p;
    }

    /**
     * URL propre : /{advisorSlug}/liste-bilan/{record}
     */
    public function getEditorUrlAttribute(): string
    {
        if (! $this->relationLoaded('advisor') && $this->advisor_user_id) {
            $this->load('advisor');
        }
        $advisorSlug = $this->advisor?->slug ?? auth()->user()?->slug ?? 'conseiller';
        $identifier  = $this->slug ?: 'nouveau-' . $this->id;
        return route('abf.editor.show', ['advisorSlug' => $advisorSlug, 'record' => $identifier]);
    }

    /**
     * Génère et persiste le slug à partir du nom/prénom du client.
     * Appelé après sauvegarde du payload step 1.
     */
    public function generateSlug(): void
    {
        if (! $this->client_last_name && ! $this->client_first_name) return;

        $base = Str::slug(
            strtolower($this->client_last_name ?? '') . '-' . strtolower($this->client_first_name ?? ''),
            '-',
            'fr'
        );

        if (! $base) return;

        // Garantir l'unicité par conseiller
        $slug = $base;
        $i    = 2;
        while (
            static::where('advisor_user_id', $this->advisor_user_id)
                ->where('slug', $slug)
                ->where('id', '!=', $this->id)
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        $this->updateQuietly(['slug' => $slug]);
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function advisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'advisor_user_id');
    }
}
