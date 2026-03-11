# Backlog del Proyecto

Lista de tareas del proyecto. Las tareas pueden cambiar de prioridad a medida que el proyecto evoluciona.
Referencia detallada de pasos: [build_steps.md](../build/build_steps.md)

---

## Alta prioridad (MVP) ✅ Completo

- [x] Crear estructura del proyecto, instalar paquetes base
- [x] Panel admin — Filament v3 (`/admin`)
- [x] Base de datos y modelos (Lugar, Ficha, Categoria, Zona, Consulta)
- [x] Seeders de datos de prueba
- [x] Resources Filament: Categoria, Zona, Lugar, Ficha, imágenes
- [x] Layout principal y rutas públicas
- [x] Página home con destacados, mapa, categorías, sección editorial
- [x] Listado de negocios con filtros (Livewire, sin reload)
- [x] Página detalle de negocio (`/negocios/{slug}`)
- [x] Página de categoría (`/categorias/{slug}`)
- [x] Página de zona (`/zonas/{slug}`)
- [x] Página de contacto con formulario (guarda en DB + mail al admin)
- [x] Página "quiénes somos" con stats en tiempo real

---

## Etapa 2 — Descubrimiento ✅ Completo

- [x] Filtros reactivos sin reload — Livewire 3 (zona, categoría, búsqueda)
- [x] Filtro "Abierto ahora" — filtra en PHP con lógica de horarios (franjas, días, horarios especiales, cruce de medianoche)
- [x] Buscador en tiempo real — debounce 300ms, busca nombre, descripción, dirección y categoría
- [x] Detección de zona por GPS con fallback a picker manual
- [x] Zona preferida persistida en cookie (30 días)
- [x] Mapa Leaflet en home y `/mapa` con filtro de zona en cascada
- [x] Diseño responsive completo (mobile-first, bottom sheet, FAB de filtros)
- [x] Badge "Abierto / Cerrado" en cards con estado en tiempo real
- [x] Carousel de destacados con touch/swipe en home
- [x] Lazy loading de imágenes en cards y carousel
- [x] Negocios similares en ficha de detalle (misma categoría, prioridad por zona)
- [x] Quick-action "Abierto ahora" en hero de home (4to acceso directo, punto pulsante)
- [x] Fix CTA sidebar en ficha: "¿Es tu negocio? Contactanos"
- [x] Rediseño UX filtros `/negocios`: barra de pills (zona + "Abierto ahora") encima de resultados; sidebar desktop solo con búsqueda y categorías; FAB mobile abre sheet con búsqueda y categorías

---

## Etapa 3 — Capa editorial ✅ Completo

- [x] Modelo Articulo con rich text, portada, publicación programada
- [x] Modelo Guia (guías temáticas) con relación M:N a Lugar
- [x] Slots destacados (`FeaturedSlot`) — curación manual desde admin para home y editorial
- [x] Sección "Del barrio" en home con artículos y guías destacados

---

## Etapa 4 — Capa comercial ✅ Completo

- [x] Modelo Promocion (ficha_id, fechas, imagen)
- [x] Planes de negocio (gratuito / básico / premium) con `featured_score` automático
- [x] Fichas premium con mayor visibilidad en listados y home
- [x] Visualización de promociones vigentes en ficha pública (card con acento lateral, imagen, fecha límite)
- [x] JSON-LD `LocalBusiness` por ficha (schema por categoría, horarios, geo, sameAs, `hasOfferCatalog` para promociones)
- [x] CTA de upgrade de plan en ficha pública (plan-aware: badge Premium / nudge Básico→Premium / CTA gratuito)

---

## Etapa 5 — Autogestión y comunidad ✅ Completo

