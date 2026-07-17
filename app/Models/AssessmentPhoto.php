<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['assessment_id', 'item_id', 'path', 'sort_order', 'gps_lat', 'gps_lng', 'gps_distance_m'])]
class AssessmentPhoto extends Model
{
    /**
     * Conversión de tipos de las coordenadas GPS.
     */
    protected function casts(): array
    {
        return [
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
        ];
    }

    /**
     * Evaluación a la que pertenece esta foto.
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * URL pública de la foto, calculada a partir de la ruta guardada.
     */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => asset('storage/'.$this->path),
        );
    }
}
