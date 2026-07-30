<?php

namespace App\Repositories;

use App\Models\Bulletin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Acceso a datos de `bulletins`. TODA consulta a este modelo vive aquí (regla
 * Service + Repository): los controladores y servicios no tocan Eloquent directo.
 */
class BulletinRepository
{
    /**
     * Boletín de un scope: se prefiere el que trae narrativa (headline) y, entre
     * esos, el más reciente. Así el boletín regional (con titular) gana sobre el
     * roll-up sin narrativa que genera de paso el workflow nacional.
     */
    public function resolveForScope(string $scopeLevel, ?string $scope): ?Bulletin
    {
        return Bulletin::query()
            ->where('scope_level', $scopeLevel)
            ->where('scope', $scope)
            ->orderByRaw("(headline IS NULL OR headline = '') asc")
            ->orderByDesc('generated_at')
            ->first();
    }

    /** Scopes hijos de un boletín (para el drill-down), ordenados por criticidad. */
    public function childrenOf(string $batch, string $childScopeLevel, ?array $filter): Collection
    {
        return Bulletin::query()
            ->where('batch_id', $batch)->where('scope_level', $childScopeLevel)
            ->when($filter, fn ($q) => $q->where($filter[0], $filter[1]))
            ->orderByDesc('critical_events')->orderByDesc('total_events')->orderBy('scope')
            ->get();
    }

    /** batch_id de la última corrida generada. */
    public function latestBatchId(): ?string
    {
        return Bulletin::query()->latest('generated_at')->value('batch_id');
    }

    /** Boletín nacional de una corrida. */
    public function nationalOfBatch(?string $batch): ?Bulletin
    {
        return $batch
            ? Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'national')->first()
            : null;
    }

    /** Boletines regionales de una corrida, en orden alfabético (menú de cobertura). */
    public function regionsOfBatch(?string $batch): Collection
    {
        return $batch
            ? Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'region')->orderBy('scope')->get()
            : new Collection;
    }

    /** Listado paginado con filtros opcionales (API pública/tablero). */
    public function search(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $query = Bulletin::query()->latest('generated_at');
        foreach (['scope_level', 'scope', 'department', 'region'] as $field) {
            if (($filters[$field] ?? null) !== null && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }

        return $query->paginate($perPage);
    }
}
