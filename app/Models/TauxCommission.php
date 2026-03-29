<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TauxCommission extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['company', 'type_placement', 'option_nom', 'taux_mensuel', 'taux_initial'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('taux_commission');
    }

    // IMPORTANT : On force le nom de la table tel qu'il est dans votre SQL
    protected $table = 'taux_commissions';

    // On désactive les timestamps si votre table n'a pas created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'company',
        'type_placement',
        'option_nom',
        'taux_mensuel',
        'taux_initial'
    ];

    protected $casts = [
        'taux_mensuel' => 'float',
        'taux_initial' => 'float',
    ];
}
