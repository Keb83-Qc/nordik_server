<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicServiceTranslation extends Model
{
    protected $table = 'public_service_translations';

    protected $fillable = [
        'public_service_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(PublicService::class, 'public_service_id');
    }
}
