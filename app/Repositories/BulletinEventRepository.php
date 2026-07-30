<?php

namespace App\Repositories;

use App\Models\BulletinEvent;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Acceso a datos de `bulletin_events`. Regla Service + Repository: nadie más
 * consulta este modelo directo.
 */
class BulletinEventRepository
{
    /** Orden por severidad (CRÍTICO → ALTO → MEDIO → resto). */
    private const SEVERITY_ORDER = "CASE severity WHEN 'CRÍTICO' THEN 0 WHEN 'ALTO' THEN 1 WHEN 'MEDIO' THEN 2 ELSE 3 END";

    /** Eventos de una corrida acotados al scope (municipio/departamento/región). */
    public function forScope(string $batch, string $scopeLevel, ?string $scope): Collection
    {
        return BulletinEvent::query()
            ->where('batch_id', $batch)
            ->when($scopeLevel === 'municipality', fn ($q) => $q->where('municipality', $scope))
            ->when($scopeLevel === 'department', fn ($q) => $q->where('department', $scope))
            ->when($scopeLevel === 'region', fn ($q) => $q->where('region', $scope))
            ->orderByRaw(self::SEVERITY_ORDER)
            ->get();
    }

    /** Listado paginado con filtros opcionales (API). */
    public function search(array $filters, int $perPage = 50): LengthAwarePaginator
    {
        $query = BulletinEvent::query()->latest('published_at');

        if (($filters['batch_id'] ?? null) !== null && $filters['batch_id'] !== '') {
            $query->where('batch_id', $filters['batch_id']);
        }
        foreach (['region', 'department', 'municipality', 'type'] as $field) {
            if (($filters[$field] ?? null) !== null && $filters[$field] !== '') {
                $query->where($field, $filters[$field]);
            }
        }
        if (array_key_exists('is_transmilenio', $filters) && $filters['is_transmilenio'] !== null) {
            $query->where('is_transmilenio', (bool) $filters['is_transmilenio']);
        }

        return $query->paginate($perPage);
    }
}
