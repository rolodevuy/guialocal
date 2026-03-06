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

### Paso 10 — Resource Zona en Filament

**Objetivo:** Poder gestionar zonas desde el admin.

**Resultado esperado:**
- `ZonaResource` con form: nombre, slug (auto)
- Table con columnas: nombre, slug, cantidad de negocios

**Criterio de terminado:**
- Desde `/admin/zonas` se puede crear, editar y eliminar zonas

---

### Paso 11 — Resource Negocio en Filament (datos básicos)

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
- Se puede crear un negocio completo desde el admin
- El listado muestra los negocios con sus filtros funcionales
- Toggle de `activo` y `featured` funcionan inline

---

### Paso 12 — Imágenes de negocio en Filament

**Objetivo:** Poder subir y gestionar imágenes de cada negocio desde el admin.

**Resultado esperado:**
- Campo `SpatieMediaLibraryFileUpload` en NegocioResource
- Colección `portada` (1 imagen) y colección `galeria` (múltiples)
- Imágenes almacenadas en `storage/app/public`

**Criterio de terminado:**
- Se puede subir una imagen de portada y múltiples de galería
- Las imágenes se ven en el form de edición
- `$negocio->getFirstMediaUrl('portada')` retorna URL válida en tinker

---

## Bloque 3 — Layout y rutas

---

### Paso 13 — Layout principal (nav + footer)

**Objetivo:** Tener el shell HTML del sitio público con navegación y pie de página.

**Resultado esperado:**
- `layouts/app.blade.php` con: header (logo + nav principal), `@yield('content')`, footer
- Nav con links a: Home, Negocios, Categorías, Contacto
- Tailwind CSS aplicado, responsive básico
- Alpine.js disponible para menú mobile

**Criterio de terminado:**
- El layout renderiza sin errores
- El menú mobile funciona (toggle con Alpine)
- Se ve correctamente en mobile y desktop

---

### Paso 14 — Definir rutas públicas

**Objetivo:** Tener todas las rutas del MVP declaradas y apuntando a sus controllers.

**Resultado esperado:**
- Rutas en `routes/web.php`:
  - `GET /` → `HomeController@index`
  - `GET /negocios` → `NegocioController@index`
  - `GET /negocios/{slug}` → `NegocioController@show`
  - `GET /categorias/{slug}` → `CategoriaController@show`
  - `GET /zonas/{slug}` → `ZonaController@show`
  - `GET /contacto` → `ContactoController@show`
  - `POST /contacto` → `ContactoController@store`
  - `GET /quienes-somos` → `PageController@about`
- Controllers creados (vacíos o con respuesta placeholder)

**Criterio de terminado:**
- `php artisan route:list` muestra todas las rutas
- Cada ruta devuelve 200 (aunque sea con placeholder)
- No hay rutas duplicadas ni conflictos

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
- La home carga con datos de los seeders
- Los negocios destacados se ven con imagen de portada
- Las categorías linkean a su página correcta

---

### Paso 16 — Listado de negocios

**Objetivo:** Tener la página `/negocios` con listado paginado y filtros básicos por URL.

**Resultado esperado:**
- `NegocioController@index` acepta query params: `categoria`, `zona`
- Consulta filtrada y paginada (12 por página)
- Vista `negocios/index.blade.php` con:
  - Grid de cards de negocio
  - Sidebar o header con filtros (links por categoría y zona)
  - Paginación de Laravel

**Criterio de terminado:**
- `/negocios` lista todos los negocios activos
- `/negocios?categoria=restaurante` filtra correctamente
- La paginación funciona y mantiene los filtros activos

---

### Paso 17 — Página detalle de negocio

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
- `/negocios/{slug}` carga la ficha correcta
- Un slug inexistente devuelve 404
- El `<title>` de la página contiene el nombre del negocio

---

### Paso 18 — Página de categoría

**Objetivo:** Listar negocios filtrados por categoría en su propia URL.

**Resultado esperado:**
- `CategoriaController@show` busca categoría por slug, 404 si no existe
- Vista `categorias/show.blade.php` muestra:
  - Nombre e info de la categoría
  - Grid de negocios de esa categoría (paginado)

**Criterio de terminado:**
- `/categorias/restaurante` lista solo restaurantes
- Un slug inexistente devuelve 404
- La paginación funciona

---

### Paso 19 — Página de zona

**Objetivo:** Listar negocios filtrados por zona en su propia URL.

**Resultado esperado:**
- `ZonaController@show` con lógica análoga a CategoriaController
- Vista `zonas/show.blade.php` similar a la de categoría

**Criterio de terminado:**
- `/zonas/{slug}` lista negocios de esa zona
- 404 para zonas inexistentes

---

