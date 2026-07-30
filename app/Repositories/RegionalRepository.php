<?php

namespace App\Repositories;

use App\Models\Regional;
use Illuminate\Database\Eloquent\Collection;

/** Acceso a datos de las regionales VISE y sus departamentos. */
class RegionalRepository
{
    /** Todas las regionales, en orden alfabético. */
    public function allOrdered(): Collection
    {
        return Regional::query()->orderBy('name')->get();
    }

    /**
     * Nombres de los departamentos de una regional (por nombre de regional),
     * ordenados alfabéticamente.
     *
     * @return array<int,string>
     */
    public function departmentNames(string $regionalName): array
    {
        return Regional::query()->where('name', $regionalName)->first()
            ?->departaments()->orderBy('name')->pluck('name')->all() ?? [];
    }
}
