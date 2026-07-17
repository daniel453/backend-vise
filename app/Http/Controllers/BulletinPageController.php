<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Models\BulletinEvent;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
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

    /**
     * Home público: directorio de entrada. Muestra el boletín nacional
     * (resumen) y las regiones como tarjetas.
     */
    public function home(): View
    {
        $batch = Bulletin::query()->latest('generated_at')->value('batch_id');

        $national = Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'national')->first();

        $regions = Bulletin::query()
            ->where('batch_id', $batch)->where('scope_level', 'region')
            ->orderByDesc('critical_events')->orderByDesc('total_events')->orderBy('scope')
            ->get();

        $updatedAt = $national?->generated_at;

        return view('boletines.home', compact('national', 'regions', 'updatedAt'));
    }

    /**
     * Página del boletín de un scope, con breadcrumb y drill-down a los
     * scopes hijos (región → departamentos → municipios).
     */
    public function show(string $level, ?string $scope = null): View
    {
        return view('boletines.detalle', $this->buildViewData($level, $scope));
    }

    /**
     * Mismo boletín en PDF (para descargar desde la web y para que el workflow
     * de n8n lo adjunte en el correo). Se sirve inline: el navegador lo abre y
     * una petición HTTP obtiene los bytes del PDF.
     */
    public function pdf(string $level, ?string $scope = null)
    {
        $data = $this->buildViewData($level, $scope);

        if (! $data['bulletin']) {
            abort(404, 'No hay un boletín generado para este scope.');
        }

        $suffix = ($scope && $data['scopeLevel'] !== 'national') ? '-'.Str::slug($scope) : '';
        $filename = 'boletin-'.$level.$suffix.'.pdf';

        return Pdf::loadView('boletines.pdf', $data)->setPaper('a4')->stream($filename);
    }

    /**
     * Carga el boletín de un scope + sus eventos, hijos, breadcrumb y stats.
     * Compartido por la vista web y la generación de PDF.
     */
    private function buildViewData(string $level, ?string $scope): array
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

        // 'level' se usa en la vista para los enlaces de PDF.
        return compact(
            'bulletin', 'scopeLevel', 'scope', 'level', 'stats', 'breadcrumb', 'children', 'childLevelSlug',
            'securityEvents', 'environmental', 'trafficTm', 'trafficOther',
        );
    }
}
