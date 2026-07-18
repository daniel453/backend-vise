<!DOCTYPE html>
<html lang="es">
<head><meta charset="utf-8"></head>
<body style="margin:0;background:#F4F6FB;font-family:'Segoe UI',Arial,sans-serif;color:#1E293B;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#F4F6FB;padding:24px 0;">
    <tr><td align="center">
      <table width="560" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:12px;overflow:hidden;border:1px solid #E2E8F0;">
        <tr><td style="background:#0A2540;padding:22px 26px;">
          <div style="font-size:11px;font-weight:bold;letter-spacing:3px;text-transform:uppercase;color:#9db4cc;">VISE · Altum Risk</div>
          <div style="font-size:20px;font-weight:bold;color:#fff;margin-top:4px;">Boletín de Seguridad Nacional</div>
          @if($dateLabel)<div style="font-size:12px;color:#b8c6d6;margin-top:2px;">{{ $dateLabel }}</div>@endif
        </td></tr>
        <tr><td style="padding:24px 26px;font-size:14px;line-height:1.6;color:#334155;">
          <p style="margin:0 0 12px;">Buen día{{ $name ? ' '.$name : '' }},</p>
          <p style="margin:0 0 12px;">Adjunto encontrará el <b>boletín de seguridad y movilidad a nivel nacional</b> del día de hoy, con el panorama de orden público, vías y alertas relevantes.</p>
          <p style="margin:0;">Cordialmente,<br><b>VISE · Altum Risk</b></p>
        </td></tr>
        <tr><td style="padding:14px 26px;background:#F8FAFF;border-top:1px solid #EDF1F7;font-size:11px;color:#B91C1C;font-weight:bold;letter-spacing:1px;">
          CONFIDENCIAL · RESERVADO
        </td></tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
