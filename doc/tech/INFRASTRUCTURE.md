# Infraestructura — Guía Local

Mapa completo de servicios, accesos, configuración y decisiones del proyecto.

---

## Resumen de servicios

| Servicio | Plataforma | Para qué | Costo | Por qué esta |
|---|---|---|---|---|
| **Servidor** | Hetzner Cloud | VPS donde corre la app | ~EUR 5/mes | Buen precio/rendimiento en Europa, baja latencia para Uruguay |
| **Panel servidor** | Ploi.io | Gestión del server, deploys, SSL, daemons | ~USD 8/mes | Alternativa simple a Forge, maneja NGINX/PHP/MySQL/SSL automáticamente |
| **Dominio** | NIC.uy (dominiosuy) | Registro de `.uy` | ~UYU 800/año c/u | Único registrar para dominios `.uy` |
| **DNS** | Cloudflare (Free) | Gestión de registros DNS | Gratis | NIC.uy no soporta MX/TXT/CNAME, Cloudflare sí y es gratis |
| **Correo** | Zoho Mail (Free) | Email profesional `info@guialocal.uy` | Gratis | Plan gratuito con dominio propio, buena alternativa a Google Workspace |
| **Repositorio** | GitHub | Código fuente y versionado | Gratis | Estándar de la industria |
| **SSL** | Let's Encrypt (via Ploi) | Certificado HTTPS | Gratis | Ploi lo renueva automáticamente |

---

## Cómo se conecta todo (flujo)

```
                    ┌─────────────┐
                    │   NIC.uy    │  ← dueño del dominio guialocal.uy
                    │  (registrar)│  ← solo registra, NO maneja DNS
                    └──────┬──────┘
                           │ delegó nameservers a:
                    ┌──────▼──────┐
                    │ Cloudflare  │  ← maneja TODO el DNS (A, MX, TXT, DKIM)
                    │  (DNS free) │  ← modo "DNS only" (nube gris), sin proxy
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
      ┌───────▼──┐  ┌──────▼──┐  ┌─────▼──────┐
      │ Registro A│  │Reg. MX  │  │ Reg. TXT   │
      │ → Hetzner│  │ → Zoho  │  │ SPF + DKIM │
      └───────┬──┘  └──────┬──┘  └────────────┘
              │            │
      ┌───────▼──────┐  ┌──▼──────────┐
      │ Hetzner VPS  │  │ Zoho Mail   │
      │ 178.156.241  │  │ info@guia.. │
      │ (Ploi maneja)│  └─────────────┘
      └──────────────┘
```

**¿Por qué tantos servicios?** Cada uno hace UNA cosa bien:
- **NIC.uy** es obligatorio (es el registrar de `.uy`), pero su panel DNS es muy limitado (solo registros A)
- **Cloudflare** resuelve esa limitación: maneja DNS completo gratis
- **Hetzner** provee el servidor físico (VPS barato y confiable)
- **Ploi** simplifica la administración del servidor (no hay que configurar NGINX/PHP/MySQL a mano)
- **Zoho** da correo profesional gratis con dominio propio

---

## Accesos y credenciales

### Servidor (Hetzner/Ploi)
- **IP:** `178.156.241.157`
- **Nombre en Ploi:** `guialocal-prod`
- **OS:** Ubuntu 24.04 LTS
- **SSH:** `ssh ploi@178.156.241.157` (desde PowerShell, NO PuTTY)
- **Clave SSH:** `C:\Users\Admin\.ssh\id_ed25519` (ed25519, agregada en Ploi)
- **Web server:** NGINX
- **PHP:** 8.3
- **BD:** MySQL 8.4
- **Cache:** Redis

### Dominio (NIC.uy)
- **Panel:** https://dominios.uy → usuario `rodritel88`
- **Dominio principal:** `guialocal.uy`
- **Dominio secundario:** `guialocal.com.uy`
- **Estado:** delegado a Cloudflare (nameservers: `clayton.ns.cloudflare.com`, `paislee.ns.cloudflare.com`)

### DNS (Cloudflare)
- **Panel:** https://dash.cloudflare.com
- **Cuenta:** `Rolodev.uy@gmail.com`
- **Plan:** Free
- **Zone ID:** `9dbbf7aed160ddc1573e1584f2d1ab07`
- **Modo proxy:** DNS only (nube gris) — Ploi maneja SSL directamente
- **SSL/TLS:** Full (strict)

