<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boletín · {{ $scope }} — Altum Risk</title>
<style>
  :root{
    --navy:#0A2540; --navy2:#12324f; --red:#DC2626; --red2:#B91C1C; --orange:#E8750A;
    --blue:#2851A3; --green:#16A34A; --purple:#7C3AED; --border:#E2E8F0; --border2:#EDF1F7;
    --text:#1E293B; --text2:#334155; --muted:#64748B; --bg:#F4F6FB; --white:#fff;
  }
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:var(--text);font-family:'Segoe UI',Arial,sans-serif;font-size:14px;line-height:1.5;}
  a{color:inherit;}
  .page{max-width:1160px;margin:0 auto;padding:16px;}

  .crumbs{font-size:12px;color:var(--muted);margin-bottom:10px;}
  .crumbs a{color:var(--blue);text-decoration:none;} .crumbs a:hover{text-decoration:underline;}
  .crumbs .sep{color:#CBD5E1;margin:0 6px;}

  .header{background:var(--navy);color:#fff;border-radius:14px;padding:20px 24px;}
  .hr-brand{font-size:12px;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.6);}
  .hr-scope{font-size:26px;font-weight:900;line-height:1.1;margin-top:2px;}
  .hr-datetime{font-size:12px;color:rgba(255,255,255,.65);margin-top:4px;}
  .hr-headline{font-size:13px;font-weight:700;color:#FFD9A8;margin-top:8px;max-width:720px;}
  .pdf-btn{display:inline-block;margin-top:12px;background:var(--red2);color:#fff;font-size:12px;font-weight:700;padding:8px 14px;border-radius:8px;text-decoration:none;}
  .pdf-btn:hover{background:#a01818;}

  .drill{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:12px 14px;margin-top:14px;}
  .drill-t{font-size:11px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:8px;}
  .chip{display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:700;color:var(--navy);background:#EEF3FB;border:1px solid #DCE6F5;border-radius:20px;padding:5px 12px;margin:0 6px 6px 0;text-decoration:none;}
  .chip:hover{background:#E0EAFB;}
  .chip .b{color:var(--red2);} .chip .b0{color:var(--muted);}

  .stats{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-top:14px;}
  .stat{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:14px 16px;}
  .stat-n{font-size:34px;font-weight:900;line-height:1;color:var(--navy);}
  .stat-n.red{color:var(--red2);} .stat-n.orange{color:var(--orange);} .stat-n.blue{color:var(--blue);} .stat-n.green{color:var(--green);} .stat-n.purple{color:var(--purple);}
  .stat-l{font-size:11px;color:var(--muted);font-weight:600;margin-top:6px;text-transform:uppercase;letter-spacing:.04em;}
  @media(max-width:760px){.stats{grid-template-columns:repeat(2,1fr);}}

  .layout{display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-top:16px;}
  @media(max-width:900px){.layout{grid-template-columns:1fr;}}
  .card{background:var(--white);border:1px solid var(--border);border-radius:12px;overflow:hidden;}
  .card + .card{margin-top:16px;}

  .flash{background:var(--navy);color:#fff;padding:10px 16px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:6px;}
  .flash-t{font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;}
  .flash-b{font-size:11px;color:rgba(255,255,255,.6);font-family:monospace;}

  .tac-card{padding:16px;border-left:5px solid var(--red2);}
  .tac-label{font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--muted);}
  .tac-title{font-size:16px;font-weight:800;color:var(--navy);margin:6px 0;}
  .tac-body{font-size:13px;color:var(--text2);}

  .evento{padding:14px 16px;border-bottom:1px solid var(--border2);}
  .evento:last-child{border-bottom:none;}
  .evento-h{display:flex;justify-content:space-between;gap:10px;align-items:flex-start;}
  .evento-t{font-size:14px;font-weight:800;color:var(--navy);}
  .evento-f{font-size:11px;color:var(--muted);white-space:nowrap;}
  .evento-d{font-size:13px;color:var(--text2);margin:6px 0 8px;}
  .tags{display:flex;flex-wrap:wrap;gap:6px;}
  .tag{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;text-transform:uppercase;letter-spacing:.03em;}
  .tag-critico{background:#FEE2E2;color:#991B1B;} .tag-alto{background:#FFEDD5;color:#9A3412;}
  .tag-medio{background:#DBEAFE;color:#1E40AF;} .tag-bajo{background:#F1F5F9;color:#475569;}
  .tag-sub{background:#EDE9FE;color:#5B21B6;} .tag-geo{background:#DCFCE7;color:#166534;}
  .tag-src{background:var(--navy);color:#fff;text-decoration:none;}

  .sec-bar{background:#0A3D2E;color:#fff;padding:9px 16px;font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;display:flex;justify-content:space-between;}
  .vias-h{background:var(--navy);color:#fff;padding:9px 16px;font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;display:flex;justify-content:space-between;}
  .vias-h.tm{background:var(--red2);}
  table{width:100%;border-collapse:collapse;}
  th{background:var(--navy2);color:rgba(255,255,255,.8);font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:8px 14px;text-align:left;}
  td{padding:9px 14px;border-bottom:1px solid var(--border2);font-size:12px;color:var(--text2);}
  tr:nth-child(even) td{background:#F8FAFF;}
  .pill{font-size:10px;font-weight:800;padding:3px 9px;border-radius:20px;}
  .pill-normal{background:#DCFCE7;color:#166534;} .pill-alerta{background:#FEF3C7;color:#92400E;}
  .pill-restringido{background:#FFEDD5;color:#9A3412;} .pill-cerrado{background:#FEE2E2;color:#991B1B;}

  .sb{padding:16px;}
  .sb-t{font-size:10px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--navy);border-bottom:2px solid var(--navy);padding-bottom:6px;margin-bottom:12px;}
  .rec{display:flex;gap:8px;font-size:12px;color:var(--text2);margin-bottom:10px;}
  .rec b{color:var(--navy);}
  .tac-item{border-left:3px solid var(--border);padding:6px 0 6px 10px;margin-bottom:10px;}
  .tac-item .lvl{font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;}
  .tac-item .lbl{font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;}
  .tac-item .val{font-size:13px;color:var(--text2);}
  .dist{display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed var(--border);font-size:12px;}
  .dist:last-child{border-bottom:none;}
  .dist .ev{font-weight:800;color:var(--orange);}

  .empty{padding:40px 16px;text-align:center;color:var(--muted);font-size:14px;}
  .footer{margin-top:18px;text-align:center;font-size:11px;color:var(--muted);}
  .footer b{color:var(--red2);letter-spacing:1px;}
</style>
</head>
<body>
@php
  $levelLabel = ['national'=>'NACIONAL','region'=>'REGIÓN','department'=>'DEPARTAMENTO','municipality'=>'MUNICIPIO'][$scopeLevel] ?? strtoupper($scopeLevel);
  $sevClass = fn($s) => ['CRÍTICO'=>'tag-critico','ALTO'=>'tag-alto','MEDIO'=>'tag-medio','BAJO'=>'tag-bajo'][$s] ?? 'tag-bajo';
  $pillClass = fn($e) => 'pill-'.strtolower($e ?? 'alerta');
  $childTitle = ['region'=>'Explora por región','departamento'=>'Explora por departamento','municipio'=>'Explora por municipio'][$childLevelSlug] ?? '';
@endphp

<div class="page">

  <div class="crumbs">
    @foreach($breadcrumb as $i => $c)
      @if($i < count($breadcrumb)-1)
        <a href="{{ route('boletin', ['level'=>$c['level'],'scope'=>$c['scope']]) }}">{{ $c['label'] }}</a><span class="sep">›</span>
      @else
        <span>{{ $c['label'] }}</span>
      @endif
    @endforeach
    <span class="sep">·</span><a href="{{ route('home') }}">Inicio</a>
  </div>

  <div class="header">
    <div class="hr-brand">Monitoreo Estratégico — {{ $levelLabel }}</div>
    <div class="hr-scope">{{ $scope }}</div>
    <div class="hr-datetime">
      @if($bulletin){{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y · H:i') }}@else Sin boletín generado @endif
    </div>
    @if($bulletin?->headline)<div class="hr-headline">{{ $bulletin->headline }}</div>@endif
    @if($bulletin)<a class="pdf-btn" href="{{ route('boletin.pdf', ['level'=>$level, 'scope'=>$scopeLevel==='national'?null:$scope]) }}" target="_blank">⬇ Exportar PDF</a>@endif
  </div>

  @if(!$bulletin)
    <div class="card" style="margin-top:16px;"><div class="empty">No hay un boletín generado para <b>{{ $scope }}</b>. Vuelve al <a href="{{ route('home') }}">inicio</a> y elige otra zona.</div></div>
  @else

  @if($children->count())
  <div class="drill">
    <div class="drill-t">{{ $childTitle }}</div>
    @foreach($children as $ch)
      <a class="chip" href="{{ route('boletin', ['level'=>$childLevelSlug,'scope'=>$ch->scope]) }}">
        {{ $ch->scope }} <span class="{{ $ch->critical_events>0?'b':'b0' }}">{{ $ch->total_events }}</span>
      </a>
    @endforeach
  </div>
  @endif

  <div class="stats">
    <div class="stat"><div class="stat-n">{{ $stats['events'] }}</div><div class="stat-l">Eventos</div></div>
    <div class="stat"><div class="stat-n blue">{{ $stats['areas'] }}</div><div class="stat-l">{{ $scopeLevel==='national'?'Regiones':'Zonas' }}</div></div>
    <div class="stat"><div class="stat-n orange">{{ $stats['roads'] }}</div><div class="stat-l">Vías afectadas</div></div>
    <div class="stat"><div class="stat-n purple">{{ $stats['transmilenio'] }}</div><div class="stat-l">🚇 TransMilenio</div></div>
    <div class="stat"><div class="stat-n green">{{ $stats['environmental'] }}</div><div class="stat-l">Alertas ambientales</div></div>
  </div>

  <div class="layout">
    <div>
      <div class="card">
        <div class="tac-card">
          <div class="tac-label">Inteligencia Táctica</div>
          <div class="tac-title">{{ $bulletin->main_threat ?? 'Sin amenaza principal registrada' }}</div>
          <div class="tac-body">
            @if($bulletin->critical_zone)Zona crítica: <b>{{ $bulletin->critical_zone }}</b>. @endif
            Tendencia: <b style="color:var(--red2)">{{ $bulletin->trend ?? '—' }}</b>.
            {{ $bulletin->critical_events }} crítico(s) de {{ $bulletin->total_events }} evento(s).
          </div>
        </div>
        <div class="flash"><span class="flash-t">Reporte de Novedades · Seguridad y Orden Público</span><span class="flash-b">▲ {{ $securityEvents->count() }} eventos</span></div>
        @forelse($securityEvents as $e)
          <div class="evento">
            <div class="evento-h">
              <div class="evento-t">{{ $e->title }}</div>
              <div class="evento-f">{{ $e->details['fecha_evento'] ?? '' }}</div>
            </div>
            @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
            <div class="tags">
              @if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif
              @if($e->subtype)<span class="tag tag-sub">{{ $e->subtype }}</span>@endif
              @if($e->municipality || $e->department)<span class="tag tag-geo">📍 {{ $e->municipality ? $e->municipality.', ' : '' }}{{ $e->department }}</span>@endif
              @if($e->source_url && \Illuminate\Support\Str::startsWith($e->source_url,'http'))<a class="tag tag-src" href="{{ $e->source_url }}" target="_blank" rel="noopener">{{ $e->media_outlet ?? 'Fuente' }}</a>@endif
            </div>
          </div>
        @empty
          <div class="empty">Sin eventos de seguridad reportados en este scope.</div>
        @endforelse
      </div>

      @if($environmental->count())
      <div class="card">
        <div class="sec-bar"><span>🌧 Alertas Ambientales</span><span>{{ $environmental->count() }} activa(s)</span></div>
        @foreach($environmental as $e)
          <div class="evento">
            <div class="evento-t">🌧 {{ $e->subtype ?? 'Alerta' }} — {{ $e->department ?? 'Colombia' }}</div>
            @if($e->summary)<div class="evento-d">{{ $e->summary }}</div>@endif
            <div class="tags">@if($e->severity)<span class="tag {{ $sevClass($e->severity) }}">{{ $e->severity }}</span>@endif @if($e->media_outlet)<span class="tag tag-sub">{{ $e->media_outlet }}</span>@endif</div>
          </div>
        @endforeach
      </div>
      @endif

      @if($trafficTm->count())
      <div class="card">
        <div class="vias-h tm"><span>🚇 Estaciones de TransMilenio</span><span>{{ $trafficTm->count() }} afectada(s)</span></div>
        <table><thead><tr><th>Estación / Corredor</th><th>Estatus</th><th>Observaciones</th></tr></thead><tbody>
          @foreach($trafficTm as $e)
            <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
          @endforeach
        </tbody></table>
      </div>
      @endif

      @if($trafficOther->count())
      <div class="card">
        <div class="vias-h"><span>Estado de Conectividad Vial</span><span>🚧 {{ $trafficOther->count() }} corredor(es)</span></div>
        <table><thead><tr><th>Corredor</th><th>Región</th><th>Estatus</th><th>Observaciones</th></tr></thead><tbody>
          @foreach($trafficOther as $e)
            <tr><td><b>{{ $e->details['via'] ?? $e->title }}</b></td><td>{{ $e->details['region'] ?? $e->department }}</td><td><span class="pill {{ $pillClass($e->details['estado'] ?? null) }}">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td>{{ $e->summary }}</td></tr>
          @endforeach
        </tbody></table>
      </div>
      @endif
    </div>

    <div>
      <div class="card"><div class="sb">
        <div class="sb-t">Recomendaciones</div>
        @if($bulletin->logistics_recommendation)<div class="rec"><span>▲</span><span><b>LOGÍSTICA:</b> {{ $bulletin->logistics_recommendation }}</span></div>@endif
        @if($bulletin->perimeter_recommendation)<div class="rec"><span>◉</span><span><b>PERÍMETROS:</b> {{ $bulletin->perimeter_recommendation }}</span></div>@endif
        @if($bulletin->operational_recommendation)<div class="rec"><span>◎</span><span><b>OPERACIONAL:</b> {{ $bulletin->operational_recommendation }}</span></div>@endif
        @if($bulletin->digital_recommendation)<div class="rec"><span>⬡</span><span><b>DIGITAL:</b> {{ $bulletin->digital_recommendation }}</span></div>@endif
        @if(!$bulletin->logistics_recommendation && !$bulletin->operational_recommendation)<div style="font-size:12px;color:var(--muted)">Sin recomendaciones para este scope.</div>@endif
      </div></div>

      <div class="card"><div class="sb">
        <div class="sb-t">Resumen Táctico</div>
        <div class="tac-item" style="border-color:var(--red2)"><div class="lvl" style="color:var(--red2)">Amenaza</div><div class="val">{{ $bulletin->main_threat ?? '—' }}</div></div>
        <div class="tac-item" style="border-color:var(--orange)"><div class="lvl" style="color:var(--orange)">Zona Crítica</div><div class="val">{{ $bulletin->critical_zone ?? '—' }}</div></div>
        <div class="tac-item" style="border-color:var(--blue)"><div class="lvl" style="color:var(--blue)">Tendencia</div><div class="val">{{ $bulletin->trend ?? '—' }}</div></div>
        @if($bulletin->electoral_context)<div class="tac-item" style="border-color:var(--purple)"><div class="lbl">Contexto electoral</div><div class="val">{{ $bulletin->electoral_context }}</div></div>@endif
      </div></div>

      @php $dist = is_array($bulletin->distribution) ? $bulletin->distribution : []; @endphp
      @if(count($dist))
      <div class="card"><div class="sb">
        <div class="sb-t">Distribución</div>
        @foreach($dist as $d)
          <div class="dist"><span>{{ $d['ciudad'] ?? $d['city'] ?? '—' }}</span><span class="ev">{{ $d['eventos'] ?? $d['events'] ?? 0 }} ev</span></div>
        @endforeach
      </div></div>
      @endif
    </div>
  </div>

  <div class="footer">
    Documento de Inteligencia · VISE-ALTUM · Generado {{ \Illuminate\Support\Carbon::parse($bulletin->generated_at)->format('d/m/Y H:i') }}
    <br><b>CONFIDENCIAL · RESERVADO</b>
  </div>
  @endif

</div>
</body>
</html>
