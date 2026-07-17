<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departaments extends Model
{
    /**
     * Ciudades/municipios que pertenecen a este departamento.
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * Regional VISE a la que pertenece este departamento.
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }
}
