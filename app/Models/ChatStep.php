<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ChatStep extends Model
{
    protected static function booted(): void
    {
        $clear = fn (self $step) => Cache::forget("chat_steps_{$step->chat_type}");

        static::saved($clear);
        static::deleted($clear);
    }

    protected $fillable = [
        'identifier',
        'chat_type',
        'question',
        'input_type',
        'options',
        'sort_order',
        'is_active'
    ];

    // C'est ce bloc qui corrige l'erreur de conversion
    protected $casts = [
        'question'  => 'array',
        'options'   => 'array',
        'is_active' => 'boolean',
    ];
}
