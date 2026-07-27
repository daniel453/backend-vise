<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
@include('boletines._pdf_styles', ['faDataUri' => $faDataUri ?? null])
<style>
  .m-city { background:#4C1D95; color:#fff; padding:6px 11px; font-size:10px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; }
  .m-city .n { float:right; font-size:8px; opacity:.85; }
  .march { padding:7px 0; border-bottom:1px solid #EEF4F0; }
  .march:last-child { border-bottom:none; }
  .march-t { font-size:10.5px; font-weight:bold; color:#14432F; line-height:1.2; }
  .march-sum { font-size:8.5px; color:#1F2937; line-height:1.45; margin-top:3px; }
  .march-meta { font-size:8.5px; color:#374151; margin-top:4px; line-height:1.55; }
  .march-meta b { color:#4C1D95; }
  .march-src { font-size:7.5px; color:#6B7280; margin-top:3px; font-style:italic; }
  .lvl { display:inline-block; color:#fff; font-size:7px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; padding:2px 8px; border-radius:3px; float:right; }
  /* Banda de cifras */
  .stat { text-align:center; border-radius:6px; padding:8px 4px; }
  .stat .num { font-size:20px; font-weight:bold; line-height:1; }
  .stat .lab { font-size:7px; letter-spacing:.5px; text-transform:uppercase; margin-top:3px; color:#4B5563; }
  /* Semáforo / listas */
  .leg { font-size:8.5px; color:#374151; line-height:1.5; }
  .dot { display:inline-block; width:9px; height:9px; border-radius:50%; vertical-align:middle; margin-right:5px; }
  .ul { font-size:8.5px; color:#1F2937; line-height:1.6; }
  .ul .ic { color:#16A34A; margin-right:4px; }
  .lines td { text-align:center; padding:4px 2px; }
  .lines .n { font-size:16px; font-weight:bold; color:#4C1D95; line-height:1; }
  .lines .l { font-size:7px; text-transform:uppercase; letter-spacing:.5px; color:#4B5563; margin-top:2px; }
</style>
</head>
<body>
@php
  $lvlC = fn($x) => ['ALTO'=>'#DC2626','MEDIO'=>'#EA580C','BAJO'=>'#16A34A'][mb_strtoupper((string)$x)] ?? '#6B7280';
  $porCiudad = $events->groupBy('city');
  $total = $events->count();
  $nCiudades = $porCiudad->count();
  $byLevel = ['ALTO'=>0,'MEDIO'=>0,'BAJO'=>0];
  foreach ($events as $e) { $k = mb_strtoupper((string) $e->level); if (isset($byLevel[$k])) { $byLevel[$k]++; } }
  $fmtFecha = function($e) {
    $partes = [];
    if ($e->event_date) { $partes[] = \Illuminate\Support\Carbon::parse($e->event_date)->locale('es')->isoFormat('D MMM'); }
    if ($e->event_time) { $partes[] = $e->event_time; }
    return implode(' · ', $partes);
  };
@endphp

  {{-- HEADER --}}
  <table class="hd">
    <tr>
      <td style="width:38%;">
        @if($logoDataUri)<img src="{{ $logoDataUri }}" class="logo" alt="Grupo Altum">@endif
        <span style="display:inline-block; vertical-align:middle; margin-left:7px;">
          <span class="brand">Grupo Altum</span>
          <span class="brand-sub" style="display:block;">Estrategia de Vigilancia Integrada</span>
        </span>
      </td>
      <td style="width:38%;">
        <div class="tt">Marchas y Movilizaciones</div>
        <div class="ts">Boletín temático · Monitoreo de protesta social</div>
      </td>
      <td style="width:24%; text-align:center;">
        <div class="dbx">
          <div class="d">{{ $generatedAt->format('d') }}</div>
          <div class="m">{{ mb_strtoupper($generatedAt->locale('es')->isoFormat('MMM')) }}</div>
          <div class="y">{{ $generatedAt->format('Y') }}</div>
        </div>
      </td>
    </tr>
  </table>
  <div class="subbar" style="background:#4C1D95;"><span class="ic" style="color:#F0B429;">&#xf0a1;</span> &nbsp;{{ $bulletin->total_marches }} marcha(s) · {{ $bulletin->cities_affected }} ciudad(es) · Actualizado {{ $generatedAt->format('H:i') }} hora Colombia</div>

  <div class="wrap">

    {{-- BANDA DE CIFRAS --}}
    <table style="width:100%; border-collapse:separate; border-spacing:5px 0; margin-bottom:8px;">
      <tr>
        <td style="width:20%;"><div class="stat" style="background:#F3EEFB;"><div class="num" style="color:#4C1D95;">{{ $total }}</div><div class="lab">Marchas</div></div></td>
        <td style="width:20%;"><div class="stat" style="background:#EEF4F0;"><div class="num" style="color:#14432F;">{{ $nCiudades }}</div><div class="lab">Ciudades</div></div></td>
        <td style="width:20%;"><div class="stat" style="background:#FDECEC;"><div class="num" style="color:#DC2626;">{{ $byLevel['ALTO'] }}</div><div class="lab">Nivel Alto</div></div></td>
        <td style="width:20%;"><div class="stat" style="background:#FDEEE3;"><div class="num" style="color:#EA580C;">{{ $byLevel['MEDIO'] }}</div><div class="lab">Nivel Medio</div></div></td>
        <td style="width:20%;"><div class="stat" style="background:#E9F7EE;"><div class="num" style="color:#16A34A;">{{ $byLevel['BAJO'] }}</div><div class="lab">Nivel Bajo</div></div></td>
      </tr>
    </table>

    {{-- RESUMEN + SEMÁFORO --}}
    <table style="width:100%; border-collapse:separate; border-spacing:5px 0; margin-bottom:8px;">
      <tr>
        <td style="width:62%; vertical-align:top;">
          <div class="card">
            <div class="ch march"><span class="ic">&#xf0a1;</span>{{ $bulletin->headline ?: 'Panorama de movilizaciones del día' }}</div>
            <div class="cb">
              @if($bulletin->conclusion)<div class="bl">{{ $bulletin->conclusion }}</div>@endif
              @if($bulletin->recommendation)<div class="bl"><span class="ic" style="color:#16A34A;">&#xf00c;</span><b>Recomendación:</b> {{ $bulletin->recommendation }}</div>@endif
              @if(!$bulletin->conclusion && !$bulletin->recommendation)<div class="bl" style="color:#6B7280;">Monitoreo permanente de la protesta social en las ciudades de mayor movilización.</div>@endif
            </div>
          </div>
        </td>
        <td style="width:38%; vertical-align:top;">
          <div class="card">
            <div class="ch"><span class="ic" style="color:#F0B429;">&#xf0eb;</span>Nivel de impacto</div>
            <div class="cb leg">
              <div><span class="dot" style="background:#DC2626;"></span><b>ALTO</b> — bloqueos totales, disturbios o afectación mayor de vías.</div>
              <div><span class="dot" style="background:#EA580C;"></span><b>MEDIO</b> — concentraciones y cierres parciales con congestión.</div>
              <div><span class="dot" style="background:#16A34A;"></span><b>BAJO</b> — plantón o marcha pequeña, afectación leve.</div>
            </div>
          </div>
        </td>
      </tr>
    </table>

    {{-- MARCHAS POR CIUDAD --}}
    @forelse($porCiudad as $ciudad => $marchas)
      <div class="card" style="margin-bottom:8px;">
        <div class="m-city">{{ $ciudad }} <span class="n">{{ count($marchas) }} marcha(s)</span></div>
        <div class="cb">
          @foreach($marchas as $e)
            <div class="march">
              <div class="march-t">{{ $e->title }}@if($e->level)<span class="lvl" style="background:{{ $lvlC($e->level) }};">{{ $e->level }}</span>@endif</div>
              @if($e->summary)<div class="march-sum">{{ $e->summary }}</div>@endif
              <div class="march-meta">
                @if($fmtFecha($e))<span><b>Cuándo:</b> {{ $fmtFecha($e) }}</span><br>@endif
                @if($e->convener)<span><b>Convoca:</b> {{ $e->convener }}</span><br>@endif
                @if($e->concentration_point)<span><b>Punto de concentración:</b> {{ $e->concentration_point }}</span><br>@endif
                @if($e->route)<span><b>Recorrido:</b> {{ $e->route }}</span><br>@endif
                @if($e->affected_roads)<span><b>Vías afectadas:</b> {{ $e->affected_roads }}</span>@endif
              </div>
              @if(!$e->convener && !$e->concentration_point && !$e->route && !$e->affected_roads)
                <div class="march-meta" style="color:#9CA3AF;">Detalle de recorrido y punto de concentración sin confirmar por la fuente.</div>
              @endif
              @if($e->media_outlet)<div class="march-src">Fuente: {{ $e->media_outlet }}</div>@endif
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="card"><div class="cb"><div class="bl" style="color:#6B7280;">No se reportaron marchas ni movilizaciones en las ciudades monitoreadas para el período.</div></div></div>
    @endforelse

    {{-- PANELES DE APOYO --}}
    <table style="width:100%; border-collapse:separate; border-spacing:5px 0; margin-top:2px;">
      <tr>
        <td style="width:34%; vertical-align:top;">
          <div class="card">
            <div class="ch"><span class="ic" style="color:#EA580C;">&#xf071;</span>Posibles afectaciones</div>
            <div class="cb ul">
              <div><span class="ic" style="color:#EA580C;">&#xf192;</span>Bloqueos y cierres temporales de vías.</div>
              <div><span class="ic" style="color:#EA580C;">&#xf192;</span>Congestión en corredores principales.</div>
              <div><span class="ic" style="color:#EA580C;">&#xf192;</span>Aglomeraciones en espacios públicos.</div>
              <div><span class="ic" style="color:#EA580C;">&#xf192;</span>Alteración de rutas de transporte público.</div>
            </div>
          </div>
        </td>
        <td style="width:34%; vertical-align:top;">
          <div class="card">
            <div class="ch"><span class="ic" style="color:#16A34A;">&#xf00c;</span>Recomendaciones</div>
            <div class="cb ul">
              <div><span class="ic">&#xf00c;</span>Verifique rutas alternas antes de desplazarse.</div>
              <div><span class="ic">&#xf00c;</span>Evite los puntos de concentración señalados.</div>
              <div><span class="ic">&#xf00c;</span>Mantenga comunicación con la Central de Monitoreo.</div>
              <div><span class="ic">&#xf00c;</span>Reporte novedades de movilidad de inmediato.</div>
            </div>
          </div>
        </td>
        <td style="width:32%; vertical-align:top;">
          <div class="card">
            <div class="ch"><span class="ic" style="color:#4C1D95;">&#xf095;</span>Líneas de atención</div>
            <div class="cb">
              <table class="lines" style="width:100%;">
                <tr>
                  <td><div class="n">123</div><div class="l">Policía</div></td>
                  <td><div class="n">165</div><div class="l">Gaula</div></td>
                  <td><div class="n">767</div><div class="l">Antiexplosivos</div></td>
                </tr>
              </table>
              <div style="text-align:center; font-size:8px; color:#6B7280; margin-top:5px;">Reporte oportuno, veraz y claro.</div>
            </div>
          </div>
        </td>
      </tr>
    </table>

  </div>

  {{-- FOOTER --}}
  <div class="footer" style="border-top-color:#7C3AED;">
    Grupo Altum · Monitoreo de Protesta Social y Movilidad · Generado {{ $generatedAt->format('d/m/Y H:i') }}
    <span class="tl">Seguridad con Propósito · Confianza Total</span>
  </div>
</body>
</html>
