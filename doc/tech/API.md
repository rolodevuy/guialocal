# API

## Arquitectura actual (MVP)

Este proyecto es un **monolito SSR** (Server-Side Rendering) con Blade + Laravel. No existe una REST API pública en el MVP.

Las páginas públicas son renderizadas directamente por los controllers con vistas Blade:

```
GET /                     → HomeController@index        → home.blade.php
GET /negocios             → NegocioController@index     → negocios/index.blade.php
GET /negocios/{slug}      → NegocioController@show      → negocios/show.blade.php
GET /categorias/{slug}    → CategoriaController@show    → categorias/show.blade.php
GET /zonas/{slug}         → ZonaController@show         → zonas/show.blade.php
GET /contacto             → ContactoController@show     → contacto.blade.php
POST /contacto            → ContactoController@store    → redirect con mensaje
GET /quienes-somos        → PageController@about        → pages/about.blade.php
GET /sitemap.xml          → SitemapController@index     → XML response
```

El panel admin es gestionado por Filament en `/admin/*` (no es parte de la API pública).

---

## API REST (Etapa 3+)

Una API REST puede agregarse en etapas posteriores sin reescribir la base, por ejemplo para:
- Una app mobile
- Integraciones de terceros
- Widgets para negocios

Cuando se implemente, seguirá convenciones RESTful con autenticación via Laravel Sanctum.

```
GET  /api/v1/negocios
GET  /api/v1/negocios/{slug}
GET  /api/v1/categorias
GET  /api/v1/zonas
```
