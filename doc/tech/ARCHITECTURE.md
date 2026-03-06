# Arquitectura del Sistema

Directorio comercial local — Guía Local

---

## Stack tecnológico

| Capa | Tecnología | Versión | Rol |
|---|---|---|---|
| Framework backend | Laravel | 12.x | Core del sistema, routing, ORM, auth, colas |
| Panel admin | Filament | 3.x | CRUD admin completo sin código extra |
| Lenguaje | PHP | 8.2+ | Runtime |
| Base de datos | MariaDB | 10.x (XAMPP) | Persistencia principal |
| Frontend público | Blade + Alpine.js | — | Renderizado SSR, interacciones livianas |
| CSS | Tailwind CSS | 3.x | Estilos y diseño responsive |
| Componentes reactivos | Livewire | 3.x | Filtros y búsqueda sin reload (Etapa 2+) |
| Imágenes/media | Spatie Media Library | 11.x | Upload, conversiones, storage |
| Búsqueda | Laravel Scout | — | Abstracción de motores de búsqueda |
| Driver búsqueda MVP | MySQL fulltext | — | Sin infraestructura extra |
| Driver búsqueda futuro | Meilisearch | — | Búsqueda avanzada cuando el volumen lo requiera |
| SEO | Spatie Laravel SEO | — | Sitemap, Open Graph, JSON-LD, meta tags |
| Servidor local | XAMPP (Apache + MySQL) | — | Entorno de desarrollo |

---

## Estructura de capas

```
┌─────────────────────────────────────────────────┐
│                  CLIENTE (browser)               │
│          Blade · Alpine.js · Tailwind            │
├─────────────────────────────────────────────────┤
│              FRONTEND PÚBLICO (SSR)              │
│   routes/web.php → Controllers → Blade Views    │
│   SEO: meta tags, sitemap, Open Graph           │
├─────────────────────────────────────────────────┤
│                  PANEL ADMIN                     │
│        Filament v3 (ruta /admin)                │
│   Resources: Negocio · Categoría · Zona         │
│   Widgets, Filtros, Media, Acciones             │
├─────────────────────────────────────────────────┤
│               LÓGICA DE NEGOCIO                  │
│     Models · Services · Actions · Policies      │
│     Laravel Scout (búsqueda)                    │
│     Spatie Media Library (imágenes)             │
│     Jobs / Queues (mails, analytics)            │
├─────────────────────────────────────────────────┤
│                BASE DE DATOS                     │
│        MariaDB 10 · Eloquent ORM                │
│     Migraciones versionadas · Seeders           │
└─────────────────────────────────────────────────┘
```

---

## Estructura de directorios Laravel

```
app/
├── Filament/
│   ├── Resources/
│   │   ├── NegocioResource.php
│   │   ├── CategoriaResource.php
│   │   └── ZonaResource.php
│   └── Widgets/
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       ├── NegocioController.php
│       ├── CategoriaController.php
│       └── ContactoController.php
├── Models/
│   ├── Negocio.php
│   ├── Categoria.php
│   ├── Zona.php
│   └── Consulta.php
└── Services/
    └── BusquedaService.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── home.blade.php
│   ├── negocios/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── categorias/
│   │   └── show.blade.php
│   └── contacto.blade.php

database/
├── migrations/
└── seeders/
```

---

## Modelo de datos (entidades principales)

```
negocios
├── id, slug, nombre, descripcion
├── direccion, telefono, email, sitio_web
├── lat, lng
├── horarios (JSON)
├── featured (bool), activo (bool), plan (enum)
├── categoria_id, zona_id
└── timestamps

categorias
├── id, slug, nombre, descripcion, icono
└── timestamps

zonas
├── id, slug, nombre
└── timestamps

(media) → Spatie Media Library (tabla polymórfica)

consultas
├── id, nombre, email, mensaje, leido
└── timestamps
```

---

## Rutas principales

```
GET  /                          → HomeController@index
GET  /negocios                  → NegocioController@index
GET  /negocios/{slug}           → NegocioController@show
GET  /categorias/{slug}         → CategoriaController@show
GET  /zonas/{slug}              → ZonaController@show
GET  /contacto                  → ContactoController@show
POST /contacto                  → ContactoController@store
GET  /quienes-somos             → PageController@about

GET  /admin/*                   → Filament (panel admin)
```

---

## Decisiones de diseño

**¿Por qué monolito?**
El tráfico inicial no justifica microservicios. Laravel como monolito SSR entrega HTML directamente, lo cual es óptimo para SEO. Una API REST puede agregarse en etapas posteriores sin rehacer la base.

**¿Por qué Filament y no Nova/Backpack?**
Filament v3 es gratuito, open source, moderno y cubre el 100% de las necesidades admin de este proyecto sin costo de licencia.

**¿Por qué Blade + Alpine en lugar de React/Vue?**
Las páginas son mayormente contenido estático con interacciones mínimas (filtros, dropdowns). Blade SSR es ideal para SEO. Alpine.js agrega interactividad sin compilación. Livewire se suma en Etapa 2 para filtros reactivos.

**¿Por qué Scout + MySQL al inicio?**
Evita infraestructura adicional (Elasticsearch, Meilisearch) en fase MVP. La interfaz Scout permite migrar el driver más adelante sin cambiar el código de la aplicación.
