<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['name', 'departament_id'])]
class City extends Model
{
    /**
     * Departamento al que pertenece esta ciudad/municipio.
     */
    public function departament(): BelongsTo
    {
        return $this->belongsTo(Departaments::class);
    }
}
