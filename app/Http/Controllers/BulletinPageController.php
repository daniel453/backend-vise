<?php

namespace App\Http\Controllers;

use App\Models\Bulletin;
use App\Services\BulletinReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BulletinPageController extends Controller
{
    public function __construct(private readonly BulletinReportService $reports) {}

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
        return view('boletines.detalle', $this->reports->viewData($level, $scope));
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

        return Pdf::loadView('boletines.pdf', $data)->setPaper('a4')->stream($filename);
    }
}
