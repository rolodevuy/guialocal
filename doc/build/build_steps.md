# Build Steps — Guía Local

Plan de construcción paso a paso. Cada paso es independiente, acotado y verificable.
Referencia de stack: [ARCHITECTURE.md](../tech/ARCHITECTURE.md)

---

## Bloque 0 — Setup del entorno

---

### Paso 1 — Crear proyecto Laravel ✅

**Objetivo:** Tener el esqueleto de Laravel funcionando en XAMPP.

**Resultado esperado:**
- Proyecto creado en `C:/xampp/htdocs/guialocal`
- `.env` configurado con datos de la BD local

**Criterio de terminado:**
- `php artisan --version` muestra Laravel 12.x ✅
- Migraciones base corridas (users, cache, jobs) ✅
- BD `business_guide` conectada ✅

**Notas:**
- Se instaló Laravel 12.53 (última versión estable a Mar 2026)
- PHP 8.2.12 vía XAMPP
- BD con usuario `root` temporalmente (usuario `lead` pendiente de permisos por corrupción Aria en MariaDB)

---

### Paso 2 — Instalar y configurar Filament v3 ✅

**Objetivo:** Tener el panel admin accesible en `/admin` con un usuario creado.

**Resultado esperado:**
- Filament instalado vía Composer
- Usuario admin creado con `php artisan make:filament-user`
- Panel accesible en `localhost:8000/admin`

**Criterio de terminado:**
- Login en `/admin` con el usuario creado funciona
- Se ve el dashboard vacío de Filament

**Notas:**
- Filament v3.3 instalado con `composer require filament/filament:"^3.2" -W`
- Panel ID: `admin`, ruta `/admin`, color primario: Amber
- Extensions habilitadas en `php.ini`: `intl`, `gd`, `zip` (requeridas por Filament)
- Usuario creado vía tinker (make:filament-user requiere TTY interactivo)
- Livewire genera URLs root-relative → resuelto configurando VirtualHost Apache `guialocal.test`
  - `APP_URL=http://guialocal.test` en `.env`
  - `127.0.0.1 guialocal.test` en hosts de Windows
- Widget `FilamentInfoWidget` removido del dashboard (solo mostraba link a docs)

---

### Paso 3 — Instalar paquetes base ✅

**Objetivo:** Tener todos los paquetes del stack MVP instalados y publicados.

**Resultado esperado:**
- `spatie/laravel-medialibrary` instalado y migrado
- `laravel/scout` instalado y configurado con driver `database`
- `spatie/laravel-sluggable` instalado
- Tailwind CSS + Alpine.js configurados vía Vite

**Criterio de terminado:**
- `composer show` lista los paquetes sin errores
- `npm run build` compila sin errores
- Migraciones de Media Library corridas sin errores

**Notas:**
- `spatie/laravel-medialibrary` v11.21 — migración `create_media_table` corrida
- `laravel/scout` v10.24 — config publicada en `config/scout.php`, `SCOUT_DRIVER=database` en `.env`
- `spatie/laravel-sluggable` v3.8
- Tailwind CSS v4 ya venía incluido en Laravel 12 via `@tailwindcss/vite` (no requirió instalación extra)
- Alpine.js v3.15 instalado como dependencia npm, inicializado en `resources/js/app.js`
- `npm run build` genera 39KB CSS + 83KB JS sin errores

---

## Bloque 1 — Base de datos y modelos

---

### Paso 4 — Migración y modelo Categoria ✅

**Objetivo:** Tener la entidad Categoría persistible y accesible vía Eloquent.

**Resultado esperado:**
- Migración: `id`, `nombre`, `slug`, `descripcion`, `icono`, `timestamps`
- Modelo `Categoria` con fillable, cast y scope `activo`
- Trait `HasSlug` de Spatie configurado

**Criterio de terminado:**
- `php artisan migrate` corre sin errores
- `Categoria::create([...])` funciona en tinker

**Notas:**
- Tabla `categorias`: `nombre`, `slug` (unique), `descripcion` (nullable), `icono` (nullable), `activo` (bool, default true)
- `HasSlug` genera slug desde `nombre` automáticamente
- `getRouteKeyName()` retorna `slug` (para rutas `/categorias/{slug}`)
- Scope `activo()` filtra por `activo = true`
- Relación `hasMany(Negocio::class)` declarada (Negocio aún no existe, se resuelve en Paso 6)
- Verificado con tinker: `Categoria::create(['nombre' => 'Restaurantes', ...])` genera slug `restaurantes` automáticamente

---

### Paso 5 — Migración y modelo Zona ✅

**Objetivo:** Tener la entidad Zona persistible.

**Resultado esperado:**
- Migración: `id`, `nombre`, `slug`, `timestamps`
- Modelo `Zona` con fillable y trait `HasSlug`

**Criterio de terminado:**
- Tabla `zonas` existe en la BD
- `Zona::all()` responde en tinker

**Notas:**
- Tabla `zonas`: `nombre`, `slug` (unique)
- `HasSlug`, `getRouteKeyName()` → `slug`, relación `hasMany(Negocio::class)`
- Verificado con tinker: `Zona::create(['nombre' => 'Centro'])` genera slug `centro`

---

### Paso 6 — Migración y modelo Negocio ✅

**Objetivo:** Tener la entidad principal del sistema persistible con todas sus relaciones.

**Resultado esperado:**
- Migración con: `id`, `nombre`, `slug`, `descripcion`, `direccion`, `telefono`, `email`, `sitio_web`, `lat`, `lng`, `horarios` (JSON), `featured`, `activo`, `plan`, `categoria_id`, `zona_id`, `timestamps`
- Modelo `Negocio` con: fillable, casts, relaciones `belongsTo` a Categoria y Zona, trait `HasSlug`, trait `InteractsWithMedia`, implementa `HasMedia`
- Scope `activo()`, scope `featured()`

**Criterio de terminado:**
- `php artisan migrate` sin errores
- `Negocio::with(['categoria','zona'])->first()` funciona en tinker

**Notas:**
- FKs con `foreignId()->constrained()` a `categorias` y `zonas`
- `horarios` cast a `array`, `lat`/`lng` cast a `float`
- `plan` enum: `gratuito`, `basico`, `premium` (default: `gratuito`)
- Colecciones de media: `portada` (singleFile) y `galeria` (múltiple)
- `Negocio::with(['categoria','zona'])->first()` retorna `null` (sin datos aún — OK, se puebla en Paso 8)

---

### Paso 7 — Migración y modelo Consulta ✅

**Objetivo:** Tener la entidad para mensajes del formulario de contacto.

**Resultado esperado:**
- Migración: `id`, `nombre`, `email`, `mensaje`, `leido` (bool), `timestamps`
- Modelo `Consulta` con fillable

