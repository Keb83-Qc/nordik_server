<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicServiceCategory extends Model
{
    protected $table = 'public_service_categories';

    protected $fillable = [
        'code',
        'is_active',
        'sort_order',
    ];

    public function translations()
    {
        return $this->hasMany(PublicServiceCategoryTranslation::class, 'public_service_category_id');
    }

    public function services()
    {
        return $this->hasMany(PublicService::class, 'public_service_category_id');
    }
}
