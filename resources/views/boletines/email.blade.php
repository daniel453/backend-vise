<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
@include('boletines._pdf_styles', ['faDataUri' => $national['faDataUri'] ?? null])
</head>
<body>

  {{-- Página 1: boletín NACIONAL --}}
  @include('boletines._pdf_section', $national)

  {{-- Una página por cada REGIONAL VISE con boletín (narrativa del workflow de regionales) --}}
  @foreach($regionales as $regional)
    <div class="page-break">
      @include('boletines._pdf_section', $regional)
    </div>
  @endforeach

  {{-- FOOTER único (position:fixed lo repite en cada página) --}}
  <div class="footer">
    VISE LTDA · Monitoreo de Orden Público · Movilidad · Gestión del Riesgo · Generado {{ $national['generatedAt']->format('d/m/Y H:i') }}
    <span class="tl">Seguridad con Propósito · Confianza Total</span>
  </div>
</body>
</html>