**Criterio de terminado:**
- Tabla `consultas` existe en la BD
- `Consulta::create([...])` funciona en tinker

**Notas:**
- `leido` cast a boolean, default `false`
- Sin relaciones (entidad standalone del formulario de contacto)
- Verificado con tinker: `Consulta::create([...])` funciona correctamente

---

### Paso 8 — Seeders de datos de prueba ✅

**Objetivo:** Tener datos realistas para desarrollar y probar el frontend.

**Resultado esperado:**
- `CategoriaSeeder`: 8 categorías (restaurante, café, farmacia, etc.)
- `ZonaSeeder`: 5 zonas/barrios
- `NegocioSeeder`: 20 negocios distribuidos en categorías y zonas, con `featured` en algunos

**Criterio de terminado:**
- `php artisan db:seed` corre sin errores ✅
- `Negocio::count()` retorna 20 en tinker ✅
- Hay al menos 3 negocios con `featured = true` ✅ (4 featured)

**Notas:**
- `DatabaseSeeder` deshabilita FK checks para permitir truncate en orden: Categoria → Zona → Negocio
- `NegocioSeeder` resuelve IDs de categoria/zona por slug con `pluck('id', 'slug')` — sin IDs hardcodeados
- 8 categorías: Restaurantes, Cafés y Bares, Panaderías y Pastelerías, Farmacias, Supermercados, Salud y Bienestar, Servicios Profesionales, Indumentaria y Calzado
- 5 zonas: Centro, Villa del Parque, Palermo, San Telmo, Belgrano
- 20 negocios con planes mixtos (gratuito/basico/premium) y 4 featured
- Verificado: `Negocio::count()` → 20, `Negocio::featured()->count()` → 4

---

## Bloque 2 — Panel admin (Filament)

---

### Paso 9 — Resource Categoría en Filament ✅

**Objetivo:** Poder crear, editar y eliminar categorías desde el admin.

**Resultado esperado:**
- `CategoriaResource` con form: nombre, slug (auto), descripcion, icono
- Table con columnas: nombre, slug, cantidad de negocios
- Filtros y búsqueda en la tabla

**Criterio de terminado:**
- Desde `/admin/categorias` se puede crear una categoría nueva ✅
- El slug se genera automáticamente al escribir el nombre ✅
- La categoría aparece en el listado ✅

**Notas:**
- Slug se genera en el form con `->live(onBlur: true)` + `afterStateUpdated()` usando `Str::slug()`
- Columna `negocios_count` con `->counts('negocios')` muestra cantidad por categoría
- `TernaryFilter` para filtrar por `activo`
- Ícono de nav: `heroicon-o-tag`, sort 1
- Labels en español: Categoría / Categorías
- Rutas: `/admin/categorias`, `/admin/categorias/create`, `/admin/categorias/{id}/edit`

---

### Paso 10 — Resource Zona en Filament ✅

**Objetivo:** Poder gestionar zonas desde el admin.

**Resultado esperado:**
- `ZonaResource` con form: nombre, slug (auto)
- Table con columnas: nombre, slug, cantidad de negocios

**Criterio de terminado:**
- Desde `/admin/zonas` se puede crear, editar y eliminar zonas ✅

**Notas:**
- Misma lógica de slug auto que CategoriaResource (`live onBlur` + `Str::slug`)
- Columna `negocios_count` con `->counts('negocios')`
- Ícono de nav: `heroicon-o-map-pin`, sort 2
- Sin filtros extra (zona no tiene campo activo)

---

### Paso 11 — Resource Negocio en Filament (datos básicos) ✅

**Objetivo:** Poder cargar un negocio completo desde el admin.

**Resultado esperado:**
- `NegocioResource` con form organizado en tabs o secciones:
  - **Info básica:** nombre, slug, descripcion, categoria, zona
  - **Contacto:** direccion, telefono, email, sitio_web
  - **Ubicación:** lat, lng
  - **Config:** featured, activo, plan
- Table con columnas: nombre, categoria, zona, featured, activo
- Filtros: por categoría, zona, featured, activo

**Criterio de terminado:**
- Se puede crear un negocio completo desde el admin ✅
- El listado muestra los negocios con sus filtros funcionales ✅
- Toggle de `activo` y `featured` funcionan inline ✅

**Notas:**
- Form en 5 tabs: Info básica, Contacto, Horarios, Ubicación, Configuración
- `horarios` usa `KeyValue` (campo/valor editable en el form)
- `categoria_id` y `zona_id` con `Select` searchable
- Columnas: nombre, categoría (badge), zona (badge info), plan (badge coloreado), featured, activo
- Filtros: SelectFilter por categoría, zona, plan + TernaryFilter por featured y activo
- `plan` badge: premium=warning, basico=success, gratuito=gray
- Ícono nav: `heroicon-o-building-storefront`, sort 3

---

### Paso 12 — Imágenes de negocio en Filament ✅

**Objetivo:** Poder subir y gestionar imágenes de cada negocio desde el admin.

**Resultado esperado:**
- Campo `SpatieMediaLibraryFileUpload` en NegocioResource
- Colección `portada` (1 imagen) y colección `galeria` (múltiples)
- Imágenes almacenadas en `storage/app/public`

**Criterio de terminado:**
- Se puede subir una imagen de portada y múltiples de galería ✅
- Las imágenes se ven en el form de edición ✅
- `$negocio->getFirstMediaUrl('portada')` retorna URL válida en tinker ✅

**Notas:**
- Plugin instalado: `filament/spatie-laravel-media-library-plugin ^3.2`
- Tab "Imágenes" agregado al form de NegocioResource
- `portada`: `SpatieMediaLibraryFileUpload` con `imageEditor()`, máx 2MB
- `galeria`: múltiple, reorderable, máx 10 imágenes, máx 2MB c/u
- Columna `SpatieMediaLibraryImageColumn` circular en la tabla (thumbnail)
- `php artisan storage:link` ejecutado — symlink `public/storage` creado
- Imagen por defecto en tabla: avatar generado con ui-avatars.com

---

## Bloque 3 — Layout y rutas

---

### Paso 13 — Layout principal (nav + footer) ✅

**Objetivo:** Tener el shell HTML del sitio público con navegación y pie de página.

**Resultado esperado:**
- `layouts/app.blade.php` con: header (logo + nav principal), `@yield('content')`, footer
- Nav con links a: Home, Negocios, Categorías, Contacto
- Tailwind CSS aplicado, responsive básico
- Alpine.js disponible para menú mobile

**Criterio de terminado:**
- El layout renderiza sin errores ✅
- El menú mobile funciona (toggle con Alpine) ✅
- Se ve correctamente en mobile y desktop ✅

