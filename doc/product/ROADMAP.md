# Roadmap del Proyecto

Evolución planificada por etapas. Cada etapa es funcional y desplegable de forma independiente.

---

## Etapa 1 — Directorio base (MVP) ✅

**Stack activo:** Laravel 12 · Filament v3 · Blade · Tailwind CSS v4 · Alpine.js · MariaDB

| Feature | Estado | Detalle técnico |
|---|---|---|
| Modelo de datos base | ✅ | Migraciones: `negocios`, `categorias`, `zonas`, `consultas` |
| Panel admin | ✅ | Filament v3: Negocio, Categoría, Zona, Consulta |
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

## Etapa 2 — Mejora de descubrimiento

**Stack adicional:** Livewire 3 · Meilisearch (opcional)

| Feature | Detalle técnico |
|---|---|
| Filtros dinámicos (sin reload) | Livewire components para filtros en tiempo real |
| SEO estructurado | JSON-LD por negocio (schema.org LocalBusiness) |
| Búsqueda mejorada | Migrar Scout driver a Meilisearch si el volumen lo justifica |
| URLs canónicas / redirects | Redirects 301 para slugs cambiados |

---

## Etapa 3 — Capa editorial

**Stack adicional:** Filament Forms (campos rich text) · TipTap o Quill

| Feature | Detalle técnico |
|---|---|
| Artículos/posts | Modelo `Article`, Resource en Filament |
| Guías temáticas | Modelo `Guide` con relación a categorías y negocios |
| Secciones destacadas | Tabla `featured_slots` con posición y prioridad |
| Tags editoriales | Relación polimórfica con `tags` |
| RSS feed | Ruta `/feed` con respuesta XML |

---

## Etapa 4 — Capa comercial

**Stack adicional:** MercadoPago SDK · Laravel Cashier (opcional)

| Feature | Detalle técnico |
|---|---|
| Promociones | Modelo `Promocion` con fechas vigencia y relación a negocio |
| Fichas premium | Campo `plan` en `negocios`: `free`, `basic`, `premium` |
| Slots destacados | Lógica de posicionamiento por plan en home y listados |
| Pagos | MercadoPago Checkout Pro o Laravel Cashier + Stripe |

---

## Etapa 5 — Plataforma completa

**Stack adicional:** Laravel Breeze (auth front) · Laravel Policies · Laravel Telescope

| Feature | Detalle técnico |
|---|---|
| Login para negocios | Laravel Breeze, guard `business` separado |
| Panel propio por negocio | Vistas Blade auth-gated, edición de ficha propia |
| Analytics de visitas | Tabla `page_views`, eventos con Jobs en cola |
| Reseñas | Modelo `Review` con moderación desde Filament |
| Favoritos | Tabla pivot `user_favoritos` |
| Notificaciones | Laravel Notifications (mail + database) |
| Suscripciones | Laravel Cashier, planes recurrentes |

---

## Ideas futuras

- Sistema de eventos locales
- Newsletter local (Mailcoach o integración simple)
- Aplicación mobile (Laravel API + React Native / Flutter)
- Integración WhatsApp Business API
- Estadísticas avanzadas por negocio (dashboard propio)
