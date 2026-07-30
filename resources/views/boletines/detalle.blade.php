<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boletín · {{ $scopeLevel==='national' ? 'Nacional' : $scope }} — VISE Ltda</title>
@include('boletines._intel_base')
<style>
  .shell{min-height:100vh;display:flex;flex-direction:column;background:var(--color-bg);}
  .topbar{display:flex;align-items:center;gap:20px;padding:0 28px;height:56px;background:#122f21;color:#f2f2f3;flex:none;}
  .topbar a{color:#cfe3d7;font-size:12.5px;text-decoration:none;letter-spacing:.04em;} .topbar a:hover{color:#fff;}
  .body{flex:1;display:grid;grid-template-columns:268px 1fr;align-items:start;}
  .side{position:sticky;top:0;align-self:start;background:#1a422f;color:#e7f0ea;min-height:calc(100vh - 56px);padding:22px 0 28px;display:flex;flex-direction:column;gap:22px;}
  .side-lbl{font-size:9.5px;letter-spacing:.2em;text-transform:uppercase;color:#70a487;}
  main{padding:0 0 56px;min-width:0;}
  .head{padding:26px 40px 22px;border-bottom:1px solid var(--color-divider);}
  .kpis{display:grid;grid-template-columns:repeat(5,1fr);border-bottom:1px solid var(--color-divider);}
  .kpi{padding:18px 22px;border-right:1px solid var(--color-divider);}
  .kpi .v{font-family:var(--font-heading);font-size:36px;line-height:1;}
  .kpi .l{font-size:10px;letter-spacing:.13em;text-transform:uppercase;color:var(--color-neutral-600);margin-top:5px;}
  .cols{padding:26px 40px 0;display:grid;grid-template-columns:minmax(0,2fr) minmax(280px,1fr);gap:32px;align-items:start;}
  .sec-h{display:flex;align-items:baseline;gap:12px;margin-bottom:14px;}
  .sec-h h2{font-size:23px;letter-spacing:.02em;color:#1d1f20;}
  .sec-h .c{font-size:11.5px;letter-spacing:.1em;text-transform:uppercase;color:var(--color-neutral-600);}
  .sec-h .ln{flex:1;border-top:1px solid var(--color-divider);}
  .evt{padding:16px 20px;}
  .evt .meta{display:flex;align-items:center;gap:9px;margin-bottom:7px;flex-wrap:wrap;}
  .evt .ti{font-family:var(--font-heading);font-size:20px;line-height:1.15;color:#1d1f20;margin-bottom:5px;}
  .evt p{margin:0 0 9px;font-size:14px;line-height:1.5;color:var(--color-neutral-700);}
  .evt .foot{display:flex;align-items:center;gap:16px;flex-wrap:wrap;border-top:1px solid var(--color-divider);padding-top:9px;}
  .evt .foot span{display:inline-flex;align-items:center;gap:6px;font-size:12px;}
  .rside{display:flex;flex-direction:column;gap:20px;position:sticky;top:20px;}
  .bar{flex:1;height:7px;background:var(--color-neutral-200);position:relative;}
  .bar>span{position:absolute;left:0;top:0;bottom:0;background:#3f8161;}
  .empty{padding:60px 40px;text-align:center;color:var(--color-neutral-600);}
  @media(max-width:900px){.body{grid-template-columns:1fr;} .side{position:static;min-height:0;} .cols{grid-template-columns:1fr;padding:22px 20px 0;} .kpis{grid-template-columns:repeat(2,1fr);} .head{padding:20px;} .rside{position:static;}}
</style>
</head>
<body>
@php
  $sevMap = [
    'CRÍTICO'=>['label'=>'CRÍTICO','bg'=>'#f3e0dd','fg'=>'#7c2d24','dot'=>'#b3372e'],
    'CRITICO'=>['label'=>'CRÍTICO','bg'=>'#f3e0dd','fg'=>'#7c2d24','dot'=>'#b3372e'],
    'ALTO'=>['label'=>'ALTO','bg'=>'#f6ecd8','fg'=>'#7a5312','dot'=>'#c08a24'],
    'MEDIO'=>['label'=>'MODERADO','bg'=>'#f6ecd8','fg'=>'#7a5312','dot'=>'#c08a24'],
    'BAJO'=>['label'=>'ESTABLE','bg'=>'#e7f0ea','fg'=>'#24563e','dot'=>'#3f8161'],
  ];
  $sev = fn($s) => $sevMap[mb_strtoupper((string) $s)] ?? ['label'=>$s ?: 'ESTABLE','bg'=>'#e7f0ea','fg'=>'#24563e','dot'=>'#3f8161'];
  $nivel = fn($crit,$total) => $crit>0 ? 'CRÍTICO' : ($total>=3 ? 'ALTO' : ($total>=1 ? 'MEDIO' : 'BAJO'));
  $host = function($url){ $h = parse_url((string) $url, PHP_URL_HOST); return $h ? preg_replace('/^www\./','',$h) : null; };
  $upd = $updatedAt ? \Illuminate\Support\Carbon::parse($updatedAt) : ($bulletin ? \Illuminate\Support\Carbon::parse($bulletin->generated_at) : null);
  $nombre = $scopeLevel==='national' ? 'Nacional' : $scope;
  $ambitoLbl = ['national'=>'Colombia','region'=>'Región','department'=>'Departamento','municipality'=>'Municipio'][$scopeLevel] ?? ucfirst($scopeLevel);
  $estadoSev = fn($e) => $sev(['CERRADO'=>'CRÍTICO','RESTRINGIDO'=>'ALTO','ALERTA'=>'MEDIO','NORMAL'=>'BAJO'][mb_strtoupper((string) ($e ?? 'ALERTA'))] ?? 'MEDIO');
  // Distribución (drill-down): usa los scopes hijos si existen; si no, el campo distribution.
  $distItems = [];
  if (isset($children) && $children->count()) {
    foreach ($children as $ch) { $distItems[] = ['n'=>$ch->scope,'ev'=>(int) $ch->total_events,'level'=>$childLevelSlug,'scope'=>$ch->scope]; }
  } elseif ($bulletin && is_array($bulletin->distribution)) {
    foreach ($bulletin->distribution as $d) { $distItems[] = ['n'=>$d['ciudad'] ?? $d['city'] ?? '—','ev'=>(int) ($d['eventos'] ?? $d['events'] ?? 0),'level'=>null,'scope'=>null]; }
  }
  $distMax = max(1, count($distItems) ? max(array_map(fn($x)=>$x['ev'],$distItems)) : 1);
  $rSev = $sev($nivel($bulletin->critical_events ?? 0, $bulletin->total_events ?? 0));
@endphp

<div class="shell">

  {{-- TOPBAR --}}
  <nav class="topbar">
    <a href="{{ route('boletin',['level'=>'nacional']) }}" style="display:flex;align-items:center;gap:11px;padding-right:20px;border-right:1px solid rgba(255,255,255,.16);height:100%;box-sizing:border-box;text-decoration:none;">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#70a487" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path></svg>
      <span style="display:flex;flex-direction:column;line-height:1.15;">
        <span style="font-family:var(--font-heading);font-weight:600;font-size:17px;letter-spacing:.1em;color:#f2f2f3;">VISE LTDA</span>
        <span style="font-size:9px;letter-spacing:.16em;text-transform:uppercase;color:#70a487;">Grupo ALTUM</span>
      </span>
    </a>
    <span style="font-family:var(--font-heading);font-size:15px;letter-spacing:.16em;text-transform:uppercase;color:#cfe3d7;">Central de Monitoreo Estratégico</span>
    <div style="margin-left:auto;display:flex;align-items:center;gap:18px;">
      <span style="display:inline-flex;align-items:center;gap:7px;font-size:12px;color:#a8c9b5;"><span style="width:7px;height:7px;background:#70a487;display:inline-block;"></span>En línea @if($upd)· actualizado {{ $upd->format('H:i') }}@endif</span>
      <a href="{{ route('destinatarios') }}">Destinatarios</a>
      <a href="{{ route('fechas') }}">Fechas especiales</a>
      <span style="border:1px solid rgba(255,255,255,.32);color:#e7f0ea;font-size:9.5px;letter-spacing:.16em;padding:4px 8px;">CONFIDENCIAL</span>
    </div>
  </nav>

  <div class="body">

    {{-- SIDEBAR COBERTURA --}}
    <aside class="side">
      <div style="padding:0 20px;">
        <div class="side-lbl" style="margin-bottom:10px;">Boletín del día</div>
        <div style="font-family:var(--font-heading);font-size:26px;line-height:1;color:#fff;">{{ $upd ? mb_strtoupper($upd->locale('es')->isoFormat('D MMM YYYY')) : '—' }}</div>
        <div style="font-size:11.5px;color:#a8c9b5;margin-top:4px;">@if($upd)Generado {{ $upd->format('H:i') }} · Bogotá @else Sin boletín @endif</div>
      </div>
      <div>
        <div class="side-lbl" style="padding:0 20px 8px;">Cobertura</div>
        @forelse($cobertura as $m)
          @php $act = $m['level']===$level && ($m['level']==='nacional' || $m['scope']===$scope); $ms = $sev($nivel($m['criticos'],$m['eventos'])); @endphp
          <a class="cov {{ $act ? 'active' : '' }}" href="{{ route('boletin',['level'=>$m['level'],'scope'=>$m['scope']]) }}">
            <span style="width:8px;height:8px;flex:none;background:{{ $ms['dot'] }};display:inline-block;"></span>
            <span class="nm">{{ $m['nombre'] }}</span>
            <span style="font-size:11.5px;color:#a8c9b5;">{{ $m['eventos'] }}</span>
            <span style="font-size:11.5px;color:{{ $ms['dot'] }};width:14px;text-align:right;">{{ $m['criticos'] }}</span>
          </a>
        @empty
          <div style="padding:0 20px;font-size:12px;color:#a8c9b5;">Sin boletines en la última corrida.</div>
        @endforelse
        <div style="display:flex;gap:14px;padding:10px 20px 0;font-size:9.5px;letter-spacing:.1em;text-transform:uppercase;color:#70a487;"><span style="margin-left:auto;">Ev.</span><span>Crít.</span></div>
      </div>
      @if($bulletin)
      <div style="padding:0 20px;margin-top:auto;display:flex;flex-direction:column;gap:9px;">
        <a class="btn btn-primary blueprint" style="width:100%;" href="{{ route('boletin.pdf',['level'=>$level,'scope'=>$scopeLevel==='national'?null:$scope]) }}" target="_blank">
          <i class="corner tl" style="color:rgba(255,255,255,.55)"></i><i class="corner tr" style="color:rgba(255,255,255,.55)"></i><i class="corner bl" style="color:rgba(255,255,255,.55)"></i><i class="corner br" style="color:rgba(255,255,255,.55)"></i>
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
          Exportar PDF</a>
        <a class="btn" style="width:100%;border-color:rgba(255,255,255,.32);color:#cfe3d7;" href="{{ route('destinatarios') }}">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16"></rect><path d="m22 6-10 7L2 6"></path></svg>
          Enviar a destinatarios</a>
      </div>
      @endif
    </aside>

    {{-- CONTENIDO --}}
    <main>
      @if(!$bulletin)
        <div class="empty">No hay un boletín generado para <b>{{ $nombre }}</b>. Elige otra cobertura en el menú.</div>
      @else
      <div class="head">
        <div style="display:flex;align-items:flex-start;gap:32px;flex-wrap:wrap;">
          <div style="flex:1;min-width:min(420px,100%);">
            <div style="font-size:10px;letter-spacing:.22em;text-transform:uppercase;color:var(--color-accent-700);margin-bottom:8px;">Monitoreo Estratégico — {{ $ambitoLbl }}</div>
            <h1 style="margin:0 0 10px;font-size:46px;line-height:1;color:#1d1f20;">{{ $nombre }}</h1>
            @if($bulletin->headline)<div style="font-family:var(--font-heading);font-size:21px;line-height:1.2;letter-spacing:.03em;text-transform:uppercase;color:var(--color-neutral-700);max-width:640px;">{{ $bulletin->headline }}</div>@endif
          </div>
          <div style="display:flex;gap:10px;align-items:center;padding-top:6px;flex-wrap:wrap;">
            <span class="tag" style="background:{{ $rSev['bg'] }};color:{{ $rSev['fg'] }};display:inline-flex;align-items:center;gap:7px;font-size:11px;letter-spacing:.1em;"><span style="width:8px;height:8px;background:{{ $rSev['dot'] }};display:inline-block;"></span>TENDENCIA {{ mb_strtoupper($bulletin->trend ?? '—') }}</span>
            @if($bulletin->critical_zone)<span class="tag tag-outline" style="font-size:11px;letter-spacing:.1em;">{{ \Illuminate\Support\Str::limit($bulletin->critical_zone, 40) }}</span>@endif
          </div>
        </div>
      </div>

      {{-- KPIs --}}
      <div class="kpis">
        <div class="kpi"><div class="v" style="color:#1a422f;">{{ $stats['events'] }}</div><div class="l">Eventos</div></div>
        <div class="kpi"><div class="v" style="color:{{ $bulletin->critical_events>0 ? '#b3372e' : '#1a422f' }};">{{ $bulletin->critical_events }}</div><div class="l">Críticos</div></div>
        <div class="kpi"><div class="v" style="color:#1a422f;">{{ $stats['areas'] }}</div><div class="l">{{ $scopeLevel==='national' ? 'Regiones' : 'Zonas' }}</div></div>
        <div class="kpi"><div class="v" style="color:#1a422f;">{{ $stats['roads'] }}</div><div class="l">Vías afectadas</div></div>
        <div class="kpi" style="border-right:0;"><div class="v" style="color:#1a422f;">{{ $stats['environmental'] }}</div><div class="l">Alertas ambientales</div></div>
      </div>

      <div class="cols">
        {{-- COLUMNA PRINCIPAL --}}
        <div style="display:flex;flex-direction:column;gap:34px;min-width:0;">

          <section>
            <div class="sec-h"><h2>Reporte de novedades</h2><span class="c">{{ $securityEvents->count() }} eventos</span><span class="ln"></span></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
              @forelse($securityEvents as $e)
                @php $s = $sev($e->severity); @endphp
                <article class="blueprint evt">
                  <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
                  <div class="meta">
                    <span class="tag" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};display:inline-flex;align-items:center;gap:6px;letter-spacing:.1em;"><span style="width:8px;height:8px;background:{{ $s['dot'] }};display:inline-block;"></span>{{ $s['label'] }}</span>
                    @if($e->subtype)<span class="tag tag-neutral" style="letter-spacing:.08em;">{{ $e->subtype }}</span>@endif
                    <span style="margin-left:auto;font-size:11.5px;color:var(--color-neutral-600);">{{ $e->details['fecha_evento'] ?? '' }}</span>
                  </div>
                  <div class="ti">{{ $e->title }}</div>
                  @if($e->summary)<p>{{ $e->summary }}</p>@endif
                  <div class="foot">
                    @if($e->municipality || $e->department)
                    <span style="color:var(--color-accent-700);"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path><circle cx="12" cy="10" r="3"></circle></svg>{{ $e->municipality ? $e->municipality.', ' : '' }}{{ $e->department }}</span>
                    @endif
                    <span style="color:var(--color-neutral-600);"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9h4"></path><path d="M18 14h-8M15 18h-5M10 6h8v4h-8z"></path></svg>Fuente:&nbsp;@if($e->source_url && \Illuminate\Support\Str::startsWith($e->source_url,'http'))<a href="{{ $e->source_url }}" target="_blank" rel="noopener" style="color:var(--color-accent-700);">{{ $e->media_outlet ?: $host($e->source_url) }}</a>@else{{ $e->media_outlet ?: '—' }}@endif</span>
                  </div>
                </article>
              @empty
                <div class="blueprint evt"><i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i><p style="margin:0;color:var(--color-neutral-600);">Sin eventos de seguridad reportados en esta cobertura.</p></div>
              @endforelse
            </div>
          </section>

          @if($environmental->count())
          <section>
            <div class="sec-h"><h2>Alertas ambientales</h2><span class="c">{{ $environmental->count() }} activa(s)</span><span class="ln"></span></div>
            <div style="display:flex;flex-direction:column;gap:14px;">
              @foreach($environmental as $e)
                @php $s = $sev($e->severity); @endphp
                <article class="blueprint evt">
                  <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
                  <div class="meta">
                    <span class="tag" style="background:{{ $s['bg'] }};color:{{ $s['fg'] }};display:inline-flex;align-items:center;gap:6px;letter-spacing:.1em;"><span style="width:8px;height:8px;background:{{ $s['dot'] }};display:inline-block;"></span>{{ $s['label'] }}</span>
                    @if($e->subtype)<span class="tag tag-neutral" style="letter-spacing:.08em;">{{ $e->subtype }}</span>@endif
                    <span style="margin-left:auto;font-size:11.5px;color:var(--color-neutral-600);">{{ $e->department ?? 'Colombia' }}</span>
                  </div>
                  @if($e->summary)<p style="margin:0;">{{ $e->summary }}</p>@endif
                </article>
              @endforeach
            </div>
          </section>
          @endif

          @if($trafficTm->count() || $trafficOther->count())
          <section>
            <div class="sec-h"><h2>Estado de conectividad vial</h2><span class="c">{{ $trafficTm->count() + $trafficOther->count() }} corredor(es)</span><span class="ln"></span></div>
            <div class="blueprint" style="padding:14px 18px;">
              <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
              <table class="table">
                <thead><tr><th>Corredor</th><th>Zona</th><th>Estatus</th><th>Observaciones</th></tr></thead>
                <tbody>
                  @foreach($trafficTm as $e)
                    @php $es = $estadoSev($e->details['estado'] ?? null); @endphp
                    <tr><td style="font-weight:600;">{{ $e->details['via'] ?? $e->title }}</td><td>TransMilenio</td><td><span class="tag" style="background:{{ $es['bg'] }};color:{{ $es['fg'] }};letter-spacing:.08em;">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td style="color:var(--color-neutral-700);">{{ $e->summary }}</td></tr>
                  @endforeach
                  @foreach($trafficOther as $e)
                    @php $es = $estadoSev($e->details['estado'] ?? null); @endphp
                    <tr><td style="font-weight:600;">{{ $e->details['via'] ?? $e->title }}</td><td>{{ $e->details['region'] ?? $e->department }}</td><td><span class="tag" style="background:{{ $es['bg'] }};color:{{ $es['fg'] }};letter-spacing:.08em;">{{ $e->details['estado'] ?? 'ALERTA' }}</span></td><td style="color:var(--color-neutral-700);">{{ $e->summary }}</td></tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>
          @endif

          @php $recs = array_filter(['Logística'=>$bulletin->logistics_recommendation,'Perímetros'=>$bulletin->perimeter_recommendation,'Operacional'=>$bulletin->operational_recommendation,'Digital'=>$bulletin->digital_recommendation]); @endphp
          @if(count($recs))
          <section>
            <div class="sec-h"><h2>Recomendaciones</h2><span class="ln"></span></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
              @foreach($recs as $k => $t)
                <div class="blueprint" style="padding:15px 18px;">
                  <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
                  <div style="font-size:10px;letter-spacing:.16em;text-transform:uppercase;color:var(--color-accent-700);margin-bottom:6px;">{{ $k }}</div>
                  <p style="margin:0;font-size:13px;line-height:1.5;color:var(--color-neutral-700);">{{ $t }}</p>
                </div>
              @endforeach
            </div>
          </section>
          @endif
        </div>

        {{-- COLUMNA DERECHA --}}
        <aside class="rside">
          <div class="blueprint" style="padding:19px 21px;background:#1a422f;color:#e7f0ea;">
            <i class="corner tl" style="color:rgba(255,255,255,.5)"></i><i class="corner tr" style="color:rgba(255,255,255,.5)"></i><i class="corner bl" style="color:rgba(255,255,255,.5)"></i><i class="corner br" style="color:rgba(255,255,255,.5)"></i>
            <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:#70a487;margin-bottom:11px;">Inteligencia táctica</div>
            <div style="font-family:var(--font-heading);font-size:21px;line-height:1.15;color:#fff;margin-bottom:9px;">{{ $bulletin->main_threat ?: ($bulletin->headline ?: 'Panorama de seguridad del día') }}</div>
            <p style="margin:0;font-size:13px;line-height:1.55;color:#cfe3d7;">{{ $bulletin->conclusion ?: ($bulletin->critical_events.' crítico(s) de '.$bulletin->total_events.' evento(s).') }}</p>
          </div>

          <div class="blueprint" style="padding:19px 21px;">
            <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
            <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--color-accent-700);margin-bottom:10px;">Resumen táctico</div>
            <table class="table"><tbody>
              <tr><td style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--color-neutral-600);width:88px;">Zona crítica</td><td style="font-size:13px;">{{ $bulletin->critical_zone ?: '—' }}</td></tr>
              <tr><td style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--color-neutral-600);">Tendencia</td><td><span class="tag" style="background:{{ $rSev['bg'] }};color:{{ $rSev['fg'] }};letter-spacing:.08em;">{{ mb_strtoupper($bulletin->trend ?? '—') }}</span></td></tr>
              <tr><td style="font-size:10px;letter-spacing:.1em;text-transform:uppercase;color:var(--color-neutral-600);">Criticidad</td><td style="font-size:13px;">{{ $bulletin->critical_events }} de {{ $bulletin->total_events }} eventos</td></tr>
            </tbody></table>
          </div>

          @if($bulletin->electoral_context)
          <div class="blueprint" style="padding:19px 21px;">
            <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
            <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--color-accent-700);margin-bottom:9px;">Contexto electoral</div>
            <p style="margin:0;font-size:13px;line-height:1.55;color:var(--color-neutral-700);">{{ $bulletin->electoral_context }}</p>
          </div>
          @endif

          @if(count($distItems))
          <div class="blueprint" style="padding:19px 21px;">
            <i class="corner tl"></i><i class="corner tr"></i><i class="corner bl"></i><i class="corner br"></i>
            <div style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--color-accent-700);margin-bottom:12px;">Distribución{{ $childLevelSlug ? ' por '.$childLevelSlug : '' }}</div>
            <div style="display:flex;flex-direction:column;gap:9px;">
              @foreach($distItems as $d)
                @php $row = '<span style="font-size:12.5px;color:#1d1f20;width:96px;flex:none;">'.e($d['n']).'</span><span class="bar"><span style="width:'.round(($d['ev']/$distMax)*100).'%;"></span></span><span style="font-size:11.5px;color:var(--color-neutral-600);width:34px;text-align:right;">'.$d['ev'].' ev</span>'; @endphp
                @if($d['level'])
                  <a href="{{ route('boletin',['level'=>$d['level'],'scope'=>$d['scope']]) }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;">{!! $row !!}</a>
                @else
                  <div style="display:flex;align-items:center;gap:10px;">{!! $row !!}</div>
                @endif
              @endforeach
            </div>
          </div>
          @endif
        </aside>
      </div>
      @endif
    </main>
  </div>

  <footer style="background:#122f21;color:#a8c9b5;padding:15px 28px;display:flex;align-items:center;gap:16px;font-size:11.5px;flex-wrap:wrap;">
    <span style="letter-spacing:.05em;">Documento de Inteligencia · VISE — Grupo ALTUM @if($upd)· Generado {{ $upd->format('d/m/Y H:i') }}@endif</span>
    <span style="margin-left:auto;letter-spacing:.2em;font-weight:600;color:#e7f0ea;">CONFIDENCIAL · RESERVADO</span>
  </footer>
</div>
</body>
</html>
