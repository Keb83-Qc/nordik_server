<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    public $translatable = [
        'title',
        'description',
        'slug',
        'content',
        'seo_title',
        'seo_description',
    ];

    protected $fillable = [
        'category_id',
        'title',
        'description',
        'slug',
        'link',
        'image',
        'sort_order',
        'type',
        'template',
        'content',
        'seo_title',
        'seo_description',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'title'           => 'array',
        'description'     => 'array',
        'slug'            => 'array',
        'content'         => 'array',
        'seo_title'       => 'array',
        'seo_description' => 'array',
        'is_published'    => 'boolean',
        'published_at'    => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) return null;
        if (str_starts_with($this->image, 'http')) return $this->image;
        if (str_starts_with($this->image, 'assets/')) return asset($this->image);
        return asset('storage/' . ltrim($this->image, '/'));
    }
}
