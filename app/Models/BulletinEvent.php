<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'batch_id', 'type', 'severity', 'subtype', 'is_transmilenio', 'region', 'department', 'municipality',
    'title', 'summary', 'media_outlet', 'source_url', 'published_at', 'details',
])]
class BulletinEvent extends Model
{
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_transmilenio' => 'boolean',
            'details' => 'array',
        ];
    }
}
