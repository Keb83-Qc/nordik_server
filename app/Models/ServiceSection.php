<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ServiceSection extends Model
{
    use HasTranslations;

    protected $fillable = [
        'service_id',
        'type',
        'data',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'data' => 'array',
        'is_active' => 'boolean',
    ];

    // Champs de data qu'on veut traduire (si on stocke des clés texte ici)
    // Exemple: title, subtitle, body...
    public $translatable = [
        // si tu veux rendre certains keys translatable directement,
        // sinon on gère ça côté view via $data['title'][locale]
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
