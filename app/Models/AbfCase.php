<?php

namespace App\Models;

use App\Services\AbfCaseCalculator;
use Illuminate\Database\Eloquent\Model;

class AbfCase extends Model
{
    protected $table = 'abf_cases';

    protected $fillable = [
        'advisor_user_id',
        'advisor_code',
        'status',
        'payload',
        'results',
        'completed_at',
        'signed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'results' => 'array',
        'completed_at' => 'datetime',
        'signed_at' => 'datetime',
    ];

    public function getProgressPercentAttribute(): ?int
    {
        $p = $this->results['progress']['percent'] ?? null;
        return $p === null ? null : (int) $p;
    }


    public function advisor()
    {
        return $this->belongsTo(\App\Models\User::class, 'advisor_user_id');
    }
}
