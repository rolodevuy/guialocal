# Base de Datos

Esquema de la base de datos del proyecto. Motor: MariaDB 10.x vía XAMPP.

---

## Tablas activas

| Tabla | Descripción |
|---|---|
| `users` | Usuarios del sistema (admins + propietarios de negocios) |
| `lugares` | Lugar físico: nombre, slug, dirección, lat/lng, categoría, zona |
| `fichas` | Perfil gestionado de un negocio (1:1 con `lugares`) |
| `categorias` | Jerarquía de categorías hasta 3 niveles |
| `zonas` | Barrios/sectores geográficos con centroide |
| `promociones` | Ofertas/promos de una ficha, con fechas y estado |
| `resenas` | Reseñas de usuarios sobre fichas, con moderación |
| `articulos` | Contenido editorial (blog) |
| `guias` | Guías temáticas con relación M:N a lugares |
| `guia_lugar` | Pivot: guia ↔ lugar (con orden) |
| `featured_slots` | Slots curados para home y editorial (polimórfico) |
| `slug_redirects` | Mapeo old_slug → new_slug para redirects 301 |
| `consultas` | Mensajes del formulario de contacto |
| `suscriptores` | Suscriptores al newsletter (email, zona, token de baja) |
| `ficha_visitas` | Log diario de visitas por ficha (para métricas Premium) |
| `media` | Spatie Media Library (polimórfica, todos los modelos) |
| `sessions` / `cache` / `jobs` | Laravel default |

---

## Esquema detallado

### users

```sql
CREATE TABLE users (
  id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name             VARCHAR(255) NOT NULL,
  email            VARCHAR(255) UNIQUE NOT NULL,
  email_verified_at TIMESTAMP NULL,
  password         VARCHAR(255) NOT NULL,
  remember_token   VARCHAR(100) NULL,
  is_admin         TINYINT(1) DEFAULT 0,  -- true = acceso a /admin (Filament)
  created_at       TIMESTAMP NULL,
  updated_at       TIMESTAMP NULL
);
```

### lugares

```sql
CREATE TABLE lugares (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) UNIQUE NOT NULL,
  rut          VARCHAR(50) UNIQUE NULL,
  direccion    VARCHAR(255) NULL,
  lat          DECIMAL(10,7) NULL,
  lng          DECIMAL(10,7) NULL,
  activo       TINYINT(1) DEFAULT 1,
  categoria_id BIGINT UNSIGNED NOT NULL,
  zona_id      BIGINT UNSIGNED NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (zona_id)      REFERENCES zonas(id) ON DELETE SET NULL
);
```

### fichas

```sql
CREATE TABLE fichas (
  id                   BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  lugar_id             BIGINT UNSIGNED NOT NULL,
  user_id              BIGINT UNSIGNED NULL,         -- propietario con acceso a /panel
  descripcion          TEXT NULL,
  telefono             VARCHAR(50) NULL,
  email                VARCHAR(255) NULL,
  sitio_web            VARCHAR(255) NULL,
  redes_sociales       JSON NULL,                   -- [{red, url}, ...]
  horarios             JSON NULL,                   -- [{dia_inicio, dia_fin, apertura, cierre, cerrado}]
  horarios_especiales  JSON NULL,                   -- [{nombre, fecha, se_repite, activo, cerrado, apertura, cierre}]
  plan                 ENUM('gratuito','basico','premium') DEFAULT 'gratuito',
  featured             TINYINT(1) DEFAULT 0,
  featured_score       INT DEFAULT 0,               -- calculado automáticamente al guardar
  estado               ENUM('pendiente','activa','rechazada','suspendida') DEFAULT 'pendiente',
  activo               TINYINT(1) DEFAULT 1,
  visitas              INT UNSIGNED DEFAULT 0,       -- se incrementa en cada visita
  created_at           TIMESTAMP NULL,
  updated_at           TIMESTAMP NULL,
  FOREIGN KEY (lugar_id) REFERENCES lugares(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id)  REFERENCES users(id) ON DELETE SET NULL
);
-- Media: colecciones 'logo' (singleFile), 'portada' (singleFile), 'galeria' (múltiple)
```

### resenas

```sql
CREATE TABLE resenas (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ficha_id   BIGINT UNSIGNED NOT NULL,
  nombre     VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NULL,     -- privado, no se muestra en la UI pública
  rating     TINYINT NOT NULL,      -- 1 a 5
  cuerpo     TEXT NOT NULL,         -- mínimo 10 caracteres
  aprobada   TINYINT(1) DEFAULT 0,  -- false = pendiente de moderación
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (ficha_id) REFERENCES fichas(id) ON DELETE CASCADE,
  INDEX idx_ficha_aprobada (ficha_id, aprobada)
);
```

### categorias

```sql
CREATE TABLE categorias (
  id                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre            VARCHAR(255) NOT NULL,
  slug              VARCHAR(255) UNIQUE NOT NULL,
  descripcion       TEXT NULL,
  icono             VARCHAR(255) NULL,
  activo            TINYINT(1) DEFAULT 1,
  parent_id         BIGINT UNSIGNED NULL,    -- self-referential, hasta 3 niveles
  nivel             TINYINT DEFAULT 1,       -- 1=familia, 2=tipo, 3=especialización
  popularidad_score INT DEFAULT 0,           -- recalculado al guardar fichas
  created_at        TIMESTAMP NULL,
  updated_at        TIMESTAMP NULL,
  FOREIGN KEY (parent_id) REFERENCES categorias(id) ON DELETE SET NULL
);
-- Media: colección 'imagen_generica' (singleFile)
```

### zonas

