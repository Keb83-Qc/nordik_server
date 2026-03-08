<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use App\Models\Language;

class BlogPost extends Model
{
    use HasFactory;
    use HasTranslations;

    public function resolveRouteBinding($value, $field = null)
    {
        // Admin / Livewire : binder par ID pour éviter de chercher dans le JSON
        if (is_numeric($value) && (request()->is('admin/*') || request()->is('livewire/*'))) {
            return $this->newQuery()->whereKey($value)->firstOrFail();
        }

        $locale = app()->getLocale();
        $fallback = Language::defaultCode() ?? config('app.fallback_locale', 'fr');

        return $this->newQuery()
            ->where("slug->{$locale}", $value)
            ->orWhere("slug->{$fallback}", $value)
            ->firstOrFail();
    }

    public $translatable = ['title', 'slug', 'content', 'category'];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'category',
        'author',
        'created_at',

        // champs virtuels (Filament)
        'title_fr',
        'title_en',
        'content_fr',
        'content_en',
        'category_fr',
        'slug_fr',
        'slug_en',
    ];

    protected $appends = [
        'title_fr',
        'title_en',
        'content_fr',
        'content_en',
        'category_fr',
        'slug_fr',
        'slug_en',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'title' => 'array',
        'slug' => 'array',
        'content' => 'array',
        'category' => 'array',
        'image' => 'string',
    ];

    // --- LOGIQUE AUTOMATIQUE POUR LES CATÉGORIES ---
    const CATEGORY_MAPPING = [
        'Actualités' => 'News',
        'Assurance'  => 'Insurance',
        'Épargne'    => 'Savings',
        'Prêt & Hypothèque' => 'Mortgage',
        'Hypothèque' => 'Mortgage',
        'Conseils Financiers' => 'Financial Advice',
        'Vie de l\'entreprise' => 'Business Life',
    ];

    protected static function booted(): void
    {
        // 1) Normalise slugs + auto-génère si manquant (avant save)
        static::saving(function (BlogPost $post) {
            $locales = \App\Models\Language::activeCodes();
            $fallback = \App\Models\Language::defaultCode() ?? config('app.fallback_locale', 'fr');

            // Assure un slug par locale active (ou fallback si titre manquant)
            foreach ($locales as $locale) {
                $title = $post->getTranslation('title', $locale, false)
                    ?: $post->getTranslation('title', $fallback, false);

                $currentSlug = $post->getTranslation('slug', $locale, false);

                // Si slug vide → générer depuis le titre
                if (blank($currentSlug) && filled($title)) {
                    $currentSlug = self::makeSeoSlug($title, $locale);
                } else {
                    // Sinon → normaliser le slug existant (accents, ponctuation, trim)
                    $currentSlug = self::makeSeoSlug((string) $currentSlug, $locale);
                }

                // Unicité par locale
                if (filled($currentSlug)) {
                    $currentSlug = self::ensureUniqueSlug($post, $locale, $currentSlug);
                    $post->setTranslation('slug', $locale, $currentSlug);
                }
            }
        });

        // 2) Si slug FR change et image interne inchangée → rename image (ton code existant, conservé)
        static::updating(function (BlogPost $post) {
            if ($post->isDirty('slug') && !$post->isDirty('image') && !empty($post->image)) {

                if (str_starts_with($post->image, 'http') || str_starts_with($post->image, 'assets')) {
                    return;
                }

                $newSlug = $post->getTranslation('slug', 'fr');
                $disk = Storage::disk('public');

                if ($disk->exists($post->image)) {
                    $extension = pathinfo($post->image, PATHINFO_EXTENSION);
                    $newName = 'blog/' . $newSlug . '-' . time() . '.' . $extension;

                    if ($disk->move($post->image, $newName)) {
                        $post->image = $newName;
                    }
                }
            }
        });
    }

    /**
     * Slug SEO robuste :
     * - enlève accents (ascii)
     * - slugify Laravel (lang aware)
     * - trim des tirets
     * - fallback si vide
     */
    public static function makeSeoSlug(string $value, string $locale): string
    {
        $value = trim($value);

        // évite les slugs générés à partir de HTML / titres très sales
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        // ascii + slug
        $slug = Str::slug(Str::ascii($value), '-', $locale);

        // important: retire les tirets en bout (ton problème principal)
        $slug = trim($slug, '-');

        return $slug;
    }

    /**
     * Assure unicité par locale (car index unique JSON MySQL = compliqué).
     * Ajoute -2, -3, etc. si collision.
     */
    public static function ensureUniqueSlug(BlogPost $post, string $locale, string $base): string
    {
        $base = trim($base, '-');
        if ($base === '') {
            return $base;
        }

        $candidate = $base;
        $i = 2;

        while (
            BlogPost::query()
            ->where("slug->{$locale}", $candidate)
            ->when($post->exists, fn($q) => $q->where('id', '!=', $post->id))
            ->exists()
        ) {
            $candidate = "{$base}-{$i}";
            $i++;
            if ($i > 200) {
                // garde-fou extrême
                $candidate = "{$base}-{$post->id}";
                break;
            }
        }

        return $candidate;
    }

    // --- Accesseurs / mutateurs existants (conservés) ---

    public function getCategoryFrAttribute()
    {
        return $this->getTranslation('category', 'fr', false);
    }

    public function setCategoryFrAttribute($value)
    {
        $this->setTranslation('category', 'fr', $value);

        if (isset(self::CATEGORY_MAPPING[$value])) {
            $this->setTranslation('category', 'en', self::CATEGORY_MAPPING[$value]);
        } else {
            $this->setTranslation('category', 'en', $value);
        }
    }

    public function getTitleFrAttribute()
    {
        return $this->getTranslation('title', 'fr', false);
    }
    public function setTitleFrAttribute($value)
    {
        $this->setTranslation('title', 'fr', $value);
    }

    public function getContentFrAttribute()
    {
        return $this->getTranslation('content', 'fr', false);
    }
    public function setContentFrAttribute($value)
    {
        $this->setTranslation('content', 'fr', $value);
    }

    public function getTitleEnAttribute()
    {
        return $this->getTranslation('title', 'en', false);
    }
    public function setTitleEnAttribute($value)
    {
        $this->setTranslation('title', 'en', $value);
    }

    public function getContentEnAttribute()
    {
        return $this->getTranslation('content', 'en', false);
    }
    public function setContentEnAttribute($value)
    {
        $this->setTranslation('content', 'en', $value);
    }

    public function getSlugFrAttribute()
    {
        return $this->getTranslation('slug', 'fr', false);
    }
    public function setSlugFrAttribute($value)
    {
        $this->setTranslation('slug', 'fr', $value);
    }

    public function getSlugEnAttribute()
    {
        return $this->getTranslation('slug', 'en', false);
    }
    public function setSlugEnAttribute($value)
    {
        $this->setTranslation('slug', 'en', $value);
    }

    public function getImageUrlAttribute()
    {
        $path = $this->image;

        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        if (str_starts_with($path, 'assets/')) return asset($path);

        return asset('storage/' . $path);
    }

    // public function getRouteKeyName()
    // {
    //     if (request()->is('admin/*') || request()->is('livewire/*')) {
    //         return 'id';
    //     }
    //     return 'slug';
    // }
}