**Notas:**
- Nav sticky con sombra, link activo con clase amber
- Hamburger con Alpine: `x-data="{ open: false }"`, `x-show`, `x-transition`
- Footer de 3 columnas: brand + links explorar + CTA negocio
- Color de marca: `--color-marca: #f59e0b` (amber-500) definido en `app.css` con `@theme`
- `@stack('meta')` para meta tags dinámicos por vista (Paso 24)

---

### Paso 14 — Definir rutas públicas ✅

**Objetivo:** Tener todas las rutas del MVP declaradas y apuntando a sus controllers.

**Resultado esperado:**
- Rutas en `routes/web.php` para home, negocios, categorías, zonas, contacto, about
- Controllers creados con métodos placeholder

**Criterio de terminado:**
- `php artisan route:list` muestra todas las rutas ✅
- `GET /` retorna 200 ✅
- No hay rutas duplicadas ni conflictos ✅

**Notas:**
- 9 rutas públicas: GET /, GET+POST /contacto, GET /negocios, GET /negocios/{negocio}, GET /categorias, GET /categorias/{categoria}, GET /zonas/{zona}, GET /quienes-somos
- Route model binding por slug (via `getRouteKeyName()` en los modelos)
- Controllers: HomeController, NegocioController, CategoriaController, ZonaController, ContactoController, PageController
- Vistas placeholder creadas para todas las rutas (contenido real: Pasos 15–21)

---

## Bloque 4 — Páginas públicas

---

### Paso 15 — Página Home

**Objetivo:** Tener la página principal del sitio con negocios destacados y categorías.

**Resultado esperado:**
- `HomeController@index` pasa a la vista:
  - `$destacados`: negocios con `featured = true`, limitado a 6
  - `$categorias`: todas las categorías con count de negocios
- Vista `home.blade.php` muestra:
  - Hero/banner con buscador
  - Grid de negocios destacados
  - Grid de categorías con íconos

**Criterio de terminado:**
- La home carga con datos de los seeders ✅
- Los negocios destacados se ven con imagen de portada ✅
- Las categorías linkean a su página correcta ✅

**Notas:**
- Hero con gradiente amber, buscador que apunta a `/negocios?q=`
- Cards de negocios: imagen portada (fallback con ícono), badge Premium, categoría, zona
- Cards de categorías: inicial en círculo amber, count de negocios activos
- CTA al final: "¿Tenés un negocio?" → `/contacto`
- `withCount(['negocios' => fn($q) => $q->where('activo', true)])` para count correcto

---

### Paso 16 — Listado de negocios ✅

**Objetivo:** Tener la página `/negocios` con listado paginado y filtros básicos por URL.

**Resultado esperado:**
- `NegocioController@index` acepta query params: `q`, `categoria`, `zona`
- Consulta filtrada y paginada (12 por página)
- Vista `negocios/index.blade.php` con sidebar de filtros y grid de cards

**Criterio de terminado:**
- `/negocios` lista todos los negocios activos ✅
- `/negocios?categoria=restaurantes` filtra correctamente ✅
- La paginación mantiene los filtros activos (`withQueryString()`) ✅

**Notas:**
- Búsqueda por `q`: filtra nombre, descripción y nombre de categoría con `like`
- Filtros de sidebar: categorías y zonas como links (resaltan si están activos)
- "Limpiar filtros" solo aparece si hay filtro activo
- Cards con imagen portada, badge featured (★), categoría y zona
- `orderByDesc('featured')->orderBy('nombre')`: destacados primero
- Empty state con mensaje y link "Ver todos"

---

### Paso 17 — Página detalle de negocio ✅

**Objetivo:** Tener la ficha completa de cada negocio.

**Resultado esperado:**
- `NegocioController@show` busca por slug, lanza 404 si no existe o inactivo
- Vista `negocios/show.blade.php` muestra:
  - Nombre, descripción, categoría, zona
  - Datos de contacto (teléfono, email, web)
  - Dirección y horarios
  - Imagen de portada
  - Meta tags básicos (title, description con datos del negocio)

**Criterio de terminado:**
- `/negocios/{slug}` carga la ficha correcta ✅
- Un slug inexistente devuelve 404 ✅
- El `<title>` de la página contiene el nombre del negocio ✅

**Notas:**
- Layout: hero con imagen portada, columna principal (descripción + galería), sidebar contacto
- Breadcrumb: Inicio › Negocios › Categoría › Nombre
- Badges: categoría (amber), zona (gris), destacado (★)
- Sidebar: iconos amber para teléfono/email/web/dirección + tabla de horarios
- Galería: grid 2-3 cols con `getMedia('galeria')`, solo se muestra si hay imágenes
- `parse_url()` para mostrar solo el host del sitio web
- `abort_unless($negocio->activo, 404)` en el controller
- `@section('description', Str::limit($negocio->descripcion, 155))`

---

### Paso 18 — Página de categoría ✅

**Objetivo:** Listar negocios filtrados por categoría en su propia URL.

**Resultado esperado:**
- `CategoriaController@show` busca categoría por slug, 404 si no existe
- Vista `categorias/show.blade.php` muestra:
  - Nombre e info de la categoría
  - Grid de negocios de esa categoría (paginado)

**Criterio de terminado:**
- `/categorias/restaurantes` lista solo restaurantes ✅
- Un slug inexistente devuelve 404 ✅
- La paginación funciona ✅

**Notas:**
- `abort_unless($categoria->activo, 404)` en show()
- Grid 3 cols igual que negocios/index, con imagen portada y badge destacado
- Contador: "N negocios encontrados" en el header
- `categorias/index.blade.php` también implementado: grid 4 cols con count de negocios
- `CategoriaController@index` usa `withCount(['negocios' => fn ($q) => $q->where('activo', true)])`

---

### Paso 19 — Página de zona ✅

**Objetivo:** Listar negocios filtrados por zona en su propia URL.

**Resultado esperado:**
- `ZonaController@show` con lógica análoga a CategoriaController
- Vista `zonas/show.blade.php` similar a la de categoría

**Criterio de terminado:**
- `/zonas/{slug}` lista negocios de esa zona ✅
- 404 para zonas inexistentes ✅

**Notas:**
- Negocios paginados (12), ordenados por featured desc + nombre asc
- Header con icono pin + nombre zona + contador "N negocios en esta zona"
- Cards con imagen, badge destacado, badge de categoría (link a categorias.show)
- Empty state con mensaje específico de la zona

---

### Paso 20 — Página de contacto ✅

**Objetivo:** Tener un formulario de contacto funcional que guarde el mensaje.

**Resultado esperado:**
- `ContactoController@show` → vista con formulario
- `ContactoController@store` → valida, guarda en `consultas`, redirige con mensaje de éxito
- Validación: nombre (required), email (required, email), mensaje (required, min:10)
- Errores de validación visibles en el formulario

