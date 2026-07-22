{{-- Una sección de boletín (encabezado + contadores + tarjetas), reutilizable
     para el nacional y para cada regional. Recibe el modelo ya preparado por
     BulletinPdfPresenter::present(). El footer NO va aquí: se define una sola
     vez en la vista contenedora (position:fixed lo repite en cada página). --}}
@php
  $tendColor = ['ALTA'=>'#DC2626','MEDIA'=>'#EA580C','BAJA'=>'#16A34A'][$tactica['tendencia']] ?? '#6B7280';
  if ($stats['criticos'] > 0)   { $niv = 'ROJA';     $nivC = '#DC2626'; }
  elseif ($stats['total'] >= 6) { $niv = 'NARANJA';  $nivC = '#EA580C'; }
  elseif ($stats['total'] > 0)  { $niv = 'AMARILLA'; $nivC = '#F0B429'; }
  else                          { $niv = 'VERDE';    $nivC = '#16A34A'; }
  $barW = fn($v,$k=12) => min($v*$k, 100);
  $riesgo = function($n){ if($n>=5)return['','#DC2626','ALTO']; if($n>=2)return['medio','#EA580C','MEDIO']; if($n>=1)return['medio','#F0B429','BAJO']; return['','#16A34A','NORMAL']; };
  $sevC = fn($x)=>['CRÍTICO'=>'#DC2626','CRITICO'=>'#DC2626','ALTO'=>'#EA580C','MEDIO'=>'#F0B429','BAJO'=>'#16A34A'][mb_strtoupper($x)] ?? '#6B7280';
