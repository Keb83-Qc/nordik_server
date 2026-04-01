<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class AbfRecommendation extends Model
{
    protected $fillable = [
        'category',
        'key',
        'title',
        'text',
        'sort_order',
        'is_active',
        'checked_by_default',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'checked_by_default'  => 'boolean',
        'sort_order'          => 'integer',
    ];

    /**
     * Retourne les recommandations actives groupées par catégorie,
     * formatées pour injection JS (window.ABF_RECOM_DEFAULTS).
     */
    public static function forJs(): array
    {
        $grouped = [];
        $cats = ['deces', 'invalidite', 'maladie-grave', 'fonds-urgence', 'retraite', 'conseils'];
        foreach ($cats as $cat) {
            $grouped[$cat] = [];
        }

        static::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->each(function (self $r) use (&$grouped) {
                $grouped[$r->category][] = [
                    'id'                 => $r->id,
                    'key'                => $r->key,
                    'title'              => $r->title,
                    'text'               => $r->text,
                    'checked_by_default' => $r->checked_by_default,
                ];
            });

        return $grouped;
    }

    /**
     * Retourne les options de dropdown par catégorie pour la blade.
     * Format : [ 'key' => '...', 'label' => '...' ]
     */
    public static function dropdownOptions(): array
    {
        $options = [];
        $cats = ['deces', 'invalidite', 'maladie-grave', 'fonds-urgence', 'retraite', 'conseils'];
        foreach ($cats as $cat) {
            $opts = [['key' => 'personalized', 'label' => 'Recommandation personnalisée']];
            static::where('category', $cat)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['key', 'title'])
                ->each(function (self $r) use (&$opts) {
                    if ($r->key && $r->key !== 'personalized') {
                        $opts[] = ['key' => $r->key, 'label' => $r->title ?? $r->key];
                    }
                });
            $options[$cat] = $opts;
        }
        return $options;
    }
}