### Correo (Zoho Mail)
- **Cuenta:** `info@guialocal.uy`
- **Panel:** https://mail.zoho.com
- **Verificación dominio:** archivo HTML en `/public/zohoverify/verifyforzoho.html` ✅
- **Registros MX/SPF/DKIM:** pendiente (requiere que Cloudflare propague nameservers)

### Repositorio (GitHub)
- **URL:** https://github.com/rolodevuy/guialocal
- **Branch principal:** `main`
- **Deploy:** push a main → deploy manual en Ploi (o auto-deploy)

---

## DNS — Configuración completa

### ¿Por qué Cloudflare?
NIC.uy (dominiosuy) solo permite crear registros tipo **A** desde su panel. Para recibir correo necesitamos registros **MX** y **TXT** (SPF, DKIM), que NIC.uy no soporta. Por eso delegamos los nameservers a Cloudflare, que es gratuito y soporta todos los tipos de registro.

### ¿Qué es "delegar nameservers"?
Significa que NIC.uy sigue siendo el dueño del dominio, pero le dice a internet "preguntale a Cloudflare dónde apunta este dominio". Es como cambiar la guía telefónica que se consulta, sin cambiar el dueño del número.

### ¿Por qué "DNS only" y no "Proxied"?
- **Proxied** (nube naranja): el tráfico pasa por Cloudflare, que actúa como intermediario. Útil para cache y protección DDoS, pero puede interferir con el SSL de Ploi.
- **DNS only** (nube gris): Cloudflare solo resuelve el DNS, el tráfico va directo al servidor. Ploi maneja el SSL con Let's Encrypt sin conflictos.

Elegimos DNS only para evitar problemas de SSL y porque Ploi ya maneja la seguridad del servidor.

### Registros DNS configurados

**Web (apuntan al servidor Hetzner):**

| Tipo | Nombre | Valor | Proxy | Por qué |
|---|---|---|---|---|
| A | `@` | `178.156.241.157` | DNS only | Dominio raíz → servidor |
| A | `www` | `178.156.241.157` | DNS only | www → servidor |

**Correo Zoho (a agregar cuando Cloudflare propague):**

| Tipo | Nombre | Valor | Prioridad | Por qué |
|---|---|---|---|---|
| MX | `@` | `mx.zoho.com` | 10 | Servidor principal de correo entrante |
| MX | `@` | `mx2.zoho.com` | 20 | Respaldo si mx.zoho.com no responde |
| MX | `@` | `mx3.zoho.com` | 50 | Segundo respaldo |
| TXT | `@` | `v=spf1 include:zohomail.com ~all` | — | SPF: autoriza a Zoho a enviar correo en nombre de guialocal.uy |
| TXT | `zmail._domainkey` | *(valor DKIM de Zoho)* | — | DKIM: firma digital para verificar que el correo no fue alterado |

> **SPF** = "¿quién puede enviar correo desde mi dominio?" → Solo Zoho.
> **DKIM** = "¿este correo es realmente de guialocal.uy?" → Firma criptográfica que lo verifica.
> **MX** = "¿a dónde van los correos enviados a @guialocal.uy?" → A los servidores de Zoho.

El valor DKIM exacto está en Zoho → Configuración de correo → Asignación de DNS.

### Pasos realizados

1. ✅ Crear cuenta en Cloudflare (plan Free)
2. ✅ Agregar dominio `guialocal.uy`
3. ✅ Cloudflare importó automáticamente los registros A existentes
4. ✅ Cambiar proxy a "DNS only" (nube gris) en ambos registros A
5. ✅ En NIC.uy → "Delegar" → nameservers de Cloudflare (`clayton.ns.cloudflare.com.` y `paislee.ns.cloudflare.com.` con punto al final)
6. ⏳ Esperando propagación de nameservers (1-24 horas)
7. ⬜ Configurar SSL/TLS en Cloudflare como "Full (strict)"
8. ⬜ Agregar registros MX, SPF y DKIM de Zoho
9. ⬜ Verificar en Zoho que los registros estén OK
10. ⬜ Repetir para `guialocal.com.uy`

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
1. Desarrollar en local (XAMPP, `guialocal.test`)
2. Commit y `git push origin main`
3. En Ploi → **Deploy now** (o auto-deploy si está activado)

### ¿Por qué este flujo?
- El código vive en GitHub (respaldo + historial)
- Ploi hace el deploy ejecutando el script automáticamente
- No se hace deploy desde la máquina local directo al servidor — siempre pasa por GitHub

---

## SSH — Conexión al servidor

