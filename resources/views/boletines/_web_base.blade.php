{{-- Cabecera web compartida por las páginas públicas de boletines: librería de
     íconos (Font Awesome 6) + paleta corporativa VISE (verde/dorado) + estilos
     base y componentes comunes (topbar, botones, tarjetas, badges, footer). --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
  :root{
    --g-900:#0E2E20; --g-800:#14432F; --g-700:#1B5E3F; --g-600:#237049; --g-500:#2E8B5A;
    --gold:#C79A2B; --gold-2:#E4C15A;
    --ink:#16241D; --slate:#43574C; --muted:#7B8A81;
    --line:#E3EAE4; --line-2:#EEF2ED; --bg:#F2F6F2; --white:#fff;
    --red:#DC2626; --orange:#EA580C; --amber:#D97706; --ok:#16A34A; --blue:#2563EB; --purple:#7C3AED;
    --shadow:0 10px 30px rgba(14,46,32,.09); --shadow-sm:0 2px 10px rgba(14,46,32,.06);
    --radius:14px;
  }
  *{box-sizing:border-box;}
  body{margin:0;background:var(--bg);color:var(--ink);font-family:'Segoe UI',system-ui,-apple-system,Arial,sans-serif;font-size:14px;line-height:1.55;}
  a{text-decoration:none;color:inherit;}
  .wrap{max-width:1160px;margin:0 auto;padding:20px 18px;}

  /* ---- Topbar ---- */
  .nav{position:sticky;top:0;z-index:30;background:var(--g-800);color:#fff;box-shadow:var(--shadow-sm);border-bottom:2px solid var(--gold);}
  .nav-in{max-width:1160px;margin:0 auto;padding:10px 18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap;}
  .logo{display:inline-flex;align-items:center;gap:11px;}
  .logo-mark{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--g-600),var(--g-900));display:inline-flex;align-items:center;justify-content:center;font-weight:900;font-size:19px;color:var(--gold-2);box-shadow:inset 0 0 0 2px rgba(199,154,43,.35);}
  .logo-word{font-weight:800;letter-spacing:.4px;font-size:16px;line-height:1;color:#fff;}
  .logo-word small{display:block;font-size:8.5px;letter-spacing:2px;color:rgba(255,255,255,.55);text-transform:uppercase;font-weight:700;margin-top:3px;}
  .nav-links{margin-left:auto;display:flex;gap:2px;flex-wrap:wrap;}
  .nav-links a{display:inline-flex;align-items:center;gap:7px;font-size:12.5px;font-weight:600;color:rgba(255,255,255,.82);padding:8px 12px;border-radius:8px;transition:.12s;}
  .nav-links a:hover{background:rgba(255,255,255,.11);color:#fff;}
  .nav-links a i{color:var(--gold-2);font-size:12px;width:14px;text-align:center;}

  /* ---- Botones ---- */
  .btn{display:inline-flex;align-items:center;gap:8px;font-size:13.5px;font-weight:700;padding:11px 18px;border-radius:10px;cursor:pointer;border:1px solid transparent;transition:.14s;}
  .btn i{font-size:13px;}
  .btn-gold{background:var(--gold);color:#241a02;} .btn-gold:hover{background:var(--gold-2);}
  .btn-ghost{background:rgba(255,255,255,.08);color:#fff;border-color:rgba(255,255,255,.30);} .btn-ghost:hover{background:rgba(255,255,255,.16);}
  .btn-solid{background:var(--g-700);color:#fff;} .btn-solid:hover{background:var(--g-600);}

  /* ---- Átomos comunes ---- */
  .eyebrow{font-size:11px;font-weight:800;letter-spacing:3px;text-transform:uppercase;}
  .section-t{display:flex;align-items:center;gap:9px;font-size:12px;font-weight:800;letter-spacing:1.5px;text-transform:uppercase;color:var(--slate);margin:26px 2px 12px;}
  .section-t i{color:var(--g-600);}
  .card{background:var(--white);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow-sm);}
  .sev-dot{width:9px;height:9px;border-radius:50%;display:inline-block;}

  /* ---- Footer ---- */
  .site-foot{max-width:1160px;margin:26px auto 30px;padding:0 18px;text-align:center;color:var(--muted);font-size:11.5px;}
  .site-foot .bar{height:1px;background:var(--line);margin:0 0 14px;}
  .site-foot b{color:var(--g-700);letter-spacing:1px;}
  .site-foot .mk{color:var(--gold);font-weight:800;}
</style>
