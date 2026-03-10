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
| Componentes reactivos | Livewire | 3.x | Filtros y búsqueda sin reload (NegociosIndex) |
| Imágenes/media | Spatie Media Library | 11.x | Upload, conversiones WebP, storage |
| Búsqueda | Laravel Scout + MySQL fulltext | — | Sin infra extra en MVP; migratable a Meilisearch |
| Servidor local | XAMPP (Apache + MariaDB) | — | Entorno de desarrollo |

---

## Estructura de capas

```
┌─────────────────────────────────────────────────┐
│                  CLIENTE (browser)               │
│          Blade · Alpine.js · Tailwind            │
├─────────────────────────────────────────────────┤
│              FRONTEND PÚBLICO (SSR)              │
│   routes/web.php → Controllers → Blade Views    │
│   SEO: meta tags, sitemap, JSON-LD, Open Graph  │
├─────────────────────────────────────────────────┤
│           PANEL DUEÑOS (/panel)                  │
│   PanelController · PanelAuthenticate middleware │
│   Vistas: panel/login · dashboard · edit        │
├─────────────────────────────────────────────────┤
│                  PANEL ADMIN                     │
│        Filament v3 (ruta /admin)                │
│   Resources: Lugar · Ficha · Categoria · Zona   │
│              Articulo · Guia · Promocion        │
│              FeaturedSlot · Consulta            │
│              Resena · User (propietarios)       │
│   Pages: ImportarNegocios (Overpass/OSM)        │
├─────────────────────────────────────────────────┤
│               LÓGICA DE NEGOCIO                  │
│     Models · Observers · Middleware             │
│     Laravel Scout (búsqueda)                    │
│     Spatie Media Library (imágenes)             │
│     config/features.php (feature flags)         │
├─────────────────────────────────────────────────┤
│                BASE DE DATOS                     │
│        MariaDB 10 · Eloquent ORM                │
│     Migraciones versionadas · Seeders           │
└─────────────────────────────────────────────────┘
```

---

## Estructura de directorios

