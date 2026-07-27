<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<style>
  html, body { margin:0; padding:0; background:transparent; }
  svg { display:block; }
  .dep { stroke:#ffffff; stroke-width:0.7; stroke-linejoin:round; }
</style>
</head>
<body>
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="{{ $viewBox }}" width="920" height="1041">
    @foreach($departments as $d)
      <path class="dep" d="{{ $d['path'] }}" fill="{{ $d['fill'] }}"></path>
    @endforeach
  </svg>
</body>
</html>
