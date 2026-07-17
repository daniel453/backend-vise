<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenAiService;
use Illuminate\Http\Request;

class AiValidationController extends Controller
{
    public function __construct(private readonly OpenAiService $openAi) {}

    /**
     * Valida con IA un registro de vulnerabilidad/riesgo (Secciones 5/6).
     * Falla abierto: si la llamada a la IA da error, quien llama igual puede
     * continuar — esa decisión vive del lado del cliente según el campo `ok`.
     */
    public function validateVulnerability(Request $request)
    {
        $data = $request->validate([
            'section_type' => ['required', 'string', 'in:5,6'],
            'identified_text' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $result = $this->openAi->validateVulnerability(
            $data['section_type'],
            $data['identified_text'],
            $data['notes'] ?? '',
        );

        return response()->json($result);
    }

    /**
     * Revisa si las respuestas de toda una sección son coherentes entre sí
     * (ej. Sección 2 — Caracterización del Entorno). Falla abierto, igual
     * que validateVulnerability.
     */
    public function validateConsistency(Request $request)
    {
        $data = $request->validate([
            'section_title' => ['required', 'string'],
            'answers' => ['required', 'array', 'min:1'],
            'answers.*.item_id' => ['required', 'string'],
            'answers.*.label' => ['required', 'string'],
            'answers.*.selected_option' => ['nullable', 'string'],
            'answers.*.notes' => ['nullable', 'string'],
        ]);

        $result = $this->openAi->checkSectionConsistency($data['section_title'], $data['answers']);

        return response()->json($result);
    }

    /**
     * Sugiere Riesgos (Sección 6) con base en las Vulnerabilidades y las
     * respuestas ya diligenciadas de las Secciones 2-4. Falla abierto: si
     * la IA no responde, el evaluador simplemente arranca de una lista vacía.
     */
    public function suggestRisks(Request $request)
    {
        $data = $request->validate([
            'vulnerabilities' => ['required', 'array', 'min:1'],
            'vulnerabilities.*.identified_text' => ['required', 'string'],
            'vulnerabilities.*.notes' => ['nullable', 'string'],
            'context_answers' => ['nullable', 'array'],
            'context_answers.*.item_id' => ['required_with:context_answers', 'string'],
            'context_answers.*.label' => ['required_with:context_answers', 'string'],
            'context_answers.*.selected_option' => ['nullable', 'string'],
            'context_answers.*.notes' => ['nullable', 'string'],
            'guide_definition' => ['nullable', 'string'],
            'guide_examples' => ['nullable', 'array'],
            'guide_examples.*' => ['string'],
        ]);

        $result = $this->openAi->suggestRisks(
            $data['vulnerabilities'],
            $data['context_answers'] ?? [],
            $data['guide_definition'] ?? '',
            $data['guide_examples'] ?? [],
        );

        return response()->json($result);
    }
}
