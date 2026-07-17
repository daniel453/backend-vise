<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  @page { margin: 22px 26px; }
  * { box-sizing: border-box; }
  body { font-family: 'DejaVu Sans', sans-serif; color: #1E293B; font-size: 11px; line-height: 1.45; margin: 0; }
  .muted { color: #64748B; }
  h1, h2, h3 { margin: 0; }

  .header { background: #0A2540; color: #fff; padding: 16px 18px; border-radius: 8px; }
  .brand { font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #9db4cc; }
  .scope { font-size: 22px; font-weight: bold; margin-top: 2px; }
  .datetime { font-size: 10px; color: #b8c6d6; margin-top: 3px; }
  .headline { font-size: 11px; font-weight: bold; color: #FFD9A8; margin-top: 6px; }

  .stats { width: 100%; border-collapse: separate; border-spacing: 6px; margin-top: 12px; }
  .stats td { background: #F4F6FB; border: 1px solid #E2E8F0; border-radius: 6px; padding: 8px 6px; text-align: center; width: 20%; }
  .stat-n { font-size: 22px; font-weight: bold; color: #0A2540; }
  .stat-l { font-size: 8px; color: #64748B; text-transform: uppercase; letter-spacing: .04em; }
  .n-red { color: #B91C1C; } .n-orange { color: #E8750A; } .n-blue { color: #2851A3; } .n-green { color: #16A34A; }

  .section { margin-top: 14px; }
  .bar { background: #0A2540; color: #fff; padding: 6px 12px; font-size: 10px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; border-radius: 5px 5px 0 0; }
  .bar.tm { background: #B91C1C; } .bar.env { background: #0A3D2E; }
  .box { border: 1px solid #E2E8F0; border-top: none; border-radius: 0 0 5px 5px; }

  .tac { border: 1px solid #E2E8F0; border-left: 4px solid #B91C1C; border-radius: 6px; padding: 12px; margin-top: 12px; }
  .tac-label { font-size: 8px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #64748B; }
  .tac-title { font-size: 14px; font-weight: bold; color: #0A2540; margin: 4px 0; }

  .evento { padding: 9px 12px; border-bottom: 1px solid #EDF1F7; }
  .evento-t { font-size: 12px; font-weight: bold; color: #0A2540; }
  .evento-d { font-size: 10.5px; color: #334155; margin: 3px 0 5px; }
  .tag { display: inline-block; font-size: 8px; font-weight: bold; padding: 2px 7px; border-radius: 10px; text-transform: uppercase; margin-right: 4px; }
  .tag-critico { background: #FEE2E2; color: #991B1B; } .tag-alto { background: #FFEDD5; color: #9A3412; }
  .tag-medio { background: #DBEAFE; color: #1E40AF; } .tag-bajo { background: #F1F5F9; color: #475569; }
  .tag-sub { background: #EDE9FE; color: #5B21B6; } .tag-geo { background: #DCFCE7; color: #166534; }
  .tag-src { background: #0A2540; color: #fff; }

  table.data { width: 100%; border-collapse: collapse; }
  table.data th { background: #12324f; color: #cfe0f2; font-size: 8px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 6px 10px; text-align: left; }
  table.data td { padding: 6px 10px; border-bottom: 1px solid #EDF1F7; font-size: 10px; color: #334155; }
  .pill { display: inline-block; font-size: 8px; font-weight: bold; padding: 2px 7px; border-radius: 10px; }
  .pill-normal { background: #DCFCE7; color: #166534; } .pill-alerta { background: #FEF3C7; color: #92400E; }
  .pill-restringido { background: #FFEDD5; color: #9A3412; } .pill-cerrado { background: #FEE2E2; color: #991B1B; }

  .recs { margin-top: 12px; }
  .rec { font-size: 10.5px; color: #334155; margin-bottom: 6px; }
  .rec b { color: #0A2540; }
  .footer { margin-top: 16px; text-align: center; font-size: 9px; color: #64748B; }
  .footer b { color: #B91C1C; letter-spacing: 1px; }
</style>
</head>
<body>
@php
  $levelLabel = ['national'=>'NACIONAL','region'=>'REGIÓN','department'=>'DEPARTAMENTO','municipality'=>'MUNICIPIO'][$scopeLevel] ?? strtoupper($scopeLevel);
  $sevClass = fn($s) => ['CRÍTICO'=>'tag-critico','ALTO'=>'tag-alto','MEDIO'=>'tag-medio','BAJO'=>'tag-bajo'][$s] ?? 'tag-bajo';
  $pillClass = fn($e) => 'pill-'.strtolower($e ?? 'alerta');
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

<div class="tac">
  <div class="tac-label">Inteligencia Táctica</div>
  <div class="tac-title">{{ $bulletin->main_threat ?? 'Sin amenaza principal registrada' }}</div>
  <div>
    @if($bulletin->critical_zone)Zona crítica: <b>{{ $bulletin->critical_zone }}</b>. @endif
    Tendencia: <b style="color:#B91C1C">{{ $bulletin->trend ?? '—' }}</b>.
    {{ $bulletin->critical_events }} crítico(s) de {{ $bulletin->total_events }} evento(s).
  </div>
</div>

<div class="section">
  <div class="bar">Seguridad y Orden Público — {{ $securityEvents->count() }} eventos</div>
  <div class="box">
    @forelse($securityEvents as $e)
      <div class="evento">
        <div class="evento-t">{{ $e->title }}</div>
        @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
        <div>
          @if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif
          @if($e->subtype)<span class="tag tag-sub">{{ $e->subtype }}</span>@endif
          @if($e->municipality || $e->department)<span class="tag tag-geo">{{ $e->municipality ? $e->municipality.', ' : '' }}{{ $e->department }}</span>@endif
          @if($e->media_outlet)<span class="tag tag-src">{{ $e->media_outlet }}</span>@endif
        </div>
      </div>
    @empty
      <div class="evento muted">Sin eventos de seguridad reportados en este scope.</div>
    @endforelse
  </div>
</div>

@if($environmental->count())
<div class="section">
  <div class="bar env">Alertas Ambientales — {{ $environmental->count() }} activa(s)</div>
  <div class="box">
    @foreach($environmental as $e)
      <div class="evento">
        <div class="evento-t">{{ $e->subtype ?? 'Alerta' }} — {{ $e->department ?? 'Colombia' }}</div>
        @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
      </div>
    @endforeach
  </div>
</div>
@endif

@if($trafficTm->count())
<div class="section">
  <div class="bar tm">Estaciones de TransMilenio — {{ $trafficTm->count() }} afectada(s)</div>
  <table class="data">
    <thead><tr><th>Estación / Corredor</th><th>Estatus</th><th>Observaciones</th></tr></thead>
    <tbody>
      @foreach($trafficTm as $e)
        <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

@if($trafficOther->count())
<div class="section">
  <div class="bar">Estado de Conectividad Vial — {{ $trafficOther->count() }} corredor(es)</div>
  <table class="data">
    <thead><tr><th>Corredor</th><th>Región</th><th>Estatus</th><th>Observaciones</th></tr></thead>
    <tbody>
      @foreach($trafficOther as $e)
        <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td>{{ $e->details['region'] ?? $e->department }}</td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

@if($bulletin->logistics_recommendation || $bulletin->perimeter_recommendation || $bulletin->operational_recommendation || $bulletin->digital_recommendation)
<div class="section recs">
  <div class="bar">Recomendaciones</div>
  <div class="box" style="padding:12px;">
    @if($bulletin->logistics_recommendation)<div class="rec"><b>LOGÍSTICA:</b> {{ $bulletin->logistics_recommendation }}</div>@endif
    @if($bulletin->perimeter_recommendation)<div class="rec"><b>PERÍMETROS:</b> {{ $bulletin->perimeter_recommendation }}</div>@endif
    @if($bulletin->operational_recommendation)<div class="rec"><b>OPERACIONAL:</b> {{ $bulletin->operational_recommendation }}</div>@endif
    @if($bulletin->digital_recommendation)<div class="rec"><b>DIGITAL:</b> {{ $bulletin->digital_recommendation }}</div>@endif
  </div>
</div>
@endif

<div class="footer">
  Documento de Inteligencia · VISE-ALTUM · Generado {{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y H:i') }}
  <br><b>CONFIDENCIAL · RESERVADO</b>
</div>
</body>
</html>
