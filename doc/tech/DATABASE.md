# Base de Datos

Esquema de la base de datos del proyecto. Motor: MariaDB 10.x vía XAMPP.

---

## Estado actual

| Tabla | Estado | Paso |
|---|---|---|
| `users` | ✅ creada (Laravel default) | Paso 1 |
| `sessions` | ✅ creada (Laravel default) | Paso 1 |
| `cache` | ✅ creada (Laravel default) | Paso 1 |
| `jobs` / `job_batches` / `failed_jobs` | ✅ creadas (Laravel default) | Paso 1 |
| `media` | ✅ creada (Spatie Media Library) | Paso 3 |
| `categorias` | ✅ creada | Paso 4 |
| `zonas` | pendiente | Paso 5 |
| `negocios` | pendiente | Paso 6 |
| `consultas` | pendiente | Paso 7 |

---

## Esquema detallado

### categorias ✅

```sql
CREATE TABLE categorias (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) UNIQUE NOT NULL,
  descripcion TEXT NULL,
  icono       VARCHAR(255) NULL,
  activo      TINYINT(1) DEFAULT 1,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL
);
```

### zonas (pendiente — Paso 5)

```sql
CREATE TABLE zonas (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(255) NOT NULL,
  slug        VARCHAR(255) UNIQUE NOT NULL,
  created_at  TIMESTAMP NULL,
  updated_at  TIMESTAMP NULL
);
```

### negocios (pendiente — Paso 6)

```sql
CREATE TABLE negocios (
  id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(255) NOT NULL,
  slug         VARCHAR(255) UNIQUE NOT NULL,
  descripcion  TEXT NULL,
  direccion    VARCHAR(255) NULL,
  telefono     VARCHAR(50) NULL,
  email        VARCHAR(255) NULL,
  sitio_web    VARCHAR(255) NULL,
  lat          DECIMAL(10,7) NULL,
  lng          DECIMAL(10,7) NULL,
  horarios     JSON NULL,
  featured     TINYINT(1) DEFAULT 0,
  activo       TINYINT(1) DEFAULT 1,
  plan         ENUM('gratuito','basico','premium') DEFAULT 'gratuito',
  categoria_id BIGINT UNSIGNED NOT NULL,
  zona_id      BIGINT UNSIGNED NOT NULL,
  created_at   TIMESTAMP NULL,
  updated_at   TIMESTAMP NULL,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (zona_id) REFERENCES zonas(id)
);
```

### consultas (pendiente — Paso 7)

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

### media (Spatie Media Library — polimórfica)

Tabla gestionada por `spatie/laravel-medialibrary`. Almacena archivos adjuntos a cualquier modelo que implemente `HasMedia`.

---

## Decisiones de diseño

- **Slugs únicos** en `categorias`, `zonas` y `negocios` para URLs limpias y SEO.
- **`horarios` como JSON** — flexible para distintos formatos de horario por negocio.
- **`plan` como enum** — escalable a planes de pago en Etapa 2+.
- **`featured` y `activo` como booleanos** — control simple de visibilidad desde el admin.
- **Media polimórfica** — un solo sistema de archivos para todos los modelos con medios.
