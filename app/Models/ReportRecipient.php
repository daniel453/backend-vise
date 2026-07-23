<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['email', 'name', 'active', 'test'])]
class ReportRecipient extends Model
{
    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'test' => 'boolean',
        ];
    }

    /**
     * Regionales VISE a las que pertenece el destinatario. Puede estar en varias.
     * Sin regionales = destinatario nacional (recibe el panorama completo). Determina
     * qué página(s) regional(es) se anexan a su PDF.
     */
    public function regionals(): BelongsToMany
    {
        return $this->belongsToMany(Regional::class);
    }
}
