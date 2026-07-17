<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Models\BulletinEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    /**
     * Lista los boletines generados (uno por scope), más recientes primero.
     * Acepta `?scope_level=` (municipality/department/region/national) y
     * `?scope=` — el HTML los usa para poblar el selector.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Bulletin::query()->latest('generated_at');

        foreach (['scope_level', 'scope', 'department', 'region'] as $filter) {
            if ($request->filled($filter)) {
                $query->where($filter, $request->string($filter));
            }
        }

        return response()->json($query->paginate(50));
    }

    /**
     * Devuelve un boletín con sus eventos — lo que el HTML muestra cuando el
     * usuario elige un scope. Los eventos son los de la misma corrida
     * (`batch_id`) que caen dentro del scope del boletín.
     */
    public function show(Bulletin $bulletin): JsonResponse
    {
        $events = BulletinEvent::query()
            ->where('batch_id', $bulletin->batch_id)
            ->when($bulletin->scope_level === 'municipality', fn ($q) => $q->where('municipality', $bulletin->scope))
            ->when($bulletin->scope_level === 'department', fn ($q) => $q->where('department', $bulletin->scope))
            ->when($bulletin->scope_level === 'region', fn ($q) => $q->where('region', $bulletin->scope))
            ->orderByRaw("CASE severity WHEN 'CRÍTICO' THEN 0 WHEN 'ALTO' THEN 1 WHEN 'MEDIO' THEN 2 ELSE 3 END")
            ->get();

        return response()->json([
            'bulletin' => $bulletin,
            'events' => $events,
        ]);
    }
}
