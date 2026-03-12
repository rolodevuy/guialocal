# Optimización de Arquitectura y Rendimiento

**Fecha:** 2026-03-12
**Alcance:** Base de datos, backend, caching, frontend, seguridad

---

## 1. Índices de base de datos

**Migración:** `2026_03_12_065742_add_performance_indexes`

| Tabla | Índice | Razón |
|-------|--------|-------|
| `fichas` | `(activo, estado, featured_score)` | `scopeActivo()` + `orderByDesc('featured_score')` se usa en casi todas las queries públicas |
| `fichas` | `(user_id)` | Queries del panel de propietario |
| `lugares` | `(categoria_id, activo)` | Filtros de HomeController, NegociosIndex, y vistas de categoría |
| `lugares` | `(zona_id, activo)` | Filtros por zona en listados y home |
| `categorias` | `(parent_id, activo)` | Navegación jerárquica de categorías |
| `categorias` | `(sector_id, activo)` | Filtros de sector en home y navegación |
| `categorias` | `(popularidad_score)` | `orderByDesc('popularidad_score')` en HomeController |
| `articulos` | `(publicado, publicado_en)` | `scopePublicado()` + ordenamiento por fecha |
| `guias` | `(publicado, publicado_en)` | `scopePublicado()` + ordenamiento por fecha |
| `promociones` | `(ficha_id, activo)` | `scopeVigente()` en ficha show |

> Las FK creadas con `foreignId()->constrained()` ya generan índices simples automáticamente en InnoDB. Estos índices compuestos cubren las combinaciones WHERE+ORDER más frecuentes.

---

## 2. N+1 queries eliminadas

### HomeController — Slots destacados
**Antes:** Cada slot ejecutaba `$item->fichas()->with([...])->first()` dentro de un `->map()` → N queries (1 por slot).
**Después:** Se precargan relaciones agrupadas por tipo (`Lugar` vs `Ficha`) con un solo `->load()` por grupo.

### HomeController — Top categorías
**Antes:** Loop de 6 categorías, cada una ejecutando `Ficha::activo()->whereHas('lugar', ...)->get()` → 6 queries.
**Después:** Una sola query trae todas las fichas de las 6 categorías; se filtra en memoria con `->filter()`.

### NegocioController — Show
**Antes:** `Lugar::where('slug', $slug)->first()` sin eager loading → queries lazy para categoria, zona, parent.
**Después:** `Lugar::with(['categoria.parent.parent', 'zona'])->where('slug', $slug)->first()` carga toda la jerarquía en 1 query.

### NegocioController — Categoría raíz
**Antes:** `Categoria::find($categoriaRaizId)?->nombre` → query extra.
**Después:** Se resuelve desde la jerarquía ya cargada con `match` sobre IDs de parent.

### Ficha::saved() — Popularidad de categoría
**Antes:** 2 queries `whereHas` separadas (una para contar activos, otra para contar premium).
**Después:** 1 query con `JOIN + SUM condicional` que obtiene ambos conteos.

**Impacto estimado:**
- Homepage: ~80-120 queries → ~20-30
- Ficha show: ~15 queries → ~8

---

## 3. Caching

### View Composer (AppServiceProvider)
**Antes:** 3 queries en cada request (`hayArticulos`, `hayGuias`, `sectoresNav`).
**Después:** Cacheadas 1 hora con `Cache::remember()`. Son datos que cambian muy raramente.

### Backup password config
**Antes:** `Schema::hasTable('settings')` + query a settings en cada boot.
**Después:** Cacheado 1 hora.

### NegociosIndex (Livewire)
**Antes:** Cache de categorías y zonas con TTL de 5 minutos → rebuilds constantes.
**Después:** TTL de 1 hora (3600s).

---

## 4. Configuración (.env)

| Variable | Antes | Después | Razón |
|----------|-------|---------|-------|
| `CACHE_STORE` | `database` | `file` | Cada operación de cache golpeaba la BD, anulando el beneficio del cache |
| `SESSION_DRIVER` | `database` | `file` | Elimina I/O de sesiones en BD; para XAMPP local es más eficiente |

> En producción con múltiples servidores se recomienda Redis para ambos.

---

## 5. Comandos artisan optimizados

### RecalcularScores
**Antes:** `Ficha::all()` cargaba todos los modelos → N updates individuales → N×2 queries `whereHas` para categorías.
**Después:**
- 6 `UPDATE` bulk por combinación plan×featured (en vez de N updates)
- 1 query con `JOIN + GROUP BY + SUM condicional` para todas las categorías
- De ~2N+6 queries → ~8 queries fijas

### PurgeRejectedClaims
**Antes:** `->get()` cargaba todos los claims en memoria.
**Después:** `->chunkById(100)` procesa en lotes de 100. Escala sin OOM.

---

## 6. Frontend — .htaccess

### Compresión GZIP
```apache
AddOutputFilterByType DEFLATE text/html text/css application/javascript ...
```
Reduce el tamaño de transferencia ~60-70% para HTML, CSS, JS y SVG.

### Cache de assets estáticos
```apache
ExpiresByType image/* "access plus 1 year"
ExpiresByType text/css "access plus 1 year"
```
Vite agrega hash al nombre de CSS/JS, así que 1 año es seguro. HTML no se cachea.

### Cache-Control immutable
```apache
Header set Cache-Control "public, max-age=31536000, immutable"
```
Para CSS/JS versionados por Vite: el browser no revalida nunca.

### Security headers
- `X-Content-Type-Options: nosniff` — previene MIME sniffing
- `X-Frame-Options: SAMEORIGIN` — previene clickjacking
- `Referrer-Policy: strict-origin-when-cross-origin` — limita datos en referrer
- `Permissions-Policy` — restringe cámara, micrófono, geolocation

---

## 7. Lazy loading de imágenes

Se agregó `loading="lazy"` a todas las `<img>` que no lo tenían:

- `zonas/show.blade.php`
- `articulos/show.blade.php` (portada + imagen relacionada)
- `articulos/index.blade.php`
- `guias/show.blade.php` (portada + negocios)
- `guias/index.blade.php`
- `eventos/show.blade.php`
- `negocios/show.blade.php` (galería + logo)

> Las imágenes del home y `_ficha_card.blade.php` ya tenían `loading="lazy"`.

Esto difiere la carga de imágenes fuera del viewport, mejorando LCP y reduciendo datos iniciales.

---

## 8. FichaResource (Filament admin)

**Antes:** `->options(Lugar::orderBy('nombre')->pluck('nombre', 'id'))` cargaba TODOS los lugares al abrir el formulario.
**Después:** `->relationship('lugar', 'nombre')->searchable()->preload()` usa búsqueda paginada server-side. Escala sin importar cuántos lugares haya.

---

## Qué NO se cambió (y por qué)

- **`QUEUE_CONNECTION=sync`**: requiere infraestructura de workers (supervisor/systemd). No viable en XAMPP local.
- **`LOG_LEVEL=debug`**: es correcto para desarrollo local.
- **`APP_DEBUG=true`**: correcto en local; cambiar a `false` solo en producción.
- **Redis para cache/session**: requiere instalar Redis en Windows. File driver es adecuado para dev.
- **`srcset` en imágenes**: requiere generar múltiples conversiones por tamaño; se puede agregar después.
- **Inline styles**: los pocos que hay son dinámicos (porcentajes, colores de sector) y no justifican extracción.
