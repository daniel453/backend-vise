<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Fechas especiales — Altum Risk</title>
<style>
  :root{--navy:#0A2540;--red2:#B91C1C;--orange:#E8750A;--muted:#64748B;--border:#E2E8F0;--bg:#F4F6FB;--white:#fff;}
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:#1E293B;font-family:'Segoe UI',Arial,sans-serif;font-size:14px;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:720px;margin:0 auto;padding:16px;}
  .hero{background:var(--navy);color:#fff;border-radius:14px;padding:24px 22px;}
  .hero .brand{font-size:11px;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:rgba(255,255,255,.55);}
  .hero h1{font-size:22px;font-weight:900;margin:6px 0 4px;}
  .hero .sub{font-size:13px;color:rgba(255,255,255,.7);}
  .hero a.back{font-size:12px;color:#cfe0f2;}
  .ok{background:#DCFCE7;color:#166534;border:1px solid #A7F3D0;border-radius:10px;padding:10px 14px;margin-top:14px;font-size:13px;}
  .err{background:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5;border-radius:10px;padding:10px 14px;margin-top:14px;font-size:13px;}
  .card{background:var(--white);border:1px solid var(--border);border-radius:12px;padding:18px;margin-top:16px;}
  .card h2{font-size:12px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin:0 0 12px;}
  form.add{display:flex;gap:8px;flex-wrap:wrap;}
  form.add input{font:inherit;font-size:14px;padding:11px 13px;border-radius:9px;border:1px solid var(--border);}
  form.add input[name=date]{min-width:170px;} form.add input[name=description]{flex:1;min-width:200px;}
  form.add button{font:inherit;font-size:14px;font-weight:700;padding:11px 20px;border-radius:9px;border:none;background:var(--red2);color:#fff;cursor:pointer;}
  table{width:100%;border-collapse:collapse;margin-top:4px;}
  th{text-align:left;font-size:10px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:var(--muted);padding:8px 10px;border-bottom:2px solid var(--border);}
  td{padding:10px;border-bottom:1px solid #EDF1F7;font-size:13px;}
  .badge{font-size:10px;font-weight:800;padding:3px 9px;border-radius:20px;background:#FFEDD5;color:#9A3412;}
  .mini{font:inherit;font-size:12px;font-weight:700;padding:5px 10px;border-radius:7px;border:1px solid #FCA5A5;background:#fff;cursor:pointer;color:var(--red2);}
  .empty{text-align:center;color:var(--muted);padding:26px;font-size:13px;}
  .note{font-size:12px;color:var(--muted);margin-top:10px;line-height:1.5;}
</style>
</head>
<body>
<div class="wrap">

  <div class="hero">
    <div class="brand">VISE · Boletines</div>
    <h1>Fechas especiales</h1>
    <div class="sub">En estos días el boletín se envía <b>cada 2 horas</b>. El resto de días, <b>una vez al día</b>.</div>
    <div style="margin-top:8px;"><a class="back" href="{{ route('home') }}">← Volver a los boletines</a></div>
  </div>

  @if(session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

  <div class="card">
    <h2>Agregar fecha especial</h2>
    <form class="add" action="{{ route('fechas.store') }}" method="post">
      @csrf
      <input type="date" name="date" required>
      <input type="text" name="description" placeholder="Motivo (ej. Elecciones, Paro nacional)">
      <button type="submit">Agregar</button>
    </form>
    <div class="note">Ese día, cada corrida del flujo (cada 2h) enviará el boletín. Los demás días solo sale una vez, a la hora configurada.</div>
  </div>

  <div class="card">
    <h2>Fechas cargadas ({{ $dates->count() }})</h2>
    @if($dates->isEmpty())
      <div class="empty">No hay fechas especiales. Sin ellas, el envío es diario.</div>
    @else
      <table>
        <thead><tr><th>Fecha</th><th>Motivo</th><th></th></tr></thead>
        <tbody>
          @foreach($dates as $d)
            <tr>
              <td><b>{{ $d->date->format('d/m/Y') }}</b> <span class="badge">cada 2h</span></td>
              <td>{{ $d->description ?? '—' }}</td>
              <td style="text-align:right;">
                <form action="{{ route('fechas.destroy', $d) }}" method="post" onsubmit="return confirm('¿Eliminar {{ $d->date->format('d/m/Y') }}?')">
                  @csrf @method('DELETE')
                  <button class="mini" type="submit">Eliminar</button>
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
