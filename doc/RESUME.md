# Guia Local — Resumen del Proyecto

> Directorio comercial local para Ciudad de la Costa, Uruguay.
> Este archivo es la referencia rápida para no tener que leer todo el codebase.

---

## Stack

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12, PHP 8.2 |
| Admin | Filament v3 (`/admin`) |
| Frontend | Blade + Alpine.js 3.15 + Tailwind CSS v4 |
| BD | MariaDB 10.x (MySQL compatible) |
| Búsqueda | Laravel Scout (driver: database) |
| Imágenes | Spatie MediaLibrary (WebP auto, conversiones) |
| Slugs | Spatie Sluggable + SlugRedirects (SEO) |
| Backups | Spatie Laravel Backup |
| Build | Vite 7 + @tailwindcss/vite |

---

## Entorno Local

- **OS:** Windows 11 Pro
- **Server:** XAMPP (Apache + MariaDB + PHP 8.2.12)
- **PHP path:** `C:\xampp\php`
- **Composer:** `C:\composer\composer.phar`
- **Virtual host:** `guialocal.test` → `C:/xampp/htdocs/guialocal/public`
- **BD:** `business_guide`, user `root`, sin password
- **Cache/Session:** file (no database)
- **Mail:** Zoho SMTP (`info@guialocal.uy`)
- **Repo:** https://github.com/rolodevuy/guialocal

---

## Entorno Producción

- **Hosting:** VPS con acceso SSH — IP: `178.156.241.157`, usuario: `ploi`
- **Dominio:** guialocal.uy
- **Deploy:** manual (no CI/CD configurado aún)
- **BD prod:** `guialocal_prod`, usuario MySQL: `guialocal`
- **Importar BD:** `mysql -u guialocal -p guialocal_prod < dump.sql`

---

## Modelos (17)

| Modelo | Tabla | Relación clave |
|--------|-------|---------------|
| User | users | hasOne Ficha (propietario) |
| Lugar | lugares | belongsTo Categoria, Zona; hasMany Ficha |
| Ficha | fichas | belongsTo Lugar, User; hasMany Promocion, Resena |
| Categoria | categorias | belongsTo Sector, parent; hasMany children, Lugar |
| Zona | zonas | hasMany Lugar |
| Sector | sectores | hasMany Categoria |
| Articulo | articulos | belongsTo Categoria, Lugar |
| Guia | guias | belongsTo Categoria; belongsToMany Lugar |
| Evento | eventos | belongsTo Lugar |
| Promocion | promociones | belongsTo Ficha |
| Resena | resenas | belongsTo Ficha |
| ClaimRequest | claim_requests | belongsTo Lugar, User(admin) |
| Suscriptor | suscriptores | belongsTo Zona |
| Consulta | consultas | — (formulario de contacto) |
| FeaturedSlot | featured_slots | morphTo slotable (Ficha, Lugar, Articulo, Guia) |
| Setting | settings | key-value store |
| SlugRedirect | slug_redirects | belongsTo Lugar |

### Arquitectura Lugar ↔ Ficha

- **Lugar** = ubicación física (nombre, dirección, lat/lng, categoría, zona)
- **Ficha** = perfil comercial (descripción, contacto, horarios, plan, estado, media)
- Un Lugar puede tener varias Fichas (en la práctica 1:1)
- Separación permite soft-gating por plan sin tocar la ubicación

### Planes (soft gating)

| Feature | Gratuito | Básico | Premium |
|---------|----------|--------|---------|
| Visitas | — | ✅ | ✅ |
| WhatsApp | — | ✅ | ✅ |
| Promociones | 0 | 1 | ∞ |
| Fotos galería | 0 | 3 | 10 |
| Logo | — | ✅ | ✅ |
| Destacado | — | — | ✅ |

### Categorías

- 3 niveles jerárquicos (parent_id, nivel 1/2/3)
- Nivel 1 tiene `sector_id` (Nivel 2/3 hereda del padre)
- 3 Sectores: Comercial, Gastronomía y Ocio, Turismo y Alojamiento

---

## Rutas Principales

### Públicas
| Ruta | Controller | Descripción |
|------|-----------|-------------|
| `/` | HomeController | Home con destacados, sectores, zonas |
| `/negocios` | NegociosIndex (Livewire) | Listado con filtros |
| `/negocios/{slug}` | NegocioController@show | Ficha del negocio |
| `/categorias` | CategoriaController@index | Grilla por sector |
| `/categorias/{cat}` | CategoriaController@show | Negocios de categoría |
| `/sectores/{sector}` | SectorController@show | Categorías del sector |
| `/zonas/{zona}` | ZonaController@show | Negocios de zona |
| `/articulos` | ArticuloController | Blog |
| `/guias` | GuiaController | Guías locales |
| `/eventos` | EventoController | Eventos |
| `/mapa` | MapaController | Mapa interactivo |
| `/contacto` | ContactoController | Formulario contacto |
| `/quienes-somos` | PageController@about | Página estática |
| `/precios` | PageController@precios | Comparación de planes |
| `/sitemap.xml` | SitemapController | SEO |
| `/feed` | FeedController | RSS/Atom |

