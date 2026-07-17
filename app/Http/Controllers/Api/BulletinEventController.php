<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BulletinEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinEventController extends Controller
{
    /**
     * Lista los eventos de boletín, más recientes primero. Acepta `?batch_id=`,
     * `?region=`, `?department=`, `?municipality=`, `?type=` y `?is_transmilenio=`
     * — el HTML usa batch_id + geo para traer los eventos de un boletín puntual.
     */
    public function index(Request $request): JsonResponse
    {
        $query = BulletinEvent::query()->latest('published_at');

        if ($request->filled('batch_id')) {
            $query->where('batch_id', $request->string('batch_id'));
        }

        foreach (['region', 'department', 'municipality', 'type'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->string($filter));
            }
        }

        if ($request->has('is_transmilenio')) {
            $query->where('is_transmilenio', $request->boolean('is_transmilenio'));
        }

        return response()->json($query->paginate(50));
    }
}