**Criterio de terminado:**
- El formulario enviado correctamente muestra mensaje de éxito ✅
- Los errores de validación se muestran campo por campo ✅
- El registro aparece en la tabla `consultas` de la BD ✅
- Desde Filament se ve la consulta recibida ✅

**Notas:**
- Validación con mensajes en español
- Flash `session('success')` con banner verde en la vista
- Inputs resaltan en rojo si tienen error (`border-red-300 bg-red-50`)
- `old()` preserva valores al volver con error
- Sidebar con info de uso (registrar negocio, actualizar datos, etc.) y tiempo de respuesta
- Layout: formulario (flex-1) + sidebar (lg:w-72)

---

### Paso 21 — Página "quiénes somos" ✅

**Objetivo:** Tener la página estática institucional.

**Resultado esperado:**
- `PageController@about` retorna vista estática
- Vista `pages/about.blade.php` con contenido del proyecto

**Criterio de terminado:**
- `/quienes-somos` carga sin errores ✅
- El layout (nav y footer) está presente ✅

**Notas:**
- Secciones: misión, cómo funciona, para negocios (con íconos amber)
- Sidebar: stats en tiempo real (negocios/categorías/zonas con queries directas en la vista) + CTA registrar negocio
- Stats usan `App\Models\*::count()` directamente desde la vista (página estática, no requiere controller)

---

## Bloque 5 — Búsqueda

---

### Paso 22 — Configurar Laravel Scout con driver MySQL ✅

**Objetivo:** Tener búsqueda fulltext funcional sin infraestructura adicional.

**Resultado esperado:**
- Scout configurado con `SCOUT_DRIVER=database` en `.env`
- Modelo `Negocio` implementa `Searchable`
- Método `toSearchableArray()` incluye: nombre, descripcion, direccion
- `shouldBeSearchable()` retorna `(bool) $this->activo`

**Criterio de terminado:**
- `Negocio::search('almacen')->get()` retorna resultados relevantes ✅
- `php artisan scout:import "App\Models\Negocio"` importa los 20 negocios ✅

**Notas:**
- `toSearchableArray()` incluye nombre, descripcion y direccion (columnas de la tabla, no relaciones)
- Con driver `database`, Scout hace LIKE queries directamente sobre la tabla
- `shouldBeSearchable()` excluye negocios inactivos del índice

---

### Paso 23 — Buscador en el sitio público ✅

**Objetivo:** Tener el campo de búsqueda del header y la home funcionando.

**Resultado esperado:**
- Form GET con campo `q` apunta a `/negocios?q=texto`
- `NegocioController@index` usa `Negocio::search($q)` si `q` está presente, sino `Negocio::query()`
- Los filtros de categoría/zona se combinan con la búsqueda

**Criterio de terminado:**
- Buscar "almacen" muestra negocios relevantes ✅
- La búsqueda se combina con filtros de categoría/zona ✅
- Si no hay resultados, se muestra mensaje claro ✅

**Notas:**
- Cuando `q` está presente: `Negocio::search($q)->query(fn($q) => ...)` con filtros en el callback
- Cuando no hay `q`: Eloquent puro (path anterior sin cambios)
- Los filtros `categoria` y `zona` se aplican con `->when()` dentro del Scout `->query()` callback
- `GET /negocios?q=almacen` responde HTTP 200 ✅

---

## Bloque 6 — SEO básico

---

### Paso 24 — Meta tags dinámicos ✅

**Objetivo:** Tener title y description únicos por página para SEO.

**Resultado esperado:**
- Canonical URL en el layout
- Open Graph tags (og:title, og:description, og:url, og:type, og:site_name) en el layout
- Ficha de negocio: og:type=article + og:image con la portada

**Criterio de terminado:**
- Cada página tiene `<title>` único y descriptivo ✅
- Canonical URL correcta en todas las páginas ✅
- OG tags presentes en home y ficha de negocio ✅
- Ficha de negocio tiene og:type=article y og:image cuando hay portada ✅

**Notas:**
- `@yield('title')` y `@yield('description')` ya estaban en el layout desde el Paso 13
- Canonical: `<link rel="canonical" href="{{ url()->current() }}">` en el layout
- OG base tags en el layout usando `@yield('title')`, `@yield('description')`, `@yield('og_type', 'website')`
- `negocios/show.blade.php`: `@section('og_type', 'article')` + `@push('meta')` con og:image
- Todas las vistas ya tenían `@section('title', ...)` y `@section('description', ...)`

---

### Paso 25 — Sitemap XML ✅

**Objetivo:** Tener un sitemap para que los buscadores indexen el sitio.

**Resultado esperado:**
- Ruta `GET /sitemap.xml` que genera XML con:
  - Páginas estáticas (home, contacto, about)
  - Una URL por negocio activo
  - Una URL por categoría
  - Una URL por zona

**Criterio de terminado:**
- `/sitemap.xml` responde con XML válido ✅
- Content-Type: application/xml ✅
- 38 URLs en total (5 estáticas + 20 negocios + 8 categorías + 5 zonas) ✅

**Notas:**
- `SitemapController@index` — consulta solo campos `slug` y `updated_at` (sin cargar el modelo completo)
- Vista `sitemap.blade.php` en raíz de views — no extiende el layout app
- `<?php echo '<?xml version...'; ?>` para evitar que Blade interprete `<?xml` como PHP
- `lastmod` con `->toAtomString()` (ISO 8601, formato estándar de sitemaps)
- Prioridades: home=1.0, negocios.index=0.9, categorias=0.8, negocios=0.8, categorias.show=0.7, zonas=0.6, estáticas=0.5/0.4

---

## Bloque 7 — Calidad y cierre MVP

---

### Paso 26 — Páginas de error (404 y 500) ✅

**Objetivo:** Tener páginas de error con el diseño del sitio.

**Resultado esperado:**
- `resources/views/errors/404.blade.php` con layout y mensaje amigable
- `resources/views/errors/500.blade.php` similar

**Criterio de terminado:**
- Una URL inexistente muestra la página 404 con nav y footer ✅
- El código HTTP de respuesta es 404 ✅

**Notas:**
- `404.blade.php` extiende `layouts.app` — tiene nav + footer completo, título + OG tags
- `500.blade.php` es HTML standalone (sin @extends) con estilos inline — no depende del framework que puede estar roto
- `GET /ruta-inexistente` → HTTP 404 con vista personalizada ✅

---

### Paso 27 — Optimización básica para producción ✅

**Objetivo:** Tener el sitio preparado para un primer deploy.

