<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Departaments extends Model
{
    /**
     * Regional VISE a la que pertenece este departamento.
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }
}
