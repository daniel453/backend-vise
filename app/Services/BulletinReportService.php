<?php

namespace App\Services;

use App\Models\Bulletin;
use App\Models\BulletinEvent;
use Illuminate\Support\Collection;

/**
 * Carga el boletín de un scope + sus eventos, hijos, breadcrumb y stats.
 * Compartido por la vista web, la generación de PDF y el envío por correo.
 */
class BulletinReportService
{
    /** Slug de URL (español) -> scope_level interno. */
    public const LEVELS = [
        'nacional' => 'national',
        'region' => 'region',
        'departamento' => 'department',
        'municipio' => 'municipality',
    ];

    public function viewData(string $level, ?string $scope): array
    {
        $scopeLevel = self::LEVELS[$level] ?? 'national';
        if ($scopeLevel === 'national') {
            $scope = 'NACIONAL';
        }

        $bulletin = Bulletin::query()
            ->where('scope_level', $scopeLevel)
            ->where('scope', $scope)
            // Preferir el boletín CON narrativa (headline). Así, en el nivel
            // región, el boletín del workflow de regionales (que sí trae
            // titular/conclusión) gana sobre el roll-up sin narrativa que el
            // workflow nacional genera de paso. Entre los que tienen narrativa,
            // el más reciente.
            ->orderByRaw("(headline IS NULL OR headline = '') asc")
            ->orderByDesc('generated_at')
            ->first();

        $events = new Collection;
        $children = new Collection;
        $childLevelSlug = null;
        $breadcrumb = [];

        if ($bulletin) {
            $batch = $bulletin->batch_id;

            $events = BulletinEvent::query()
                ->where('batch_id', $batch)
                ->when($scopeLevel === 'municipality', fn ($q) => $q->where('municipality', $scope))
                ->when($scopeLevel === 'department', fn ($q) => $q->where('department', $scope))
                ->when($scopeLevel === 'region', fn ($q) => $q->where('region', $scope))
                ->orderByRaw("CASE severity WHEN 'CRÍTICO' THEN 0 WHEN 'ALTO' THEN 1 WHEN 'MEDIO' THEN 2 ELSE 3 END")
                ->get();

            // Scopes hijos para el drill-down.
            [$childScopeLevel, $childLevelSlug, $childFilter] = match ($scopeLevel) {
                'national' => ['region', 'region', null],
                'region' => ['department', 'departamento', ['region', $scope]],
                'department' => ['municipality', 'municipio', ['department', $scope]],
                default => [null, null, null],
            };
            if ($childScopeLevel) {
                $children = Bulletin::query()
                    ->where('batch_id', $batch)->where('scope_level', $childScopeLevel)
                    ->when($childFilter, fn ($q) => $q->where($childFilter[0], $childFilter[1]))
                    ->orderByDesc('critical_events')->orderByDesc('total_events')->orderBy('scope')
                    ->get();
            }

            // Breadcrumb (de lo general a lo particular).
            $breadcrumb[] = ['label' => 'Nacional', 'level' => 'nacional', 'scope' => 'NACIONAL'];
            if ($bulletin->region && $scopeLevel !== 'national') {
                $breadcrumb[] = ['label' => $bulletin->region, 'level' => 'region', 'scope' => $bulletin->region];
            }
            if ($bulletin->department && in_array($scopeLevel, ['department', 'municipality'], true)) {
                $breadcrumb[] = ['label' => $bulletin->department, 'level' => 'departamento', 'scope' => $bulletin->department];
            }
            if ($scopeLevel === 'municipality') {
                $breadcrumb[] = ['label' => $bulletin->scope, 'level' => 'municipio', 'scope' => $bulletin->scope];
            }
        }

        $securityEvents = $events->whereIn('type', ['security', 'electoral'])->values();
        $environmental = $events->where('type', 'environmental')->values();
        $trafficTm = $events->where('type', 'traffic')->where('is_transmilenio', true)->values();
        $trafficOther = $events->where('type', 'traffic')->where('is_transmilenio', false)->values();

        $stats = [
            'events' => $bulletin?->total_events ?? $events->count(),
            'areas' => $scopeLevel === 'national'
                ? ($bulletin?->regions_affected ?? $events->pluck('region')->filter()->unique()->count())
                : $events->pluck('municipality')->filter()->unique()->count(),
            'roads' => $trafficTm->count() + $trafficOther->count(),
            'transmilenio' => $trafficTm->count(),
            'environmental' => $environmental->count(),
        ];

        // 'level' se usa en la vista para los enlaces de PDF.
        return compact(
            'bulletin', 'scopeLevel', 'scope', 'level', 'stats', 'breadcrumb', 'children', 'childLevelSlug',
            'securityEvents', 'environmental', 'trafficTm', 'trafficOther',
        );
    }
}