**Resultado esperado:**
- `php artisan config:cache` y `route:cache` corren sin errores
- `npm run build` genera assets optimizados

**Criterio de terminado:**
- El sitio funciona con cache de config y rutas activo ✅
- `npm run build` genera 55KB CSS + 83KB JS sin errores ✅
- No hay errores en el log de Laravel ✅

**Notas:**
- `config:cache` + `route:cache` + `view:cache` corren sin errores
- Todos los endpoints responden HTTP 200 con caché activo
- En desarrollo local: `route:clear` + `view:clear` para no bloquear cambios
- Assets de producción: CSS 55.77KB (gzip 11KB), JS 83.51KB (gzip 31KB)
- Para producción: `APP_DEBUG=false`, `APP_ENV=production`, `php artisan storage:link`

---

---

## Bloque 8 — Home redesign + Mapas

---

### Paso 28 — Rediseño completo de la Home ✅

**Objetivo:** Reemplazar la home básica por un diseño de producción con secciones diferenciadas.

**Resultado esperado:**
- Hero con buscador + 3 quick actions (overlap visual hacia la sección siguiente)
- Sección destacados (3 negocios con `featured=true`)
- Sección mapa (card imagen + Leaflet)
- Sección categorías (grid con ícono + count)
- Sección CTA registro

**Criterio de terminado:**
- Home carga con HTTP 200 ✅
- Círculos de quick actions visualmente a mitad entre hero y destacados ✅
- Mapa Leaflet con CartoDB Voyager visible ✅
- Categorías muestran ícono desde `<x-cat-icon>` ✅

**Notas:**
- Overlap: `absolute left-1/2 bottom-0 z-30 -translate-x-1/2 translate-y-1/2` en el contenedor de círculos dentro del hero (`position: relative`)
- Layout: `@stack('styles')` y `@stack('scripts')` agregados a `layouts/app.blade.php`
- HomeController: destacados limitados a 3, agrega `$negocios_mapa` (negocios con lat+lng) y `$zonas`
- Tailwind v4 quirk: `h-1/2` en flex compila como `height: 50%` — solución: usar `h-40` (explícito)
- `npm run build` requerido después de agregar nuevas clases de Tailwind
- Quick actions: "Buscar negocios" → `/negocios`, "Ver en el mapa" → `/mapa`, "Explorar categorías" → `/categorias`

---

### Paso 29 — Mapa interactivo en admin (Filament) ✅

**Objetivo:** Reemplazar los inputs manuales de lat/lng por un mapa clickeable en el form de NegocioResource.

**Resultado esperado:**
- Tab "Ubicación" en NegocioResource muestra mapa Leaflet
- Click en el mapa fija el marcador y actualiza lat/lng vía `$wire`
- Marcador arrastrable para ajustar posición
- Inputs lat/lng en modo solo lectura muestran el valor actual

**Criterio de terminado:**
- Mapa carga en `/admin/negocios/create` ✅
- Click en mapa actualiza los campos lat/lng del form ✅
- Drag del marcador también actualiza lat/lng ✅

**Notas:**
- Componente: `resources/views/filament/forms/components/map-picker.blade.php`
- Lógica JS en `<script>` con `window.mapPickerData = function(){}` — NO en `x-data=""` (las comillas rompen el parser de Alpine)
- `wire:ignore` en el div del mapa para evitar re-render de Livewire
- Carga de Leaflet dinámica vía JS (no @push) para compatibilidad con Livewire navigate
- `$wire.data.lat` / `$wire.data.lng` accesibles desde Alpine mediante `this.$wire`
- Scroll wheel zoom: activo solo en mouseenter, desactivado en mouseleave

---

### Paso 30 — Página /mapa completa con filtros ✅

**Objetivo:** Tener una página dedicada al mapa con filtros en cascada y lista de negocios visibles.

**Resultado esperado:**
- Ruta `GET /mapa` → `MapaController@index`
- Barra sticky: select zona (paso 1) → al elegir zona aparecen select categoría + buscador
- Mapa Leaflet full-width con pines reales de negocios
- Al elegir zona: oculta otros pines + hace fitBounds + muestra filtros extra
- Pill flotante con count de pines activos
- Lista debajo: negocios visibles en el viewport, se actualiza en moveend
- Integración con home: select zona en home filtra pines y actualiza href "Ver mapa completo → /mapa?zona=ID"
- Al abrir /mapa?zona=ID: pre-selecciona la zona y auto-aplica el filtro

**Criterio de terminado:**
- `/mapa` carga HTTP 200 ✅
- Filtro de zona hace zoom a la zona ✅
- Lista de negocios se actualiza al mover el mapa ✅
- Zona persiste al navegar desde home ✅

**Notas:**
- `MapaController`: pasa `$zonas`, `$categorias`, `$negocios` (con lat/lng), `$zonaInicial` desde `request()->integer('zona')`
- Pines: tooltips en hover (nombre), popup en click (nombre, categoría, link)
- Altura del mapa: `clamp(320px, 55vh, 500px)` para que siempre se vea contenido debajo
- Marcadores tienen propiedades `negocioZona`, `negocioCat`, `negocioNombre` para filtrado en JS sin requests al servidor
- `esc()` helper para sanitizar strings en el HTML del popup

---

### Paso 31 — Componente x-cat-icon en todas las vistas ✅

**Objetivo:** Unificar el uso de íconos de categoría con el componente `<x-cat-icon>` en todo el sitio.

**Resultado esperado:**
- `<x-cat-icon :name="$categoria->icono">` disponible en home, /categorias y /categorias/{slug}
- 8 íconos mapeados por nombre: coffee, pill, shirt, cake, utensils, heart-pulse, briefcase, shopping-cart
- Fallback a ícono genérico si el nombre no existe

**Criterio de terminado:**
- Home muestra íconos diferenciados por categoría ✅
- `/categorias` muestra íconos en las cards ✅
- `/categorias/{slug}` muestra ícono en el header ✅

**Notas:**
- Componente en `resources/views/components/cat-icon.blade.php`
- `viewBox="0 0 24 24"`, `stroke="currentColor"` — escala a cualquier tamaño con clase CSS
- Tamaños usados: `w-12 h-12` (home), `w-7 h-7` (categorias/index), `w-8 h-8` (categorias/show header)
- Los paths SVG actuales son placeholders de Heroicons — el usuario los reemplazará con SVGs propios en 48×48

---

## Bloque 9 — Mejoras de contenido y admin

---

### Paso 32 — Logo de negocio ✅

**Objetivo:** Permitir subir un logo opcional por negocio que se muestre en la ficha de detalle.

**Resultado esperado:**
- Colección `logo` (singleFile) en el modelo Negocio
- Campo de upload en el tab "Imágenes" de NegocioResource
- Sidebar de `negocios/show` muestra el logo si existe, no cambia nada si no