### Paso 20 — Página de contacto

**Objetivo:** Tener un formulario de contacto funcional que guarde el mensaje.

**Resultado esperado:**
- `ContactoController@show` → vista con formulario
- `ContactoController@store` → valida, guarda en `consultas`, redirige con mensaje de éxito
- Validación: nombre (required), email (required, email), mensaje (required, min:20)
- Errores de validación visibles en el formulario

**Criterio de terminado:**
- El formulario enviado correctamente muestra mensaje de éxito
- Los errores de validación se muestran campo por campo
- El registro aparece en la tabla `consultas` de la BD
- Desde Filament se ve la consulta recibida

---

### Paso 21 — Página "quiénes somos"

**Objetivo:** Tener la página estática institucional.

**Resultado esperado:**
- `PageController@about` retorna vista estática
- Vista `pages/about.blade.php` con contenido del proyecto

**Criterio de terminado:**
- `/quienes-somos` carga sin errores
- El layout (nav y footer) está presente

---

## Bloque 5 — Búsqueda

---

### Paso 22 — Configurar Laravel Scout con driver MySQL

**Objetivo:** Tener búsqueda fulltext funcional sin infraestructura adicional.

**Resultado esperado:**
- Scout configurado con `SCOUT_DRIVER=database` en `.env`
- Modelo `Negocio` implementa `Searchable`
- Método `toSearchableArray()` incluye: nombre, descripcion, categoria, zona
- Índices fulltext aplicados en la migración o con `php artisan scout:sync-index-settings`

**Criterio de terminado:**
- `Negocio::search('cafe')->get()` retorna resultados relevantes en tinker

---

### Paso 23 — Buscador en el sitio público

**Objetivo:** Tener el campo de búsqueda del header y la home funcionando.

**Resultado esperado:**
- Form GET con campo `q` apunta a `/negocios?q=texto`
- `NegocioController@index` usa `Negocio::search($q)` si `q` está presente, sino `Negocio::query()`
- Los filtros de categoría/zona se combinan con la búsqueda

**Criterio de terminado:**
- Buscar "café" muestra negocios relevantes
- La búsqueda se combina con filtros de categoría/zona
- Si no hay resultados, se muestra un mensaje claro

---

## Bloque 6 — SEO básico

---

### Paso 24 — Meta tags dinámicos

**Objetivo:** Tener title y description únicos por página para SEO.

**Resultado esperado:**
- Stack en `layouts/app.blade.php` para `@stack('meta')`
- Cada vista pushea su propio `<title>` y `<meta name="description">`
- Páginas con entidad dinámica (negocio, categoría) usan datos del modelo

**Criterio de terminado:**
- Cada página tiene un `<title>` único y descriptivo
- La ficha de negocio tiene description con los primeros 160 chars de su descripción

---

### Paso 25 — Sitemap XML

**Objetivo:** Tener un sitemap para que los buscadores indexen el sitio.

**Resultado esperado:**
- Ruta `GET /sitemap.xml` que genera XML con:
  - Páginas estáticas (home, contacto, about)
  - Una URL por negocio activo
  - Una URL por categoría
  - Una URL por zona

**Criterio de terminado:**
- `/sitemap.xml` responde con XML válido
- Las URLs de los negocios están en el sitemap
- El XML se puede validar en un validador online

---

## Bloque 7 — Calidad y cierre MVP

---

### Paso 26 — Páginas de error (404 y 500)

**Objetivo:** Tener páginas de error con el diseño del sitio.

**Resultado esperado:**
- `resources/views/errors/404.blade.php` con layout y mensaje amigable
- `resources/views/errors/500.blade.php` similar

**Criterio de terminado:**
- Una URL inexistente muestra la página 404 con nav y footer
- El código HTTP de respuesta es 404

---

### Paso 27 — Optimización básica para producción

**Objetivo:** Tener el sitio preparado para un primer deploy.

**Resultado esperado:**
- `php artisan config:cache` y `route:cache` corren sin errores
- `npm run build` genera assets optimizados
- `.env` de producción tiene `APP_DEBUG=false` y `APP_ENV=production`
- Storage link creado: `php artisan storage:link`

**Criterio de terminado:**
- El sitio funciona con cache de config y rutas activo
- Las imágenes de negocios son accesibles desde el browser
- No hay errores en el log de Laravel

---

## Notas

- Los pasos de **Etapa 2 en adelante** (Livewire, mapas, SEO avanzado, editorial, comercial) se agregarán a este archivo cuando comience cada etapa.
- Cada paso puede ejecutarse en una sola sesión de trabajo.
- El orden dentro de cada bloque es el recomendado, pero pasos de distintos bloques pueden hacerse en paralelo si el contexto lo permite.
