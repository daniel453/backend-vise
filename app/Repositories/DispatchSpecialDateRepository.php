<?php

namespace App\Repositories;

use App\Models\DispatchSpecialDate;
use Illuminate\Database\Eloquent\Collection;

/** Acceso a datos de las fechas especiales (envío cada 2h en vez de diario). */
class DispatchSpecialDateRepository
{
    public function isSpecialOn(string $date): bool
    {
        return DispatchSpecialDate::query()->whereDate('date', $date)->exists();
    }

    public function allOrdered(): Collection
    {
        return DispatchSpecialDate::query()->orderBy('date')->get();
    }

    public function upsert(string $date, ?string $description): void
    {
        DispatchSpecialDate::query()->updateOrCreate(
            ['date' => $date],
            ['description' => $description],
        );
    }

    public function delete(DispatchSpecialDate $date): void
    {
        $date->delete();
    }
}
