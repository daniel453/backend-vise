<?php

namespace App\Support;

use App\Models\Departaments;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

/**
 * Arma el mapa de calor de Colombia para el boletín nacional: pinta cada
 * departamento donde opera VISE según su nivel de riesgo (calculado desde los
 * eventos del boletín) y lo rasteriza a PNG con Browsershot (Chrome headless)
 * para incrustarlo en el PDF (dompdf no renderiza bien SVG complejo).
 *
 * Degradación elegante: si Browsershot/Chrome no está disponible, dataUri()
 * devuelve null y la vista muestra solo la tabla de "Nivel de riesgo por
 * departamento" — el PDF nunca falla por el mapa.
 */
class ColombiaMap
{
    /** Colores por nivel (paleta de la infografía VISE). */
    private const COLOR = [
        'ALTO' => '#DC2626',
        'MEDIO' => '#F97316',
        'BAJO' => '#FACC15',
        'NORMAL' => '#22C55E',
    ];

    private const COLOR_NO_VISE = '#E5E7EB';   // depto sin cobertura VISE

    private const NIVEL_RANK = ['ALTO' => 0, 'MEDIO' => 1, 'BAJO' => 2, 'NORMAL' => 3];

    /**
     * Modelo del mapa: viewBox, lista de departamentos (path + color + nivel) y
     * el panel "nivel de riesgo por departamento" (solo VISE con riesgo).
     *
     * @return array{viewBox:string, departments:array<int,array>, panel:array<int,array>}|null
     */
    public static function riesgos(Collection $allEvents): ?array
    {
        $asset = resource_path('data/colombia_map.json');
        if (! is_file($asset)) {
            return null;
        }
        $svg = json_decode((string) file_get_contents($asset), true);
        if (! is_array($svg) || empty($svg['departments'])) {
            return null;
        }

        // Departamentos VISE (los que tienen regional asignada), normalizados.
        $viseNames = Departaments::query()->whereNotNull('regional_id')->pluck('name')
            ->mapWithKeys(fn ($n) => [self::norm($n) => true])->all();

        // Eventos por departamento (normalizado).
        $porDepto = $allEvents
            ->filter(fn ($e) => (bool) $e->department)
            ->groupBy(fn ($e) => self::norm($e->department));

        $departments = [];
        $panel = [];
        foreach ($svg['departments'] as $d) {
            $key = self::norm($d['name']);
            $esVise = isset($viseNames[$key]);
            if (! $esVise) {
                $departments[] = ['name' => $d['name'], 'path' => $d['path'], 'fill' => self::COLOR_NO_VISE, 'level' => null];

                continue;
            }
            $eventos = $porDepto->get($key, collect());
            $nivel = self::nivel($eventos);
            $departments[] = ['name' => $d['name'], 'path' => $d['path'], 'fill' => self::COLOR[$nivel], 'level' => $nivel];
            if ($nivel !== 'NORMAL') {
                $panel[] = ['name' => $d['name'], 'level' => $nivel, 'color' => self::COLOR[$nivel], 'eventos' => $eventos->count()];
            }
        }

        usort($panel, fn ($a, $b) => (self::NIVEL_RANK[$a['level']] <=> self::NIVEL_RANK[$b['level']]) ?: ($b['eventos'] <=> $a['eventos']));

        return ['viewBox' => $svg['viewBox'] ?? '0 0 613 694', 'departments' => $departments, 'panel' => $panel];
    }

    /**
     * PNG del mapa como data URI (base64), o null si no se pudo rasterizar.
     * Cachea por hash del contenido para no invocar Chrome en cada envío.
     */
    public static function dataUri(array $riesgos): ?string
    {
        $html = view('boletines._mapa', $riesgos)->render();
        $hash = substr(sha1($html), 0, 16);
        $dir = storage_path('app/maps');
        $cacheFile = $dir.'/'.$hash.'.png';

        if (is_file($cacheFile)) {
            return 'data:image/png;base64,'.base64_encode((string) file_get_contents($cacheFile));
        }

        try {
            if (! is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
            // Aspecto del viewBox (613x694) escalado; fondo transparente.
            $shot = Browsershot::html($html)
                ->windowSize(920, 1041)
                ->deviceScaleFactor(2)
                ->setScreenshotType('png')
                ->transparentBackground();

            if ($node = config('services.browsershot.node_binary')) {
                $shot->setNodeBinary($node);
            }
            if ($npm = config('services.browsershot.npm_binary')) {
                $shot->setNpmBinary($npm);
            }
            if ($chrome = config('services.browsershot.chrome_path')) {
                $shot->setChromePath($chrome);
            }

            $shot->save($cacheFile);

            return 'data:image/png;base64,'.base64_encode((string) file_get_contents($cacheFile));
        } catch (\Throwable $e) {
            // Chrome/puppeteer no instalado o falló: el PDF sale sin mapa.
            Log::warning('ColombiaMap: no se pudo rasterizar el mapa: '.$e->getMessage());

            return null;
        }
    }

    /** Nivel de riesgo determinista a partir de los eventos del departamento. */
    private static function nivel(Collection $eventos): string
    {
        if ($eventos->isEmpty()) {
            return 'NORMAL';
        }
        $critico = $eventos->contains(fn ($e) => in_array(mb_strtoupper((string) $e->severity), ['CRÍTICO', 'CRITICO', 'ALTO'], true));
        $n = $eventos->count();
        if ($critico || $n >= 4) {
            return 'ALTO';
        }
        if ($n >= 2) {
            return 'MEDIO';
        }

        return 'BAJO';
    }

    /** Normaliza nombre de departamento: minúsculas, sin acentos, espacios colapsados. */
    private static function norm(?string $s): string
    {
        $s = mb_strtolower(trim((string) $s));
        $s = strtr($s, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u', 'ñ' => 'n']);

        return preg_replace('/\s+/', ' ', $s);
    }
}
