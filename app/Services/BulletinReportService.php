<?php

namespace App\Services;

use App\Repositories\BulletinEventRepository;
use App\Repositories\BulletinRepository;
use Illuminate\Support\Collection;

/**
 * Carga el boletín de un scope + sus eventos, hijos, breadcrumb y stats.
 * Compartido por la vista web, la generación de PDF y el envío por correo.
 * El acceso a datos va por los repositorios (regla Service + Repository).
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

    public function __construct(
        private readonly BulletinRepository $bulletins,
        private readonly BulletinEventRepository $events,
    ) {}

    public function viewData(string $level, ?string $scope): array
    {
        $scopeLevel = self::LEVELS[$level] ?? 'national';
        if ($scopeLevel === 'national') {
            $scope = 'NACIONAL';
        }

        $bulletin = $this->bulletins->resolveForScope($scopeLevel, $scope);

        $events = new Collection;
        $children = new Collection;
        $childLevelSlug = null;
        $breadcrumb = [];

        if ($bulletin) {
            $batch = $bulletin->batch_id;

            $events = $this->events->forScope($batch, $scopeLevel, $scope);

            // Scopes hijos para el drill-down.
            [$childScopeLevel, $childLevelSlug, $childFilter] = match ($scopeLevel) {
                'national' => ['region', 'region', null],
                'region' => ['department', 'departamento', ['region', $scope]],
                'department' => ['municipality', 'municipio', ['department', $scope]],
                default => [null, null, null],
            };
            if ($childScopeLevel) {
                $children = $this->bulletins->childrenOf($batch, $childScopeLevel, $childFilter);
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