```
app/
├── Filament/
│   └── Resources/
│       ├── LugarResource.php          ← lugar físico (nombre, ubicación, categoría)
│       ├── FichaResource.php          ← perfil gestionado (contacto, horarios, plan, propietario)
│       ├── CategoriaResource.php      ← jerarquía 3 niveles
│       ├── ZonaResource.php
│       ├── ArticuloResource.php
│       ├── GuiaResource.php
│       ├── PromocionResource.php
│       ├── FeaturedSlotResource.php   ← slots destacados editoriales
│       ├── ConsultaResource.php       ← solo lectura, badge no-leídos
│       ├── ResenaResource.php         ← moderación: aprobar/rechazar/bulk, badge pendientes
│       ├── UserResource.php           ← gestión de propietarios (acceso a /panel)
│       └── SuscriptorResource.php     ← newsletter: lista, baja individual/bulk, badge activos
├── Filament/
│   └── Pages/
│       └── ImportarNegocios.php       ← herramienta de carga masiva desde OpenStreetMap (Overpass API)
├── Filament/
│   └── Widgets/
│       ├── StatsOverviewWidget.php    ← KPIs: fichas por plan, visitas, pendientes
│       ├── TopFichasWidget.php        ← top 10 fichas más visitadas (table widget)
│       └── ActividadPorZonaWidget.php ← fichas y visitas por zona con barra de distribución
├── Console/
│   └── Commands/
│       └── NewsletterEnviar.php       ← newsletter:enviar [--zona=ID] [--dry-run]
├── Http/
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── NegocioController.php      ← visitas, cerca (Haversine), similares (fallback), reseñas
│   │   ├── CategoriaController.php
│   │   ├── ZonaController.php
│   │   ├── GuiaController.php
│   │   ├── ArticuloController.php
│   │   ├── MapaController.php
│   │   ├── ContactoController.php
│   │   ├── PageController.php
│   │   ├── SitemapController.php
│   │   ├── FeedController.php
│   │   ├── ResenaController.php       ← store() con feature flag + throttle 5/min
│   │   ├── NewsletterController.php   ← subscribe() + baja() por token
│   │   └── PanelController.php        ← login/logout + dashboard + edit/update ficha
│   └── Middleware/
│       └── PanelAuthenticate.php      ← redirige a panel.login si no autenticado
├── Livewire/
│   └── NegociosIndex.php              ← filtros reactivos: zona, categoría, búsqueda, abiertos
├── Mail/
│   ├── NuevaConsulta.php
│   ├── NewsletterMail.php             ← markdown; contenido por zona; link de baja
│   └── BienvenidaNewsletterMail.php   ← se envía al suscribirse; incluye zona y link de baja
├── Models/
│   ├── User.php                       ← implements FilamentUser; is_admin controla acceso a /admin
│   ├── Lugar.php                      ← lugar físico (slug, lat/lng, categoría, zona)
│   ├── Ficha.php                      ← PLAN_LIMITS, planIncluye(), isAbiertoAhora(), visitas
│   ├── Categoria.php                  ← jerarquía hasta 3 niveles (parent_id)
│   ├── Zona.php
│   ├── Articulo.php
│   ├── Guia.php
│   ├── Promocion.php
│   ├── FeaturedSlot.php
│   ├── SlugRedirect.php               ← redirects 301 para slugs cambiados
│   ├── Resena.php                     ← scopes aprobada()/pendiente(), accessor $stars
│   ├── Suscriptor.php                 ← token_baja autogenerado, scope activo()
│   └── Consulta.php
├── Observers/
│   └── LugarObserver.php              ← guarda slug anterior en slug_redirects al cambiar
├── Services/
│   └── OverpassService.php            ← consulta Overpass API (OSM); parsea elementos a array normalizado
└── Providers/
    └── AppServiceProvider.php         ← registra LugarObserver

config/
└── features.php                       ← feature flags: FEATURE_RESENAS

resources/views/
├── layouts/
│   ├── app.blade.php                  ← layout público principal
│   └── panel.blade.php                ← layout del panel de dueños (navbar propio)
├── livewire/
│   └── negocios-index.blade.php       ← barra pills + cards + sidebar categorías
├── emails/
│   ├── newsletter.blade.php           ← template markdown del newsletter periódico
│   └── newsletter-bienvenida.blade.php ← template markdown del mail de bienvenida
├── newsletter/
│   └── baja.blade.php                 ← confirmación de baja exitosa
├── filament/pages/
│   └── importar-negocios.blade.php    ← formulario + tabla de resultados OSM + importación
├── filament/forms/components/
│   └── map-picker-zona.blade.php      ← mapa Leaflet para fijar lat_centro/lng_centro de Zona
├── filament/widgets/
│   └── actividad-por-zona.blade.php   ← tabla custom con barra de distribución
├── panel/
│   ├── login.blade.php                ← login standalone
│   ├── dashboard.blade.php            ← stats + plan gating + upgrade banner
│   └── edit.blade.php                 ← edición de descripción, contacto, redes
└── negocios/
    └── show.blade.php                 ← JSON-LD, promos, reseñas (flag), WhatsApp (plan), cerca/similares
```

---

## Modelo de datos (entidades principales)

```
users
├── id, name, email, password, remember_token
├── is_admin (bool) — true: acceso a /admin, false: solo /panel
└── timestamps

lugares
├── id, nombre, slug (unique), rut (nullable)
├── direccion, lat, lng
├── categoria_id → FK categorias
├── zona_id → FK zonas (nullable)
├── activo (bool)
└── timestamps

fichas  (1:1 con lugares)
├── id, lugar_id → FK lugares (cascadeOnDelete)
├── user_id → FK users nullable (propietario con acceso al panel)
├── descripcion, telefono, email, sitio_web
├── redes_sociales (JSON), horarios (JSON), horarios_especiales (JSON)
├── plan (enum: gratuito | basico | premium)
├── featured (bool), featured_score (int, calculado automáticamente al guardar)
├── estado (enum: pendiente | activa | rechazada | suspendida)
├── activo (bool)
├── visitas (int unsigned, se incrementa por NegocioController@show)
└── timestamps
   ↳ media: 'logo' (singleFile), 'portada' (singleFile), 'galeria' (múltiple, WebP)

resenas
├── id, ficha_id → FK fichas
├── nombre, email (nullable, privado)
├── rating (tinyint 1-5)
├── cuerpo (text, min 10 chars)
├── aprobada (bool, default false — require moderación)
└── timestamps

categorias
├── id, nombre, slug, descripcion, icono, activo
├── parent_id → FK nullable (jerarquía hasta 3 niveles)
├── nivel (1=familia, 2=tipo, 3=especialización)
├── popularidad_score (int, calculado al guardar fichas)
└── timestamps
   ↳ media: 'imagen_generica' (singleFile)

zonas
├── id, slug, nombre
├── lat_centro, lng_centro (centroides para auto-detección GPS)
└── timestamps

promociones
├── id, ficha_id → FK fichas
├── titulo, descripcion, fecha_inicio, fecha_fin, activo
└── timestamps
   ↳ media: 'imagen' (singleFile)

slug_redirects
├── old_slug, new_slug, model_type
└── timestamps

articulos, guias, featured_slots, consultas, media → ver DATABASE.md
```

