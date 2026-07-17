<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'batch_id', 'scope_level', 'scope', 'department', 'region', 'mode', 'headline', 'total_events',
    'critical_events', 'high_impact_events', 'regions_affected', 'roads_affected',
    'electoral_events', 'main_threat', 'critical_zone', 'trend', 'sources_consulted',
    'electoral_context', 'logistics_recommendation', 'perimeter_recommendation',
    'operational_recommendation', 'digital_recommendation', 'heat_map', 'distribution', 'generated_at',
])]
class Bulletin extends Model
{
    protected function casts(): array
    {
        return [
            'heat_map' => 'array',
            'distribution' => 'array',
            'generated_at' => 'datetime',
        ];
    }
}