```sql
CREATE TABLE zonas (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) UNIQUE NOT NULL,
  lat_centro  DECIMAL(10,7) NULL,   -- centroide para auto-detección GPS
  lng_centro  DECIMAL(10,7) NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL
);
```

### promociones

```sql
CREATE TABLE promociones (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ficha_id     BIGINT UNSIGNED NOT NULL,
  titulo       VARCHAR(255) NOT NULL,
  descripcion  TEXT NULL,
  fecha_inicio DATE NULL,
  fecha_fin    DATE NULL,
  activo       TINYINT(1) DEFAULT 1,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  FOREIGN KEY (ficha_id) REFERENCES fichas(id) ON DELETE CASCADE
);
-- Media: colección 'imagen' (singleFile)
```

### slug_redirects

```sql
CREATE TABLE slug_redirects (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  old_slug   VARCHAR(255) NOT NULL,
  new_slug   VARCHAR(255) NOT NULL,
  model_type VARCHAR(255) NOT NULL,  -- ej: 'App\Models\Lugar'
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
```

Creadas automáticamente por `LugarObserver` cuando cambia el `slug` de un lugar. El `NegocioController@show` busca en esta tabla si no encuentra el slug directamente y redirige 301.

### articulos

```sql
CREATE TABLE articulos (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo       VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) UNIQUE NOT NULL,
  extracto     TEXT NULL,
  cuerpo       LONGTEXT NULL,   -- rich text (Filament TipTap)
  publicado    TINYINT(1) DEFAULT 0,
  publicado_en TIMESTAMP NULL,
  categoria_id BIGINT UNSIGNED NULL,
  lugar_id     BIGINT UNSIGNED NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL
);
-- Media: colección 'portada' (singleFile)
```

### guias

```sql
CREATE TABLE guias (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo       VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) UNIQUE NOT NULL,
  intro        TEXT NULL,
  cuerpo       LONGTEXT NULL,
  publicado    TINYINT(1) DEFAULT 0,
  publicado_en TIMESTAMP NULL,
  categoria_id BIGINT UNSIGNED NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL
);

CREATE TABLE guia_lugar (
  guia_id  BIGINT UNSIGNED NOT NULL,
  lugar_id BIGINT UNSIGNED NOT NULL,   -- relación a lugar (no a ficha)
  orden    INT DEFAULT 0,
  PRIMARY KEY (guia_id, lugar_id)
);
-- Media: colección 'portada' (singleFile)
```

### featured_slots

```sql
CREATE TABLE featured_slots (
  id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  posicion      VARCHAR(50) NOT NULL,   -- ej: 'home_destacados', 'home_editorial'
  slotable_type VARCHAR(255) NOT NULL,  -- polimórfico
  slotable_id   BIGINT UNSIGNED NOT NULL,
  orden         INT DEFAULT 0,
  activo        TINYINT(1) DEFAULT 1,
  valido_hasta  DATE NULL,
  created_at    TIMESTAMP NULL,
  updated_at    TIMESTAMP NULL
);
```

### consultas

```sql
CREATE TABLE consultas (
  id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(255) NOT NULL,
  email      VARCHAR(255) NOT NULL,
  mensaje    TEXT NOT NULL,
  leido      TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
);
```

### suscriptores

```sql
CREATE TABLE suscriptores (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email       VARCHAR(255) UNIQUE NOT NULL,
  zona_id     BIGINT UNSIGNED NULL,              -- zona preferida del suscriptor (nullable = toda la ciudad)
  token_baja  CHAR(36) UNIQUE NOT NULL,          -- UUID generado automáticamente en Model::creating()
  activo      TINYINT(1) DEFAULT 1,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL,
  FOREIGN KEY (zona_id) REFERENCES zonas(id) ON DELETE SET NULL,
  INDEX idx_activo_zona (activo, zona_id)
);
```

El token se usa en `/newsletter/baja/{token}` para dar de baja sin login. Si un email ya existe y se suscribe de nuevo, se reactiva y actualiza la zona.

### ficha_visitas

```sql
CREATE TABLE ficha_visitas (
  id        BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ficha_id  BIGINT UNSIGNED NOT NULL,
  fecha     DATE NOT NULL,
  cantidad  INT UNSIGNED DEFAULT 0,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  FOREIGN KEY (ficha_id) REFERENCES fichas(id) ON DELETE CASCADE,
  UNIQUE KEY uq_ficha_fecha (ficha_id, fecha),
  INDEX idx_ficha_fecha (ficha_id, fecha)
);
```

Un registro por ficha por día. Se inserta/actualiza en cada visita a la ficha pública via UPSERT (`cantidad + 1`). Se usa en `PanelController@index` para mostrar el gráfico de los últimos 30 días a fichas Premium.

---

## Decisiones de diseño

- **Slugs únicos** en `categorias`, `zonas`, `lugares` para URLs limpias y SEO.
- **JSON para datos flexibles** (`horarios`, `redes_sociales`): evita tablas de relación para estructuras simples y variables por negocio.
- **`plan` como enum** con `featured_score` calculado: orden en resultados controlado por reglas de negocio, no por consultas complejas.
- **Media polimórfica** (Spatie): un solo sistema de archivos para todos los modelos con medios.
- **`is_admin` en users**: separa admins de propietarios sin tablas adicionales. Los propietarios tienen `is_admin=false` y solo acceden a `/panel`.
- **`aprobada=false` default en reseñas**: toda reseña pasa por moderación antes de ser pública.
- **`visitas` en fichas**: contador simple, sin tabla de eventos por visita. Suficiente para mostrar popularidad relativa; se puede migrar a una tabla de eventos si se necesita análisis temporal.
