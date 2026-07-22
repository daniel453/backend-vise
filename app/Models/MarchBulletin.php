<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Encabezado del boletín temático de marchas. Sus marchas son los MarchEvent
 * con el mismo batch_id.
 */
#[Fillable([
    'batch_id', 'headline', 'conclusion', 'total_marches',
    'cities_affected', 'recommendation', 'generated_at',
])]
class MarchBulletin extends Model
{
    protected function casts(): array
    {
        return ['generated_at' => 'datetime'];
    }

    public function events(): HasMany
    {
        return $this->hasMany(MarchEvent::class, 'batch_id', 'batch_id');
    }
}
