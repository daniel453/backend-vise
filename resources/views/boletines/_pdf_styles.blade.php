{{-- Estilos compartidos del PDF de boletines (una sola definición para todo el
     documento, se incluye una vez en el <head>). Necesita $faDataUri. --}}
<style>
  /* Identidad VISE — panorama operativo verde, estilo infografía (tarjetas
     redondeadas + iconos Font Awesome). Mismos datos del presenter. */
  @page { margin: 0 0 30pt 0; }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', sans-serif; background: #EAF1EC; color: #1F2937; font-size: 10px; line-height: 1.4; }
  strong, b { font-weight: bold; }
@if(!empty($faDataUri))
  @font-face { font-family: 'FA'; font-style: normal; font-weight: normal; src: url("{{ $faDataUri }}") format("truetype"); }
@endif
  .ic { font-family: 'FA'; font-weight: normal; font-style: normal; }

  /* HEADER */
  .hd { width: 100%; background: #14432F; color: #fff; border-collapse: collapse; }
  .hd td { padding: 10px 16px; vertical-align: middle; }
  .logo { height: 56px; background: #fff; border-radius: 5px; padding: 3px; vertical-align: middle; }
  .reg { font-size: 8px; letter-spacing: 1.5px; color: #A7D7B8; text-transform: uppercase; margin-top: 5px; }
  .tt { font-size: 17px; font-weight: bold; text-transform: uppercase; line-height: 1.05; text-align: center; }
  .ts { font-size: 8px; letter-spacing: 2px; color: #A7D7B8; text-transform: uppercase; text-align: center; margin-top: 4px; }
  .dbx { background: #fff; color: #14432F; border-radius: 6px; padding: 4px 13px; text-align: center; }
  .dbx .d { font-size: 21px; font-weight: bold; line-height: 1; }
  .dbx .m { font-size: 8px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
  .dbx .y { font-size: 8px; color: #4B7A5E; }
  .subbar { background: #1B5E3F; color: #fff; text-align: center; padding: 5px; font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; border-bottom: 3px solid #F0B429; }

  /* Marca GRUPO ALTUM en el header + rótulo de monitoreo */
  .brand { font-size: 15px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; line-height: 1; }
  .brand-sub { font-size: 6.5px; letter-spacing: 2px; color: #A7D7B8; text-transform: uppercase; margin-top: 3px; }
  .h-monitoreo { font-size: 7px; letter-spacing: 2px; color: #A7D7B8; text-transform: uppercase; font-weight: bold; margin-bottom: 4px; }

  /* HERO — titular del día (data del navy) */
  .hero { width: 100%; background: #1B5E3F; color: #fff; border-collapse: collapse; }
  .hero td { padding: 9px 16px; vertical-align: middle; }
  .hero-alerta { font-size: 7px; font-weight: bold; letter-spacing: 2.5px; color: #FFC9A3; text-transform: uppercase; margin-bottom: 5px; }
  .hero-titulo { font-size: 15px; font-weight: bold; text-transform: uppercase; line-height: 1.12; }
  .hero-sub { font-size: 8px; color: #C9E4D3; margin-top: 5px; }
  .hero-estado { text-align: right; white-space: nowrap; }
  .hero-estado-lbl { font-size: 6.5px; letter-spacing: 2px; color: rgba(255,255,255,0.5); text-transform: uppercase; }
  .hero-estado-val { font-size: 8px; font-weight: bold; letter-spacing: 1.5px; color: #4ADE80; text-transform: uppercase; margin-top: 3px; }

  /* RADAR bar */
  .radar { width: 100%; background: #14432F; color: #fff; border-collapse: collapse; border-bottom: 3px solid #F0B429; }
  .radar td { padding: 5px 16px; vertical-align: middle; }
  .radar-l { font-size: 9px; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; }
  .radar-r { text-align: right; white-space: nowrap; }
  .radar-upd { font-size: 8px; color: rgba(255,255,255,0.55); margin-right: 12px; }
  .nivel-badge { display: inline-block; font-size: 9px; font-weight: bold; padding: 3px 12px; letter-spacing: 1.5px; text-transform: uppercase; color: #fff; border-radius: 3px; }

  .wrap { padding: 9px; }
  .grid { width: 100%; border-collapse: separate; border-spacing: 8px 0; }
  .grid > tbody > tr > td { vertical-align: top; }

  /* Tarjetas */
  .card { background: #fff; border: 1px solid #CFE3D6; border-radius: 7px; overflow: hidden; }
  .ch { background: #1B5E3F; color: #fff; padding: 6px 11px; font-size: 9px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; }
  .ch.env { background: #0A3D2E; } .ch.march { background: #4C1D95; }
  .ch .ic { margin-right: 6px; }
  .cb { padding: 9px 11px; }

  /* Contador (5 cifras) */
  .stats { width: 100%; border-collapse: collapse; background: #fff; border: 1px solid #CFE3D6; border-radius: 7px; }
  .stats td { width: 20%; text-align: center; padding: 8px 6px; border-right: 1px solid #E1EDE4; }
  .stats td:last-child { border-right: none; }
  .stat-n { font-size: 25px; font-weight: bold; line-height: 1; color: #1B5E3F; }
  .stat-n.red { color: #DC2626; } .stat-n.orange { color: #EA580C; } .stat-n.purple { color: #7C3AED; }
  .stat-l { font-size: 7px; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #6B7280; margin-top: 3px; }
  .stat-bar { height: 3px; margin-top: 5px; background: #E1EDE4; border-radius: 2px; }
  .stat-bar-fill { height: 3px; border-radius: 2px; }

  /* Viñetas / filas */
  .bl { padding: 4px 0; border-bottom: 1px solid #EEF4F0; font-size: 9px; color: #374151; }
  .bl:last-child { border-bottom: none; }
  .bl .ic { color: #1B5E3F; margin-right: 5px; }
  .bl b { color: #14432F; }
  .nivbadge { display: inline-block; color: #fff; font-size: 12px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 4px 14px; border-radius: 4px; }
  .rrow { padding: 4px 0; border-bottom: 1px solid #EEF4F0; font-size: 9px; color: #374151; }
  .rrow:last-child { border-bottom: none; }
  .rpill { float: right; color: #fff; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 8px; border-radius: 3px; }
  .rpill.medio { color: #1F2937; }

  /* Eventos */
  .evt { padding: 6px 0; border-bottom: 1px solid #EEF4F0; }
  .evt:last-child { border-bottom: none; }
  .evt-t { font-size: 11px; font-weight: bold; color: #14432F; line-height: 1.2; }
  .evt-d { font-size: 8.5px; color: #374151; margin: 2px 0 3px; line-height: 1.4; }
  .evt.min { padding: 4px 0; font-size: 9px; }
  .evt.min .mt { font-weight: bold; color: #1F2937; }
  .evt.min .mg { color: #6B7280; font-size: 8.5px; }
  .tag { display: inline-block; color: #fff; font-size: 7px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; padding: 2px 7px; border-radius: 2px; margin-right: 4px; }
  .tag.geo { background: #EAF3EC; color: #1B5E3F; border: 1px solid #B7D8C1; }
  .dotmin { font-family: 'FA'; font-size: 7px; }

  /* FOOTER */
  .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 30pt; background: #14432F; color: #fff; text-align: center; padding: 6px 14px; font-size: 8px; letter-spacing: 0.5px; border-top: 3px solid #F0B429; }
  .footer .tl { color: #A7D7B8; font-weight: bold; letter-spacing: 2px; display: block; margin-top: 3px; text-transform: uppercase; }

  /* Salto de página entre secciones (nacional + cada regional en el correo combinado) */
  .page-break { page-break-before: always; }
</style>
