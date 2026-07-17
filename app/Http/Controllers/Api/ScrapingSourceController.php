<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScrapingSource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScrapingSourceController extends Controller
{
    /**
     * Lista la matriz de fuentes (Paso 1 - Contexto), en el orden original.
     * Acepta `?group=Medios nacionales` para filtrar un solo grupo — el flujo
     * de n8n que arma los boletines usa ese filtro para traer solo los
     * dominios de medios, en vez de tener la lista embebida en el workflow.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ScrapingSource::query()->orderBy('sort_order');

        if ($request->filled('group')) {
            $query->where('group', $request->string('group'));
        }

        return response()->json($query->get(['group', 'source', 'coverage', 'domain']));
    }

    /**
     * Devuelve solo los dominios del grupo "Medios nacionales" — la lista
     * blanca que arma la consulta `site:` de Google News en el flujo de n8n.
     */
    public function nationalMediaDomains(): JsonResponse
    {
        $domains = ScrapingSource::query()
            ->where('group', 'Medios nacionales')
            ->whereNotNull('domain')
            ->orderBy('sort_order')
            ->pluck('domain');

        return response()->json(['domains' => $domains]);
    }
}
