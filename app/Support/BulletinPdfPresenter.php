<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Prepara TODO el contenido del boletín para el PDF ANTES de renderizar, para que
 * quepa en UNA sola página sin recortes feos ni enlaces que redirijan a la
 * plataforma: TODOS los eventos quedan DENTRO del boletín. Estrategia de eventos:
 * los más severos van "destacados" (con descripción) y el resto se lista de forma
 * compacta (una línea: título + severidad). Además reconcilia el total de eventos
 * con la distribución por región (mismo origen -> suman igual). La vista solo pinta.
 */
class BulletinPdfPresenter
{
    /**
     * Tope de SEGURIDAD por campo (muy por encima de lo que genera la IA): evita
     * que un texto descontrolado rompa la maqueta, pero NO recorta el texto real
     * (no aparece "…" en contenido normal). El ajuste a una página se hace
     * limitando la CANTIDAD de ítems, no cortando el texto.
     */
    public const LIMIT = [
        'titulo_hero' => 130,
        'conclusion' => 360,
        'evento_titulo' => 150,
        'evento_descripcion' => 320,
        'evento_compacto' => 95,
        'recomendacion' => 240,
        'resumen_tactico' => 260,
        'zona_critica' => 120,
        'alerta_ambiental' => 260,
    ];

    public const MAX_FEATURED = 3;      // eventos con descripción
    public const MAX_COMPACT = 4;       // resto como una línea (siguen en el boletín)
    public const MAX_ALERTAS = 2;
    public const MAX_DISTRIBUCION = 5;

    /** Orden de severidad: CRÍTICO primero. */
    private const SEV_RANK = ['CRÍTICO' => 0, 'CRITICO' => 0, 'ALTO' => 1, 'MEDIO' => 2, 'BAJO' => 3];

    /**
     * Truncado inteligente (Problema 1): corta en FRASE completa (. ! ?) si cae
     * dentro del presupuesto y no queda demasiado corta; si no, en la última
     * PALABRA completa. Nunca corta a mitad de palabra.
     */
    public static function smart(?string $text, int $limit): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', (string) $text));
        if ($text === '' || mb_strlen($text) <= $limit) {
            return $text;
        }

        $slice = mb_substr($text, 0, $limit);

        // 1) Cortar al final de una frase completa dentro del presupuesto.
        if (preg_match('/^(.*[\.\!\?])(?:\s|$)/us', $slice, $m) && mb_strlen($m[1]) >= (int) ($limit * 0.5)) {
            return trim($m[1]);
        }

        // 2) Cortar en la última palabra completa.
        $lastSpace = mb_strrpos($slice, ' ');
        if ($lastSpace !== false && $lastSpace >= (int) ($limit * 0.4)) {
            $slice = mb_substr($slice, 0, $lastSpace);
        }

