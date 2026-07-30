<?php

namespace App\Repositories;

use App\Models\ScrapingSource;
use Illuminate\Support\Collection;

/** Acceso a datos de la matriz de fuentes de scraping (scraping_sources). */
class ScrapingSourceRepository
{
    /** Fuentes (columnas públicas) en su orden original; opcionalmente de un grupo. */
    public function list(?string $group): Collection
    {
        $query = ScrapingSource::query()->orderBy('sort_order');
        if ($group !== null && $group !== '') {
            $query->where('group', $group);
        }

        return $query->get(['group', 'source', 'coverage', 'domain']);
    }

    /** Dominios del grupo "Medios nacionales" (whitelist del flujo n8n). */
    public function nationalMediaDomains(): Collection
    {
        return ScrapingSource::query()
            ->where('group', 'Medios nacionales')
            ->whereNotNull('domain')
            ->orderBy('sort_order')
            ->pluck('domain');
    }
}
