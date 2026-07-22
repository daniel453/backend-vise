<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Una marcha/movilización para el boletín temático de marchas.
 */
#[Fillable([
    'batch_id', 'city', 'title', 'event_date', 'event_time', 'convener',
    'concentration_point', 'route', 'affected_roads', 'level', 'summary',
    'media_outlet', 'source_url', 'details',
])]
class MarchEvent extends Model
{
    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'details' => 'array',
        ];
    }
}
