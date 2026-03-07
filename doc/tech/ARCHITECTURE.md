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
| CSS | Tailwind CSS | 4.x | Estilos y diseño responsive |
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
│   Resources: Lugar · Ficha · Categoría · Zona   │
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
│   └── Resources/
│       ├── LugarResource.php         ← lugar físico (nombre, ubicación, categoría)
│       ├── FichaResource.php         ← perfil gestionado (contacto, horarios, plan)
│       ├── CategoriaResource.php     ← jerarquía 3 niveles
│       ├── ZonaResource.php
│       └── ConsultaResource.php      ← solo lectura, badge no-leídos
├── Http/
│   └── Controllers/
│       ├── HomeController.php
│       ├── NegocioController.php
│       ├── CategoriaController.php
│       ├── ZonaController.php
│       ├── MapaController.php        ← página /mapa
│       ├── ContactoController.php
│       ├── PageController.php
│       └── SitemapController.php
├── Mail/
│   └── NuevaConsulta.php             ← notificación email al recibir consulta
├── Models/
│   ├── Lugar.php              ← lugar físico
│   ├── Ficha.php              ← perfil gestionado (1:1 con Lugar)
│   ├── Categoria.php          ← jerarquía 3 niveles (parent_id)
│   ├── Zona.php
│   └── Consulta.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── components/
│   │   └── cat-icon.blade.php        ← íconos de categoría (SVG inline)
│   ├── filament/forms/components/
│   │   └── map-picker.blade.php      ← mapa Leaflet para picker de lat/lng en admin
│   ├── emails/
│   │   └── nueva-consulta.blade.php  ← template email Markdown
│   ├── home.blade.php
│   ├── mapa.blade.php                ← página /mapa con filtros en cascada
│   ├── negocios/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── categorias/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── zonas/
│   │   └── show.blade.php
│   ├── errors/
│   │   ├── 404.blade.php
│   │   └── 500.blade.php
│   └── contacto.blade.php

database/
├── migrations/
└── seeders/
```

---

## Modelo de datos (entidades principales)

```
lugares
├── id, nombre, slug, rut (unique, nullable)
├── direccion, lat, lng
├── categoria_id → FK categorias
├── zona_id → FK zonas (nullable)
├── activo (bool)
└── timestamps

fichas
├── id, lugar_id → FK lugares (cascadeOnDelete)
├── descripcion, telefono, email, sitio_web
├── horarios (JSON), horarios_especiales (JSON), redes_sociales (JSON)
├── plan (enum: gratuito, basico, premium)
├── featured (bool), featured_score (smallint)
├── estado (enum: pendiente, activa, rechazada, suspendida)
├── activo (bool)
└── timestamps
   ↳ media: colecciones 'logo' (singleFile), 'portada' (singleFile), 'galeria' (múltiple)

categorias
├── id, nombre, slug, descripcion, icono, activo
├── parent_id → FK nullable a categorias (jerarquía hasta 3 niveles)
├── nivel (1=familia, 2=tipo, 3=especialización)
├── popularidad_score
└── timestamps
   ↳ media: colección 'imagen_generica' (singleFile)

zonas
├── id, slug, nombre
├── lat_centro, lng_centro (centroides para auto-detección)
└── timestamps

(media) → Spatie Media Library (tabla polymórfica)

consultas
├── id, nombre, email, mensaje, leido
└── timestamps
```

Diseño de clasificación: ver [CATEGORIAS.md](../product/CATEGORIAS.md)

---

## Rutas principales

```
GET  /                          → HomeController@index
GET  /negocios                  → NegocioController@index
GET  /negocios/{slug}           → NegocioController@show
GET  /categorias                → CategoriaController@index
GET  /categorias/{slug}         → CategoriaController@show   (?zona=ID)
GET  /zonas/{slug}              → ZonaController@show        (?categoria=ID)
GET  /mapa                      → MapaController@index       (?zona=ID)
GET  /contacto                  → ContactoController@show
POST /contacto                  → ContactoController@store
GET  /quienes-somos             → PageController@about
GET  /sitemap.xml               → SitemapController@index

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
