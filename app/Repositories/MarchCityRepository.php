<?php

namespace App\Repositories;

use App\Models\MarchCity;
use Illuminate\Database\Eloquent\Collection;

/** Acceso a datos de las ciudades monitoreadas del boletín de marchas. */
class MarchCityRepository
{
    public function allOrdered(): Collection
    {
        return MarchCity::query()
            ->orderByDesc('active')->orderBy('sort_order')->orderBy('name')
            ->get();
    }

    public function upsertByName(string $name): void
    {
        MarchCity::query()->updateOrCreate(
            ['name' => trim($name)],
            ['active' => true, 'sort_order' => (int) MarchCity::query()->max('sort_order') + 1],
        );
    }

    public function toggleActive(MarchCity $city): void
    {
        $city->update(['active' => ! $city->active]);
    }

    public function delete(MarchCity $city): void
    {
        $city->delete();
    }
}
