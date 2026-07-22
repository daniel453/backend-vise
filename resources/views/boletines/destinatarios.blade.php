<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Destinatarios del reporte nacional — Altum Risk</title>
<style>
  :root{--navy:#0A2540;--navy2:#12324f;--red2:#B91C1C;--green:#16A34A;--muted:#64748B;--border:#E2E8F0;--bg:#F4F6FB;--white:#fff;}
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:#1E293B;font-family:'Segoe UI',Arial,sans-serif;font-size:14px;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:760px;margin:0 auto;padding:16px;}
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
  form.add input[name=email]{flex:2;min-width:220px;} form.add input[name=name]{flex:1;min-width:150px;}
  form.add button{font:inherit;font-size:14px;font-weight:700;padding:11px 20px;border-radius:9px;border:none;background:var(--red2);color:#fff;cursor:pointer;}

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
    <div class="brand">VISE · Boletines</div>
    <h1>Destinatarios del boletín</h1>
    <div class="sub">Cada correo recibe el boletín en PDF cuando se envía. Si le asignas una <b>regional</b>, recibe <b>Nacional + su regional</b>; si lo dejas en <b>Nacional</b>, recibe el panorama nacional con todas las regionales.</div>
    <div style="margin-top:8px;"><a class="back" href="{{ route('home') }}">← Volver a los boletines</a></div>
  </div>

  @if(session('ok'))<div class="ok">{{ session('ok') }}</div>@endif
  @if($errors->any())<div class="err">{{ $errors->first() }}</div>@endif

  <div class="card">
    <h2>Agregar destinatario</h2>
    <form class="add" action="{{ route('destinatarios.store') }}" method="post">
      @csrf
      <input type="email" name="email" placeholder="correo@empresa.com" required>
      <input type="text" name="name" placeholder="Nombre o empresa (opcional)">
      <select name="regional_id" style="font:inherit;font-size:14px;padding:11px 13px;border-radius:9px;border:1px solid var(--border);flex:1;min-width:160px;background:#fff;">
        <option value="">Nacional (todas las regionales)</option>
        @foreach($regionals as $rg)
          <option value="{{ $rg->id }}">Regional {{ $rg->name }}</option>
        @endforeach
      </select>
      <button type="submit">Agregar</button>
    </form>
  </div>

  <div class="card">
    <h2>Probar / Enviar el boletín</h2>
    <form class="add" action="{{ route('destinatarios.prueba') }}" method="post" style="margin-bottom:12px;">
      @csrf
      <input type="email" name="test_email" placeholder="tucorreo@para-probar.com" required>
      <button type="submit">Enviar prueba a este correo</button>
    </form>
    <form action="{{ route('destinatarios.enviar') }}" method="post" onsubmit="return confirm('¿Enviar el boletín nacional a TODOS los destinatarios activos AHORA?')">
      @csrf
      <button type="submit" style="font:inherit;font-size:14px;font-weight:700;padding:11px 20px;border-radius:9px;border:none;background:#0A2540;color:#fff;cursor:pointer;">Enviar a TODOS ahora</button>
    </form>
    <div style="font-size:12px;color:#64748B;margin-top:10px;line-height:1.5;">
      La <b>prueba</b> manda solo a ese correo (para verificar que las cuentas Gmail funcionan, sin spamear a los clientes).
      “Enviar a todos” ignora el horario y manda ya a los activos.
    </div>
  </div>

  <div class="card">
    <h2>Lista de destinatarios ({{ $recipients->count() }})</h2>
    @if($recipients->isEmpty())
      <div class="empty">Aún no hay correos. Agrega el primero arriba.</div>
    @else
      <table>
        <thead><tr><th>Correo</th><th>Nombre</th><th>Ámbito</th><th>Estado</th><th></th></tr></thead>
        <tbody>
          @foreach($recipients as $r)
            <tr>
              <td><b>{{ $r->email }}</b></td>
              <td>{{ $r->name ?? '—' }}</td>
              <td>{{ $r->regional ? 'Regional '.$r->regional->name : 'Nacional' }}</td>
              <td><span class="badge {{ $r->active ? 'badge-on' : 'badge-off' }}">{{ $r->active ? 'Activo' : 'Inactivo' }}</span></td>
              <td style="text-align:right;white-space:nowrap;">
                <form class="inline" action="{{ route('destinatarios.toggle', $r) }}" method="post">
                  @csrf @method('PATCH')
                  <button class="mini" type="submit">{{ $r->active ? 'Desactivar' : 'Activar' }}</button>
                </form>
                <form class="inline" action="{{ route('destinatarios.destroy', $r) }}" method="post" onsubmit="return confirm('¿Eliminar {{ $r->email }}?')">
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
