<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{
    /**
     * Le pregunta a la IA si un registro de vulnerabilidad/riesgo es lo
     * suficientemente específico y real como para dejarlo en la evaluación,
     * siguiendo el mismo prompt/esquema ya validado en el prototipo web.
     *
     * @return array{ok: bool, valid?: bool, reason?: string, suggestion?: string, error?: string}
     */
    public function validateVulnerability(string $sectionType, string $identifiedText, string $notes): array
    {
        $apiKey = config('services.openai.key');

        if (! $apiKey) {
            return ['ok' => false, 'error' => 'missing_api_key'];
        }

        $criteria = $sectionType === '5'
            ? 'a real and specific VULNERABILITY: a concrete weakness or deficient physical/procedural CONDITION that currently exists at the property or its surroundings, with an identifiable location within or around the property (e.g. "cuarto de racks", "puerta principal", "segundo piso"). '
                .'A vulnerability entry must describe ONLY the deficient condition itself — not its consequences, not a threat scenario, and not a corrective action. '
                .'Reject it (valid=false) when it falls into any of these common mistakes evaluators make: '
                ."(a) it frames the entry around the potential impact or exploitation instead of the condition itself — e.g. it leans on phrases like 'facilita el ingreso', 'riesgo para la continuidad operativa', 'vector de intrusión', 'permite sustracción', 'facilita la planificación delictiva', 'imposible detección anticipada' — that kind of threat/impact reasoning belongs in the separate Riesgos section, not here; "
                ."(b) it reads as a recommendation or corrective action rather than a description of what currently exists — e.g. 'debe estar bajo control exclusivo de...', 'se recomienda instalar...', 'corrección inmediata' — recommendations belong in a different section; "
                .'(c) it is a vague, generic statement with no site-specific location, as if copy-pasted from a checklist without describing what was actually found at this property; '
                .'(d) the Description just restates the Identified title in different words without adding any concrete new detail (material, missing device, exact location, extent of the damage, etc.). '
                .'If the entry is borderline — the condition itself is clear and specific but it also adds one extra clause about impact — do not be pedantic, approve it. Only reject when the entry fails to primarily describe a concrete, located condition.'
            : 'a real and specific RISK: it must combine what could happen (a concrete threat scenario, not a vague impact category), why or how it could happen (which vulnerability or gap it exploits), and in which specific part of the property. '
                .'Reject it (valid=false) when it falls into any of these common mistakes evaluators make: '
                ."(a) it reads as a recommendation or corrective action instead of a risk scenario — e.g. 'se recomienda instalar...', 'debería contar con...' — recommendations belong in a different section, not here; "
                ."(b) the 'what could happen' part is just a vague, generic impact category instead of a concrete scenario — e.g. 'afectación a personas e infraestructura', 'fallas en sistemas de seguridad', 'pérdidas económicas' with nothing describing the actual event; "
                .'(c) it crams several distinct, unrelated risk types into one entry as a list (e.g. joined by "/" or commas: "hurto / fraude / afectación reputacional", "asonada, vandalismo, manifestaciones") instead of committing to one specific, coherent scenario — each distinct risk belongs in its own separate entry; '
                .'(d) it has no identifiable location within or around the property. '
                .'Closely related causes that describe the same single event chain (e.g. a short circuit causing a fire) are NOT a list of unrelated risks — do not reject those. When in doubt, approve it.';

        $schema = [
            'name' => 'vulnerability_check',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'valid' => ['type' => 'boolean'],
                    'reason' => ['type' => 'string'],
                    'suggestion' => ['type' => 'string'],
                ],
                'required' => ['valid', 'reason', 'suggestion'],
                'additionalProperties' => false,
            ],
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You assess whether a security risk-assessment record describes '.$criteria.
                                ' When you reject an entry, the reason must name which specific mistake it made and the suggestion must show concretely how to rewrite it as a bare condition (or point out that it belongs in another section). '
                                .'Answer strictly with the requested JSON. Write reason/suggestion in Spanish, since the end user is Spanish-speaking.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Identified: {$identifiedText}\nDescription: {$notes}",
                        ],
                    ],
                    'response_format' => ['type' => 'json_schema', 'json_schema' => $schema],
                    'max_completion_tokens' => 400,
                ]);

            if ($response->failed()) {
                Log::warning('OpenAI request failed', ['status' => $response->status(), 'body' => $response->body()]);

                return ['ok' => false, 'error' => 'http_'.$response->status()];
            }

            $verdict = json_decode($response->json('choices.0.message.content'), true);

            return [
                'ok' => true,
                'valid' => (bool) ($verdict['valid'] ?? false),
                'reason' => $verdict['reason'] ?? '',
                'suggestion' => $verdict['suggestion'] ?? '',
            ];
        } catch (\Throwable $e) {
            Log::warning('OpenAI request threw', ['message' => $e->getMessage()]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Le pregunta a la IA dos cosas sobre las respuestas de toda una sección:
     * (1) si son coherentes entre sí — todas describen el mismo inmueble en
     * la misma visita, así que una contradicción entre ellas (ej. "no hay
     * fuerza pública cercana" + "tiempo de respuesta menor a 5 minutos") es
     * una señal real de mala calidad de datos que un solo desplegable no
     * detecta; y (2) si la "Nota"/Observación de cada respuesta realmente
     * tiene relación con la opción elegida, o si el evaluador puso texto
     * genérico/irrelevante que no aporta nada sobre esa elección puntual.
     *
     * @param  array<int, array{item_id: string, label: string, selected_option: ?string, notes: ?string}>  $answers
     * @return array{ok: bool, consistent?: bool, warnings?: array<int, array{summary: string, related_item_ids: array<int, string>, recommendation: string}>, error?: string}
     */
    public function checkSectionConsistency(string $sectionTitle, array $answers): array
    {
        $apiKey = config('services.openai.key');

        if (! $apiKey) {
            return ['ok' => false, 'error' => 'missing_api_key'];
        }

        $validItemIds = collect($answers)->pluck('item_id')->all();

        $schema = [
            'name' => 'section_consistency_check',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'consistent' => ['type' => 'boolean'],
                    'warnings' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'summary' => ['type' => 'string'],
                                'related_item_ids' => [
                                    'type' => 'array',
                                    'description' => 'The item_id values (from the input list) of the specific answers involved in this contradiction.',
                                    'items' => ['type' => 'string'],
                                ],
                                'recommendation' => [
                                    'type' => 'string',
                                    'description' => 'A short, practical suggestion for what the evaluator should check or correct to resolve this specific contradiction.',
                                ],
                            ],
                            'required' => ['summary', 'related_item_ids', 'recommendation'],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
                'required' => ['consistent', 'warnings'],
                'additionalProperties' => false,
            ],
        ];

        $lines = collect($answers)->map(function (array $answer) {
            $option = $answer['selected_option'] ?? '(sin responder)';
            $notes = trim((string) ($answer['notes'] ?? ''));

            return "- [{$answer['item_id']}] {$answer['label']}: {$option}".($notes !== '' ? " — Nota: {$notes}" : '');
        })->implode("\n");

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You review a physical security risk-assessment section titled \"{$sectionTitle}\", filled out by a single evaluator about a single property during a single site visit. Each answer line starts with its item_id in brackets, e.g. \"[2.9] Presencia de Fuerza Pública: No se observa\", and may include a note after \" — Nota: \". Perform two independent checks and report ANY issues you find as warnings:\n\n1) CROSS-ANSWER CONTRADICTIONS: flag genuine contradictions between two or more answers (e.g. \"no security force observed nearby\" together with \"authority response time under 5 minutes\"). Do not flag an answer just because it is negative or concerning on its own.\n\n2) NOTE RELEVANCE: be conservative here — the goal is to catch genuinely lazy/empty input, NOT to nitpick notes that are already useful. For every answer that includes a Nota, flag it ONLY if the note is truly generic/boilerplate (e.g. \"todo bien\", \"sin novedad\", placeholder or copy-pasted text repeated across items with no real detail), is completely unrelated to the item's topic, or directly contradicts the selected option (e.g. option is \"Buena\" but the note describes deficiencies). Do NOT flag a note just because it doesn't restate the selected option's wording — if it already contains concrete, specific details relevant to the topic (distances, locations, counts, names, descriptions of what was observed), it is a GOOD note and must be left alone, even if it doesn't explicitly reference the chosen category. When in doubt, do not flag it. An empty note is fine and should never be flagged.\n\nFor every warning, set related_item_ids to the item_id(s) involved: for a cross-answer contradiction include every item_id involved (normally two); for a note-relevance issue include ONLY that single item_id.\n\nRecommendations must never demand exact/precise identifying data: no exact headcounts (\"número de personas\", \"cantidad de indigentes\"), no exact addresses or street numbers, no exact business names, no exact times. An approximate distance (\"a unos 20-30 metros\", \"a dos cuadras\") plus a relative location (\"en la misma cuadra\", \"al lado\", \"en la esquina\") is already fully sufficient detail — never ask for more precision than that. Concrete worked example of an ALREADY GOOD note that must never be flagged nor asked to add more detail: for item \"Presencia de establecimientos de alto riesgo\" with option \"Sí, muy cercana\", the note \"Hay dos bares ubicados a unos 20-30 metros de la empresa, en la misma cuadra, lo que evidencia una cercanía significativa\" is complete and specific as-is — it must NOT be flagged, and no recommendation should ask for the bars' names or exact address. Ask only for qualitative, descriptive detail: what type of activity or condition was observed, roughly where (approximate distance/relative position), and why it supports the option chosen.\n\nFor a note-relevance issue, the item's own label/category (e.g. \"Presencia de establecimientos de alto riesgo\", \"Condiciones generales de seguridad\") is fixed by the question itself — it is NOT something the evaluator chose and never needs to be \"justified\". Never phrase a recommendation as \"para justificar la categoría/el tipo de...\". Instead, phrase it around the SELECTED OPTION (e.g. \"Sí, muy cercana\", \"Deficiente\") and ask for the concrete detail that supports that specific choice — e.g. exact location/distance of what was observed, or what specifically made the condition deficient — not why the general topic matters.\n\nThe recommendation field is the most important part — it must tell the evaluator EXACTLY what single correction would resolve the issue, never a vague instruction to \"review\" or \"verify\" something in general terms. You cannot know which of the two answers is factually wrong, so phrase it as a concrete conditional naming the exact values to change, e.g. (write it in Spanish): \"Verifique en sitio: si NO hay Fuerza Pública cercana, cambie 'Tiempo estimado de respuesta' a 'Más de 20 minutos'; si SÍ la hay, cambie 'Presencia de Fuerza Pública' a 'Sí, a distancia razonable'.\" Bad example (never do this): \"Revisar la disponibilidad de Fuerza Pública y su efectividad.\" For a note-relevance issue, name the specific fact the note should mention instead of the generic option, e.g.: \"Reescriba la nota indicando, por ejemplo, cuál es la unidad de Fuerza Pública más cercana y a qué distancia queda, para justificar un tiempo de respuesta menor a 5 minutos.\" Bad example (never do this): \"Reescribir la nota para que sea más específica.\"\n\nDo not invent missing information. CRITICAL: the warnings array must contain ONLY entries that require a real correction. If, after analyzing an answer, your conclusion is that it is actually fine/adequate/does not need any change, DO NOT add it to warnings at all — there is no such thing as an \"informational\" or \"all clear\" warning entry. Every single object in warnings must describe a problem the evaluator still has to fix. Answer strictly with the requested JSON. Write warning summaries and recommendations in Spanish, since the end user is Spanish-speaking. If you find no issues of either kind, return consistent=true and an empty warnings array.",
                        ],
                        [
                            'role' => 'user',
                            'content' => $lines,
                        ],
                    ],
                    'response_format' => ['type' => 'json_schema', 'json_schema' => $schema],
                    'max_completion_tokens' => 600,
                ]);

            if ($response->failed()) {
                Log::warning('OpenAI request failed', ['status' => $response->status(), 'body' => $response->body()]);

                return ['ok' => false, 'error' => 'http_'.$response->status()];
            }

            $verdict = json_decode($response->json('choices.0.message.content'), true);

            $warnings = array_values(array_filter(array_map(fn ($w) => [
                'summary' => $w['summary'] ?? '',
                // Drop any item_id the model might hallucinate outside the given list.
                'related_item_ids' => array_values(array_intersect($w['related_item_ids'] ?? [], $validItemIds)),
                'recommendation' => $w['recommendation'] ?? '',
            ], $verdict['warnings'] ?? []), function ($w) {
                // Safety net: despite the prompt, the model sometimes still adds a
                // "warning" whose own text says the answer is actually fine. Drop
                // those here instead of trusting the model's `consistent` flag —
                // otherwise the evaluator gets forced through the guided review
                // for something the AI itself just approved.
                return ! $this->soundsLikeNoIssue($w['summary'].' '.$w['recommendation']);
            }));

            return [
                'ok' => true,
                // Recomputed from the filtered list, not from the model's own
                // `consistent` flag, so both fields can never contradict each other.
                'consistent' => empty($warnings),
                'warnings' => $warnings,
            ];
        } catch (\Throwable $e) {
            Log::warning('OpenAI request threw', ['message' => $e->getMessage()]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Le pide a la IA que proponga Riesgos (Sección 6) con base en las
     * Vulnerabilidades ya identificadas (Sección 5) y en las respuestas ya
     * diligenciadas de las Secciones 2-4 — se corre una sola vez, al abrir
     * la Sección 6 por primera vez, para darle al evaluador un punto de
     * partida en vez de la hoja en blanco. El evaluador puede editar,
     * eliminar o agregar más después; esto solo siembra sugerencias.
     *
     * @param  array<int, array{identified_text: string, notes: string}>  $vulnerabilities
     * @param  array<int, array{item_id: string, label: string, selected_option: ?string, notes: ?string}>  $contextAnswers
     * @param  array<int, string>  $guideExamples
     * @return array{ok: bool, risks?: array<int, array{identified_text: string, notes: string}>, error?: string}
     */
    public function suggestRisks(array $vulnerabilities, array $contextAnswers, string $guideDefinition, array $guideExamples): array
    {
        $apiKey = config('services.openai.key');

        if (! $apiKey) {
            return ['ok' => false, 'error' => 'missing_api_key'];
        }

        $schema = [
            'name' => 'risk_suggestions',
            'strict' => true,
            'schema' => [
                'type' => 'object',
                'properties' => [
                    'risks' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'identified_text' => ['type' => 'string', 'description' => 'Short risk title, e.g. "Intrusión por barrera perimetral deteriorada en costado norte".'],
                                'notes' => ['type' => 'string', 'description' => 'One or two sentences: what could happen, why/how, and in which specific part of the property.'],
                            ],
                            'required' => ['identified_text', 'notes'],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
                'required' => ['risks'],
                'additionalProperties' => false,
            ],
        ];

        $vulnLines = collect($vulnerabilities)->map(fn (array $v) => "- {$v['identified_text']}".(trim((string) ($v['notes'] ?? '')) !== '' ? " — {$v['notes']}" : ''))->implode("\n");

        $contextLines = collect($contextAnswers)->map(function (array $a) {
            $option = $a['selected_option'] ?? '(sin responder)';
            $notes = trim((string) ($a['notes'] ?? ''));

            return "- [{$a['item_id']}] {$a['label']}: {$option}".($notes !== '' ? " — Nota: {$notes}" : '');
        })->implode("\n");

        $examplesList = collect($guideExamples)->map(fn ($e) => "- {$e}")->implode("\n");

        $userContent = "Vulnerabilidades ya identificadas en esta propiedad:\n{$vulnLines}\n\n"
            ."Respuestas ya registradas sobre el entorno y el inmueble:\n{$contextLines}";

        try {
            $response = Http::withToken($apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are a physical security assessor proposing preliminary risks (Sección 6) for a bank branch site visit, grounded in what the evaluator already found. Definition of a risk here: \"{$guideDefinition}\" Examples of well-formed risks:\n{$examplesList}\n\n"
                                .'Given the vulnerabilities already identified and the context answers about the surroundings and the property, propose between 2 and 4 concrete, realistic risks that plausibly follow from that specific input — do not invent risks unrelated to what was actually reported. Prefer deriving one risk per vulnerability when it clearly supports one, but use judgment: skip a vulnerability if no coherent risk follows from it, and you may add a risk that emerges from combining the context answers even without a matching vulnerability line, if well supported. '
                                .'Each risk must combine what could happen (a concrete scenario, not a vague impact category), why or how it could happen (which specific vulnerability or gap it exploits), and in which specific part of the property — matching the style of the examples above. Do not phrase any risk as a recommendation or corrective action, and do not cram multiple unrelated risk types into one entry. '
                                .'Answer strictly with the requested JSON, in Spanish since the end user is Spanish-speaking. If the input truly gives no basis for any real risk, return an empty risks array — never invent generic filler.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $userContent,
                        ],
                    ],
                    'response_format' => ['type' => 'json_schema', 'json_schema' => $schema],
                    'max_completion_tokens' => 800,
                ]);

            if ($response->failed()) {
                Log::warning('OpenAI request failed', ['status' => $response->status(), 'body' => $response->body()]);

                return ['ok' => false, 'error' => 'http_'.$response->status()];
            }

            $verdict = json_decode($response->json('choices.0.message.content'), true);

            $risks = array_values(array_filter(array_map(fn ($r) => [
                'identified_text' => trim((string) ($r['identified_text'] ?? '')),
                'notes' => trim((string) ($r['notes'] ?? '')),
            ], $verdict['risks'] ?? []), fn ($r) => $r['identified_text'] !== '' && $r['notes'] !== ''));

            return [
                'ok' => true,
                'risks' => $risks,
            ];
        } catch (\Throwable $e) {
            Log::warning('OpenAI request threw', ['message' => $e->getMessage()]);

            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Detecta si el texto de una advertencia en realidad dice "esto está
     * bien" — red de seguridad para cuando el modelo, pese al prompt, agrega
     * una "advertencia" que en el fondo no reporta ningún problema real.
     */
    private function soundsLikeNoIssue(string $text): bool
    {
        $normalized = mb_strtolower($text);

        $noIssuePhrases = [
            'no requiere cambio',
            'no debe ser cambiada',
            'no debe ser modificada',
            'no necesita cambio',
            'no necesita corrección',
            'no es necesario cambiar',
            'no es necesario modificar',
            'es adecuada y no',
            'es correcta y no',
            'no presenta ningún problema',
            'no presenta ninguna inconsistencia',
            'sin problema',
            'no hay problema',
            'está bien y no',
            'es relevante y no debe',
        ];

        foreach ($noIssuePhrases as $phrase) {
            if (str_contains($normalized, $phrase)) {
                return true;
            }
        }

        return false;
    }
}
