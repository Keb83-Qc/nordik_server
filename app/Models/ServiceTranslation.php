<?

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceTranslation extends Model
{
    protected $fillable = ['service_id', 'locale', 'title', 'slug', 'excerpt', 'content'];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
