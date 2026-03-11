# Infraestructura — Guía Local

Mapa completo de servicios, accesos y configuración del proyecto.

---

## Resumen de servicios

| Servicio | Plataforma | Para qué | Costo |
|---|---|---|---|
| **Servidor** | Hetzner Cloud | VPS donde corre la app | ~EUR 5/mes |
| **Panel servidor** | Ploi.io | Gestión del server, deploys, SSL, daemons | ~USD 8/mes |
| **Dominio** | NIC.uy (dominiosuy) | Registro de `guialocal.uy` y `guialocal.com.uy` | ~UYU 800/año c/u |
| **DNS** | NIC.uy (pendiente migrar a Cloudflare) | Apuntar dominio al servidor | Incluido / Gratis |
| **Correo** | Zoho Mail | Email profesional `info@guialocal.uy` | Gratis (plan free) |
| **Repositorio** | GitHub | Código fuente | Gratis |

---

## Accesos y credenciales

### Servidor (Hetzner/Ploi)
- **IP:** `178.156.241.157`
- **Nombre en Ploi:** `guialocal-prod`
- **OS:** Ubuntu 24.04 LTS
- **SSH:** `ssh ploi@178.156.241.157`
- **Clave SSH:** `C:\Users\Admin\.ssh\id_ed25519` (ed25519, agregada en Ploi)
- **Web server:** NGINX
- **PHP:** 8.3
- **BD:** MySQL 8.4
- **Cache:** Redis

### Dominio (NIC.uy)
- **Panel:** https://dominios.uy → usuario `rodritel88`
- **Dominio principal:** `guialocal.uy`
- **Dominio secundario:** `guialocal.com.uy`
- **Limitación:** NIC.uy solo soporta registros tipo A (no MX, TXT, CNAME)
- **DNS actual:**
  - `@` → `178.156.241.157` (A)
  - `www` → `178.156.241.157` (A)

### Correo (Zoho Mail)
- **Cuenta:** `info@guialocal.uy`
- **Panel:** https://mail.zoho.com
- **Verificación dominio:** archivo HTML en `/public/zohoverify/verifyforzoho.html` ✅
- **Registros MX/SPF/DKIM:** ⚠️ PENDIENTE (requiere migrar DNS a Cloudflare)

### Repositorio (GitHub)
- **URL:** https://github.com/rolodevuy/guialocal
- **Branch principal:** `main`
- **Deploy:** push a main → deploy manual en Ploi (o auto-deploy)

---

## DNS — Estado actual y pendientes

### Problema
NIC.uy tiene un panel DNS muy limitado: solo permite registros tipo **A**. Para correo (Zoho) se necesitan registros **MX**, **TXT** (SPF, DKIM, DMARC) que NIC.uy no soporta.

### Solución: migrar DNS a Cloudflare
1. Crear cuenta en [cloudflare.com](https://cloudflare.com) (gratis)
2. Agregar dominio `guialocal.uy` (plan Free)
3. Cloudflare da 2 nameservers (ej: `anna.ns.cloudflare.com`, `bob.ns.cloudflare.com`)
4. En NIC.uy → click **"Delegar"** → poner los nameservers de Cloudflare
5. En Cloudflare recrear los registros A existentes
6. Agregar los registros de Zoho (ver abajo)
7. Opcionalmente conectar Cloudflare API en Ploi (DNS Providers → Cloudflare → API key)

### Registros a crear en Cloudflare (post-migración)

**Registros actuales (recrear):**

| Tipo | Nombre | Valor | TTL |
|---|---|---|---|
| A | `@` | `178.156.241.157` | Auto |
| A | `www` | `178.156.241.157` | Auto |

**Registros Zoho Mail (agregar):**

| Tipo | Nombre | Valor | Prioridad |
|---|---|---|---|
| MX | `@` | `mx.zoho.com` | 10 |
| MX | `@` | `mx2.zoho.com` | 20 |
| MX | `@` | `mx3.zoho.com` | 50 |
| TXT | `@` | `v=spf1 include:zohomail.com ~all` | — |
| TXT | `zmail._domainkey` | *(valor DKIM largo de Zoho)* | — |

> El valor DKIM exacto está en el panel de Zoho → Asignación de DNS.

---

## Deploy

### Script de deploy (Ploi)
```bash
cd /home/ploi/guialocal.uy
git pull origin main
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
echo "" | sudo -S service php8.4-fpm reload
echo "🚀 Application deployed!"
```

### Flujo de deploy
1. Desarrollar en local (XAMPP)
2. `git push origin main`
3. En Ploi → **Deploy now** (o auto-deploy activado)

---

## SSH — Conexión al servidor

```bash
# Desde PowerShell o Git Bash (NO PuTTY)
ssh ploi@178.156.241.157

# Una vez dentro, ir al proyecto:
cd guialocal.uy

# Comandos útiles:
php artisan optimize:clear          # limpiar cache
php artisan media-library:regenerate --only-missing  # regenerar imágenes
php artisan queue:failed            # ver jobs fallidos
```

- **Clave privada:** `C:\Users\Admin\.ssh\id_ed25519`
- **Usuario:** `ploi`
- **No usar PuTTY** (necesita conversión a .ppk). PowerShell nativo funciona directo.

---

## Backups

- **Herramienta:** Spatie Laravel Backup
- **Configuración:** desde admin `/admin/backups` → botón Configuración
- **Opciones:** hora de backup, password, prefijo de archivo, retención (días)
- **Schedule:** configurable desde admin, default 01:30 AM
- **Storage:** disco local (`storage/app/private/`)
- **Ploi:** también tiene backups propios del servidor (revisar plan)

---

## Entorno local (desarrollo)

| Componente | Detalle |
|---|---|
| **Stack** | XAMPP (Windows 11) |
| **PHP** | 8.2.12 (`C:\xampp\php`) |
| **BD** | MariaDB 10.x |
| **Composer** | `C:\composer\composer.phar` |
| **Virtual host** | `guialocal.test` → `C:/xampp/htdocs/guialocal/public` |
| **BD nombre** | `business_guide` |
| **BD usuario** | `root` (sin password) |

---

## Pendientes de infraestructura

- [ ] **Migrar DNS a Cloudflare** para poder agregar registros MX/TXT/DKIM de Zoho
- [ ] **Configurar registros Zoho** en Cloudflare (MX, SPF, DKIM, DMARC)
- [ ] **Correr `php artisan media-library:regenerate`** en producción (vía SSH)
- [ ] **Repetir config DNS para `guialocal.com.uy`** cuando se migre
- [ ] **Firma de correo** — configurar en Zoho una vez el correo funcione