@endphp

  {{-- HEADER --}}
  <table class="hd">
    <tr>
      <td style="width:36%;">
        @if($logoDataUri)<img src="{{ $logoDataUri }}" class="logo" alt="Grupo Altum">@endif
        <span style="display:inline-block; vertical-align:middle; margin-left:7px;">
          <span class="brand">Grupo Altum</span>
          <span class="brand-sub" style="display:block;">Estrategia de Vigilancia Integrada</span>
        </span>
      </td>
      <td style="width:40%;">
        <div class="tt">Panorama de Orden Público y Movilidad</div>
        <div class="ts">Boletín de Seguridad · {{ $levelLabel }}</div>
      </td>
      <td style="width:24%; text-align:center;">
        <div class="h-monitoreo">Monitoreo Estratégico — {{ $scope }}</div>
        <div class="dbx">
          <div class="d">{{ $generatedAt->format('d') }}</div>
          <div class="m">{{ mb_strtoupper($generatedAt->locale('es')->isoFormat('MMM')) }}</div>
          <div class="y">{{ $generatedAt->format('Y') }}</div>
        </div>
      </td>
    </tr>
  </table>

  {{-- HERO: titular del día + estado radar (data del navy) --}}
  <table class="hero">
    <tr>
      <td>
        <div class="hero-alerta">● Alerta Táctica · Nivel {{ $niv }} · {{ $levelLabel }}</div>
        <div class="hero-titulo">{{ $titulo ?: ('Panorama de seguridad — '.$scope) }}</div>
        <div class="hero-sub">Reporte consolidado de inteligencia · {{ $stats['total'] }} evento(s) monitorizado(s) · Fuentes verificadas.</div>
      </td>
      <td class="hero-estado">
        <div class="hero-estado-lbl">Estado Radar</div>
        <div class="hero-estado-val">● Activo · Tiempo Real</div>
      </td>
    </tr>
  </table>

  {{-- RADAR bar --}}
  <table class="radar">
    <tr>
      <td class="radar-l">● Radar de Sucesos — {{ $scope }}</td>
      <td class="radar-r"><span class="radar-upd">Actualizado {{ $generatedAt->format('d/m/Y · H:i') }} · Hora Colombia</span><span class="nivel-badge" style="background:{{ $nivC }};">Nivel {{ $niv }}</span></td>
    </tr>
  </table>

  <div class="wrap">

    {{-- CONTADOR (5 cifras) --}}
    <table class="stats" style="margin-bottom:9px;">
      <tr>
        <td><div class="stat-n">{{ $stats['total'] }}</div><div class="stat-l">Eventos</div><div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['total'],10) }}%;background:#1B5E3F;"></div></div></td>
        <td><div class="stat-n red">{{ $stats['criticos'] }}</div><div class="stat-l">Críticos</div><div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['criticos'],20) }}%;background:#DC2626;"></div></div></td>
        <td><div class="stat-n purple">{{ $stats['marchas'] }}</div><div class="stat-l">Marchas</div><div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['marchas'],20) }}%;background:#7C3AED;"></div></div></td>
        <td><div class="stat-n orange">{{ $stats['vias'] }}</div><div class="stat-l">Vías Afectadas</div><div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['vias'],15) }}%;background:#EA580C;"></div></div></td>
        <td><div class="stat-n">{{ $stats['ambientales'] }}</div><div class="stat-l">Ambientales</div><div class="stat-bar"><div class="stat-bar-fill" style="width:{{ $barW($stats['ambientales'],25) }}%;background:#16A34A;"></div></div></td>
      </tr>
    </table>

    {{-- FILA 1: Estado / Riesgo / Táctico --}}
    <table class="grid">
      <tr>
        <td style="width:34%;">
          <div class="card">
            <div class="ch"><span class="ic">&#xf3ed;</span>Estado Actual</div>
            <div class="cb">
              <div class="bl" style="text-align:center;"><span class="nivbadge" style="background:{{ $nivC }};">Alerta {{ $niv }}</span></div>
              <div class="bl">{{ $conclusion }}</div>
            </div>
          </div>
        </td>
        <td style="width:33%;">
          <div class="card">
            <div class="ch"><span class="ic">&#xf200;</span>Riesgo por Región</div>
            <div class="cb">
              @foreach($distribucion as $r)
                @php [$mc,$col,$lbl] = $riesgo($r['eventos']); @endphp
                <div class="rrow"><span class="rpill {{ $mc }}" style="background:{{ $col }};">{{ $lbl }}</span>{{ $r['nombre'] }} <span style="color:#9CA3AF;">({{ $r['eventos'] }})</span></div>
              @endforeach
            </div>
          </div>
        </td>
        <td style="width:33%;">
          <div class="card">
            <div class="ch"><span class="ic">&#xf05b;</span>Resumen Táctico</div>
            <div class="cb">
              <div class="bl"><span class="ic" style="color:#DC2626;">&#xf06a;</span><b>Amenaza:</b> {{ $tactica['amenaza'] ?: 'Sin datos' }}</div>
              <div class="bl"><span class="ic" style="color:#EA580C;">&#xf3c5;</span><b>Zona crítica:</b> {{ $tactica['zona'] ?: 'N/A' }}</div>
              <div class="bl"><span class="ic" style="color:{{ $tendColor }};">&#xf201;</span><b>Tendencia:</b> <b style="color:{{ $tendColor }};">{{ $tactica['tendencia'] }}</b></div>
            </div>
          </div>
        </td>
      </tr>
    </table>

    {{-- FILA 2: Novedades + Marchas | Recomendaciones + Ambientales --}}
    <table class="grid" style="margin-top:8px;">
      <tr>
        <td style="width:62%;">
          <div class="card">
            <div class="ch"><span class="ic">&#xf0f3;</span>Reporte de Novedades · Seguridad y Orden Público</div>
            <div class="cb">
              @forelse($eventos as $e)
                <div class="evt">
                  <div class="evt-t">{{ $e['titulo'] }}</div>
                  @if($e['descripcion'])<div class="evt-d">{{ $e['descripcion'] }}</div>@endif
                  <div><span class="tag" style="background:{{ $sevC($e['severidad']) }};">{{ $e['severidad'] }}</span>@if($e['geo'])<span class="tag geo">{{ $e['geo'] }}</span>@endif</div>
                </div>
              @empty
                <div class="evt"><div class="evt-d">Sin eventos de seguridad reportados en el período.</div></div>
              @endforelse
              @foreach($eventosCompactos as $e)
                <div class="evt min"><span class="dotmin" style="color:{{ $sevC($e['severidad']) }};">&#xf111;</span> <span class="mt">{{ $e['titulo'] }}</span>@if($e['geo'])<span class="mg"> · {{ $e['geo'] }}</span>@endif<span class="tag" style="background:{{ $sevC($e['severidad']) }};float:right;">{{ $e['severidad'] }}</span></div>
              @endforeach
            </div>
          </div>
          @if(count($marchas))
            <div class="card" style="margin-top:8px;">
              <div class="ch march"><span class="ic">&#xf0a1;</span>Marchas y Movilizaciones · {{ $stats['marchas'] }}</div>
              <div class="cb">
                @foreach($marchas as $m)
                  <div class="evt min"><span class="dotmin" style="color:#7C3AED;">&#xf111;</span> <span class="mt">{{ $m['titulo'] }}</span>@if($m['geo'])<span class="mg"> · {{ $m['geo'] }}</span>@endif<span class="tag" style="background:#7C3AED;float:right;">{{ in_array(mb_strtoupper($m['severidad']),['CRÍTICO','CRITICO','ALTO','MEDIO','BAJO']) ? $m['severidad'] : 'MARCHA' }}</span></div>
                @endforeach
              </div>
            </div>
          @endif
        </td>
        <td style="width:38%;">
          <div class="card">
            <div class="ch"><span class="ic">&#xf058;</span>Recomendaciones</div>
            <div class="cb">
              @forelse($recomendaciones as $r)
                <div class="bl"><span class="ic" style="color:#16A34A;">&#xf00c;</span><b>{{ $r['label'] }}:</b> {{ $r['texto'] }}</div>
              @empty
                <div class="bl" style="color:#6B7280;">Sin recomendaciones específicas para el período.</div>
              @endforelse
            </div>
          </div>
          @if(count($ambientales))
            <div class="card" style="margin-top:8px;">
              <div class="ch env"><span class="ic">&#xf740;</span>Alertas Ambientales</div>
              <div class="cb">
                @foreach($ambientales as $a)
                  <div class="evt"><div class="evt-t" style="font-size:10px;">{{ $a['titulo'] }}</div>@if($a['descripcion'])<div class="evt-d">{{ $a['descripcion'] }}</div>@endif</div>
                @endforeach
              </div>
            </div>
          @endif
        </td>
      </tr>
    </table>

  </div>
