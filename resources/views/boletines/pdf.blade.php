<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
@include('boletines._pdf_styles', ['faDataUri' => $faDataUri ?? null])
</head>
<body>

  @include('boletines._pdf_section')

  {{-- FOOTER (una sola vez; position:fixed lo repite en cada página) --}}
  <div class="footer">
    VISE LTDA · Monitoreo de Orden Público · Movilidad · Gestión del Riesgo · Generado {{ $generatedAt->format('d/m/Y H:i') }}
    <span class="tl">Seguridad con Propósito · Confianza Total</span>
  </div>
</body>
</html>
