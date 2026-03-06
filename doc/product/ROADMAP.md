# Roadmap del Proyecto

Evolución planificada por etapas. Cada etapa es funcional y desplegable de forma independiente.

---

## Etapa 1 — Directorio base (MVP)

**Stack activo:** Laravel 11 · Filament v3 · Blade · Tailwind CSS · MySQL

| Feature | Detalle técnico |
|---|---|
| Modelo de datos base | Migraciones: `negocios`, `categorias`, `zonas`, `imagenes` |
| Panel admin | Filament v3: Resources para Negocio, Categoría, Zona |
| Carga de imágenes | Spatie Media Library |
| Página home | Blade + negocios destacados (scope `featured`) |
| Listado de negocios | Paginación Laravel, filtro por categoría y zona |
| Página detalle | Ruta `/negocios/{slug}` con SEO meta básico |
| Búsqueda básica | Laravel Scout + MySQL fulltext driver |
| Página contacto | Formulario → tabla `consultas`, mail con Mailable |
| Página "quiénes somos" | Blade estático |
| Diseño responsive | Tailwind CSS + Alpine.js para interacciones mínimas |

---

## Etapa 2 — Mejora de descubrimiento

**Stack adicional:** Livewire 3 · Leaflet.js · Meilisearch (opcional)

| Feature | Detalle técnico |
|---|---|
| Filtros dinámicos | Livewire components sin reload de página |
| Mapa de negocios | Leaflet.js con coordenadas lat/lng en tabla `negocios` |
| Galería de fotos | Spatie Media Library multi-colección |
| SEO avanzado | Spatie Laravel SEO: sitemap, Open Graph, JSON-LD |
| Búsqueda mejorada | Migrar Scout driver a Meilisearch si el volumen lo justifica |
| URLs canónicas | Slugs únicos, redirects 301 para SEO |

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