```bash
# Desde PowerShell (NO PuTTY)
ssh ploi@178.156.241.157

# Una vez dentro, ir al proyecto:
cd guialocal.uy

# Comandos útiles:
php artisan optimize:clear                           # limpiar cache
php artisan media-library:regenerate --only-missing   # regenerar imágenes
php artisan queue:failed                              # ver jobs fallidos
```

### ¿Por qué SSH y no hacer todo desde Ploi?
Ploi tiene panel web para deploys y config básica, pero para comandos artisan puntuales (como regenerar imágenes o debug) necesitás terminal directa.

### ¿Por qué no PuTTY?
Windows 10/11 ya tiene cliente SSH integrado. PuTTY necesita convertir la clave a formato `.ppk`. Con PowerShell funciona directo con la clave que generamos.

### Configuración SSH
- **Clave privada:** `C:\Users\Admin\.ssh\id_ed25519` (ed25519)
- **Clave pública:** `C:\Users\Admin\.ssh\id_ed25519.pub`
- **Usuario remoto:** `ploi`
- La clave pública fue agregada en Ploi → SSH Keys → desplegada a `guialocal-prod`

---

## Backups

- **Herramienta:** Spatie Laravel Backup
- **Configuración:** desde admin `/admin/backups` → botón Configuración
- **Opciones:** hora de backup, password, prefijo de archivo, retención (días)
- **Schedule:** configurable desde admin, default 01:30 AM
- **Storage:** disco local (`storage/app/private/`)
- **Ploi:** también tiene backups propios del servidor (revisar plan)

### ¿Por qué Spatie Backup?
Backup completo de la app (BD + archivos) en un solo .zip, con rotación automática. Se puede proteger con password y descargar desde el admin.

---

## Correo — Zoho Mail

### ¿Por qué Zoho?
- Plan gratuito con dominio propio (1 cuenta, 5 GB)
- Alternativa a Google Workspace (que es pago)
- Interfaz web decente, IMAP/POP3 disponible

### Verificación del dominio
Zoho necesita verificar que sos dueño del dominio. Como NIC.uy no soportaba TXT, usamos el método de **archivo HTML**: se sube un archivo a `/public/zohoverify/verifyforzoho.html` y Zoho lo busca por HTTP.

### Configuración pendiente
Una vez que Cloudflare propague los nameservers:
1. Agregar registros MX (para recibir correo)
2. Agregar registro TXT SPF (para que los correos enviados no caigan en spam)
3. Agregar registro TXT DKIM (firma digital de autenticidad)
4. Verificar en Zoho → "Verificar todos los registros"
5. Configurar firma de correo

---

## Optimización de imágenes

### ¿Por qué?
Con solo 4 fichas el backup ya pesa 4 MB. Con 1000+ negocios sería inmanejable. Además el sitio se ve en móvil (iOS) donde el ancho de banda importa.

### Cómo funciona
Spatie Media Library genera automáticamente una versión `optimized` (WebP + resize + sharpen) de cada imagen al subirla. Los modelos configurados:

| Modelo | Tamaño optimizado | Uso |
|---|---|---|
| Ficha (portada) | 1200x400 | Banner principal de la ficha |
| Ficha (logo) | 300x300 | Logo del negocio |
| Ficha (galería) | 1200x800 | Fotos de la galería |
| Categoría | 800x450 | Imagen de categoría |
| Evento | 1200x630 | Portada de evento |
| Artículo | 1200x630 | Portada de artículo |
| Guía | 1200x630 | Portada de guía |
| Promoción | 800x600 | Imagen de promoción |

### Para imágenes existentes
Después de activar las conversiones, hay que regenerar las imágenes ya subidas:
```bash
ssh ploi@178.156.241.157
cd guialocal.uy
php artisan media-library:regenerate --only-missing
```
Esto se corre UNA vez, no va en el script de deploy.

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

- [ ] **Agregar registros Zoho en Cloudflare** (MX, SPF, DKIM) — cuando propague
- [ ] **Verificar registros en Zoho** → "Verificar todos los registros"
- [ ] **Configurar SSL/TLS en Cloudflare** como "Full (strict)"
- [ ] **Correr `php artisan media-library:regenerate --only-missing`** en producción (vía SSH)
- [ ] **Configurar firma de correo** en Zoho
- [ ] **Repetir config DNS para `guialocal.com.uy`** cuando se migre a Cloudflare
- [ ] **Conectar Cloudflare API en Ploi** (opcional, para gestionar DNS desde Ploi)
