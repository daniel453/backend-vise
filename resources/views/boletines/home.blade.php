<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Boletines de Seguridad — Altum Risk</title>
<style>
  :root{--navy:#0A2540;--navy2:#12324f;--red:#DC2626;--red2:#B91C1C;--orange:#E8750A;--blue:#2851A3;--green:#16A34A;--muted:#64748B;--border:#E2E8F0;--bg:#F4F6FB;--white:#fff;}
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:#1E293B;font-family:'Segoe UI',Arial,sans-serif;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:1060px;margin:0 auto;padding:16px;}

  .hero{background:var(--navy);color:#fff;border-radius:16px;padding:34px 30px;text-align:center;}
  .hero .brand{font-size:11px;font-weight:800;letter-spacing:4px;text-transform:uppercase;color:rgba(255,255,255,.55);}
  .hero h1{font-size:30px;font-weight:900;margin:8px 0 4px;}
  .hero .sub{font-size:14px;color:rgba(255,255,255,.7);}
  .hero .upd{font-size:12px;color:rgba(255,255,255,.5);margin-top:8px;font-family:monospace;}

  .actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;margin:22px auto 0;}
  .btn{font:inherit;font-size:14px;font-weight:700;padding:12px 20px;border-radius:10px;cursor:pointer;border:1px solid transparent;display:inline-flex;align-items:center;gap:8px;text-decoration:none;}
  .btn-red{background:var(--red2);color:#fff;}
  .btn-red:hover{background:#a01818;}
  .btn-ghost{background:transparent;color:#fff;border-color:rgba(255,255,255,.35);}
  .btn-ghost:hover{background:rgba(255,255,255,.1);}

  .section-t{font-size:12px;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--muted);margin:26px 4px 10px;}

  .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;}
  @media(max-width:720px){.cards{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:460px){.cards{grid-template-columns:1fr;}}
  .card{background:var(--white);border:1px solid var(--border);border-radius:13px;padding:18px;display:block;transition:.12s;border-left:5px solid var(--border);}
  .card:hover{box-shadow:0 6px 18px rgba(10,37,64,.10);transform:translateY(-2px);}
  .card.crit{border-left-color:var(--red2);} .card.alto{border-left-color:var(--orange);} .card.ok{border-left-color:var(--green);}
  .card .name{font-size:17px;font-weight:800;color:var(--navy);}
  .card .meta{font-size:12px;color:var(--muted);margin-top:6px;}
  .card .n{font-weight:800;}
  .card .n.red{color:var(--red2);} .card .n.green{color:var(--green);}

  .nacional{background:linear-gradient(120deg,var(--navy),var(--navy2));color:#fff;border:none;border-left:5px solid var(--red2);}
  .nacional .name{color:#fff;} .nacional .meta{color:rgba(255,255,255,.7);}
  .nacional .headline{font-size:12px;color:#FFD9A8;margin-top:8px;}

  .empty{background:var(--white);border:1px solid var(--border);border-radius:13px;padding:36px;text-align:center;color:var(--muted);}
  .foot{text-align:center;font-size:11px;color:var(--muted);margin-top:26px;}
</style>
</head>
<body>
@php $sevClass = fn($b) => ($b && $b->critical_events>0)?'crit':(($b && $b->total_events>=3)?'alto':'ok'); @endphp
<div class="wrap">

  <div class="hero">
    <div class="brand">VISE · Boletines</div>
    <h1>Boletín de Seguridad</h1>
    <div class="sub">Consulta el panorama de seguridad, orden público y movilidad de tu zona.</div>
    @if($updatedAt)<div class="upd">Última actualización: {{ \Illuminate\Support\Carbon::parse($updatedAt)->format('d/m/Y · H:i') }}</div>@endif

    <div class="actions">
      <a class="btn btn-red" href="{{ route('boletin.pdf', ['level'=>'nacional']) }}" target="_blank">⬇ Exportar boletín nacional (PDF)</a>
      <a class="btn btn-ghost" href="{{ route('destinatarios') }}">✉ Destinatarios del reporte</a>
      <a class="btn btn-ghost" href="{{ route('fechas') }}">📅 Fechas especiales</a>
    </div>
  </div>

  @if(!$national && $regions->isEmpty())
    <div class="empty" style="margin-top:16px;">Aún no hay boletines generados. Vuelve más tarde.</div>
  @else

  <div class="section-t">Panorama nacional</div>
  <a class="card nacional" href="{{ route('boletin', ['level'=>'nacional']) }}">
    <div class="name">🇨🇴 Colombia — Boletín Nacional</div>
    <div class="meta"><span class="n">{{ $national?->total_events ?? 0 }}</span> eventos · <span class="n">{{ $national?->critical_events ?? 0 }}</span> críticos</div>
    @if($national?->headline)<div class="headline">{{ $national->headline }}</div>@endif
  </a>

  <div class="section-t">Por región</div>
  <div class="cards">
    @forelse($regions as $r)
      <a class="card {{ $sevClass($r) }}" href="{{ route('boletin', ['level'=>'region','scope'=>$r->scope]) }}">
        <div class="name">{{ $r->scope }}</div>
        <div class="meta"><span class="n {{ $r->critical_events>0?'red':'' }}">{{ $r->total_events }}</span> eventos · <span class="n">{{ $r->critical_events }}</span> críticos · {{ $r->roads_affected }} vías</div>
      </a>
    @empty
      <div class="empty">Sin boletines regionales en la última corrida.</div>
    @endforelse
  </div>

  @endif
</div>
</body>
</html>
