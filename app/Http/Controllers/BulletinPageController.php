<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Services\BulletinReportService;
use App\Support\BulletinPdfPresenter;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BulletinPageController extends Controller
{
    public function __construct(private readonly BulletinReportService $reports) {}

    /**
     * Entrada del portal: el tablero de la Central de Monitoreo se abre en el
     * boletín NACIONAL; desde el menú de cobertura se navega a cada regional.
     */
    public function home(): RedirectResponse
    {
        return redirect()->route('boletin', ['level' => 'nacional']);
    }

    /**
     * Tablero del boletín de un scope (nacional o regional), con el menú de
     * cobertura (Nacional + regiones) y drill-down a los scopes hijos.
     */
    public function show(string $level, ?string $scope = null): View
    {
        $data = $this->reports->viewData($level, $scope);
        $data['cobertura'] = $this->coberturaMenu($data['bulletin']->batch_id ?? Bulletin::query()->latest('generated_at')->value('batch_id'));
        $data['updatedAt'] = $data['bulletin']->generated_at ?? null;

        return view('boletines.detalle', $data);
    }

    /**
     * Menú de cobertura del sidebar: Nacional + las regiones de la última
     * corrida, con su conteo de eventos/críticos.
     *
     * @return array<int,array{level:string,scope:?string,nombre:string,eventos:int,criticos:int}>
     */
    private function coberturaMenu(?string $batch): array
    {
        if (! $batch) {
            return [];
        }
        $menu = [];
        $nacional = Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'national')->first();
        if ($nacional) {
            $menu[] = ['level' => 'nacional', 'scope' => null, 'nombre' => 'Nacional', 'eventos' => (int) $nacional->total_events, 'criticos' => (int) $nacional->critical_events];
        }
        $regions = Bulletin::query()->where('batch_id', $batch)->where('scope_level', 'region')->orderBy('scope')->get();
        foreach ($regions as $r) {
            $menu[] = ['level' => 'region', 'scope' => $r->scope, 'nombre' => $r->scope, 'eventos' => (int) $r->total_events, 'criticos' => (int) $r->critical_events];
        }

        return $menu;
    }

    /**
     * Mismo boletín en PDF (para descargar desde la web). Se sirve inline: el
     * navegador lo abre o lo descarga.
     */
    public function pdf(string $level, ?string $scope = null)
    {
        $data = $this->reports->viewData($level, $scope);

        if (! $data['bulletin']) {
            abort(404, 'No hay un boletín generado para este scope.');
        }

        $suffix = ($scope && $data['scopeLevel'] !== 'national') ? '-'.Str::slug($scope) : '';
        $filename = 'boletin-'.$level.$suffix.'.pdf';

        return Pdf::loadView('boletines.pdf', (new BulletinPdfPresenter)->present($data))->setPaper('a4')->stream($filename);
    }
}
