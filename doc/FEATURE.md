# Features del Proyecto

Descripción de cada feature por etapa. Referencia: ver [ROADMAP](product/ROADMAP.md) para fechas y stack, [ARCHITECTURE](tech/ARCHITECTURE.md) para implementación técnica.

---

## Etapa 1 — MVP ✅

### Home con negocios destacados
Página principal con carousel de fichas destacadas. Prioridad: 1) slots curados (`FeaturedSlot`), 2) algoritmo automático (top categorías por `popularidad_score`, rotación horaria entre empates), 3) fallback a fichas `featured=true`.

### Listado de negocios (Livewire)
Página `/negocios` con fichas activas paginadas (12 por página). Livewire 3: filtros y búsqueda sin reload. Estado sincronizado en URL (`#[Url]` attributes) para links compartibles.

### Búsqueda en tiempo real
Debounce 300ms. Busca en: nombre, descripción, dirección y nombre de categoría. `LIKE` MySQL es suficiente para el volumen actual.

### Barra de filtros y pills
Barra de pills encima de los resultados: zona (dropdown desktop / modal mobile) + "Abierto ahora" (pill con punto pulsante). Sidebar desktop: solo búsqueda y categorías. Mobile: FAB con bottom sheet que incluye búsqueda y categorías.

### Filtro "Abierto ahora"
Toggle que filtra según horario actual. Lógica en PHP sobre el JSON de `horarios`: soporta franjas por día/rango, horarios especiales por fecha con `se_repite`, cierre por fecha especial y cruce de medianoche. Timezone: `America/Montevideo`.

### Badge Abierto / Cerrado en cards
Badge verde pulsante "Abierto" o gris "Cerrado". Estado calculado en tiempo real en cada render.

### Página detalle de negocio
Ruta `/negocios/{slug}`. Información completa + horarios resaltando el día actual + galería + mapa + negocios similares.

### Negocios similares en ficha
Hasta 4 negocios de la misma categoría (incluye subcategorías/padre). Prioridad: misma zona primero. Ordenados por `featured_score`. Incluyen badge "Abierto" y link "Ver todos →".

### Galería de fotos
Spatie Media Library: logo (singleFile), portada (singleFile), galería (múltiple). Conversiones WebP (calidad 82). Fallback de portada: imagen genérica de categoría.

### Quick-actions en hero (home)
Cuatro accesos directos: Buscar, Ver mapa, Por categoría, Abierto ahora (punto verde pulsante → `/negocios?abiertos=1`).

### Detección de zona por GPS
Botón en el hero detecta coordenadas y encuentra la zona más cercana por distancia euclidiana sobre centroides. Fallback a picker manual.

### Zona preferida (cookie 30 días)
La zona elegida (GPS o picker) se persiste en cookie. Se respeta en home y como filtro inicial en `/negocios`.

### Categorías y Zonas
`/categorias/{slug}`, `/zonas/{slug}`. Jerarquía de categorías hasta 3 niveles (familia → tipo → especialización). Las zonas listan negocios con pills de categoría para filtrar.

### Mapa de negocios
`/mapa` con markers Leaflet.js. Filtros en cascada zona/categoría. Picker interactivo de lat/lng en admin (mapa Leaflet embebido en Filament).

### Panel admin (Filament)
Acceso en `/admin`. Resources: Lugar, Ficha, Categoria, Zona, Articulo, Guia, Promocion, FeaturedSlot, Consulta, Resena, User. Badge de no-leídos en Consultas y pendientes en Reseñas.

### Consultas y contacto
Formulario público → tabla `consultas` → mail al admin (`MAIL_ADMIN`). Admin ve consultas con toggle leído/no-leído.

### SEO
Meta tags dinámicos, Open Graph, sitemap XML (`/sitemap.xml`), RSS feed (`/feed`), JSON-LD `LocalBusiness` por ficha (tipo por categoría, horarios, geo, `sameAs`, `hasOfferCatalog` para promociones).

---

## Etapa 2 — Capa editorial ✅

### Artículos
Modelo `Articulo` con rich text (Filament TipTap), portada, publicación programada. Ruta `/articulos/{slug}`.

### Guías temáticas
Modelo `Guia` con intro, cuerpo rich text, portada y relación M:N a Lugar (con orden). Ruta `/guias/{slug}`.

### Slots editoriales (FeaturedSlot)
Curación manual para home y listados. Polimórfico (`slotable_type`, `slotable_id`). Soporta `valido_hasta`. Gestionado desde `/admin/featured-slots`.

### Sección "Del barrio" en home
Grid de hasta 3 artículos o guías desde `FeaturedSlot(home_editorial)`. Solo aparece si hay slots activos.

---

## Etapa 3 — Capa comercial ✅

### Planes de negocio (soft gating)
Tres planes: `gratuito`, `basico`, `premium`. El `featured_score` se calcula automáticamente al guardar (premium=50, básico=20, gratuito=0, +30 si `featured=true`). Los límites por feature están en `Ficha::PLAN_LIMITS`. Ver [TIERS.md](business/TIERS.md).

### Fichas premium
Mayor `featured_score` garantiza aparición prioritaria en resultados y home.

### Promociones en ficha pública
Cards con acento lateral ámbar, imagen opcional, chip de fecha y badge "Sin vencimiento". Incluidas en JSON-LD `hasOfferCatalog`.

### CTA plan-aware en sidebar de ficha
- Premium → badge "Plan Premium activo" (sin CTA)
- Básico → nudge con 3 beneficios + botón "Quiero el plan Premium" (pre-llena contacto)
- Gratuito → botón "Contactanos para gestionarlo"

---

## Etapa 4 — Autogestión y comunidad ✅

