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

---

## Sectores (feature transversal) ✅

Agrupa las categorías nivel 1 en 3 verticales temáticos que estructuran toda la guía.

### Modelo Sector
Tabla `sectores` con `nombre`, `slug`, `descripcion`, `nombre_corto`, `color_classes` (JSON con clases Tailwind literales por paleta). Helper `color($key, $default)` para acceder a clases desde Blade. 3 sectores creados: **Comercial** (amber), **Gastronomía y Ocio** (rose), **Turismo y Alojamiento** (sky).

### FK sector_id en categorias
Solo categorías de nivel 1 tienen `sector_id`. Las de nivel 2 heredan el sector del padre. Selector de sector en `CategoriaResource`.

### Admin /admin/sectors
`SectorResource` en Filament (grupo Directorio). Permite crear/editar sectores con nombre, slug auto, descripcion, nombre_corto y color_classes JSON.

### Público /sectores/{slug}
Micrositio por sector: hero con tint del color del sector, stats (negocios + categorías), grid de categorías del sector, sección de destacados (top 6 por `featured_score`). `SectorController@show`.

### Home agrupada por sector
Tarjetas "Explorar la guía" entre hero y destacados. Tabs Alpine.js en la sección de destacados: Todos / Comercial / Gastronomía / Turismo. Partial `_ficha_card.blade.php` reutilizable.

### Navbar y footer con sectores
Navbar reemplaza "Negocios" y "Categorías" por los 3 sectores (Comercial, Gastronomía, Turismo) con active state. Footer con links a sectores via view composer `$sectoresNav` en `AppServiceProvider`.

---

## Etapa 7 — Eventos locales ✅

### Eventos locales
Modelo `Evento` (titulo, slug, descripcion, fecha_inicio, fecha_fin, hora_inicio, hora_fin, lugar_id, publicado). Tabla `eventos` con índice `(publicado, fecha_inicio)`. `scopePublicado` + `scopeProximo`. Media collection `portada` con conversión WebP.

`EventoResource` en Filament con tabs: Contenido, Fecha y hora, Imagen, Relaciones, Configuración. Tabla con imagen circular, fechas y lugar. Grupo Contenido.

Páginas públicas: `/eventos` (listado paginado con badges "Hoy"/"Esta semana") y `/eventos/{slug}` (ficha con sidebar cuándo/dónde).

Módulo en home: sección "Eventos próximos" con 3 cards. Oculta si no hay eventos próximos. Reemplazó la sección mapa en home (el mapa sigue en `/mapa`).

---

## Verificación de propietarios ✅

### Reclamar negocio
Formulario público en `/negocios/{slug}/reclamar`. Botón "Reclamalo y gestionalo" en la ficha (solo si sin propietario asignado). El propietario sube su constancia de RUT (imagen/PDF). Admin aprueba o rechaza desde Filament.

Al aprobar: crea cuenta de usuario, vincula la ficha (`user_id`), marca `verified_at`, envía email con credenciales al propietario.

### Badge verificado
Componente `<x-verified-badge>` con SVG check en círculo amber. Se muestra en `negocios/show.blade.php` y en `_ficha_card.blade.php` cuando `ficha.verified_at != null` y `ficha.user_id != null`. Sin propietario asignado, el badge no se muestra aunque exista `verified_at`.

### Emails de aprobación/rechazo
Mailables `ClaimApproved` y `ClaimRejected`. Templates con tablas HTML, paneles destacados y mejor copy. Tema amber (`#d97706`) en botones, panel y header. Firma "El equipo de Guía Local".

### Limpieza automática
Comando `claim:cleanup` elimina las constancias de reclamos rechazados con más de 90 días.

---

## Mejoras transversales ✅

### Página /precios
Página pública estática con los 3 planes del directorio comparados. `PageController@precios` → vista `pages/precios.blade.php`. 3 cards: Gratuito (gratis), Básico (consultar), Premium (consultar). Tabla de features con ✓/✗ por plan. Sección "¿Tenés dudas?" con CTA a `/contacto?asunto=consulta-planes`. Los botones de CTA pre-llenan el asunto: `alta-negocio`, `upgrade-basico`, `upgrade-premium`.

### Formulario de contacto mejorado
Campo `asunto` nullable en tabla `consultas` (migración `add_asunto_to_consultas`). El asunto llega como query param `?asunto=` desde /precios u otras páginas y se mapea a un label legible en Blade. Viaja como hidden input POST. El controller lo valida como `nullable|string`. `NuevaConsulta` mailable: subject incluye el asunto si está presente (`$consulta->asunto ?? 'Nueva consulta recibida'`).

### Email de confirmación al usuario
`ConsultaRecibida` mailable enviado al email del remitente al guardar la consulta. Incluye resumen del mensaje enviado y botón a `/precios`. Tema amber.

### Watermark en imágenes genéricas
Overlay "imagen ilustrativa" superpuesto en imágenes de categoría usadas como fallback de portada. Visible en `negocios/show.blade.php` y en `_ficha_card.blade.php`. Detección: `$portadaUrl` existe (la categoría tiene imagen genérica) pero `getFirstMediaUrl('portada')` está vacío (la ficha no tiene portada propia).

### Dashboard panel: horarios agrupados
En el dashboard del panel propietario (`/panel`), el horario semanal muestra rangos de días consecutivos con el mismo horario en lugar de filas individuales. Ejemplo: "Lun – Vie 09:00 – 18:00" en lugar de 5 filas. Layout adaptativo: horario + contacto + descripción en grid de 2 columnas desktop, 1 columna mobile.

---

## Ideas futuras / pendiente

- **Comparativa vs categoría** — cruzar visitas propias con el promedio de fichas del mismo rubro (requiere más datos históricos)
- **Eventos locales escalables** — precio entrada, link tickets, categoría de evento
- **Favoritos** — requiere auth de usuario público (sistema separado a propietarios)
- **Meilisearch** — migración de Scout si el volumen lo justifica (interfaz Scout ya preparada)
- **Aplicación mobile** — consumo de API Laravel (requiere capa API REST)
