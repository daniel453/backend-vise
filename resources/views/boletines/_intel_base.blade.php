{{-- Design-system del portal de boletines VISE (estilo "Central de Monitoreo /
     blueprint"): fuentes Barlow, paleta verde institucional, y los componentes
     base (blueprint + esquinas, tags, tablas, botones). Importado por el detalle. --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow:wght@400;500;600;700&family=Barlow+Condensed:wght@400;500;600;700&display=swap">
<style>
  :root{
    --color-bg:#f2f2f3; --color-surface:#e9e9ea; --color-text:#1d1f20;
    --color-divider:color-mix(in srgb,#1d1f20 16%,transparent);
    --color-neutral-100:#f5f5f8; --color-neutral-200:#e7e7ea; --color-neutral-300:#d4d4d7;
    --color-neutral-600:#7a7a7d; --color-neutral-700:#5d5d60; --color-neutral-800:#424244; --color-neutral-900:#2b2b2d;
    /* Verde institucional VISE */
    --color-accent-100:#e7f0ea; --color-accent-200:#cfe3d7; --color-accent-300:#a8c9b5;
    --color-accent-400:#70a487; --color-accent-500:#3f8161; --color-accent:#3f8161;
    --color-accent-600:#2f6b4e; --color-accent-700:#24563e; --color-accent-800:#1a422f; --color-accent-900:#122f21;
    --font-heading:"Barlow Condensed",system-ui,sans-serif; --font-body:"Barlow",system-ui,sans-serif;
    --space-2:6.8px; --space-3:10.2px; --space-4:13.6px;
  }
  *,*::before,*::after{box-sizing:border-box;}
  body{margin:0;background:var(--color-bg);color:var(--color-text);font-family:var(--font-body);font-size:15px;line-height:1.55;}
  h1,h2,h3,h4{font-family:var(--font-heading);font-weight:600;line-height:1.12;margin:0;}
  a{color:var(--color-accent-700);text-underline-offset:3px;} a:hover{color:var(--color-accent-900);}
  img{display:block;max-width:100%;}
  svg{display:block;}

  /* Blueprint: marco cuadrado con marcas de registro en las esquinas */
  .blueprint{position:relative;border:1px solid var(--color-divider);border-radius:0;background:transparent;}
  .blueprint>.corner{position:absolute;width:11px;height:11px;color:color-mix(in srgb,var(--color-text) 55%,transparent);}
  .blueprint>.corner::before,.blueprint>.corner::after{content:"";position:absolute;background:currentColor;}
  .blueprint>.corner::before{left:5px;top:0;width:1px;height:100%;}
  .blueprint>.corner::after{top:5px;left:0;width:100%;height:1px;}
  .blueprint>.corner.tl{top:-6px;left:-6px;} .blueprint>.corner.tr{top:-6px;right:-6px;}
  .blueprint>.corner.bl{bottom:-6px;left:-6px;} .blueprint>.corner.br{bottom:-6px;right:-6px;}

  /* Tags */
  .tag{display:inline-flex;align-items:center;font-size:11px;letter-spacing:.02em;padding:3px 10px;border-radius:0;}
  .tag-neutral{background:var(--color-neutral-100);color:var(--color-neutral-800);}
  .tag-outline{border:1px solid var(--color-accent);color:var(--color-accent);}

  /* Tablas */
  .table{width:100%;border-collapse:collapse;font-size:14px;}
  .table th{text-align:left;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:color-mix(in srgb,var(--color-text) 60%,transparent);padding:var(--space-2);border-bottom:1px solid var(--color-divider);}
  .table td{padding:var(--space-2);border-bottom:1px solid color-mix(in srgb,var(--color-text) 8%,transparent);}
  .table tbody tr:hover{background:color-mix(in srgb,var(--color-text) 4%,transparent);}

  /* Botones */
  .btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;text-decoration:none;font-family:var(--font-heading);font-weight:600;font-size:14px;line-height:1.2;color:var(--color-text);background:transparent;border:1px solid var(--color-divider);padding:var(--space-2) calc(var(--space-3)*1.2);border-radius:0;transition:.12s;}
  .btn:hover{background:color-mix(in srgb,var(--color-text) 7%,transparent);}
  .btn-primary{background:var(--color-accent);border-color:var(--color-accent);color:#fff;}
  .btn-primary:hover{background:var(--color-accent-600);}

  /* Menú lateral (cobertura) */
  .cov{width:100%;display:flex;align-items:center;gap:11px;padding:11px 20px;background:transparent;border:0;border-left:3px solid transparent;color:#e7f0ea;cursor:pointer;text-align:left;font-family:var(--font-body);text-decoration:none;transition:.12s;}
  .cov:hover{background:rgba(255,255,255,.09);color:#e7f0ea;}
  .cov.active{background:rgba(255,255,255,.13);border-left-color:#70a487;}
  .cov .nm{flex:1;font-family:var(--font-heading);font-size:17px;letter-spacing:.03em;color:#e7f0ea;}
  .cov.active .nm{color:#fff;}
</style>
