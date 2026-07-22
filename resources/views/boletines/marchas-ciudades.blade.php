<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ciudades del boletín de marchas — Altum Risk</title>
<style>
  :root{--navy:#4C1D95;--border:#E2E8F0;--muted:#64748B;--red2:#B91C1C;--bg:#F4F6FB;--white:#fff;}
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:#1E293B;font-family:'Segoe UI',Arial,sans-serif;font-size:14px;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:680px;margin:0 auto;padding:16px;}
  .hero{background:var(--navy);color:#fff;border-radius:14px;padding:24px 22px;}
  .hero .brand{font-size:11px;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.55);}
  .hero h1{font-size:22px;font-weight:900;margin:6px 0 4px;}
  .hero .sub{font-size:13px;color:rgba(255,255,255,.7);}
  .hero a.back{font-size:12px;color:#e9d5ff;}
  .ok{background:#DCFCE7;color:#166534;border:1px solid #A7F3D0;border-radius:10px;padding:10px 14px;margin-top:14px;font-size:13px;}
  .err{background:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;border-radius:10px;padding:10px 14px;margin-top:14px;font-size:13px;}
  .card{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:18px;margin-top:16px;}
  .card h2{font-size:12px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin:0 0 12px;}
  form.add{display:flex;gap:8px;flex-wrap:wrap;}
  form.add input{font:inherit;font-size:14px;padding:11px 13px;border-radius:9px;border:1px solid var(--border);flex:2;min-width:220px;}
  form.add button{font:inherit;font-size:14px;font-weight:700;padding:11px 20px;border-radius:9px;border:none;background:var(--navy);color:#fff;cursor:pointer;}
  table{width:100%;border-collapse:collapse;margin-top:4px;}
  th{text-align:left;font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--muted);padding:8px 10px;border-bottom:2px solid var(--border);}
  td{padding:10px;border-bottom:1px solid #EDF1F7;font-size:13px;}
  .badge{font-size:10px;font-weight:800;padding:3px 9px;border-radius:20px;}
  .badge-on{background:#DCFCE7;color:#166534;} .badge-off{background:#F1F5F9;color:#64748B;}
  .mini{font:inherit;font-size:12px;font-weight:700;padding:5px 10px;border-radius:7px;border:1px solid var(--border);background:#fff;cursor:pointer;}
  .mini.del{color:var(--red2);border-color:#FCA5A5;}
  .empty{text-align:center;color:var(--muted);padding:26px;font-size:13px;}
  .inline{display:inline;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hero">
    <div class="brand">Grupo Altum · Boletines</div>
    <h1>Ciudades del boletín de marchas</h1>
    <div class="sub">El workflow de marchas busca movilizaciones solo en las ciudades <b>activas</b> de esta lista.</div>
    <div style="margin-top:8px;"><a class="back" href="{{ route('home') }}">← Volver a los boletines</a></div>
  </div>

  @if(session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

  <div class="card">
    <h2>Agregar ciudad</h2>
    <form class="add" action="{{ route('marchas.ciudades.store') }}" method="post">
      @csrf
      <input type="text" name="name" placeholder="Ej. Cúcuta" required>
      <button type="submit">Agregar</button>
    </form>
  </div>

  <div class="card">
    <h2>Ciudades monitoreadas ({{ $cities->count() }})</h2>
    @if($cities->isEmpty())
      <div class="empty">Aún no hay ciudades. Agrega la primera arriba.</div>
    @else
      <table>
        <thead><tr><th>Ciudad</th><th>Estado</th><th></th></tr></thead>
        <tbody>
          @foreach($cities as $c)
            <tr>
              <td><b>{{ $c->name }}</b></td>
              <td><span class="badge {{ $c->active ? 'badge-on' : 'badge-off' }}">{{ $c->active ? 'Activa' : 'Inactiva' }}</span></td>
              <td style="text-align:right;white-space:nowrap;">
                <form class="inline" action="{{ route('marchas.ciudades.toggle', $c) }}" method="post">
                  @csrf @method('PATCH')
                  <button class="mini" type="submit">{{ $c->active ? 'Desactivar' : 'Activar' }}</button>
                </form>
                <form class="inline" action="{{ route('marchas.ciudades.destroy', $c) }}" method="post" onsubmit="return confirm('¿Eliminar {{ $c->name }}?')">
                  @csrf @method('DELETE')
                  <button class="mini del" type="submit">Eliminar</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

</div>
</body>
</html>
