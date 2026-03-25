<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatStep extends Model
{
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
