<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'date', 'city', 'address', 'responsible_party',
    'start_time', 'end_time',
    'gps_lat', 'gps_lng', 'gps_accuracy_m', 'gps_distance_m',
    'general_notes', 'conclusions', 'status',
])]
class Assessment extends Model
{
    /**
     * Conversión de tipos de columnas (fecha y coordenadas GPS).
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
        ];
    }

    /**
     * Usuario (evaluador) que realizó esta evaluación.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Ítems (respuestas por pregunta) de esta evaluación.
     */
    public function items(): HasMany
    {
        return $this->hasMany(AssessmentItem::class);
    }

    /**
     * Fotos adjuntas a esta evaluación.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(AssessmentPhoto::class);
    }
}