### Panel Propietarios (`/panel`)
| Ruta | Descripción |
|------|-------------|
| `/panel/login` | Login propietario |
| `/panel` | Dashboard con métricas |
| `/panel/editar` | Editar ficha propia |

### Claim
| Ruta | Descripción |
|------|-------------|
| `/negocios/{slug}/reclamar` | Formulario de reclamo |

### Newsletter
| Ruta | Descripción |
|------|-------------|
| `POST /newsletter/suscribir` | Suscripción |
| `/newsletter/baja/{token}` | Baja con token |

---

## Filament Admin (`/admin`)

### Resources (16)
Articulo, Categoria, ClaimRequest, Consulta, Evento, FeaturedSlot, Ficha, Guia, Lugar, Promocion, Resena, Sector, Suscriptor, User, Zona

### Pages
- **Backups** — Gestión de backups
- **ImportarNegocios** — Importador OSM (OpenStreetMap)

### Widgets
- StatsOverviewWidget, TopFichasWidget, ActividadPorZonaWidget

---

## Comandos Artisan

| Comando | Descripción |
|---------|-------------|
| `osm:importar-masivo` | Importa negocios de OSM por zona (--activar, --dry-run) |
| `app:recalcular-scores` | Recalcula featured_score y popularidad_score en bulk |
| `claims:purge-rejected` | Limpia constancias de claims rechazados (--days=90) |
| `newsletter:enviar` | Envía newsletter a suscriptores |

---

## Vistas Blade (por directorio)

```
views/
├── home.blade.php
├── contacto.blade.php
├── mapa.blade.php
├── feed.blade.php
├── sitemap.blade.php
├── layouts/
│   ├── app.blade.php          ← layout público principal
│   └── panel.blade.php        ← layout panel propietarios
├── partials/
│   └── _ficha_card.blade.php  ← card reutilizable
├── components/
│   ├── social-icon.blade.php
│   ├── cat-icon.blade.php
│   └── verified-badge.blade.php
├── pages/
│   ├── about.blade.php
│   └── precios.blade.php
├── negocios/
│   ├── index.blade.php        ← wrapper Livewire
│   ├── show.blade.php
│   └── claim.blade.php
├── categorias/
│   ├── index.blade.php
│   └── show.blade.php
├── sectores/show.blade.php
├── zonas/show.blade.php
├── articulos/index.blade.php, show.blade.php
├── guias/index.blade.php, show.blade.php
├── eventos/index.blade.php, show.blade.php
├── panel/login.blade.php, dashboard.blade.php, edit.blade.php
├── livewire/negocios-index.blade.php
├── emails/ (8 templates: newsletter, claims, consultas)
├── errors/404, 429, 500
├── newsletter/baja.blade.php
└── filament/ (importar, backups, map-picker, widgets)
```

---

## Migraciones (35)

Orden cronológico de la BD, desde users hasta performance indexes.
Todo completado y ejecutado. Ver `database/migrations/` para detalle.

---

## Optimizaciones Aplicadas

- 11 índices compuestos en BD (fichas, lugares, categorías, artículos, etc.)
- N+1 eliminados en HomeController, NegocioController
- Cache 1h en View Composers (nav), Livewire (categorías, zonas, conteos)
- Bulk UPDATE en RecalcularScores (6 queries vs N)
- chunkById en PurgeRejectedClaims
- .htaccess: gzip, expires 1 año, cache-control immutable, security headers
- Lazy loading en todas las imágenes no-hero
- Session/Cache driver: file (no database)

Ver `doc/OPT.md` para detalle completo.

---

## Estado del Proyecto

- **Etapas 1-7 completadas** ✅ (MVP → Eventos)
- **Transversales completados:** Sectores, Verificación, Precios, Newsletter, OSM Importer
- **89 negocios reales** importados de OpenStreetMap (Atlántida 39, Lagomar 31, Parque del Plata 19)
- **Próximos pasos:** deployment a producción, más contenido, posible CI/CD

---

## Documentación

| Archivo | Contenido |
|---------|-----------|
| `doc/build/build_steps.md` | Plan paso a paso (fuente de verdad del progreso) |
| `doc/tech/ARCHITECTURE.md` | Stack y arquitectura técnica |
| `doc/product/ROADMAP.md` | Etapas del producto |
| `doc/FEATURE.md` | Features por etapa |
| `doc/OPT.md` | Optimizaciones de rendimiento |
| `doc/RESUME.md` | **Este archivo** — resumen completo |
