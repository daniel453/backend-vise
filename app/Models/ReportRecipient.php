<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['email', 'name', 'regional_id', 'active', 'test'])]
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
     * Regional VISE a la que pertenece el destinatario (nulo = nacional, recibe
     * el panorama completo). Determina qué página regional se anexa a su PDF.
     */
    public function regional(): BelongsTo
    {
        return $this->belongsTo(Regional::class);
    }
}
