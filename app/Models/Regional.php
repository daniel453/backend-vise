<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name'])]
class Regional extends Model
{
    /**
     * Departamentos que pertenecen a esta regional VISE.
     */
    public function departaments(): HasMany
    {
        return $this->hasMany(Departaments::class);
    }

    /**
     * Destinatarios asignados a esta regional (un destinatario puede estar en varias).
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(ReportRecipient::class);
    }
}
