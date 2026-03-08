<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicServiceCategoryTranslation extends Model
{
    protected $table = 'public_service_category_translations';

    protected $fillable = [
        'public_service_category_id',
        'locale',
        'name',
        'slug',
    ];

    public function category()
    {
        return $this->belongsTo(PublicServiceCategory::class, 'public_service_category_id');
    }
}
