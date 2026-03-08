<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    protected $fillable = ['code', 'name', 'is_active', 'is_default', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'sort_order' => 'integer',
    ];

    public static function activeCodes(): array
    {
        return Cache::remember('languages.active_codes', 3600, function () {
            return self::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->values()
                ->all();
        });
    }

    public static function defaultCode(): string
    {
        return Cache::remember('languages.default_code', 3600, function () {
            return self::query()
                ->where('is_default', true)
                ->value('code')
                ?: 'fr';
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('languages.active_codes');
        Cache::forget('languages.default_code');
    }

    protected static function booted()
    {
        static::saved(fn() => self::clearCache());
        static::deleted(fn() => self::clearCache());
    }
}
