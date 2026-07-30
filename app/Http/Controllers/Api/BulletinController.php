<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;
use App\Repositories\BulletinEventRepository;
use App\Repositories\BulletinRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinController extends Controller
{
    public function __construct(
        private readonly BulletinRepository $bulletins,
        private readonly BulletinEventRepository $events,
    ) {}

    /**
     * Lista los boletines generados (uno por scope), más recientes primero.
     * Acepta `?scope_level=`, `?scope=`, `?department=`, `?region=`.
     */
    public function index(Request $request): JsonResponse
    {
        return response()->json($this->bulletins->search(
            $request->only(['scope_level', 'scope', 'department', 'region'])
        ));
    }

    /**
     * Devuelve un boletín con sus eventos (los de la misma corrida que caen
     * dentro de su scope).
     */
    public function show(Bulletin $bulletin): JsonResponse
    {
        return response()->json([
            'bulletin' => $bulletin,
            'events' => $this->events->forScope($bulletin->batch_id, $bulletin->scope_level, $bulletin->scope),
        ]);
    }
}