- [x] Analytics de visitas — contador `visitas` en `fichas`, incremento en cada visita, columna en Filament con badge por rangos
- [x] Redirects 301 automáticos — `LugarObserver` guarda slug viejo en `slug_redirects`; controller redirige automáticamente
- [x] Botón WhatsApp flotante — verde, fijo en ficha pública, solo plan Básico+, usa URL de `redes_sociales`
- [x] Reseñas con moderación — feature flag `FEATURE_RESENAS` (default off), modelo+migración, Filament con aprobar/rechazar/bulk, badge de pendientes, formulario público con estrellas Alpine, throttle 5/min
- [x] Panel de autogestión para dueños (`/panel`) — login propio, middleware `PanelAuthenticate`, dashboard con stats + plan features, edición de descripción/contacto/redes sociales
- [x] Soft gating de planes — `Ficha::PLAN_LIMITS`, `planIncluye()`, stats con candado en dashboard, sección de features por plan, banner de upgrade específico por tier, WhatsApp gateado en ficha pública
- [x] Gestión de propietarios — `UserResource` en Filament (crear/editar usuarios del panel), `is_admin` en `users`, `canAccessPanel()` en User model, migración con upgrade automático de usuarios existentes
- [x] Separación admin/propietario — mismo guard `web`, mismo modelo `User`, separados por `is_admin`; propietarios solo acceden a `/panel`, admins solo a `/admin`

---

## Optimizaciones técnicas ✅ Hecho

- [x] Fix N+1 en HomeController: `negocios_count` con una sola query batch
- [x] Fix N+1 en NegociosIndex: eager load `categoria.parent.parent` para accessor `raiz`
- [x] Cache 5 min en categorías y zonas del sidebar (NegociosIndex)
- [x] Búsqueda extendida: incluye nombre de categoría
- [x] Timezone configurado: `America/Montevideo` (fix para `isAbiertoAhora()`)
- [x] Script `start-dev.bat` — levanta artisan serve (puerto 8090) + ngrok de un solo click
- [x] Trust proxies configurado en `bootstrap/app.php` (ngrok, Cloudflare, proxies en general)

---

## Documentación ✅ Actualizada

- [x] `ARCHITECTURE.md` — stack, capas, estructura de directorios, rutas, auth, feature flags
- [x] `DATABASE.md` — esquema completo y actualizado (lugares, fichas, resenas, users, slug_redirects, etc.)
- [x] `TIERS.md` — scope de cada plan, featured_score, PLAN_LIMITS, gating, upgrade flow
- [x] `FEATURE.md` — features por etapa incluyendo Etapa 4 y 5 completas
- [x] `BACKLOG.md` — este archivo

---

## Etapa 6 — Insights y descubrimiento geográfico (próxima)

Orden de implementación acordado:

- [x] **Dashboard admin** — widgets en Filament: fichas por plan, visitas totales del mes, top 10 fichas más visitadas, consultas y reseñas pendientes, actividad por zona
- [x] **"Otros [categoría] cerca"** — sección en ficha de detalle; misma categoría raíz + orden por distancia Haversine sobre lat/lng de `lugares`; NO mezcla categorías no relacionadas; fallback a similares si no hay lat/lng
- [x] **Newsletter local** — tabla `suscriptores` (email, zona_id, token_baja); formulario en home (prellenado con zona de cookie); reactivación si ya existe; baja por token; `SuscriptorResource` en Filament (grupo "Comunicación", badge con total activos); `NewsletterMail` markdown; `newsletter:enviar` con `--zona` y `--dry-run`
- [x] **Dashboard de métricas Premium** — tabla `ficha_visitas` (ficha_id, fecha, cantidad); UPSERT diario en `NegocioController@show`; gráfico de barras CSS en `/panel` solo para Premium; teaser con CTA para plan Básico

---

## Etapa 7 — Eventos locales ✅ Completo

- [x] **Eventos locales** — modelo `Evento` (titulo, slug, descripcion, fecha_inicio, fecha_fin, hora_inicio, hora_fin, lugar_id, publicado); tabla `eventos` con índice `(publicado, fecha_inicio)`; `scopePublicado` + `scopeProximo`; media collection `portada` con conversión webp
- [x] **EventoResource Filament** — tabs: Contenido, Fecha y hora, Imagen, Relaciones, Configuración; tabla con imagen circular, fechas y lugar; grupo Contenido, ícono calendario
- [x] **Páginas públicas** — `/eventos` (listado paginado con badges "Hoy"/"Esta semana") y `/eventos/{slug}` (ficha con sidebar de cuándo/dónde)
- [x] **Módulo en home** — reemplazó sección mapa por "Eventos próximos" (3 cards, se oculta si no hay eventos); se eliminó Leaflet del home (queda disponible en `/mapa`)

