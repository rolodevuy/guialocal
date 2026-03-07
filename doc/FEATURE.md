# Features del Proyecto

Descripción de cada feature por etapa. Referencia: ver [ROADMAP](product/ROADMAP.md) para fechas y stack, [ARCHITECTURE](tech/ARCHITECTURE.md) para implementación técnica.

---

## Etapa 1 — MVP ✅

### Home con negocios destacados
Página principal que muestra una selección de negocios marcados como `featured`. Incluye acceso rápido a categorías principales y buscador.

### Listado de negocios
Página `/negocios` con todos los negocios activos paginados. Permite filtrar por categoría y zona mediante parámetros en la URL.

### Búsqueda básica
Campo de búsqueda por nombre, descripción y tags. Implementado con Laravel Scout + MySQL fulltext. Resultados en página de listado.

### Página detalle de negocio
Ruta `/negocios/{slug}`. Muestra toda la información del negocio: nombre, descripción, dirección, teléfono, email, sitio web, horarios, categoría, zona, logo y galería de imágenes.

### Galería de fotos
Cada negocio puede tener logo (singleFile), portada (singleFile) y múltiples imágenes en galería. Gestionadas con Spatie Media Library. El logo aparece en el sidebar de la ficha; la portada en el encabezado de la card.

### Categorías
Ruta `/categorias/{slug}`. Lista los negocios pertenecientes a una categoría, con pills de zona para filtrar. Solo aparecen las zonas que tienen negocios en esa categoría. Sistema jerárquico de hasta 3 niveles (familia → tipo → especialización) con `parent_id` self-referential. Ver [CATEGORIAS.md](product/CATEGORIAS.md).

### Zonas
Ruta `/zonas/{slug}`. Lista los negocios de una zona o barrio, con pills de categoría para filtrar. Solo aparecen las categorías que tienen negocios en esa zona.

### Mapa de negocios
Página `/mapa` con markers por negocio usando Leaflet.js. Filtros en cascada por zona y categoría. Los negocios tienen campos `lat` y `lng` cargables desde el admin con picker interactivo.

### Panel admin
Acceso en `/admin`. Permite crear, editar y eliminar negocios, categorías y zonas. Gestión de imágenes incluida (logo, portada, galería). Implementado con Filament v3.

### Consultas en admin
Sección `/admin/consultas` con badge de no-leídos en la navegación. Vista completa de cada consulta, toggle leído/no-leído desde la tabla. Solo lectura (sin crear ni editar desde admin).

### Página de contacto
Formulario con nombre, email y mensaje. Los envíos se guardan en la tabla `consultas` y se envía notificación por mail al administrador (`MAIL_ADMIN` en `.env`).

### Página "quiénes somos"
Página estática con información del proyecto/equipo. Muestra stats en tiempo real (negocios activos, categorías, zonas).

### SEO básico
Meta tags dinámicos, Open Graph, sitemap XML (`/sitemap.xml`) y URLs canónicas. Implementado con Spatie Laravel SEO.

---

## Etapa 2 — Mejora de descubrimiento

### Filtros dinámicos (sin reload)
Filtros por categoría, zona y otras propiedades reactivos sin recargar la página. Implementado con Livewire 3.

### SEO estructurado
Datos JSON-LD (schema.org LocalBusiness) por negocio.

### Búsqueda mejorada
Migrar Scout driver a Meilisearch si el volumen lo justifica *(actualmente MySQL fulltext)*.

### URLs canónicas / redirects
Redirects 301 automáticos para slugs cambiados.

---

## Etapa 3 — Capa editorial

### Artículos
Sección de artículos y notas editoriales. Modelo `Article` con título, cuerpo rich text, imagen de portada y relaciones opcionales a negocios o categorías.

### Guías temáticas
Contenido editorial agrupado por tema (ej: "Los mejores cafés de X barrio"). Relacionado a negocios y categorías.

### Secciones destacadas
Slots configurables en home y listados para mostrar contenido editorial o negocios con visibilidad especial. Gestionados desde el admin.

---

## Etapa 4 — Capa comercial

### Promociones
Los negocios pueden tener promociones vigentes con fecha de inicio/fin, descripción y imagen. Visibles en la ficha del negocio y en una sección especial.

### Planes de negocio
Los negocios tienen un plan (`free`, `basic`, `premium`) que determina su visibilidad, posición en listados y acceso a features premium.

### Fichas premium
Negocios con plan premium tienen mayor visibilidad en home, categorías y resultados de búsqueda.

---

## Etapa 5 — Plataforma completa

### Login para negocios
Los dueños de negocios pueden registrarse y acceder a un panel propio para gestionar su ficha, ver estadísticas y activar promociones.

### Reseñas
Los usuarios registrados pueden dejar reseñas con puntaje y comentario en cada negocio. Las reseñas se moderan desde el admin.

### Favoritos
Los usuarios pueden guardar negocios como favoritos y acceder a su lista personalizada.

### Analytics de visitas
Registro de visitas a fichas de negocios. Los dueños ven métricas básicas (vistas, clics en teléfono/web) desde su panel.

### Notificaciones
Notificaciones por mail y en sistema para eventos relevantes: nuevas reseñas, consultas, vencimiento de plan, etc.

---

## Ideas futuras

- **Eventos locales** — agenda de eventos con fecha, lugar y relación a negocios
- **Newsletter** — suscripción y envío de contenido editorial local
- **WhatsApp Business** — botón directo desde la ficha del negocio
- **Aplicación mobile** — consumo de API Laravel con app nativa
- **Estadísticas avanzadas** — dashboard completo para negocios premium
- **Otros negocios cerca