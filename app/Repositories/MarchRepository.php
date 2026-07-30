<?php

namespace App\Repositories;

use App\Models\MarchBulletin;
use App\Models\MarchEvent;
use Illuminate\Database\Eloquent\Collection;

/** Acceso a datos del boletín temático de marchas (march_bulletins / march_events). */
class MarchRepository
{
    /** Último boletín de marchas generado. */
    public function latestBulletin(): ?MarchBulletin
    {
        return MarchBulletin::query()->latest('generated_at')->first();
    }

    /** Marchas de una corrida, ordenadas por ciudad y fecha. */
    public function eventsOfBatch(string $batch): Collection
    {
        return MarchEvent::query()
            ->where('batch_id', $batch)
            ->orderBy('city')->orderBy('event_date')
            ->get();
    }
}