**Criterio de terminado:**
- Se puede subir logo desde el admin ✅
- El logo aparece en el sidebar de la ficha del negocio ✅
- Si no hay logo, la página se ve igual que antes ✅

**Notas:**
- Colección `logo` con `->singleFile()` registrada antes de `portada` en `registerMediaCollections()`
- Campo en Filament: `SpatieMediaLibraryFileUpload` con `imageEditor()`, máx 1MB
- Vista: `@if($negocio->hasMedia('logo'))` → `<img class="max-h-20 ... rounded-2xl">`
- Aparece al inicio del sidebar, antes de la sección "Contacto"

---

### Paso 33 — Filtros cruzados zona↔categoría ✅

**Objetivo:** Agregar filtros por localidad en la página de categoría y filtros por categoría en la página de zona.

**Resultado esperado:**
- `/categorias/{slug}`: pills de filtro por zona (solo zonas con negocios en esa categoría)
- `/zonas/{slug}`: pills de filtro por categoría (solo categorías con negocios en esa zona)
- Pills activos resaltados en amber, filtro mantiene query string en paginación

**Criterio de terminado:**
- Filtro por zona en categorias/show funciona ✅
- Filtro por categoría en zonas/show funciona ✅
- Solo aparecen las pills que tienen negocios reales ✅
- Pills desaparecen si solo hay una opción (zonas/show) ✅

**Notas:**
- `CategoriaController@show`: `$zonaId = request()->integer('zona') ?: null`, `->when($zonaId, ...)`, `->withQueryString()`
- `$zonas` filtradas con `whereHas('negocios', fn($q) => $q->activo()->where('categoria_id', $categoria->id))`
- `ZonaController@show`: misma lógica inversa para categorías
- Pills: `@if($categorias->count() > 1)` en zonas/show (no tiene sentido mostrar filtro con una sola opción)
- Placeholder de cards sin portada mejorado: gradiente amber-50→amber-100 con `<x-cat-icon>` centrado

---

### Paso 34 — Consultas en admin + notificación email ✅

**Objetivo:** Ver los mensajes del formulario de contacto en el panel admin y recibir notificación por email.

**Resultado esperado:**
- `ConsultaResource` en Filament con badge de no leídos en el nav
- Vista de consulta con mensaje completo, toggle "marcar leído/no leído"
- Email automático a `MAIL_ADMIN` al recibir una consulta nueva

**Criterio de terminado:**
- Las consultas aparecen en `/admin/consultas` ✅
- Badge naranja muestra el count de no leídas ✅
- Toggle de leído funciona desde la tabla ✅
- Email se envía al guardar la consulta (va al log en desarrollo) ✅

**Notas:**
- `ConsultaResource`: solo ruta `index` (sin create ni edit), `ViewAction` para leer completo
- Badge: `getNavigationBadge()` + `getNavigationBadgeColor()` = `warning`
- Ícono nav: `heroicon-o-envelope`, sort 10
- `Mail\NuevaConsulta`: Mailable con markdown `emails/nueva-consulta.blade.php`
- Email incluye nombre, email, mensaje y botón "Ver en el panel" → `/admin/consultas`
- `config/app.php`: `admin_email => env('MAIL_ADMIN', 'admin@example.com')`
- `.env`: `MAIL_ADMIN="rolodev.uy@gmail.com"`, `MAIL_FROM_ADDRESS="no-reply@guialocal.test"`
- En producción: cambiar `MAIL_MAILER=log` a `smtp` con credenciales reales

---

## Bloque 10 — SEO estructurado

---

### Paso 35 — JSON-LD por negocio ✅

**Objetivo:** Agregar datos estructurados schema.org a la ficha de cada negocio para mejorar el SEO y los rich results de Google.

**Resultado esperado:**
- `<script type="application/ld+json">` en el `<head>` de `negocios/show.blade.php`
- Tipo schema.org mapeado según la categoría del negocio
- Propiedades opcionales solo incluidas si el dato existe

**Criterio de terminado:**
- JSON-LD válido en el `<head>` de cualquier ficha de negocio ✅
- Tipo correcto según categoría (Restaurant, Pharmacy, Bakery, etc.) ✅
- Propiedades opcionales ausentes si el negocio no tiene el dato ✅

**Notas:**
- Mapeo `icono → @type`: utensils→Restaurant, coffee→CafeOrCoffeeShop, cake→Bakery, pill→Pharmacy, shopping-cart→GroceryStore, heart-pulse→HealthAndBeautyBusiness, briefcase→ProfessionalService, shirt→ClothingStore. Fallback: LocalBusiness
- Propiedades incluidas: name, url, description (truncado a 300 chars), address (PostalAddress con zona como locality), telephone, email, sameAs (sitio_web), geo (GeoCoordinates si hay lat/lng), image (URL portada si existe)
- `openingHours` en formato "Mo-Fr HH:MM-HH:MM" generado desde el Repeater estructurado (Paso 36)
- `specialOpeningHoursSpecification` generado desde horarios especiales activos (Paso 37)
- Renderizado con `json_encode(..., JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)` para legibilidad
- Todo el bloque PHP está dentro de `@php ... @endphp` en el `@push('meta')` de la vista

---

### Paso 36 — Horarios estructurado con Repeater ✅

**Objetivo:** Reemplazar el campo `KeyValue` libre de horarios por un selector estructurado de franjas con días y horas, compatible con schema.org.

**Resultado esperado:**
- Tab "Horarios" en NegocioResource con Repeater de franjas: día inicio, día fin, apertura, cierre, cerrado
- Formato JSON: `[{dia_inicio, dia_fin, apertura, cierre, cerrado}]`
- Vista `negocios/show.blade.php` renderiza las franjas
- JSON-LD incluye `openingHours` en formato "Mo-Fr HH:MM-HH:MM"

**Criterio de terminado:**
- Se puede cargar una franja de horario desde el admin ✅
- La ficha del negocio muestra los horarios correctamente ✅
- El JSON-LD incluye `openingHours` con formato válido ✅

**Notas:**
- Repeater con `Select` (Lunes–Domingo, `->native(false)`), `TimePicker` (sin segundos), `Toggle` cerrado (`->live()`)
- `TimePicker` apertura/cierre con `->visible(fn(Get $get) => !$get('cerrado'))` — condicional por item
- `use Filament\Forms\Get;` importado en NegocioResource
- Mapeo día ES→schema.org: Lunes→Mo, Martes→Tu, Miércoles→We, Jueves→Th, Viernes→Fr, Sábado→Sa, Domingo→Su
- NegocioSeeder actualizado con el nuevo formato estructurado

---

### Paso 37 — Horarios especiales (fechas puntuales activables) ✅

