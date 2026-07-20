<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  /* Paleta corporativa VISE–ALTUM (replicada del boletín oficial de inteligencia).
     dompdf no soporta CSS custom properties: los hex van en línea. */
  @page { margin: 0 0 30pt 0; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', sans-serif; color: #1F2937; font-size: 10px; line-height: 1.4; }
  a { color: #2851A3; text-decoration: none; }
  strong, b { font-weight: bold; }
  .page { padding: 0; }

  /* ── HEADER ── */
  .header { width: 100%; border-bottom: 3px solid #1B2A4A; border-collapse: collapse; }
  .header td { vertical-align: middle; padding: 8px 16px; }
  .h-logo { width: 52%; }
  .h-logo img { height: 68px; width: auto; vertical-align: middle; }
  .logo-bar { display: inline-block; width: 5px; height: 46px; background: #E8192C; vertical-align: middle; margin: 0 12px; }
  .logo-text { font-size: 19px; font-weight: bold; color: #1B2A4A; letter-spacing: -0.5px; text-transform: uppercase; line-height: 1; }
  .logo-sub { font-size: 7px; letter-spacing: 2px; color: #6B7280; text-transform: uppercase; margin-top: 3px; }
  .h-center { text-align: center; border-left: 1px solid #D1D9E6; border-right: 1px solid #D1D9E6; }
  .hc-tipo { font-size: 8px; font-weight: bold; letter-spacing: 2px; color: #1B2A4A; text-transform: uppercase; border: 1.5px solid #1B2A4A; padding: 3px 10px; }
  .hc-sub { font-size: 8px; font-weight: bold; letter-spacing: 1.5px; color: #6B7280; text-transform: uppercase; margin-top: 5px; }
  .h-right { text-align: right; white-space: nowrap; }
  .hr-label { font-size: 7px; letter-spacing: 2px; color: #6B7280; text-transform: uppercase; font-weight: bold; }
  .hr-datetime { font-size: 18px; font-weight: bold; color: #1B2A4A; line-height: 1.1; }
  .hr-ampm { font-size: 9px; color: #6B7280; }

  /* ── HERO ── (navy sólido: dompdf no renderiza background cover, evitamos el negro) */
  .hero { position: relative; background: #1B2A4A; color: #fff; border-bottom: 3px solid #E8192C; }
  .hero-content { position: relative; padding: 12px 20px 13px; }
  .hero-alerta { font-size: 7px; font-weight: bold; letter-spacing: 2.5px; color: #FF6B6B; text-transform: uppercase; margin-bottom: 4px; }
  .hero-title { font-size: 19px; font-weight: bold; color: #fff; line-height: 1.08; letter-spacing: 0.3px; text-transform: uppercase; }
  .hero-sub { font-size: 8px; color: #C7D0DE; margin-top: 5px; }
  .hero-estado { position: absolute; top: 9px; right: 18px; text-align: right; }
  .hero-estado-lbl { font-size: 6.5px; letter-spacing: 2px; color: rgba(255,255,255,0.5); text-transform: uppercase; }
  .hero-estado-val { font-size: 8px; font-weight: bold; letter-spacing: 1.5px; color: #4ADE80; text-transform: uppercase; margin-top: 2px; }

  /* ── RADAR BAR ── */
  .radar { width: 100%; background: #243555; color: #fff; border-collapse: collapse; }
  .radar td { padding: 5px 20px; vertical-align: middle; }
  .radar-label { font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
  .radar-right { text-align: right; white-space: nowrap; }
  .radar-period { font-size: 8px; color: rgba(255,255,255,0.55); margin-right: 12px; }
  .nivel-badge { font-size: 9px; font-weight: bold; padding: 3px 12px; letter-spacing: 1.5px; text-transform: uppercase; color: #fff; }

  /* ── STATS ── */
  .stats { width: 100%; border-collapse: collapse; border-bottom: 2px solid #D1D9E6; }
  .stats td { width: 20%; text-align: center; padding: 6px 8px 7px; border-right: 1px solid #E5EAF2; }
  .stats td:last-child { border-right: none; }
  .stat-n { font-size: 25px; font-weight: bold; line-height: 1; color: #1B2A4A; }
  .stat-n.red { color: #E8192C; } .stat-n.orange { color: #E8750A; }
  .stat-n.purple { color: #7C3AED; } .stat-n.green { color: #0A7C5C; }
  .stat-l { font-size: 7px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #6B7280; margin-top: 3px; }
  .stat-bar { height: 3px; margin-top: 5px; background: #E5EAF2; }
  .stat-bar-fill { height: 3px; }

  /* ── LAYOUT PRINCIPAL ── */
  .layout { width: 100%; border-collapse: collapse; }
  .layout > tbody > tr > td { vertical-align: top; }
  .main { width: 62%; padding: 8px 16px 8px 18px; border-right: 1px solid #D1D9E6; }
  .side { width: 38%; padding: 8px 18px 8px 14px; }

  /* Cards tácticas */
  .cards { width: 100%; border-collapse: collapse; margin-bottom: 9px; }
  .cards td { width: 50%; vertical-align: top; }
  .cards td:first-child { padding-right: 6px; }
  .cards td:last-child { padding-left: 6px; }
  .card { border: 1px solid #D1D9E6; padding: 8px 10px; }
  .card.red { border-top: 3px solid #E8192C; }
  .card.blue { border-top: 3px solid #2851A3; }
  .card-lbl { font-size: 7px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #6B7280; margin-bottom: 4px; }
  .card-title { font-size: 11px; font-weight: bold; color: #1B2A4A; line-height: 1.2; padding-bottom: 4px; margin-bottom: 4px; border-bottom: 1px solid #E5EAF2; }
  .card-body { font-size: 8.5px; color: #374151; line-height: 1.4; }

  /* Flash bar (encabezado de sección) */
  .flash { width: 100%; background: #1B2A4A; color: #fff; border-collapse: collapse; }
  .flash td { padding: 5px 12px; vertical-align: middle; }
  .flash-title { font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
  .flash-badge { font-size: 8px; font-weight: bold; letter-spacing: 1px; color: rgba(255,255,255,0.6); text-align: right; white-space: nowrap; }
  .flash.env { background: #0A3D2E; }

  /* Eventos */
  .evento { padding: 5px 2px 5px 0; border-bottom: 1px solid #E5EAF2; }
  .evento:last-child { border-bottom: none; }
  .evento.crit { background: #FFF5F5; border-left: 3px solid #E8192C; padding-left: 9px; }
  .evento-t { font-size: 11px; font-weight: bold; color: #1B2A4A; line-height: 1.2; }
  .evento.crit .evento-t { color: #B01023; }
  .evento-d { font-size: 8.5px; color: #374151; line-height: 1.4; margin: 2px 0 4px; }
  .tag { display: inline-block; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 7px; margin-right: 4px; }
  .tag-critico { background: #E8192C; color: #fff; }
  .tag-alto { background: #E8750A; color: #fff; }
  .tag-medio { background: #2851A3; color: #fff; }
  .tag-bajo { background: #0A7C5C; color: #fff; }
  .tag-geo { background: #EEF3FC; color: #2851A3; border: 1px solid #C7D8F2; }
  /* Lista compacta: el resto de eventos, una línea c/u, DENTRO del boletín */
  .minlist { width: 100%; border-collapse: collapse; margin-top: 8px; border-top: 1px solid #E5EAF2; }
  .minlist td { padding: 3px 0; border-bottom: 1px solid #F0F2F6; vertical-align: middle; font-size: 9px; }
  .minlist tr:last-child td { border-bottom: none; }
  .min-sev { width: 14px; }
  .dot { display: inline-block; width: 7px; height: 7px; }
  .dot-critico { background: #E8192C; } .dot-alto { background: #E8750A; }
  .dot-medio { background: #2851A3; } .dot-bajo { background: #0A7C5C; }
  .min-t { color: #1F2937; font-weight: bold; }
  .min-geo { color: #6B7280; font-weight: normal; }
  .min-tag { text-align: right; white-space: nowrap; width: 58px; }

  /* Sidebar */
  .sb { border-bottom: 1px solid #D1D9E6; padding-bottom: 8px; margin-bottom: 9px; }
  .sb:last-child { border-bottom: none; margin-bottom: 0; }
  .sb-title { font-size: 8px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #1B2A4A; border-bottom: 2px solid #1B2A4A; padding-bottom: 4px; margin-bottom: 6px; }
  .sb-title .mk { color: #E8192C; }
  .rec { padding: 4px 0; border-bottom: 1px solid #E5EAF2; font-size: 9px; color: #374151; line-height: 1.4; }
  .rec:last-child { border-bottom: none; }
  .rec .mk { color: #E8192C; font-weight: bold; }
  .rec b { color: #1B2A4A; }
  .tac { padding: 5px 9px; margin-bottom: 5px; background: #F5F6F8; border-left: 3px solid #D1D9E6; }
  .tac:last-child { margin-bottom: 0; }
  .tac.critico { border-left-color: #E8192C; background: #FFF5F5; }
  .tac.alerta { border-left-color: #E8750A; background: #FFFBF0; }
  .tac-nivel { font-size: 7px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 1px; }
  .tac-label { font-size: 10px; font-weight: bold; color: #1B2A4A; margin-bottom: 1px; }
  .tac-val { font-size: 9px; color: #374151; line-height: 1.3; }
  .dist { padding: 4px 0; border-bottom: 1px solid #E5EAF2; font-size: 9px; color: #374151; }
  .dist:last-child { border-bottom: none; }
  .dist .n { font-weight: bold; color: #E8750A; float: right; font-size: 11px; }
  .gri { width: 100%; border: 1px solid #D1D9E6; border-collapse: collapse; }
  .gri td { padding: 3px 8px; border-bottom: 1px solid #E5EAF2; font-size: 8.5px; }
  .gri tr:last-child td { border-bottom: none; }
  .gri-city { font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; color: #243555; }
  .gri-phone { text-align: right; color: #3A6BC4; font-weight: bold; }

  /* ── FOOTER FIJO ── (repetido al pie de cada página, en el margen inferior) */
  .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 30pt; background: #1B2A4A; color: #fff; text-align: center; padding: 6px 14px; font-size: 8px; letter-spacing: 0.5px; border-top: 2px solid #E8192C; }
  .footer a { color: #C7D0DE; }
  .footer-conf { display: block; color: #FF6B6B; font-weight: bold; letter-spacing: 1.5px; margin-top: 3px; }
</style>
</head>
<body>
@php
  $tendColor = ['ALTA'=>'#E8192C','MEDIA'=>'#E8750A','BAJA'=>'#0A7C5C'][$tactica['tendencia']] ?? '#6B7280';
  if ($stats['criticos'] > 0)      { $nivel = 'CRÍTICO';  $nivelColor = '#E8192C'; }
  elseif ($stats['total'] > 0)     { $nivel = 'ELEVADO';  $nivelColor = '#E8750A'; }
  else                             { $nivel = 'ESTABLE';  $nivelColor = '#0A7C5C'; }
  $heroTitle = $titulo ?: ('Panorama de seguridad — '.$scope);
  $barW = fn($v,$k=12) => min($v*$k, 100);
@endphp

<div class="page">

  {{-- HEADER --}}
  <table class="header">
    <tr>
      <td class="h-logo">
        @if($logoDataUri)<img src="{{ $logoDataUri }}" alt="Grupo Altum">@endif
        <span class="logo-bar"></span>
        <span style="display:inline-block;vertical-align:middle;">
          <span class="logo-text">Grupo Altum</span>
          <span class="logo-sub" style="display:block;">Estrategia de Vigilancia Integrada</span>
        </span>
      </td>
      <td class="h-center">
        <span class="hc-tipo">Reporte Táctico</span>
        <div class="hc-sub">Boletín Oficial de Inteligencia</div>
      </td>
      <td class="h-right">
        <div class="hr-label">Monitoreo Estratégico — {{ $scope }}</div>
        <div class="hr-datetime">{{ $generatedAt->format('d/m/Y') }}</div>
        <div class="hr-ampm">{{ $generatedAt->format('H:i') }} · Hora Colombia</div>
      </td>
    </tr>
  </table>

  {{-- HERO --}}
  <div class="hero">
    <div class="hero-estado">
      <div class="hero-estado-lbl">Estado Radar</div>
      <div class="hero-estado-val">● Activo · Tiempo Real</div>
    </div>
    <div class="hero-content">
      <div class="hero-alerta">● Alerta Táctica · Nivel {{ $nivel }} · {{ $levelLabel }}</div>
      <div class="hero-title">{{ $heroTitle }}</div>
      <div class="hero-sub">Reporte consolidado de inteligencia · {{ $stats['total'] }} evento(s) monitorizado(s) · Fuentes verificadas.</div>
    </div>
  </div>

  {{-- RADAR BAR --}}
  <table class="radar">
    <tr>
      <td class="radar-label">◉ Radar de Sucesos — {{ $scope }}</td>
      <td class="radar-right">
        <span class="nivel-badge" style="background:{{ $nivelColor }};">Nivel {{ $nivel }}</span>
      </td>
    </tr>
  </table>

  {{-- STATS --}}
  <table class="stats">
    <tr>
      <td>
        <div class="stat-n">{{ $stats['total'] }}</div><div class="stat-l">Eventos</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['total'],10) }}%;background:#1B2A4A;"></div></div>
      </td>
      <td>
        <div class="stat-n red">{{ $stats['criticos'] }}</div><div class="stat-l">Críticos</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['criticos'],20) }}%;background:#E8192C;"></div></div>
      </td>
      <td>
        <div class="stat-n orange">{{ $stats['vias'] }}</div><div class="stat-l">Vías Afectadas</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['vias'],15) }}%;background:#E8750A;"></div></div>
      </td>
      <td>
        <div class="stat-n purple">{{ $stats['transmilenio'] }}</div><div class="stat-l">TransMilenio</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['transmilenio'],30) }}%;background:#7C3AED;"></div></div>
      </td>
      <td>
        <div class="stat-n green">{{ $stats['ambientales'] }}</div><div class="stat-l">Ambientales</div>
        <div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['ambientales'],25) }}%;background:#0A7C5C;"></div></div>
      </td>
    </tr>
  </table>

  {{-- MAIN + SIDEBAR --}}
  <table class="layout">
    <tr>
      <td class="main">

        {{-- Cards tácticas --}}
        <table class="cards">
          <tr>
            <td>
              <div class="card red">
                <div class="card-lbl">Inteligencia Táctica</div>
                <div class="card-title">{{ $tactica['amenaza'] ?: 'Sin amenaza principal' }}</div>
                <div class="card-body">
                  Zona crítica: <strong>{{ $tactica['zona'] ?: 'N/A' }}</strong>.
                  Tendencia: <strong style="color:{{ $tendColor }};">{{ $tactica['tendencia'] }}</strong>.
                </div>
              </div>
            </td>
            <td>
              <div class="card blue">
                <div class="card-lbl">Conclusión Ejecutiva</div>
                <div class="card-body">{{ $conclusion }}</div>
              </div>
            </td>
          </tr>
        </table>

        {{-- Flash bar + eventos --}}
        <table class="flash">
          <tr>
            <td class="flash-title">Reporte de Novedades · Seguridad y Orden Público</td>
            <td class="flash-badge">▲ {{ $stats['total'] }} Eventos · Fuentes Verificadas</td>
          </tr>
        </table>

        @forelse($eventos as $e)
          <div class="evento {{ $e['esCritico'] ? 'crit' : '' }}">
            <div class="evento-t">{{ $e['titulo'] }}</div>
            @if($e['descripcion'])<div class="evento-d">{{ $e['descripcion'] }}</div>@endif
            <div>
              @php $sev = mb_strtoupper($e['severidad']); @endphp
              <span class="tag tag-{{ in_array($sev,['CRÍTICO','CRITICO']) ? 'critico' : ($sev==='ALTO' ? 'alto' : ($sev==='MEDIO' ? 'medio' : 'bajo')) }}">{{ $e['severidad'] }}</span>
              @if($e['geo'])<span class="tag tag-geo">{{ $e['geo'] }}</span>@endif
            </div>
          </div>
        @empty
          <div class="evento"><div class="evento-d" style="margin:6px 0;">Sin eventos de seguridad reportados en el período.</div></div>
        @endforelse

        @if(count($eventosCompactos))
          <table class="minlist">
            @foreach($eventosCompactos as $e)
              @php $sv = mb_strtoupper($e['severidad']); @endphp
              <tr>
                <td class="min-sev"><span class="dot dot-{{ in_array($sv,['CRÍTICO','CRITICO']) ? 'critico' : ($sv==='ALTO' ? 'alto' : ($sv==='MEDIO' ? 'medio' : 'bajo')) }}"></span></td>
                <td class="min-t">{{ $e['titulo'] }}@if($e['geo'])<span class="min-geo"> · {{ $e['geo'] }}</span>@endif</td>
                <td class="min-tag"><span class="tag tag-{{ in_array($sv,['CRÍTICO','CRITICO']) ? 'critico' : ($sv==='ALTO' ? 'alto' : ($sv==='MEDIO' ? 'medio' : 'bajo')) }}">{{ $e['severidad'] }}</span></td>
              </tr>
            @endforeach
          </table>
        @endif

        @if(count($ambientales))
          <table class="flash env" style="margin-top:14px;">
            <tr>
              <td class="flash-title">Alertas Ambientales</td>
              <td class="flash-badge">{{ count($ambientales) }} alerta(s)</td>
            </tr>
          </table>
          @foreach($ambientales as $a)
            <div class="evento">
              <div class="evento-t" style="font-size:11px;">{{ $a['titulo'] }}</div>
              @if($a['descripcion'])<div class="evento-d">{{ $a['descripcion'] }}</div>@endif
            </div>
          @endforeach
        @endif

      </td>
      <td class="side">

        <div class="sb">
          <div class="sb-title"><span class="mk">▸</span> Recomendaciones</div>
          @forelse($recomendaciones as $r)
            <div class="rec"><span class="mk">▸</span> <b>{{ $r['label'] }}:</b> {{ $r['texto'] }}</div>
          @empty
            <div class="rec" style="color:#6B7280;">Sin recomendaciones específicas para el período.</div>
          @endforelse
        </div>

        <div class="sb">
          <div class="sb-title"><span class="mk">▸</span> Resumen Táctico</div>
          <div class="tac {{ $stats['criticos'] > 0 ? 'critico' : 'alerta' }}">
            <div class="tac-nivel" style="color:{{ $stats['criticos'] > 0 ? '#E8192C' : '#E8750A' }};">{{ $stats['criticos'] > 0 ? 'CRÍTICO' : 'ALERTA' }}</div>
            <div class="tac-label">Amenaza principal</div>
            <div class="tac-val">{{ $tactica['amenaza'] ?: 'Sin datos' }}</div>
          </div>
          <div class="tac alerta">
            <div class="tac-nivel" style="color:#E8750A;">ALERTA</div>
            <div class="tac-label">Zona crítica</div>
            <div class="tac-val">{{ $tactica['zona'] ?: 'N/A' }}</div>
          </div>
        </div>

        @if(count($distribucion))
          <div class="sb">
            <div class="sb-title"><span class="mk">▸</span> {{ $distTitle }}</div>
            @foreach($distribucion as $d)
              <div class="dist"><span class="n">{{ $d['eventos'] }}</span>{{ $d['nombre'] }}</div>
            @endforeach
          </div>
        @endif

      </td>
    </tr>
  </table>

  {{-- FOOTER --}}
  <div class="footer">
    Intel Ops Document · VISE–ALTUM · Estrategia Integrada © {{ $generatedAt->format('Y') }} · Generado {{ $generatedAt->format('d/m/Y H:i') }} · Hora Colombia
    <span class="footer-conf">CONFIDENCIAL · REPORTE TÁCTICO ALIANZA VISE–ALTUM · RESERVADO</span>
  </div>

</div>
</body>
</html>