        return rtrim($slice, " \t\n\r.,;:—-") . '…';
    }

    /** Construye el modelo listo para la vista a partir de viewData(). */
    public function present(array $v): array
    {
        $bulletin = $v['bulletin'];
        $scopeLevel = $v['scopeLevel'];

        // Todos los eventos del scope, de una sola fuente -> total y distribución
        // siempre cuadran (Problema 2).
        $allEvents = collect()
            ->concat($v['securityEvents'])
            ->concat($v['environmental'])
            ->concat($v['trafficTm'])
            ->concat($v['trafficOther']);
        $total = $allEvents->count();

        // --- Distribución por región DERIVADA de los eventos (suma == total) ---
        $porRegion = $allEvents
            ->groupBy(fn ($e) => $e->region ?: 'Sin ubicación')
            ->map->count()
            ->sortDesc();

        $distribucion = $porRegion->take(self::MAX_DISTRIBUCION)
            ->map(fn ($n, $nombre) => ['nombre' => $nombre, 'eventos' => $n])
            ->values()->all();
        $mostrado = array_sum(array_column($distribucion, 'eventos'));
        if ($mostrado < $total) { // el resto agrupado, para que siga sumando total
            $distribucion[] = ['nombre' => 'Otras', 'eventos' => $total - $mostrado];
        }

        // --- Estrategia de eventos: TODOS quedan en el boletín (nada redirige) ---
        // Ordenados por severidad, los primeros van "destacados" (con descripción)
        // y el resto en una lista compacta de una línea. Así entra en una página.
        $seguridad = $v['securityEvents']
            ->sortBy(fn ($e) => self::SEV_RANK[mb_strtoupper((string) $e->severity)] ?? 9)
            ->values();

        $eventos = $seguridad->take(self::MAX_FEATURED)->map(function ($e) {
            $sev = mb_strtoupper((string) $e->severity);

            return [
                'titulo' => self::smart($e->title, self::LIMIT['evento_titulo']),
                'descripcion' => self::smart($e->summary, self::LIMIT['evento_descripcion']),
                'severidad' => $e->severity,
                'esCritico' => in_array($sev, ['CRÍTICO', 'CRITICO'], true),
                'geo' => trim(($e->municipality ? $e->municipality . ', ' : '') . ($e->department ?? ''), ', '),
            ];
        })->all();

        // Resto de eventos, compactos (una línea) — se quedan DENTRO del boletín.
        $eventosCompactos = $seguridad->slice(self::MAX_FEATURED, self::MAX_COMPACT)->map(function ($e) {
            $sev = mb_strtoupper((string) $e->severity);
            $geo = trim(($e->municipality ? $e->municipality . ', ' : '') . ($e->department ?? ''), ', ');

            return [
                'titulo' => self::smart($e->title, self::LIMIT['evento_compacto']),
                'severidad' => $e->severity,
                'esCritico' => in_array($sev, ['CRÍTICO', 'CRITICO'], true),
                'geo' => $geo,
            ];
        })->values()->all();

        // --- Recomendaciones (60 c/u) ---
        $recomendaciones = collect([
            'LOGÍSTICA' => $bulletin->logistics_recommendation,
            'PERÍMETROS' => $bulletin->perimeter_recommendation,
            'OPERACIONAL' => $bulletin->operational_recommendation,
            'DIGITAL' => $bulletin->digital_recommendation,
        ])->filter()
            ->map(fn ($texto, $label) => ['label' => $label, 'texto' => self::smart($texto, self::LIMIT['recomendacion'])])
            ->values()->all();

        // --- Alertas ambientales (100 c/u) ---
        $ambientales = $v['environmental']->take(self::MAX_ALERTAS)->map(fn ($e) => [
            'titulo' => trim(($e->subtype ?? 'Alerta') . ' — ' . ($e->department ?? 'Colombia')),
            'descripcion' => self::smart($e->summary, self::LIMIT['alerta_ambiental']),
        ])->all();

        // --- Conclusión vs Resumen táctico SIN redundancia (Problema 4) ---
        // Conclusión = narrativa ejecutiva (NO menciona zona/tendencia).
        // Resumen táctico = datos estructurados (amenaza + chips zona/tendencia).
        $conclusion = self::smart(
            $bulletin->conclusion ?: self::sintetizarConclusion($bulletin, $total),
            self::LIMIT['conclusion']
        );

        return [
            'levelLabel' => ['national' => 'NACIONAL', 'region' => 'REGIÓN', 'department' => 'DEPARTAMENTO', 'municipality' => 'MUNICIPIO'][$scopeLevel] ?? mb_strtoupper($scopeLevel),
            'scope' => $v['scope'],
            'generatedAt' => Carbon::parse($bulletin->generated_at),
            'titulo' => self::smart($bulletin->headline, self::LIMIT['titulo_hero']),
            'conclusion' => $conclusion,
            'tactica' => [
                'amenaza' => self::smart($bulletin->main_threat ?? '', self::LIMIT['resumen_tactico']),
                'zona' => self::smart($bulletin->critical_zone ?? '', self::LIMIT['zona_critica']),
                'tendencia' => mb_strtoupper((string) ($bulletin->trend ?? '—')),
            ],
            'stats' => [
                'total' => $total,
                'criticos' => $seguridad->filter(fn ($e) => in_array(mb_strtoupper((string) $e->severity), ['CRÍTICO', 'CRITICO'], true))->count(),
                'areas' => $porRegion->keys()->reject(fn ($k) => $k === 'Sin ubicación')->count(),
                'vias' => $v['trafficTm']->count() + $v['trafficOther']->count(),
                'transmilenio' => $v['trafficTm']->count(),
                'ambientales' => $v['environmental']->count(),
            ],
            'eventos' => $eventos,
            'eventosCompactos' => $eventosCompactos,
            'recomendaciones' => $recomendaciones,
            'ambientales' => $ambientales,
            'distribucion' => $distribucion,
            'distTitle' => ['region' => 'Distribución por región', 'departamento' => 'Distribución por departamento', 'municipio' => 'Distribución por municipio'][$v['childLevelSlug'] ?? ''] ?? 'Distribución por región',
            'platformUrl' => self::platformUrl($v),
            'logoDataUri' => self::brandAsset('altum-logo.png', 'image/png'),
        ];
    }

    /** Lee un asset de marca de resources/brand y lo devuelve como data URI (o null si falta). */
    private static function brandAsset(string $file, string $mime): ?string
    {
        $path = resource_path('brand/' . $file);
        if (! is_file($path)) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($path));
    }

    /** Conclusión sintetizada SIN repetir zona/tendencia (esos van en la táctica). */
    private static function sintetizarConclusion($bulletin, int $total): string
    {
        $amenaza = $bulletin->main_threat ?: ($bulletin->headline ?: 'Panorama de seguridad del día');
        $accion = $bulletin->operational_recommendation ?: ($bulletin->logistics_recommendation ?: '');

        $txt = rtrim($amenaza, '. ') . '. Se registran ' . $total . ' evento(s), ' . (int) $bulletin->critical_events . ' crítico(s).';
        if ($accion !== '') {
            $txt .= ' ' . rtrim($accion, '. ') . '.';
        }

        return $txt;
    }

    private static function platformUrl(array $v): string
    {
        $slug = ['national' => 'nacional', 'region' => 'region', 'department' => 'departamento', 'municipality' => 'municipio'][$v['scopeLevel']] ?? 'nacional';

        return route('boletin', ['level' => $slug, 'scope' => $v['scopeLevel'] === 'national' ? null : $v['scope']]);
    }
}