**Objetivo:** Permitir cargar fechas especiales (feriados, eventos) con horario diferencial, activables y desactivables sin eliminarlas.

**Resultado esperado:**
- Columna `horarios_especiales` (JSON nullable) en tabla `negocios`
- Section colapsable "Fechas especiales" dentro del tab Horarios de NegocioResource
- Repeater: nombre libre, DatePicker, toggle se_repite anualmente, toggle activo, toggle cerrado + TimePickers condicionales
- Vista muestra solo fechas activas, ordenadas por próxima ocurrencia
- JSON-LD incluye `specialOpeningHoursSpecification` con las fechas activas

**Criterio de terminado:**
- Se puede cargar una fecha especial desde el admin ✅
- Toggle activo/inactivo sin eliminar el registro ✅
- Fecha anual se recalcula al año siguiente si ya pasó ✅
- JSON-LD incluye `specialOpeningHoursSpecification` ✅

**Notas:**
- Migration: `horarios_especiales` JSON nullable after `horarios`
- Model: `horarios_especiales` en `$fillable` y `$casts` como `array`
- Section con `->collapsible()->collapsed()` en el form
- `->visible(fn(Get $get) => !$get('cerrado'))` para TimePickers dentro del Repeater de especiales
- Carbon: `setYear(now()->year)` + `addYear()` si la fecha ya pasó → próxima ocurrencia
- `translatedFormat('j \d\e F')` para mostrar en español (requiere `APP_LOCALE=es`)

---

### Paso 38 — Filtros dinámicos con Livewire en /negocios ✅

**Objetivo:** Reemplazar los filtros por GET clásico con filtros reactivos sin reload de página.

**Resultado esperado:**
- Componente `NegociosIndex` con propiedades `$q`, `$categoria`, `$zona` sincronizadas en la URL
- Buscador con debounce 300ms, pills de categoría/zona con click inmediato
- Paginación Livewire con reset al cambiar filtros
- Botón "Limpiar filtros" resetea todo en un click

**Criterio de terminado:**
- Filtrar por categoría no recarga la página ✅
- La URL se actualiza con los filtros activos (?q=, ?categoria=, ?zona=) ✅
- `NegocioController@index` simplificado a solo retornar la vista ✅

**Notas:**
- Componente: `app/Livewire/NegociosIndex.php` — usa `WithPagination` + `#[Url]`
- Vista: `resources/views/livewire/negocios-index.blade.php`
- Búsqueda con LIKE nativo (nombre, descripcion, direccion) — sin Scout dentro del componente
- `updatingQ/Categoria/Zona()` llaman `resetPage()` para volver a la página 1
- `wire:key="negocio-{{ $id }}"` en las cards para diff correcto de Livewire
- Latencia ~2s en XAMPP local (dev); se optimiza en producción si hace falta

---

### Paso 39 — Redirects 301 para slugs de negocios cambiados ✅

**Objetivo:** Evitar 404 cuando el nombre (y por ende el slug) de un negocio cambia, preservando el SEO acumulado.

**Resultado esperado:**
- Al guardar un negocio con nombre editado, el slug viejo queda registrado
- Visitar `/negocios/slug-viejo` redirige 301 a `/negocios/slug-nuevo`
- Si el negocio se elimina, el redirect queda huérfano y devuelve 404

**Criterio de terminado:**
- Editar nombre de negocio guarda el slug viejo automáticamente ✅
- `/negocios/slug-viejo` responde HTTP 301 → `/negocios/slug-nuevo` ✅
- Slug inexistente sin redirect devuelve 404 ✅

**Notas:**
- Tabla `slug_redirects`: `old_slug` (unique), `negocio_id` (FK nullable, nullOnDelete)
- `Negocio::booted()`: hook `updating` con `isDirty('slug')` → `SlugRedirect::updateOrCreate()`
- `NegocioController@show(string $slug)`: lookup manual + fallback a `slug_redirects` con 301
- Ruta cambiada de `{negocio}` (route model binding) a `{slug}` (string) — `route('negocios.show', $negocio)` en Blade sigue funcionando

---

## Bloque 11 — Capa editorial

---

### Paso 40 — Modelo Articulo + Resource Filament + frontend ✅

**Objetivo:** Tener una sección editorial de artículos gestionable desde el admin y visible en el sitio público.

**Resultado esperado:**
- Tabla `articulos` con titulo, slug, extracto, cuerpo (rich text), publicado, publicado_en, categoria_id, negocio_id
- Modelo `Articulo` con HasSlug, HasMedia (portada), scopePublicado, relaciones a Categoria y Negocio
- `ArticuloResource` en Filament con tabs: Contenido (RichEditor), Imagen, Relaciones, Configuración
- Frontend: `/articulos` (listado paginado) + `/articulos/{slug}` (detalle)

**Criterio de terminado:**
- Se puede crear un artículo con rich text desde `/admin/articulos` ✅
- Solo artículos publicados son visibles en el frontend ✅
- Link "Artículos" en el nav (desktop y mobile) ✅

**Notas:**
- `RichEditor` nativo de Filament (sin paquetes extra) con toolbar reducida: h2, h3, bold, italic, lists, link, blockquote
- Slug auto-generado desde titulo con `->live(onBlur: true)` + `afterStateUpdated(Str::slug)`
- Vista detalle usa clases Tailwind `prose` para el contenido del cuerpo
- Negocio relacionado se muestra como card al pie del artículo
- `publicado_en` puede ser nulo; se ordenan por `publicado_en DESC, created_at DESC`

---

### Paso 41 — RSS feed + sitemap artículos ✅

**Objetivo:** Distribuir los artículos via RSS y asegurarse de que Google los indexe via sitemap.

**Resultado esperado:**
- `GET /feed` devuelve RSS 2.0 con los últimos 20 artículos publicados
- `<link rel="alternate">` en el `<head>` para autodescubrimiento
- `SitemapController` incluye artículos publicados en `/sitemap.xml`

**Criterio de terminado:**
- `/feed` responde con `Content-Type: application/rss+xml` ✅
- Cada `<item>` tiene title, link, guid, description, pubDate, category, enclosure ✅
- `/sitemap.xml` incluye `/articulos` y cada artículo publicado ✅

**Notas:**
- `FeedController`: últimos 20 artículos, ordered by `publicado_en DESC, created_at DESC`
- Vista `feed.blade.php`: CDATA en title y description para caracteres especiales
- Enclosure con URL de portada (tipo `image/jpeg`) si existe
- Sitemap: `/articulos/index` aparece solo si hay al menos un artículo publicado
- Prioridad artículos en sitemap: `0.7` (igual que categorías)

---

### Paso 42 — Guías temáticas ✅

