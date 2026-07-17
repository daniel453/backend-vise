<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\BulletinEvent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulletinPageController extends Controller
{
    /** Slug de URL (español) -> scope_level interno. */
    private const LEVELS = [
        'nacional' => 'national',
        'region' => 'region',
        'departamento' => 'department',
        'municipio' => 'municipality',
    ];

    /** scope_level interno -> slug de URL. */
    private function slug(string $scopeLevel): string
    {
        return array_flip(self::LEVELS)[$scopeLevel] ?? 'nacional';
    }

    /**
     * Home público: directorio de entrada. Muestra el boletín nacional
     * (resumen), las regiones como tarjetas y una búsqueda por lugar.
     */
    public function home(): View
    {
        $batch = Bulletin::query()->latest('generated_at')->value('batch_id');

        $national = Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'national')->first();

        $regions = Bulletin::query()
            ->where('batch_id', $batch)->where('scope_level', 'region')
            ->orderByDesc('critical_events')->orderByDesc('total_events')->orderBy('scope')
            ->get();

        // Lugares para el autocompletado de búsqueda (departamentos + municipios con boletín).
        $places = Bulletin::query()
            ->where('batch_id', $batch)
            ->whereIn('scope_level', ['department', 'municipality'])
            ->orderBy('scope')
            ->get(['scope_level', 'scope'])
            ->unique('scope')
            ->values();

        $updatedAt = $national?->generated_at;

        return view('boletines.home', compact('national', 'regions', 'places', 'updatedAt'));
    }

    /**
     * Busca un lugar escrito por el usuario y redirige a su boletín (prefiere
     * municipio sobre departamento). Si no hay, vuelve al home.
     */
    public function search(Request $request): RedirectResponse
    {
        $q = trim((string) $request->query('q'));
        if ($q === '') {
            return redirect()->route('home');
        }

        $batch = Bulletin::query()->latest('generated_at')->value('batch_id');
        $match = Bulletin::query()
            ->where('batch_id', $batch)
            ->whereIn('scope_level', ['municipality', 'department', 'region'])
            ->whereRaw('LOWER(scope) = ?', [mb_strtolower($q)])
            ->orderByRaw("CASE scope_level WHEN 'municipality' THEN 0 WHEN 'department' THEN 1 ELSE 2 END")
            ->first();

        if (! $match) {
            return redirect()->route('home')->with('notFound', $q);
        }

        return redirect()->route('boletin', ['level' => $this->slug($match->scope_level), 'scope' => $match->scope]);
    }

    /**
     * Página del boletín de un scope, con breadcrumb y drill-down a los
     * scopes hijos (región → departamentos → municipios).
     */
    public function show(string $level, ?string $scope = null): View
    {
        $scopeLevel = self::LEVELS[$level] ?? 'national';
        if ($scopeLevel === 'national') {
            $scope = 'NACIONAL';
        }

        $bulletin = Bulletin::query()
            ->where('scope_level', $scopeLevel)
            ->where('scope', $scope)
            ->latest('generated_at')
            ->first();

        $events = collect();
        $children = collect();
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

        return view('boletines.detalle', compact(
            'bulletin', 'scopeLevel', 'scope', 'stats', 'breadcrumb', 'children', 'childLevelSlug',
            'securityEvents', 'environmental', 'trafficTm', 'trafficOther',
        ));
    }
}
