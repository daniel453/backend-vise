<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Ciudad monitoreada por el workflow de marchas. Administrable: el flujo n8n
 * lee las activas; agregar/quitar no toca el workflow.
 */
#[Fillable(['name', 'active', 'sort_order'])]
class MarchCity extends Model
{
    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }
}