---

## Sectores (transversal) ✅ Completo

- [x] Modelo `Sector` con slug, nombre, descripcion, nombre_corto, `color_classes` JSON
- [x] Tabla `sectores` con 3 verticales: Comercial, Gastronomía y Ocio, Turismo y Alojamiento
- [x] FK `sector_id` en `categorias` (solo nivel 1; nivel 2 hereda del padre)
- [x] Admin `/admin/sectors` — SectorResource; selector sector en CategoriaResource
- [x] Público `/sectores/{slug}` — micrositio con hero coloreado, stats, categorías, destacados
- [x] Home agrupada por sector — tarjetas "Explorar la guía" + tabs de destacados por sector
- [x] Navbar con sectores directos (Comercial / Gastronomía / Turismo)
- [x] Footer con links a sectores via view composer `$sectoresNav`

---

## Verificación de propietarios ✅ Completo

- [x] **Reclamar negocio** — formulario `/negocios/{slug}/reclamar` con upload constancia RUT
- [x] **ClaimRequestResource** — Filament con acciones Aprobar/Rechazar, badge pendientes
- [x] **Badge verificado** — componente `<x-verified-badge>` SVG check amber; requiere `user_id` + `verified_at`
- [x] **Campo verified_at** — timestamp nullable en `fichas`
- [x] **Emails claim** — templates mejorados con tablas HTML, paneles, tema amber
- [x] **Limpieza constancias rechazadas** — comando `claim:cleanup` elimina >90 días

---

## Mejoras transversales ✅ Completo

- [x] **Página /precios** — 3 planes comparados con ✓/✗ por feature; CTAs pre-llenan asunto
- [x] **Campo asunto en consultas** — nullable, mapeo a label, en subject del email admin
- [x] **Email confirmación usuario** — `ConsultaRecibida` mailable con botón a /precios
- [x] **Tema emails amber** — color #d97706, firma "El equipo de Guía Local"
- [x] **Watermark imagen ilustrativa** — overlay en portadas de categoría usadas como fallback
- [x] **Dashboard panel: horarios agrupados** — "Lun – Vie 09:00 – 18:00" en lugar de filas individuales
- [x] **Backups automáticos** — spatie/laravel-backup, panel admin, schedule configurable desde admin
- [x] **Importador OSM** — OverpassService + página Filament; modo localidad + modo radio; detección de duplicados
- [x] **Optimización imágenes** — conversión WebP resize automático en todos los modelos con media

---

## Documentación ✅ Actualizada

- [x] `build_steps.md` — pasos hasta Paso 67 (sectores, /precios, asunto contacto, watermark, horarios agrupados)
- [x] `ROADMAP.md` — etapas 5-7 + sectores + mejoras transversales
- [x] `FEATURE.md` — sectores, eventos, verificación, mejoras transversales
- [x] `DATABASE.md` — tabla sectores, campo asunto en consultas, verified_at en fichas, claim_requests planificada
- [x] `DECISIONS.md` — decisiones técnicas y de producto documentadas
- [x] `BACKLOG.md` — este archivo

---

## Pendiente / Ideas futuras (sin fecha)

- [ ] Eventos locales — escalables: precio entrada, link tickets, categoría de evento *(scope acordado, ampliar cuando haya volumen)*
- [ ] Favoritos — requiere sistema de auth para usuarios públicos (distinto a propietarios) *(dejar para el final, utilidad a validar)*
- [ ] Meilisearch — migración de Scout si el volumen lo justifica (interfaz Scout ya preparada)
- [x] Páginas de error personalizadas — 404 con buscador integrado (form → `/negocios?busqueda=`), links alternativos, 500 standalone con CSS inline (no depende de Blade/Tailwind)
- [ ] Aplicación mobile — consumo de API Laravel (requiere añadir capa API REST)
- [ ] Deploy a producción *(postergado, sin fecha)*