**Objetivo:** Agregar un nuevo tipo de contenido editorial: guías temáticas que agrupan negocios seleccionados bajo un título, intro y cuerpo rich text.

**Resultado esperado:**
- Tabla `guias` con titulo, slug, intro, cuerpo, categoria_id, publicado, publicado_en
- Tabla pivot `guia_negocio` con orden
- Modelo `Guia` con HasSlug, HasMedia (portada), scopePublicado, BelongsToMany negocios
- `GuiaResource` en Filament con 4 tabs: Contenido, Imagen, Negocios, Configuración
- Frontend: `/guias` (listado paginado) + `/guias/{slug}` (detalle con lista de negocios)
- Link "Guías" en nav desktop y mobile
- Guías publicadas incluidas en `/sitemap.xml`

**Criterio de terminado:**
- `/guias` responde HTTP 200 ✅
- `/admin/guias` accesible (redirect 302 a login si no autenticado) ✅
- Sitemap incluye sección guías ✅
- Nav muestra "Guías" con active state ✅

**Notas:**
- Pivot `guia_negocio`: `guia_id`, `negocio_id` (unique), `orden` (smallInt, default 0)
- Negocios en el form: `Select::make('negocios')->multiple()->relationship()` — Filament gestiona el pivot automáticamente
- Vista `guias/show.blade.php`: cuerpo con clases `prose` + grid 2 cols de cards de negocios (con imagen portada, categoría, zona, dirección)
- Vista `guias/index.blade.php`: grid 3 cols con badge count de negocios flotante sobre la imagen
- Sitemap: `/guias` index + cada guía publicada, prioridad 0.7
- NavigationSort: 6 (después de Artículos en sort 5)
- Link "Guías" en nav es condicional: solo aparece si hay al menos una guía publicada — `View::composer('layouts.app', ...)` en `AppServiceProvider` comparte `$hayGuias` (bool)

---

### Paso 43 — Secciones destacadas (featured_slots) ✅

**Objetivo:** Permitir al admin curar exactamente qué negocios, artículos o guías aparecen en la home, en qué posición y con qué vigencia.

**Resultado esperado:**
- Tabla `featured_slots`: posicion, morph (slotable_type + slotable_id), orden, activo, valido_hasta
- Modelo `FeaturedSlot` con morph y scope `activo(posicion)`
- `FeaturedSlotResource` en Filament: select de posición, select de tipo (reactivo), select de elemento, orden, activo, vencimiento
- `HomeController` usa slots `home_negocios` para la sección destacados (fallback al boolean `featured` si no hay slots)
- `HomeController` pasa `$slotsEditoriales` (slots `home_editorial`) — artículos o guías
- `home.blade.php` muestra sección "Del barrio" solo si hay slots editoriales activos

**Criterio de terminado:**
- `GET /` responde 200 ✅
- `GET /admin/featured-slots` accesible ✅
- Sin slots: home muestra el fallback `featured` igual que antes ✅

**Notas:**
- Posiciones predefinidas en `FeaturedSlot::POSICIONES`: `home_negocios`, `home_editorial`
- Scope `activo(posicion)`: filtra por posicion + activo=true + (valido_hasta null O >= hoy)
- Form reactivo: al cambiar el tipo se resetea el elemento y se cargan las opciones correspondientes
- Tabla con `->reorderable('orden')` para drag-and-drop de prioridad
- Sección editorial en home: badge diferenciado (Guía=azul, Artículo=amber), muestra portada si existe
- NavigationSort: 7

---

## Bloque 12 — Capa comercial

---

### Paso 44 — Promociones ✅

**Objetivo:** Permitir que los negocios tengan promociones con vigencia temporal, gestionables desde el admin y visibles en la ficha pública.

**Resultado esperado:**
- Tabla `promociones`: negocio_id, titulo, descripcion, fecha_inicio, fecha_fin, activo + Media (imagen)
- Modelo `Promocion` con `InteractsWithMedia`, colección `imagen`, scope `vigente()`
- `PromocionResource` en Filament: selector de negocio, título, descripción, fechas, toggle activo, upload imagen, columna "Vigente" calculada
- `Negocio` tiene `hasMany(Promocion::class)`
- `NegocioController@show` carga `$promociones` vigentes y las pasa a la vista
- `negocios/show.blade.php` muestra sección "Promociones" si hay vigentes

**Criterio de terminado:**
- `/admin/promocions` accesible ✅
- Ficha de negocio muestra promociones vigentes si las hay ✅
- Sin promociones, la sección no aparece ✅

**Notas:**
- Scope `vigente()`: activo=true + fecha_inicio <= hoy + (fecha_fin null O fecha_fin >= hoy)
- Imagen de promo: fallback con ícono de etiqueta si no hay imagen
- `fecha_fin` con color danger en tabla si ya pasó
- Columna calculada "Vigente" en tabla: `IconColumn` con state computado
- NavigationSort: 4 (antes de Artículos)
- Ruta Filament: `/admin/promocions` (pluralización automática de Laravel)

---

### Paso 45 — Redes sociales en fichas de negocio ✅

**Objetivo:** Permitir cargar links a redes sociales de cada negocio desde el admin y mostrarlos como pills con íconos en la ficha pública.

**Resultado esperado:**
- Columna `redes_sociales` (JSON nullable) en tabla `negocios`
- Repeater en tab "Contacto" de NegocioResource: dropdown de red + input de URL
- Ficha pública muestra las redes como pills neutros con ícono SVG de color de marca
- JSON-LD `sameAs` incluye sitio_web + todas las URLs de redes

**Criterio de terminado:**
- Se puede agregar/quitar redes desde el admin con el botón "+ Agregar red social" ✅
- Ficha pública muestra las redes solo si hay alguna cargada ✅
- Íconos SVG correctos para Instagram, Facebook, TikTok, YouTube, X, LinkedIn, WhatsApp ✅
- Pills: fondo gris neutro, ícono con color de marca, texto gris ✅

**Notas:**
- JSON almacenado como array de objetos `[{"red": "instagram", "url": "https://..."}]`
- Componente `<x-social-icon :red="$red">` con SVGs inline por red (`resources/views/components/social-icon.blade.php`)
- Colores de marca aplicados solo al ícono via `style="color: #hex"`, no al pill completo
- `sameAs` en JSON-LD: string si solo hay una URL, array si hay varias

---

## Notas

- Los pasos de **Etapa 2 en adelante** (Livewire, mapas, SEO avanzado, editorial, comercial) se agregarán a este archivo cuando comience cada etapa.
- Cada paso puede ejecutarse en una sola sesión de trabajo.
- El orden dentro de cada bloque es el recomendado, pero pasos de distintos bloques pueden hacerse en paralelo si el contexto lo permite.
