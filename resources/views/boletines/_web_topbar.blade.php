{{-- Barra de navegación superior, compartida por las páginas públicas. --}}
<nav class="nav">
  <div class="nav-in">
    <a class="logo" href="{{ route('home') }}">
      <span class="logo-mark">V</span>
      <span class="logo-word">VISE <small>Ltda · Inteligencia de Seguridad</small></span>
    </a>
    <div class="nav-links">
      <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i> Inicio</a>
      <a href="{{ route('destinatarios') }}"><i class="fa-solid fa-envelope"></i> Destinatarios</a>
      <a href="{{ route('fechas') }}"><i class="fa-solid fa-calendar-days"></i> Fechas especiales</a>
      <a href="{{ route('marchas.ciudades') }}"><i class="fa-solid fa-person-walking"></i> Ciudades de marchas</a>
    </div>
  </div>
</nav>
