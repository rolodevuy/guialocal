# Roadmap del Proyecto

Evolución planificada por etapas. Cada etapa es funcional y desplegable de forma independiente.

---

## Etapa 1 — Directorio base (MVP) ✅

**Stack activo:** Laravel 12 · Filament v3 · Blade · Tailwind CSS v4 · Alpine.js · MariaDB

| Feature | Estado | Detalle técnico |
|---|---|---|
| Modelo de datos base | ✅ | Migraciones: `lugares`, `fichas`, `categorias` (jerárquica, 3 niveles), `zonas`, `consultas` |
| Panel admin | ✅ | Filament v3: Lugar, Ficha, Categoría (jerarquía), Zona, Consulta |
| Carga de imágenes | ✅ | Spatie Media Library: logo, portada, galería |
| Página home | ✅ | Hero + quick-actions overlap + destacados + mapa + categorías |
| Listado de negocios | ✅ | Paginación, filtro por categoría y zona |
| Página detalle | ✅ | `/negocios/{slug}` con logo, galería, sidebar contacto |
| Búsqueda básica | ✅ | Laravel Scout + MySQL fulltext driver |
| Página contacto | ✅ | Formulario → `consultas` + email a admin (Mailable) |
| Consultas en admin | ✅ | ConsultaResource con badge no-leídos, toggle leído |
| Página "quiénes somos" | ✅ | Blade estático con stats en tiempo real |
| Diseño responsive | ✅ | Tailwind CSS v4 + Alpine.js |
| Páginas de error | ✅ | 404 y 500 con diseño del sitio |
| SEO básico | ✅ | Meta tags, Open Graph, sitemap XML, canonical |
| Mapa de negocios | ✅ | Leaflet.js + página /mapa con filtros en cascada |
| Mapa en admin | ✅ | Picker lat/lng clickeable en NegocioResource |
| Página de zona | ✅ | `/zonas/{slug}` con filtro por categoría |
| Página de categoría | ✅ | `/categorias/{slug}` con filtro por zona |
| Componente cat-icon | ✅ | `<x-cat-icon>` SVG inline por categoría |

---

## Etapa 2 — Mejora de descubrimiento ✅

**Stack adicional:** Livewire 3

| Feature | Estado | Detalle técnico |
|---|---|---|
| Filtros dinámicos (sin reload) | ✅ | Livewire 3: `NegociosIndex` con `#[Url]`, debounce 300ms |
| Filtro "Abierto ahora" | ✅ | Lógica en PHP sobre JSON horarios, timezone Montevideo |
| Badge Abierto/Cerrado en cards | ✅ | Estado calculado en tiempo real en cada render |
| Detección GPS de zona | ✅ | Coordenadas + centroide más cercano, fallback picker manual |
| Zona preferida en cookie | ✅ | Cookie 30 días, pre-aplicada en `/negocios` y home |
| Negocios similares en ficha | ✅ | Misma categoría raíz, prioridad zona, ordered by featured_score |
| Mapa Leaflet | ✅ | `/mapa` con filtros en cascada + lista de negocios visibles en viewport |
| Diseño responsive completo | ✅ | FAB mobile + bottom sheet, carousel con swipe |

---

## Etapa 3 — Capa editorial ✅

**Stack adicional:** Filament Forms (RichEditor nativo)

| Feature | Estado | Detalle técnico |
|---|---|---|
| Artículos | ✅ | Modelo `Articulo`, RichEditor, portada, publicación programada |
| Guías temáticas | ✅ | Modelo `Guia`, relación M:N a Lugar (con orden) |
| Secciones destacadas | ✅ | `FeaturedSlot` polimórfico para home y editorial |
| Sección "Del barrio" en home | ✅ | Grid de artículos/guías desde `FeaturedSlot(home_editorial)` |
| RSS feed | ✅ | `/feed` con RSS 2.0, enclosure de imagen, CDATA |

---

## Etapa 4 — Capa comercial ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Promociones | ✅ | Modelo `Promocion` con fechas, imagen, scope `vigente()` |
| Fichas premium | ✅ | Campo `plan` enum + `featured_score` calculado automáticamente |
| Slots destacados | ✅ | Posicionamiento por `featured_score` en home y listados |
| CTA plan-aware en sidebar | ✅ | Badge Premium / nudge Básico→Premium / CTA gratuito |
| JSON-LD LocalBusiness | ✅ | Schema.org por ficha: tipo por categoría, horarios, geo, sameAs, hasOfferCatalog |

---

## Etapa 5 — Autogestión de negocios ✅

**Stack adicional:** Laravel Auth (guard web) · Middleware PanelAuthenticate

