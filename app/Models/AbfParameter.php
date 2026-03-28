<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbfParameter extends Model
{
    protected $fillable = [
        'group',
        'key',
        'label',
        'type',
        'value',
        'options',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Retourne tous les paramètres sous forme de tableau imbriqué :
     * [ 'groupe' => [ 'cle' => 'valeur', ... ], ... ]
     */
    public static function allAsMap(): array
    {
        return static::orderBy('group')->orderBy('sort_order')->get()
            ->groupBy('group')
            ->map(fn ($items) => $items->pluck('value', 'key'))
            ->toArray();
    }

    /**
     * Récupère une valeur unique par groupe + clé.
     */
    public static function getValue(string $group, string $key, mixed $default = null): mixed
    {
        return static::where('group', $group)->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Met à jour ou crée un paramètre.
     */
    public static function setValue(string $group, string $key, mixed $value): void
    {
        static::where('group', $group)->where('key', $key)->update(['value' => $value]);
    }
}