### Analytics de visitas
Campo `visitas` (int) en `fichas`. Se incrementa en `NegocioController@show`. Visible en el panel del dueño (solo plan Básico+) y en la tabla de Filament con badge por rangos (gris < 10, info 100+, success 500+).

### Redirects 301 automáticos
`LugarObserver::updating()`: si el `slug` cambia, guarda el anterior en `slug_redirects`. `NegocioController@show` busca en esa tabla si no encuentra el slug y redirige con 301.

### WhatsApp flotante
Botón flotante verde en la ficha pública si `$ficha->planIncluye('whatsapp')` (Básico+) y el negocio tiene URL de WhatsApp en `redes_sociales`. Abre en nueva pestaña.

### Reseñas con moderación
Modelo `Resena` con feature flag `FEATURE_RESENAS` (default `false`). Formulario Alpine con estrellas clicables. Toda reseña entra como `aprobada=false`. Filament: aprobar/rechazar individual + bulk, badge de pendientes en navegación, filtro Aprobadas/Pendientes/Todas.

### Panel de autogestión para dueños (`/panel`)
Login propio (guard `web`, middleware `PanelAuthenticate`). Dashboard con stats (visitas, promos, reseñas), sección "¿Qué incluye tu plan?" con ✅/❌ por feature, banner de upgrade específico por plan. Edición de descripción, teléfono, email, web, Instagram, Facebook, WhatsApp. Fuerza de contraseña mínima 8 chars. Fotos/horarios/plan/dirección: solo admin.

### Gestión de propietarios (Filament UserResource)
`/admin/users`: crear y editar cuentas de propietarios (nombre, email, contraseña). Toggle `is_admin`. Protección: no permite borrar el último admin. El admin asigna el user a una ficha desde `Fichas → tab Configuración → Propietario`.

---

## Etapa 5 — Insights y descubrimiento geográfico ✅

### Dashboard admin (Filament widgets)
Tres widgets auto-descubiertos en `/admin`:
- `StatsOverviewWidget`: fichas activas, plan Premium/Básico/Gratuito, visitas totales, consultas sin leer, reseñas pendientes (respeta feature flag).
- `TopFichasWidget`: tabla full-width con top 10 fichas por visitas (link a ficha pública, badge de plan).
- `ActividadPorZonaWidget`: tabla custom con barra de distribución; muestra fichas activas, Premium y Básico por zona, visitas y % del total.

### "Otros [categoría] cerca"
Sección en la ficha de detalle que muestra hasta 4 negocios de la **misma categoría raíz** (nivel 1), ordenados por distancia Haversine calculada en SQL sobre `lat/lng` de `lugares`. Solo aparece si el lugar tiene coordenadas. Badge de distancia en cada card (metros si < 1 km). Fallback a "Negocios similares" (por `featured_score`) si no hay lat/lng o no hay resultados.

### Newsletter local
- **Suscripción**: formulario en home con email + selector de zona (prellenado con cookie de zona preferida). Si el email ya existe, reactiva la suscripción y actualiza la zona.
- **Baja**: ruta `/newsletter/baja/{token}` con token UUID único por suscriptor. Vista de confirmación.
- **Admin**: `SuscriptorResource` en Filament bajo grupo "Comunicación"; badge con total de suscriptores activos; filtros por estado y zona; acción "Dar de baja" individual y bulk.
- **Mail de bienvenida**: `BienvenidaNewsletterMail` (Markdown); se envía automáticamente al suscribirse por primera vez; incluye zona, lista de beneficios y link de baja.
- **Mail periódico**: `NewsletterMail` en Markdown; subject con nombre de zona; contenido: nuevos negocios (últimos 7 días en la zona), promociones vigentes, último artículo publicado; link de baja al pie.
- **Despacho**: `php artisan newsletter:enviar [--zona=ID] [--dry-run]`; barra de progreso; solo envía si hay contenido; captura errores por destinatario sin detener el envío.

---

## Optimizaciones técnicas

- **N+1 eliminado**: `negocios_count` en HomeController con query batch; eager load `categoria.parent.parent` en NegociosIndex.
- **Cache**: categorías y zonas del sidebar cacheadas 5 minutos.
- **Timezone**: `America/Montevideo` en `config/app.php` — crítico para `isAbiertoAhora()`.
- **Throttle**: reseñas máx 5/min por IP; suscripción newsletter máx 3/min por IP.
- **Trust proxies**: configurado en `bootstrap/app.php` para funcionar detrás de ngrok/Cloudflare.
- **Script dev**: `start-dev.bat` levanta `artisan serve` (puerto 8090) + ngrok con un doble click.

---

## Etapa 6 — Insights y métricas ✅

### Dashboard de métricas Premium
Sección en `/panel` solo visible para fichas con plan Premium. Muestra un gráfico de barras CSS de los últimos 30 días de visitas. La barra de hoy se resalta en ámbar oscuro. Tooltip por día al hover. Resumen: total del período y mejor día. Para plan Básico, se muestra un teaser con CTA a Premium.

Implementación: tabla `ficha_visitas` (`ficha_id`, `fecha` DATE, `cantidad` INT UNSIGNED) con índice único compuesto. Se hace UPSERT en `NegocioController@show` por cada visita. `PanelController@index` rellena los 30 días con ceros para fechas sin registros.

---

## Ideas futuras / pendiente

- **Comparativa vs categoría** — cruzar visitas propias con el promedio de fichas del mismo rubro (requiere más datos históricos)
- **Eventos locales** — agenda con fecha, lugar, relación a negocios; requiere definición de scope
- **Favoritos** — requiere auth de usuario público (sistema separado a propietarios)
- **Meilisearch** — migración de Scout si el volumen lo justifica
- **Páginas de error personalizadas** — 404 con buscador, 500 temática
