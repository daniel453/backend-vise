<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Regional extends Model
{
    /**
     * Departamentos que pertenecen a esta región natural de Colombia.
     */
    public function departaments(): HasMany
    {
        return $this->hasMany(Departaments::class);
    }
}
