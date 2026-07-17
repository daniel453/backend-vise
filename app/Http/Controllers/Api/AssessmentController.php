<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAssessmentRequest;
use App\Models\Assessment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssessmentController extends Controller
{
    /**
     * Lista las evaluaciones (más recientes primero), con el evaluador precargado.
     */
    public function index()
    {
        return Assessment::with('evaluator:id,name,position')
            ->latest()
            ->paginate(20);
    }

    /**
     * Muestra una evaluación con sus ítems y las URLs de sus fotos.
     */
    public function show(Assessment $assessment)
    {
        $assessment->load(['evaluator:id,name,position', 'items', 'photos']);

        $assessment->photos->each(fn ($photo) => $photo->append('url'));

        return $assessment;
    }

    /**
     * Crea una evaluación completa (metadatos + ítems + fotos) en una sola
     * transacción — replica el flujo guardarRespuestas()/_subirFotos() del
     * prototipo en Apps Script, ahora contra un esquema relacional normalizado.
     */
    public function store(StoreAssessmentRequest $request)
    {
        $data = $request->validated();

        $assessment = DB::transaction(function () use ($data, $request) {
            $assessment = Assessment::create([
                'user_id' => $request->user()->id,
                'date' => $data['date'],
                'city' => $data['city'],
                'address' => $data['address'],
                'responsible_party' => $data['responsible_party'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'] ?? null,
                'gps_lat' => $data['gps_lat'] ?? null,
                'gps_lng' => $data['gps_lng'] ?? null,
                'gps_accuracy_m' => $data['gps_accuracy_m'] ?? null,
                'gps_distance_m' => $data['gps_distance_m'] ?? null,
                'general_notes' => $data['general_notes'] ?? null,
                'conclusions' => $data['conclusions'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $assessment->items()->create([
                    'item_id' => $item['item_id'],
                    'selected_option' => $item['selected_option'] ?? null,
                    'notes' => $item['notes'] ?? null,
                    'identified_text' => $item['identified_text'] ?? null,
                    'other_value' => $item['other_value'] ?? null,
                    'ai_verification' => $item['ai_verification'] ?? null,
                    'source' => $item['source'] ?? null,
                ]);

                foreach ($item['photos'] ?? [] as $order => $photo) {
                    $path = $this->storeBase64Photo($assessment->id, $item['item_id'], $photo['data'], $order);

                    if ($path) {
                        $assessment->photos()->create([
                            'item_id' => $item['item_id'],
                            'path' => $path,
                            'sort_order' => $order,
                            'gps_lat' => $photo['gps_lat'] ?? null,
                            'gps_lng' => $photo['gps_lng'] ?? null,
                            'gps_distance_m' => $photo['gps_distance_m'] ?? null,
                        ]);
                    }
                }
            }

            return $assessment;
        });

        return response()->json($assessment->load('items', 'photos'), 201);
    }

    /**
     * Decodifica un string "data:image/jpeg;base64,...." y lo guarda en
     * storage/app/public/assessments/{assessment_id}/. Devuelve la ruta
     * relativa (para guardar en la BD) o null si la foto venía mal formada.
     */
    private function storeBase64Photo(int $assessmentId, string $itemId, string $base64Photo, int $order): ?string
    {
        if (! str_contains($base64Photo, 'base64,')) {
            return null;
        }

        [$header, $encoded] = explode('base64,', $base64Photo, 2);
        $extension = str_contains($header, 'image/png') ? 'png' : 'jpg';
        $binary = base64_decode($encoded);

        if ($binary === false) {
            return null;
        }

        $safeItemId = Str::slug($itemId, '_');
        $filename = "{$safeItemId}_{$order}.{$extension}";
        $relativePath = "assessments/{$assessmentId}/{$filename}";

        Storage::disk('public')->put($relativePath, $binary);

        return $relativePath;
    }
}
