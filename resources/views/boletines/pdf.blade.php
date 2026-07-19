<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 20px 24px; }
  * { box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; color: #1E293B; font-size: 10px; line-height: 1.4; margin: 0; }

  .header { background: #0A2540; color: #fff; padding: 12px 16px; border-radius: 8px; }
  .brand { font-size: 8px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #9db4cc; }
  .scope { font-size: 19px; font-weight: bold; margin-top: 1px; }
  .datetime { font-size: 9px; color: #b8c6d6; margin-top: 2px; }
  .headline { font-size: 11px; font-weight: bold; color: #FFD9A8; margin-top: 5px; }

  .stats { width: 100%; border-collapse: separate; border-spacing: 5px; margin-top: 8px; }
  .stats td { background: #F4F6FB; border: 1px solid #E2E8F0; border-radius: 6px; padding: 6px 4px; text-align: center; width: 20%; }
  .stat-n { font-size: 19px; font-weight: bold; color: #0A2540; }
  .stat-l { font-size: 7px; color: #64748B; text-transform: uppercase; letter-spacing: .03em; }
  .n-red { color: #B91C1C; } .n-orange { color: #E8750A; } .n-blue { color: #2851A3; } .n-green { color: #16A34A; }

  .concl { border: 1px solid #E2E8F0; border-left: 4px solid #B91C1C; border-radius: 6px; padding: 9px 12px; margin-top: 10px; }
  .lbl { font-size: 8px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #64748B; }
  .concl-t { font-size: 13px; font-weight: bold; color: #0A2540; margin: 3px 0; }
  .concl-b { font-size: 10px; color: #334155; }

  .cols { width: 100%; border-collapse: collapse; margin-top: 10px; }
  .cols > tbody > tr > td { vertical-align: top; }
  .col-l { width: 56%; padding-right: 8px; }
  .col-r { width: 44%; padding-left: 8px; }

  .bar { background: #0A2540; color: #fff; padding: 5px 10px; font-size: 9px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; border-radius: 5px 5px 0 0; }
  .box { border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 5px 5px; }
  .evento { padding: 6px 10px; border-bottom: 1px solid #EDF1F7; }
  .evento:last-child { border-bottom: none; }
  .evento-t { font-size: 10.5px; font-weight: bold; color: #0A2540; }
  .evento-d { font-size: 9px; color: #475569; margin: 2px 0 3px; }
  .tag { display: inline-block; font-size: 7px; font-weight: bold; padding: 1px 6px; border-radius: 10px; text-transform: uppercase; margin-right: 3px; }
  .tag-critico { background: #FEE2E2; color: #991B1B; } .tag-alto { background: #FFEDD5; color: #9A3412; }
  .tag-medio { background: #DBEAFE; color: #1E40AF; } .tag-bajo { background: #F1F5F9; color: #475569; }
  .tag-geo { background: #DCFCE7; color: #166534; }
  .more { padding: 6px 10px; font-size: 8.5px; color: #64748B; font-style: italic; }

  .rec-box { border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 5px 5px; padding: 9px 11px; }
  .rec { font-size: 9.5px; color: #334155; margin-bottom: 6px; }
  .rec:last-child { margin-bottom: 0; }
  .rec b { color: #0A2540; }
  .tacrow { font-size: 9px; color: #334155; margin-bottom: 4px; }
  .tacrow b { color: #0A2540; }

  .footer { margin-top: 12px; text-align: center; font-size: 8px; color: #64748B; }
  .footer b { color: #B91C1C; letter-spacing: 1px; }
</style>
</head>
<body>
@php
  $levelLabel = ['national'=>'NACIONAL','region'=>'REGIÓN','department'=>'DEPARTAMENTO','municipality'=>'MUNICIPIO'][$scopeLevel] ?? strtoupper($scopeLevel);
  $sevClass = fn($s) => ['CRÍTICO'=>'tag-critico','ALTO'=>'tag-alto','MEDIO'=>'tag-medio','BAJO'=>'tag-bajo'][$s] ?? 'tag-bajo';
  $topEvents = $securityEvents->take(5);
  $restantes = $securityEvents->count() - $topEvents->count();
  $tieneRecs = $bulletin->logistics_recommendation || $bulletin->perimeter_recommendation || $bulletin->operational_recommendation || $bulletin->digital_recommendation;
@endphp

<div class="header">
  <div class="brand">VISE · Monitoreo Estratégico — {{ $levelLabel }}</div>
  <div class="scope">{{ $scope }}</div>
  <div class="datetime">{{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y · H:i') }}</div>
  @if($bulletin->headline)<div class="headline">{{ $bulletin->headline }}</div>@endif
</div>

<table class="stats">
  <tr>
    <td><div class="stat-n">{{ $stats['events'] }}</div><div class="stat-l">Eventos</div></td>
    <td><div class="stat-n n-blue">{{ $stats['areas'] }}</div><div class="stat-l">{{ $scopeLevel==='national'?'Regiones':'Zonas' }}</div></td>
    <td><div class="stat-n n-orange">{{ $stats['roads'] }}</div><div class="stat-l">Vías afectadas</div></td>
    <td><div class="stat-n n-red">{{ $stats['transmilenio'] }}</div><div class="stat-l">TransMilenio</div></td>
    <td><div class="stat-n n-green">{{ $stats['environmental'] }}</div><div class="stat-l">Ambientales</div></td>
  </tr>
</table>

<div class="concl">
  <div class="lbl">Conclusión</div>
  @if($bulletin->conclusion)
    <div class="concl-b" style="font-size:11px;line-height:1.5;">{{ $bulletin->conclusion }}</div>
  @else
    <div class="concl-t">{{ $bulletin->main_threat ?? $bulletin->headline ?? 'Panorama de seguridad del día' }}</div>
    <div class="concl-b">
      Tendencia <b style="color:#B91C1C">{{ $bulletin->trend ?? '—' }}</b>.
      @if($bulletin->critical_zone) Zona crítica: <b>{{ $bulletin->critical_zone }}</b>.@endif
      {{ $bulletin->total_events }} evento(s), {{ $bulletin->critical_events }} crítico(s)@if($stats['roads']) · {{ $stats['roads'] }} vía(s) afectada(s)@endif@if($stats['environmental']) · {{ $stats['environmental'] }} alerta(s) ambiental(es)@endif.
    </div>
  @endif
</div>

<table class="cols">
  <tr>
    <td class="col-l">
      <div class="bar">Eventos destacados</div>
      <div class="box">
        @forelse($topEvents as $e)
          <div class="evento">
            <div class="evento-t">{{ $e->title }}</div>
            @if($e->summary)<div class="evento-d">{{ \Illuminate\Support\Str::limit($e->summary, 140) }}</div>@endif
            <div>
              @if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif
              @if($e->municipality || $e->department)<span class="tag tag-geo">{{ $e->municipality ? $e->municipality.', ' : '' }}{{ $e->department }}</span>@endif
            </div>
          </div>
        @empty
          <div class="evento evento-d">Sin eventos de seguridad reportados.</div>
        @endforelse
        @if($restantes > 0)
          <div class="more">+ {{ $restantes }} evento(s) más. Detalle completo en la plataforma.</div>
        @endif
      </div>
    </td>
    <td class="col-r">
      <div class="bar">Recomendaciones</div>
      <div class="rec-box">
        @if($tieneRecs)
          @if($bulletin->logistics_recommendation)<div class="rec"><b>LOGÍSTICA:</b> {{ $bulletin->logistics_recommendation }}</div>@endif
          @if($bulletin->perimeter_recommendation)<div class="rec"><b>PERÍMETROS:</b> {{ $bulletin->perimeter_recommendation }}</div>@endif
          @if($bulletin->operational_recommendation)<div class="rec"><b>OPERACIONAL:</b> {{ $bulletin->operational_recommendation }}</div>@endif
          @if($bulletin->digital_recommendation)<div class="rec"><b>DIGITAL:</b> {{ $bulletin->digital_recommendation }}</div>@endif
        @else
          <div class="rec" style="color:#64748B">Sin recomendaciones específicas para esta corrida.</div>
        @endif
      </div>

      <div class="bar" style="margin-top:10px;">Resumen táctico</div>
      <div class="rec-box">
        <div class="tacrow"><b>Amenaza:</b> {{ $bulletin->main_threat ?? '—' }}</div>
        <div class="tacrow"><b>Zona crítica:</b> {{ $bulletin->critical_zone ?? '—' }}</div>
        <div class="tacrow"><b>Tendencia:</b> {{ $bulletin->trend ?? '—' }}</div>
        @if($bulletin->electoral_context)<div class="tacrow"><b>Contexto electoral:</b> {{ $bulletin->electoral_context }}</div>@endif
      </div>
    </td>
  </tr>
</table>

<div class="footer">
  Documento de Inteligencia · VISE-ALTUM · Generado {{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y H:i') }}
  <br><b>CONFIDENCIAL · RESERVADO</b>
</div>
</body>
</html>
