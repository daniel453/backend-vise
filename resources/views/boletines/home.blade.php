<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boletines de Seguridad — VISE Ltda</title>
@include('boletines._web_base')
<style>
  /* ---- Hero ---- */
  .hero{background:linear-gradient(135deg,var(--g-800),var(--g-900));color:#fff;border-radius:18px;padding:30px;display:flex;gap:26px;align-items:center;flex-wrap:wrap;box-shadow:var(--shadow);}
  .hero-l{flex:1 1 340px;min-width:270px;}
  .hero h1{font-size:29px;font-weight:900;margin:11px 0 8px;line-height:1.15;}
  .hero-sub{font-size:14px;color:rgba(255,255,255,.76);max-width:520px;}
  .hero-cta{margin-top:20px;}
  .hero-r{flex:0 0 auto;}
  .upd-card{background:rgba(255,255,255,.06);border:1px solid rgba(199,154,43,.45);border-radius:14px;padding:18px 24px;min-width:238px;text-align:center;}
  .upd-lbl{font-size:11px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--gold-2);}
  .upd-lbl i{margin-right:5px;}
  .upd-date{font-size:26px;font-weight:900;margin-top:9px;line-height:1.15;color:#fff;}
  .upd-time{font-size:12.5px;color:rgba(255,255,255,.66);margin-top:6px;font-variant-numeric:tabular-nums;}

  /* ---- Panel nacional ---- */
  .nat{display:flex;align-items:center;gap:18px;background:linear-gradient(120deg,var(--g-700),var(--g-800));color:#fff;border-radius:var(--radius);padding:20px 22px;border-left:6px solid var(--gold);box-shadow:var(--shadow-sm);transition:.14s;}
  .nat:hover{box-shadow:var(--shadow);transform:translateY(-2px);}
  .nat-icon{width:52px;height:52px;flex:0 0 auto;border-radius:12px;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;font-size:23px;color:var(--gold-2);}
  .nat-body{flex:1;min-width:0;}
  .nat-eyebrow{font-size:10.5px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);}
  .nat-title{font-size:19px;font-weight:900;margin-top:2px;}
  .nat-meta{font-size:13px;color:rgba(255,255,255,.8);margin-top:5px;}
  .nat-meta b{font-weight:900;} .nat-meta b.red{color:#FCA5A5;}
  .nat-headline{font-size:12.5px;color:var(--gold-2);margin-top:8px;line-height:1.4;}
  .nat-go{flex:0 0 auto;font-size:18px;color:rgba(255,255,255,.55);}

  /* ---- Tarjetas de región ---- */
  .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;}
  @media(max-width:820px){.cards{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:520px){.cards{grid-template-columns:1fr;}}
  .rcard{display:flex;flex-direction:column;background:var(--white);border:1px solid var(--line);border-radius:var(--radius);padding:17px 18px;border-top:4px solid var(--line);box-shadow:var(--shadow-sm);transition:.14s;}
  .rcard:hover{box-shadow:var(--shadow);transform:translateY(-3px);}
  .rcard.crit{border-top-color:var(--red);} .rcard.alto{border-top-color:var(--amber);} .rcard.ok{border-top-color:var(--ok);}
  .rcard-h{display:flex;justify-content:space-between;align-items:center;}
  .rcard-name{font-size:16.5px;font-weight:800;color:var(--g-800);display:inline-flex;align-items:center;gap:8px;}
  .rcard-name i{color:var(--g-600);font-size:14px;}
  .rcard-stats{display:flex;flex-wrap:wrap;gap:14px;margin-top:12px;font-size:12.5px;color:var(--slate);}
  .rcard-stats i{margin-right:5px;color:var(--muted);}
  .rcard-stats .red{color:var(--red);font-weight:700;} .rcard-stats .red i{color:var(--red);}
  .rcard-go{margin-top:14px;font-size:12px;font-weight:700;color:var(--g-700);display:inline-flex;align-items:center;gap:6px;}
  .rcard:hover .rcard-go i{transform:translateX(3px);}
  .rcard-go i{transition:.14s;}

  .empty{background:var(--white);border:1px dashed var(--line);border-radius:var(--radius);padding:36px;text-align:center;color:var(--muted);}
</style>
</head>
<body>
@php
  $sevClass = fn($b) => ($b && $b->critical_events>0)?'crit':(($b && $b->total_events>=3)?'alto':'ok');
  $upd = $updatedAt ? \Illuminate\Support\Carbon::parse($updatedAt) : null;
@endphp

@include('boletines._web_topbar')

<div class="wrap">

  <div class="hero">
    <div class="hero-l">
      <div class="eyebrow" style="color:var(--gold-2)">Boletín de Seguridad · Orden Público y Movilidad</div>
      <h1>El panorama de tu zona, siempre al día</h1>
      <div class="hero-sub">Consulta el estado de seguridad, orden público y movilidad por región y departamento. Se actualiza automáticamente cada dos horas.</div>
      <div class="hero-cta">
        <a class="btn btn-gold" href="{{ route('boletin.pdf', ['level'=>'nacional']) }}" target="_blank"><i class="fa-solid fa-file-arrow-down"></i> Exportar boletín nacional (PDF)</a>
      </div>
    </div>
    <div class="hero-r">
      <div class="upd-card">
        <div class="upd-lbl"><i class="fa-regular fa-clock"></i> Última actualización</div>
        @if($upd)
          <div class="upd-date">{{ ucfirst($upd->locale('es')->isoFormat('D [de] MMMM [de] YYYY')) }}</div>
          <div class="upd-time">{{ $upd->format('H:i') }} · hora Colombia</div>
        @else
          <div class="upd-date" style="font-size:20px;">Sin boletín aún</div>
        @endif
      </div>
    </div>
  </div>

  @if(!$national && $regions->isEmpty())
    <div class="empty" style="margin-top:20px;"><i class="fa-regular fa-folder-open" style="font-size:24px;color:var(--muted);"></i><div style="margin-top:8px;">Aún no hay boletines generados. Vuelve más tarde.</div></div>
  @else

  <div class="section-t"><i class="fa-solid fa-shield-halved"></i> Panorama nacional</div>
  <a class="nat" href="{{ route('boletin', ['level'=>'nacional']) }}">
    <div class="nat-icon"><i class="fa-solid fa-earth-americas"></i></div>
    <div class="nat-body">
      <div class="nat-eyebrow">Colombia</div>
      <div class="nat-title">Boletín Nacional</div>
      <div class="nat-meta"><b>{{ $national?->total_events ?? 0 }}</b> eventos · <b class="red">{{ $national?->critical_events ?? 0 }}</b> críticos</div>
      @if($national?->headline)<div class="nat-headline">{{ $national->headline }}</div>@endif
    </div>
    <div class="nat-go"><i class="fa-solid fa-arrow-right"></i></div>
  </a>

  <div class="section-t"><i class="fa-solid fa-location-dot"></i> Por región (sucursal)</div>
  <div class="cards">
    @forelse($regions as $r)
      <a class="rcard {{ $sevClass($r) }}" href="{{ route('boletin', ['level'=>'region','scope'=>$r->scope]) }}">
        <div class="rcard-h">
          <span class="rcard-name"><i class="fa-solid fa-location-dot"></i> {{ $r->scope }}</span>
          <span class="sev-dot" style="background:{{ $r->critical_events>0?'var(--red)':($r->total_events>=3?'var(--amber)':'var(--ok)') }};"></span>
        </div>
        <div class="rcard-stats">
          <span><i class="fa-solid fa-circle-exclamation"></i>{{ $r->total_events }} eventos</span>
          <span class="{{ $r->critical_events>0?'red':'' }}"><i class="fa-solid fa-triangle-exclamation"></i>{{ $r->critical_events }} críticos</span>
          <span><i class="fa-solid fa-road"></i>{{ $r->roads_affected }} vías</span>
        </div>
        <div class="rcard-go">Ver boletín <i class="fa-solid fa-arrow-right"></i></div>
      </a>
    @empty
      <div class="empty">Sin boletines regionales en la última corrida.</div>
    @endforelse
  </div>

  @endif
</div>

<div class="site-foot">
  <div class="bar"></div>
  <span class="mk">VISE Ltda</span> · Inteligencia de Seguridad y Movilidad · Monitoreo permanente 24/7
</div>
</body>
</html>
