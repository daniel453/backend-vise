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
  .march-meta { font-size:8.5px; color:#374151; margin-top:3px; line-height:1.5; }
  .march-meta b { color:#4C1D95; }
  .lvl { display:inline-block; color:#fff; font-size:7px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; padding:2px 8px; border-radius:3px; float:right; }
</style>
</head>
<body>
@php
  $lvlC = fn($x) => ['ALTO'=>'#DC2626','MEDIO'=>'#EA580C','BAJO'=>'#16A34A'][mb_strtoupper((string)$x)] ?? '#6B7280';
  $porCiudad = $events->groupBy('city');
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

    @if($bulletin->headline || $bulletin->conclusion)
      <div class="card" style="margin-bottom:9px;">
        <div class="ch march"><span class="ic">&#xf0a1;</span>{{ $bulletin->headline ?: 'Panorama de movilizaciones del día' }}</div>
        <div class="cb">
          @if($bulletin->conclusion)<div class="bl">{{ $bulletin->conclusion }}</div>@endif
          @if($bulletin->recommendation)<div class="bl"><span class="ic" style="color:#16A34A;">&#xf00c;</span><b>Recomendación:</b> {{ $bulletin->recommendation }}</div>@endif
        </div>
      </div>
    @endif

    @forelse($porCiudad as $ciudad => $marchas)
      <div class="card" style="margin-bottom:8px;">
        <div class="m-city">{{ $ciudad }} <span class="n">{{ count($marchas) }} marcha(s)</span></div>
        <div class="cb">
          @foreach($marchas as $e)
            <div class="march">
              <div class="march-t">{{ $e->title }}@if($e->level)<span class="lvl" style="background:{{ $lvlC($e->level) }};">{{ $e->level }}</span>@endif</div>
              <div class="march-meta">
                @if($fmtFecha($e))<span><b>Cuándo:</b> {{ $fmtFecha($e) }}</span><br>@endif
                @if($e->convener)<span><b>Convoca:</b> {{ $e->convener }}</span><br>@endif
                @if($e->concentration_point)<span><b>Concentración:</b> {{ $e->concentration_point }}</span><br>@endif
                @if($e->route)<span><b>Recorrido:</b> {{ $e->route }}</span><br>@endif
                @if($e->affected_roads)<span><b>Vías afectadas:</b> {{ $e->affected_roads }}</span>@endif
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="card"><div class="cb"><div class="bl" style="color:#6B7280;">No se reportaron marchas ni movilizaciones en las ciudades monitoreadas para el período.</div></div></div>
    @endforelse

  </div>

  {{-- FOOTER --}}
  <div class="footer" style="border-top-color:#7C3AED;">
    Grupo Altum · Monitoreo de Protesta Social y Movilidad · Generado {{ $generatedAt->format('d/m/Y H:i') }}
    <span class="tl">Seguridad con Propósito · Confianza Total</span>
  </div>
</body>
</html>
