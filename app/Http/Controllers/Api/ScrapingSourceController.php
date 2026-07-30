<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ScrapingSourceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScrapingSourceController extends Controller
{
    public function __construct(private readonly ScrapingSourceRepository $sources) {}

    /**
     * Lista la matriz de fuentes (Paso 1 - Contexto), en el orden original.
     * Acepta `?group=Medios nacionales` para filtrar un solo grupo.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->sources->list($request->input('group')));
    }

    /**
     * Devuelve solo los dominios del grupo "Medios nacionales" — la lista
     * blanca que arma la consulta `site:` de Google News en el flujo de n8n.
     */
    public function nationalMediaDomains(): JsonResponse
    {
        return response()->json(['domains' => $this->sources->nationalMediaDomains()]);
    }
}