| Feature | Estado | Detalle técnico |
|---|---|---|
| Login/registro para propietarios | ✅ | Guard `web`, mismo modelo `User`, separado por `is_admin` |
| Panel propietario `/panel` | ✅ | Dashboard con stats, sección plan features, banner upgrade |
| Edición de ficha propia | ✅ | Descripción, contacto, redes sociales |
| Gestión de horarios en panel | ✅ | Editor semanal Alpine.js + días especiales; compatible con `isAbiertoAhora()` |
| Gestión de propietarios (admin) | ✅ | `UserResource` en Filament: crear/editar, toggle is_admin, protección último admin |
| Fichas vinculadas a users | ✅ | `fichas.user_id` FK a `users` |
| Soft gating de planes | ✅ | `Ficha::PLAN_LIMITS`, `planIncluye()`, stats con candado, WhatsApp gateado |
| Reseñas con moderación | ✅ | Modelo `Resena`, feature flag, formulario Alpine, bulk approve en Filament |

---

## Etapa 6 — Insights y métricas ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Dashboard admin (Filament widgets) | ✅ | StatsOverview, TopFichas, ActividadPorZona |
| "Otros cerca" en ficha | ✅ | Distancia Haversine en SQL sobre lat/lng; fallback a similares |
| Newsletter local | ✅ | Suscriptores, baja por token, `SuscriptorResource`, comando `newsletter:enviar` |
| Métricas Premium en panel | ✅ | Tabla `ficha_visitas`, UPSERT diario, gráfico barras CSS 30 días; teaser Básico |

---

## Sectores (transversal, implementado en paralelo) ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Modelo Sector | ✅ | nombre, slug, descripcion, nombre_corto, `color_classes` JSON con clases Tailwind |
| 3 verticales | ✅ | Comercial (amber), Gastronomía y Ocio (rose), Turismo y Alojamiento (sky) |
| FK sector_id en categorias | ✅ | Solo nivel 1; nivel 2 hereda del padre |
| Admin /admin/sectors | ✅ | SectorResource + selector sector en CategoriaResource |
| Público /sectores/{slug} | ✅ | Micrositio con hero coloreado, stats, categorías, destacados por sector |
| Home agrupada por sector | ✅ | Tabs destacados por sector; tarjetas "Explorar la guía" |
| Navbar con sectores | ✅ | Comercial / Gastronomía / Turismo como items directos del nav |
| Footer con links a sectores | ✅ | View composer `$sectoresNav` |

---

## Etapa 7 — Eventos locales ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Modelo Evento | ✅ | titulo, slug, descripcion, fecha_inicio, fecha_fin, hora_inicio, hora_fin, lugar_id, publicado |
| EventoResource Filament | ✅ | Tabs: Contenido, Fecha y hora, Imagen, Relaciones, Configuración |
| Páginas públicas | ✅ | `/eventos` (listado con badges "Hoy"/"Esta semana") y `/eventos/{slug}` |
| Módulo en home | ✅ | "Eventos próximos" reemplazó sección mapa en home |

---

## Verificación de propietarios ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Formulario reclamo | ✅ | `/negocios/{slug}/reclamar` con upload de constancia RUT |
| ClaimRequestResource | ✅ | Filament con acciones Aprobar/Rechazar, badge pendientes |
| Badge verificado | ✅ | Componente `<x-verified-badge>` con SVG check en círculo amber |
| Campo verified_at en fichas | ✅ | Timestamp nullable; badge solo si `user_id` + `verified_at` |
| Emails aprobación/rechazo | ✅ | Mailables con templates mejorados (tablas HTML, tema amber) |
| Limpieza constancias rechazadas | ✅ | Comando `claim:cleanup`, elimina constancias > 90 días |

---

## Mejoras transversales ✅

| Feature | Estado | Detalle técnico |
|---|---|---|
| Página /precios | ✅ | 3 planes comparados; CTAs pre-llenan asunto en contacto |
| Campo asunto en consultas | ✅ | Nullable, mapeo a label legible, incluido en subject del email |
| Email confirmación al usuario | ✅ | `ConsultaRecibida` mailable con botón a /precios |
| Tema emails amber | ✅ | Color #d97706 en botones, panel y header; firma "El equipo de Guía Local" |
| Watermark imagen ilustrativa | ✅ | Overlay en portadas de categoría usadas como fallback |
| Dashboard panel: horarios agrupados | ✅ | Días consecutivos con mismo horario agrupados: "Lun – Vie 09:00 – 18:00" |
| Backups automáticos | ✅ | spatie/laravel-backup, panel admin, schedule configurable |
| Importador OSM | ✅ | OverpassService + página Filament; modo localidad + modo radio |
| Optimización imágenes | ✅ | Conversión WebP resize automático en todos los modelos con media |

---

## Ideas futuras (sin fecha)

- Eventos locales escalables: precio entrada, link tickets, categoría de evento
- Favoritos — requiere auth de usuario público (distinto a propietarios)
- Meilisearch — migración de Scout si el volumen lo justifica
- Aplicación mobile — consumo de API Laravel (requiere capa API REST)
- Deploy a producción