---

## Rutas principales

```
GET  /                              → HomeController@index
GET  /negocios                      → NegocioController@index (Livewire)
GET  /negocios/{slug}               → NegocioController@show  (incrementa visitas)
POST /negocios/{slug}/resenas       → ResenaController@store  [throttle:5,1]
GET  /categorias / /{slug}          → CategoriaController
GET  /zonas/{slug}                  → ZonaController@show
GET  /articulos/{slug}              → ArticuloController@show
GET  /guias / /{guia}               → GuiaController
GET  /mapa                          → MapaController@index
GET  /contacto                      → ContactoController@show
POST /contacto                      → ContactoController@store
GET  /quienes-somos                 → PageController@about
GET  /sitemap.xml                   → SitemapController@index
GET  /feed                          → FeedController@index

# Newsletter
POST /newsletter/suscribir          → NewsletterController@subscribe  [throttle:3,1]
GET  /newsletter/baja/{token}       → NewsletterController@baja

# Panel de dueños
GET  /panel/login                   → PanelController@showLogin  [guest]
POST /panel/login                   → PanelController@login      [guest]
POST /panel/logout                  → PanelController@logout
GET  /panel                         → PanelController@index      [PanelAuthenticate]
GET  /panel/editar                  → PanelController@edit       [PanelAuthenticate]
PUT  /panel/editar                  → PanelController@update     [PanelAuthenticate]

GET  /admin/*                       → Filament (solo users con is_admin = true)
```

---

## Autenticación — dos accesos, un modelo

| | `/admin` (Filament) | `/panel` (dueños) |
|---|---|---|
| Guard | `web` | `web` |
| Modelo | `User` | `User` |
| Control de acceso | `canAccessPanel()` → `is_admin = true` | `PanelAuthenticate` + `user->ficha` existe |
| Login | Filament built-in | `panel/login.blade.php` custom |
| Crear usuarios | `UserResource` en Filament | — |

---

## Feature flags

`config/features.php` (vars de `.env`):

| Flag | Variable | Default | Efecto cuando `false` |
|---|---|---|---|
| `features.resenas` | `FEATURE_RESENAS` | `false` | Oculta formulario y sección de reseñas en la ficha pública. La tabla e infraestructura Filament existen siempre. |

---

## Decisiones de diseño

**¿Por qué monolito?**
El tráfico inicial no justifica microservicios. Laravel SSR entrega HTML directamente, óptimo para SEO. API REST puede agregarse en etapas posteriores sin rehacer la base.

**¿Por qué Filament y no Nova/Backpack?**
Filament v3 es gratuito, open source, moderno y cubre el 100% de las necesidades admin sin costo de licencia.

**¿Por qué Blade + Alpine en lugar de React/Vue?**
Las páginas son mayormente contenido estático. Blade SSR es ideal para SEO. Alpine.js agrega interactividad sin compilación. Livewire se usa solo donde hay reactividad real (filtros de `/negocios`).

**¿Por qué Scout + MySQL al inicio?**
Evita infraestructura adicional en fase MVP. La interfaz Scout permite migrar a Meilisearch sin cambiar el código de la aplicación.

**¿Por qué dos accesos con el mismo modelo User?**
Simplifica el modelo de datos — una sola tabla `users`. La separación se hace con `is_admin` + `canAccessPanel()`. Los propietarios no pueden acceder a `/admin` aunque estén en la misma tabla.

**¿Por qué soft gating de planes en lugar de hard?**
En esta fase el admin controla los planes manualmente. El soft gating informa al dueño sin bloquear técnicamente, lo que facilita excepciones y casos especiales sin código extra. Ver `TIERS.md`.
