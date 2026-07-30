<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\BulletinEventRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BulletinEventController extends Controller
{
    public function __construct(private readonly BulletinEventRepository $events) {}

    /**
     * Lista los eventos de boletín, más recientes primero. Acepta `?batch_id=`,
     * `?region=`, `?department=`, `?municipality=`, `?type=` y `?is_transmilenio=`.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['batch_id', 'region', 'department', 'municipality', 'type']);
        if ($request->has('is_transmilenio')) {
            $filters['is_transmilenio'] = $request->boolean('is_transmilenio');
        }

        return response()->json($this->events->search($filters));
    }
}
