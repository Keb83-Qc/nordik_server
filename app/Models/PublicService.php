<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicService extends Model
{
    protected $table = 'public_services';

    protected $fillable = [
        'public_service_category_id',
        'code',
        'is_active',
        'sort_order',
    ];

    public function translations()
    {
        return $this->hasMany(PublicServiceTranslation::class, 'public_service_id');
    }

    public function category()
    {
        return $this->belongsTo(PublicServiceCategory::class, 'public_service_category_id');
    }
}
