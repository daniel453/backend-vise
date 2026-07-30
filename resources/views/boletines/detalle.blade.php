<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boletín · {{ $scope }} — VISE Ltda</title>
@include('boletines._web_base')
<style>
  .crumbs{font-size:12px;color:var(--muted);margin-bottom:12px;}
  .crumbs a{color:var(--g-700);font-weight:600;} .crumbs a:hover{text-decoration:underline;}
  .crumbs .sep{color:#C3D0C6;margin:0 6px;}

  /* Header */
  .header{background:linear-gradient(135deg,var(--g-800),var(--g-900));color:#fff;border-radius:18px;padding:24px 26px;border-left:6px solid var(--gold);box-shadow:var(--shadow);}
  .hr-brand{font-size:11px;font-weight:800;letter-spacing:2.5px;text-transform:uppercase;color:var(--gold-2);}
  .hr-scope{font-size:27px;font-weight:900;line-height:1.1;margin-top:3px;}
  .hr-datetime{font-size:13px;color:rgba(255,255,255,.72);margin-top:6px;}
  .hr-datetime i{color:var(--gold-2);margin-right:5px;}
  .hr-headline{font-size:13px;font-weight:600;color:var(--gold-2);margin-top:9px;max-width:760px;line-height:1.45;}
  .pdf-btn{display:inline-flex;align-items:center;gap:7px;margin-top:14px;background:var(--gold);color:#241a02;font-size:12.5px;font-weight:700;padding:9px 15px;border-radius:9px;}
  .pdf-btn:hover{background:var(--gold-2);}

  /* Drill */
  .drill{background:var(--white);border:1px solid var(--line);border-radius:var(--radius);padding:13px 15px;margin-top:16px;box-shadow:var(--shadow-sm);}
  .drill-t{font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--slate);margin-bottom:9px;display:flex;align-items:center;gap:7px;}
  .drill-t i{color:var(--g-600);}
  .chip{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:var(--g-800);background:#EAF3EC;border:1px solid #D3E6D8;border-radius:20px;padding:5px 12px;margin:0 6px 6px 0;}
  .chip:hover{background:#DCEDDF;}
  .chip .b{color:var(--red);} .chip .b0{color:var(--muted);}

  /* Stats */
  .stats{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-top:16px;}
  @media(max-width:760px){.stats{grid-template-columns:repeat(2,1fr);}}
  .stat{background:var(--white);border:1px solid var(--line);border-radius:var(--radius);padding:15px 16px;box-shadow:var(--shadow-sm);}
  .stat-ic{font-size:15px;color:var(--g-600);}
  .stat-n{font-size:31px;font-weight:900;line-height:1;color:var(--g-800);margin-top:8px;}
  .stat-n.red{color:var(--red);} .stat-n.orange{color:var(--orange);} .stat-n.blue{color:var(--blue);} .stat-n.green{color:var(--ok);} .stat-n.purple{color:var(--purple);}
  .stat-l{font-size:11px;color:var(--muted);font-weight:600;margin-top:6px;text-transform:uppercase;letter-spacing:.04em;}

  /* Layout */
  .layout{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-top:16px;}
  @media(max-width:900px){.layout{grid-template-columns:1fr;}}
  .panel{background:var(--white);border:1px solid var(--line);border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow-sm);}
  .panel + .panel{margin-top:16px;}
  .panel-head{background:var(--g-800);color:#fff;padding:10px 16px;font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;display:flex;justify-content:space-between;align-items:center;gap:8px;}
  .panel-head i{color:var(--gold-2);margin-right:6px;}
  .panel-head.tm{background:var(--red);} .panel-head.env{background:var(--g-600);}
  .panel-head .n{font-weight:700;color:rgba(255,255,255,.75);letter-spacing:.5px;}

  .tac-card{padding:17px;border-left:5px solid var(--gold);}
  .tac-label{font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--slate);}
  .tac-title{font-size:16px;font-weight:800;color:var(--g-800);margin:6px 0;}
  .tac-body{font-size:13px;color:var(--slate);}
  .tac-body b{color:var(--g-800);}

  /* Eventos */
  .evento{padding:15px 16px;border-bottom:1px solid var(--line-2);}
  .evento:last-child{border-bottom:none;}
  .evento-h{display:flex;justify-content:space-between;gap:10px;align-items:flex-start;}
  .evento-t{font-size:14.5px;font-weight:800;color:var(--g-800);}
  .evento-f{font-size:11px;color:var(--muted);white-space:nowrap;}
  .evento-f i{margin-right:4px;}
  .evento-d{font-size:13px;color:var(--slate);margin:7px 0 9px;}
  .tags{display:flex;flex-wrap:wrap;gap:6px;align-items:center;}
  .tag{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.03em;display:inline-flex;align-items:center;gap:4px;}
  .tag-critico{background:#FEE2E2;color:#991B1B;} .tag-alto{background:#FFEDD5;color:#9A3412;}
  .tag-medio{background:#DBEAFE;color:#1E40AF;} .tag-bajo{background:#F1F5F9;color:#475569;}
  .tag-sub{background:#EDE9FE;color:#5B21B6;} .tag-geo{background:#DCFCE7;color:#166534;}
  /* Fuente (de qué página se tomó) */
  .src{display:inline-flex;align-items:center;gap:6px;font-size:11.5px;font-weight:600;color:var(--g-700);background:#EAF3EC;border:1px solid #D3E6D8;border-radius:7px;padding:5px 10px;margin-top:9px;}
  .src:hover{background:#DCEDDF;}
  .src i{color:var(--gold);} .src b{color:var(--g-800);font-weight:800;} .src .dom{color:var(--muted);font-weight:500;}
  .src-plain{cursor:default;}

  /* Tablas de vías */
  table{width:100%;border-collapse:collapse;}
  th{background:var(--g-700);color:rgba(255,255,255,.85);font-size:9px;font-weight:700;letter-spacing:1.2px;text-transform:uppercase;padding:9px 14px;text-align:left;}
  td{padding:10px 14px;border-bottom:1px solid var(--line-2);font-size:12.5px;color:var(--slate);}
  tr:nth-child(even) td{background:#F7FAF7;}
  td b{color:var(--g-800);}
  .pill{font-size:10px;font-weight:800;padding:3px 9px;border-radius:20px;}
  .pill-normal{background:#DCFCE7;color:#166534;} .pill-alerta{background:#FEF3C7;color:#92400E;}
  .pill-restringido{background:#FFEDD5;color:#9A3412;} .pill-cerrado{background:#FEE2E2;color:#991B1B;}

  /* Sidebar */
  .sb{padding:16px;}
  .sb-t{font-size:10.5px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--g-800);border-bottom:2px solid var(--g-700);padding-bottom:7px;margin-bottom:13px;display:flex;align-items:center;gap:7px;}
  .sb-t i{color:var(--gold);}
  .rec{display:flex;gap:9px;font-size:12.5px;color:var(--slate);margin-bottom:11px;}
  .rec i{color:var(--g-600);margin-top:2px;} .rec b{color:var(--g-800);}
  .tac-item{border-left:3px solid var(--line);padding:5px 0 5px 11px;margin-bottom:11px;}
  .tac-item .lvl{font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;}
  .tac-item .lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;}
  .tac-item .val{font-size:13px;color:var(--slate);}
  .dist{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px dashed var(--line);font-size:12.5px;}
  .dist:last-child{border-bottom:none;}
  .dist .ev{font-weight:800;color:var(--g-700);}

  .empty{padding:40px 16px;text-align:center;color:var(--muted);font-size:14px;}
</style>
</head>
<body>
@php
  $levelLabel = ['national'=>'NACIONAL','region'=>'REGIÓN','department'=>'DEPARTAMENTO','municipality'=>'MUNICIPIO'][$scopeLevel] ?? strtoupper($scopeLevel);
  $sevClass = fn($s) => ['CRÍTICO'=>'tag-critico','ALTO'=>'tag-alto','MEDIO'=>'tag-medio','BAJO'=>'tag-bajo'][$s] ?? 'tag-bajo';
  $pillClass = fn($e) => 'pill-'.strtolower($e ?? 'alerta');
  $childTitle = ['region'=>'Explora por región','departamento'=>'Explora por departamento','municipio'=>'Explora por municipio'][$childLevelSlug] ?? '';
  $host = function($url){ $h = parse_url((string)$url, PHP_URL_HOST); return $h ? preg_replace('/^www\./','',$h) : null; };
@endphp

@include('boletines._web_topbar')

<div class="wrap">

  <div class="crumbs">
    @foreach($breadcrumb as $i => $c)
      @if($i < count($breadcrumb)-1)
        <a href="{{ route('boletin', ['level'=>$c['level'],'scope'=>$c['scope']]) }}">{{ $c['label'] }}</a><span class="sep">›</span>
      @else
        <span>{{ $c['label'] }}</span>
      @endif
    @endforeach
    <span class="sep">·</span><a href="{{ route('home') }}"><i class="fa-solid fa-house" style="font-size:10px;"></i> Inicio</a>
  </div>

  <div class="header">
    <div class="hr-brand">Monitoreo Estratégico — {{ $levelLabel }}</div>
    <div class="hr-scope">{{ $scope }}</div>
    <div class="hr-datetime">
      <i class="fa-regular fa-clock"></i>@if($bulletin){{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->locale('es')->isoFormat('D [de] MMMM [de] YYYY · HH:mm') }} · hora Colombia@else Sin boletín generado @endif
    </div>
    @if($bulletin?->headline)<div class="hr-headline">{{ $bulletin->headline }}</div>@endif
    @if($bulletin)<a class="pdf-btn" href="{{ route('boletin.pdf', ['level'=>$level, 'scope'=>$scopeLevel==='national'?null:$scope]) }}" target="_blank"><i class="fa-solid fa-file-arrow-down"></i> Exportar PDF</a>@endif
  </div>

  @if(!$bulletin)
    <div class="panel" style="margin-top:16px;"><div class="empty">No hay un boletín generado para <b>{{ $scope }}</b>. Vuelve al <a href="{{ route('home') }}" style="color:var(--g-700);font-weight:700;">inicio</a> y elige otra zona.</div></div>
  @else

  @if($children->count())
  <div class="drill">
    <div class="drill-t"><i class="fa-solid fa-diagram-project"></i> {{ $childTitle }}</div>
    @foreach($children as $ch)
      <a class="chip" href="{{ route('boletin', ['level'=>$childLevelSlug,'scope'=>$ch->scope]) }}">
        <i class="fa-solid fa-location-dot" style="color:var(--g-600);font-size:10px;"></i> {{ $ch->scope }} <span class="{{ $ch->critical_events>0?'b':'b0' }}">{{ $ch->total_events }}</span>
      </a>
    @endforeach
  </div>
  @endif

  <div class="stats">
    <div class="stat"><i class="fa-solid fa-layer-group stat-ic"></i><div class="stat-n">{{ $stats['events'] }}</div><div class="stat-l">Eventos</div></div>
    <div class="stat"><i class="fa-solid fa-location-dot stat-ic"></i><div class="stat-n blue">{{ $stats['areas'] }}</div><div class="stat-l">{{ $scopeLevel==='national'?'Regiones':'Zonas' }}</div></div>
    <div class="stat"><i class="fa-solid fa-road stat-ic"></i><div class="stat-n orange">{{ $stats['roads'] }}</div><div class="stat-l">Vías afectadas</div></div>
    <div class="stat"><i class="fa-solid fa-train-subway stat-ic"></i><div class="stat-n purple">{{ $stats['transmilenio'] }}</div><div class="stat-l">TransMilenio</div></div>
    <div class="stat"><i class="fa-solid fa-cloud-showers-heavy stat-ic"></i><div class="stat-n green">{{ $stats['environmental'] }}</div><div class="stat-l">Alertas ambientales</div></div>
  </div>

  <div class="layout">
    <div>
      <div class="panel">
        <div class="tac-card">
          <div class="tac-label">Inteligencia Táctica</div>
          <div class="tac-title">{{ $bulletin->main_threat ?? 'Sin amenaza principal registrada' }}</div>
          <div class="tac-body">
            @if($bulletin->critical_zone)Zona crítica: <b>{{ $bulletin->critical_zone }}</b>. @endif
            Tendencia: <b>{{ $bulletin->trend ?? '—' }}</b>.
            {{ $bulletin->critical_events }} crítico(s) de {{ $bulletin->total_events }} evento(s).
          </div>
        </div>
        <div class="panel-head"><span><i class="fa-solid fa-bolt"></i>Reporte de Novedades · Seguridad y Orden Público</span><span class="n">{{ $securityEvents->count() }} eventos</span></div>
        @forelse($securityEvents as $e)
          <div class="evento">
            <div class="evento-h">
              <div class="evento-t">{{ $e->title }}</div>
              @if($e->details['fecha_evento'] ?? null)<div class="evento-f"><i class="fa-regular fa-calendar"></i>{{ $e->details['fecha_evento'] }}</div>@endif
            </div>
            @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
            <div class="tags">
              @if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif
              @if($e->subtype)<span class="tag tag-sub">{{ $e->subtype }}</span>@endif
              @if($e->municipality || $e->department)<span class="tag tag-geo"><i class="fa-solid fa-location-dot"></i> {{ $e->municipality ? $e->municipality.', ' : '' }}{{ $e->department }}</span>@endif
            </div>
            @if($e->source_url && \Illuminate\Support\Str::startsWith($e->source_url,'http'))
              <a class="src" href="{{ $e->source_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Fuente: <b>{{ $e->media_outlet ?: $host($e->source_url) }}</b>@if($e->media_outlet && $host($e->source_url))<span class="dom">· {{ $host($e->source_url) }}</span>@endif</a>
            @elseif($e->media_outlet)
              <span class="src src-plain"><i class="fa-solid fa-newspaper"></i> Fuente: <b>{{ $e->media_outlet }}</b></span>
            @endif
          </div>
        @empty
          <div class="empty">Sin eventos de seguridad reportados en este scope.</div>
        @endforelse
      </div>

      @if($environmental->count())
      <div class="panel">
        <div class="panel-head env"><span><i class="fa-solid fa-cloud-showers-heavy"></i>Alertas Ambientales</span><span class="n">{{ $environmental->count() }} activa(s)</span></div>
        @foreach($environmental as $e)
          <div class="evento">
            <div class="evento-t"><i class="fa-solid fa-triangle-exclamation" style="color:var(--amber);margin-right:5px;"></i>{{ $e->subtype ?? 'Alerta' }} — {{ $e->department ?? 'Colombia' }}</div>
            @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
            <div class="tags">@if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif</div>
            @if($e->source_url && \Illuminate\Support\Str::startsWith($e->source_url,'http'))
              <a class="src" href="{{ $e->source_url }}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i> Fuente: <b>{{ $e->media_outlet ?: $host($e->source_url) }}</b></a>
            @elseif($e->media_outlet)
              <span class="src src-plain"><i class="fa-solid fa-newspaper"></i> Fuente: <b>{{ $e->media_outlet }}</b></span>
            @endif
          </div>
        @endforeach
      </div>
      @endif

      @if($trafficTm->count())
      <div class="panel">
        <div class="panel-head tm"><span><i class="fa-solid fa-train-subway"></i>Estaciones de TransMilenio</span><span class="n">{{ $trafficTm->count() }} afectada(s)</span></div>
        <table><thead><tr><th>Estación / Corredor</th><th>Estatus</th><th>Observaciones</th></tr></thead><tbody>
          @foreach($trafficTm as $e)
            <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
          @endforeach
        </tbody></table>
      </div>
      @endif

      @if($trafficOther->count())
      <div class="panel">
        <div class="panel-head"><span><i class="fa-solid fa-road-barrier"></i>Estado de Conectividad Vial</span><span class="n">{{ $trafficOther->count() }} corredor(es)</span></div>
        <table><thead><tr><th>Corredor</th><th>Región</th><th>Estatus</th><th>Observaciones</th></tr></thead><tbody>
          @foreach($trafficOther as $e)
            <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td>{{ $e->details['region'] ?? $e->department }}</td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
          @endforeach
        </tbody></table>
      </div>
      @endif
    </div>

    <div>
      <div class="panel"><div class="sb">
        <div class="sb-t"><i class="fa-solid fa-clipboard-check"></i> Recomendaciones</div>
        @if($bulletin->logistics_recommendation)<div class="rec"><i class="fa-solid fa-truck-fast"></i><span><b>LOGÍSTICA:</b> {{ $bulletin->logistics_recommendation }}</span></div>@endif
        @if($bulletin->perimeter_recommendation)<div class="rec"><i class="fa-solid fa-shield-halved"></i><span><b>PERÍMETROS:</b> {{ $bulletin->perimeter_recommendation }}</span></div>@endif
        @if($bulletin->operational_recommendation)<div class="rec"><i class="fa-solid fa-gears"></i><span><b>OPERACIONAL:</b> {{ $bulletin->operational_recommendation }}</span></div>@endif
        @if($bulletin->digital_recommendation)<div class="rec"><i class="fa-solid fa-wifi"></i><span><b>DIGITAL:</b> {{ $bulletin->digital_recommendation }}</span></div>@endif
        @if(!$bulletin->logistics_recommendation && !$bulletin->operational_recommendation)<div style="font-size:12px;color:var(--muted)">Sin recomendaciones para este scope.</div>@endif
      </div></div>

      <div class="panel"><div class="sb">
        <div class="sb-t"><i class="fa-solid fa-crosshairs"></i> Resumen Táctico</div>
        <div class="tac-item" style="border-color:var(--red)"><div class="lvl" style="color:var(--red)">Amenaza</div><div class="val">{{ $bulletin->main_threat ?? '—' }}</div></div>
        <div class="tac-item" style="border-color:var(--orange)"><div class="lvl" style="color:var(--orange)">Zona Crítica</div><div class="val">{{ $bulletin->critical_zone ?? '—' }}</div></div>
        <div class="tac-item" style="border-color:var(--g-600)"><div class="lvl" style="color:var(--g-700)">Tendencia</div><div class="val">{{ $bulletin->trend ?? '—' }}</div></div>
        @if($bulletin->electoral_context)<div class="tac-item" style="border-color:var(--purple)"><div class="lbl">Contexto electoral</div><div class="val">{{ $bulletin->electoral_context }}</div></div>@endif
      </div></div>

      @php $dist = is_array($bulletin->distribution) ? $bulletin->distribution : []; @endphp
      @if(count($dist))
      <div class="panel"><div class="sb">
        <div class="sb-t"><i class="fa-solid fa-chart-simple"></i> Distribución</div>
        @foreach($dist as $d)
          <div class="dist"><span>{{ $d['ciudad'] ?? $d['city'] ?? '—' }}</span><span class="ev">{{ $d['eventos'] ?? $d['events'] ?? 0 }} ev</span></div>
        @endforeach
      </div></div>
      @endif
    </div>
  </div>

  @endif

</div>

<div class="site-foot">
  <div class="bar"></div>
  Documento de Inteligencia · <span class="mk">VISE Ltda</span> @if($bulletin)· Generado {{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y H:i') }}@endif
  <br><b>CONFIDENCIAL · RESERVADO</b>
</div>
</body>
</html>
