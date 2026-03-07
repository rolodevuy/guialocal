# Guía de Deployment — Guía Local

Notas técnicas para pasar de entorno local (XAMPP/dev) a producción.

---

## Variables de entorno críticas

### Queue (colas de trabajo)

**Dev (actual):**
```env
QUEUE_CONNECTION=sync
```
Con `sync`, los jobs (como conversiones de imagen) corren **de forma inmediata y sincrónica** al subir un archivo. No requiere worker. Ideal para desarrollo local.

**Producción:**
```env
QUEUE_CONNECTION=database   # o redis
```
Con `database` o `redis`, los jobs se encolan y requieren un worker corriendo:

```bash
php artisan queue:work --daemon --tries=3
```

Configurarlo como servicio (supervisor, systemd, etc.):
```ini
# /etc/supervisor/conf.d/guialocal-worker.conf
[program:guialocal-worker]
command=php /var/www/guialocal/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
```

> ⚠️ Si el worker no está corriendo con `QUEUE_CONNECTION=database`, las conversiones de imagen (WebP) **nunca se generarán** aunque el upload aparezca exitoso en el admin.

---

### Storage / Imágenes

**Disk URL relativo:** `config/filesystems.php` usa `'url' => '/storage'` (relativo, sin dominio).
Esto hace que todas las URLs de Spatie Media Library sean `/storage/...`, resolubles desde cualquier dominio. No cambiar a `APP_URL.'/storage'` — rompe ngrok y otros proxies.

**Dev:** `FILESYSTEM_DISK=local` → archivos en `storage/app/public/`, servidos vía symlink.

**Producción:** Considerar S3 o storage compatible:
```env
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=sa-east-1
AWS_BUCKET=guialocal-media
```

Si se queda en disco local: asegurarse de que `php artisan storage:link` esté en el deploy script.

---

### APP_URL y proxies

```env
APP_URL=https://tudominio.com
```

Si hay load balancer o proxy (nginx, Cloudflare, ngrok):
→ Ya está configurado en `bootstrap/app.php`:
```php
$middleware->trustProxies(at: '*');
```
Esto permite que Laravel use los headers `X-Forwarded-Host`, `X-Forwarded-Proto`, etc., y genere URLs correctas detrás de un proxy.

---

### Cache y sesiones

**Dev:** `CACHE_STORE=database`, `SESSION_DRIVER=database` (sin infra extra).

**Producción recomendado:** Redis para mejor performance:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

---

## Conversiones de imagen (WebP)

Spatie Media Library genera conversiones WebP automáticamente al subir imágenes. Los modelos configurados son:

| Modelo | Colecciones con WebP |
|---|---|
| `Ficha` | `portada`, `logo` |
| `Categoria` | `imagen_generica` |
| `Articulo` | `portada` |
| `Guia` | `portada` |

Si se migra a producción con imágenes ya subidas, regenerar conversiones:
```bash
php artisan media-library:regenerate --only-missing
```

> Nota: los registros de media del modelo `Negocio` (modelo anterior a la migración a `Ficha`/`Lugar`) van a fallar con este comando — son registros huérfanos. Se puede ignorar esos errores o limpiar la tabla `media` manualmente.

---

## Checklist de deploy

- [ ] Copiar `.env.production` con valores reales (sin debug, con APP_KEY)
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] `php artisan storage:link`
- [ ] `php artisan migrate --force`
- [ ] `php artisan media-library:regenerate --only-missing`
- [ ] Configurar worker de colas (supervisor/systemd)
- [ ] `npm run build` (si el CSS no está commiteado)
- [ ] Verificar permisos en `storage/` y `bootstrap/cache/` (writable por el proceso web)

---

## Comandos útiles en producción

```bash
# Limpiar todo el cache
php artisan optimize:clear

# Re-cachear todo
php artisan optimize

# Ver jobs pendientes en la cola
php artisan queue:monitor

# Ver jobs fallidos
php artisan queue:failed

# Reintentar jobs fallidos
php artisan queue:retry all
```
