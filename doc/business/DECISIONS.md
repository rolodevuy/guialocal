# DECISIONS.md

Registro de decisiones técnicas, de producto y de negocio tomadas durante el desarrollo. Cada decisión incluye el contexto, la alternativa elegida y el razonamiento.

---

## Decisiones técnicas

### SSR monolith vs API-first
**Decisión:** Monolito SSR con Blade + Alpine.js.

**Alternativas consideradas:** API REST + SPA (React/Vue), microservicios.

**Razonamiento:** El directorio es un sitio de contenido donde el SEO es crítico. Una SPA complica el SSR sin Next.js/Nuxt, que hubiera requerido infraestructura adicional. El monolito Laravel con Blade genera HTML directamente en el servidor, ideal para indexación y tiempo al primer byte. Alpine.js cubre toda la interactividad necesaria sin overhead de una SPA.

---

### Filament v3 para el panel admin
**Decisión:** Filament v3 como framework del panel de administración.

**Alternativas consideradas:** Nova, Backpack, admin custom con Livewire puro.

**Razonamiento:** Filament ofrece Resources CRUD completos, tablas con filtros, formularios con campos avanzados (Repeater, MediaLibrary integration, mapa Leaflet embebido), bulk actions y notificaciones — todo con configuración mínima. La comunidad es activa y la compatibilidad con Laravel 12 y Spatie packages es de primera clase. Nova es de pago. Backpack requiere más configuración manual.

---

### Blade + Alpine.js vs React/Vue
**Decisión:** Blade para templates, Alpine.js para interactividad del lado cliente, Livewire para componentes reactivos con estado de servidor.

**Alternativas consideradas:** Vue 3 + Inertia.js, React + Inertia.js.

**Razonamiento:** Blade + Alpine cubre el 90% de los casos de interactividad (menú mobile, tabs, toggles, carruseles, formularios con validación en tiempo real). Livewire se usa donde se necesita reactividad con datos del servidor (filtros de negocios, componentes de búsqueda). Inertia hubiera requerido mantener un build pipeline más complejo y dos capas de componentes (Laravel + Vue/React).

---

### Un solo modelo User para admin y propietario
**Decisión:** Un modelo `User` con campo `is_admin` booleano para separar roles.

**Alternativas consideradas:** Dos guards separados, dos tablas separadas (admins + business_users), Spatie Laravel Permission.

**Razonamiento:** La separación de contexto se logra con middleware (`PanelAuthenticate`) y rutas separadas (`/admin` para Filament, `/panel` para propietarios). Un solo modelo evita duplicación de lógica de auth, facilita el reset de contraseña nativo de Laravel y simplifica la gestión de usuarios en Filament. Spatie Permission sería overkill para dos roles simples.

---

### Soft gating en lugar de hard gating
**Decisión:** Los planes se implementan como "soft gating": el contenido existe pero con restricción de acceso o visualización según el plan, en lugar de bloquear el registro de funcionalidades.

**Alternativas consideradas:** Hard gating (features inaccesibles para planes inferiores), feature flags por BD.

**Razonamiento:** El soft gating permite que el propietario vea qué funcionalidades existen y tenga un incentivo claro para hacer upgrade. Los nudges con CTAs específicos por plan (badge Premium, "Quiero el plan Básico") son más efectivos comercialmente que simplemente ocultar funciones. `Ficha::PLAN_LIMITS` + `planIncluye()` centraliza la lógica.

---

### JSON para horarios y redes sociales
**Decisión:** `horarios` y `redes_sociales` como columnas JSON en `fichas`.

**Alternativas consideradas:** Tabla `ficha_horarios` normalizada, tabla `ficha_redes`.

**Razonamiento:** Los horarios son estructuras flexibles (rango de días, franja horaria, toggle cerrado) que varían mucho entre negocios. Una tabla normalizada requeriría JOINs para cada carga de ficha. Con JSON, los horarios son un array de objetos directamente accesible en Eloquent con cast `array`. El volumen no justifica la complejidad de una tabla separada. La compatibilidad con schema.org `openingHours` se resuelve con una función de mapeo al renderizar.

---

### Sectores como dimensión extra sobre categorías
**Decisión:** Crear un modelo `Sector` que agrupa categorías nivel 1 en 3 verticales temáticos, en lugar de usar tags o atributos genéricos.

**Alternativas consideradas:** Tags polimórficos en categorías, columna enum `vertical` en categorias, sin agrupación.

**Razonamiento:** Los sectores permiten crear micrositios visuales con identidad propia (color, hero, URL `/sectores/{slug}`), y sirven como eje de navegación principal en el navbar. FK directa `sector_id` en categorías nivel 1 es simple y performante. El `color_classes` JSON con clases Tailwind literales es necesario porque Tailwind v4 no permite generar clases dinámicas en runtime.

---

### Spatie MediaLibrary para uploads
**Decisión:** Spatie Laravel Media Library para todas las colecciones de imágenes.

**Alternativas consideradas:** Storage manual con Intervention Image, Cloudinary, S3 + Lambda resize.

**Razonamiento:** Spatie Media Library integra con Filament de forma nativa (plugin oficial), gestiona conversiones WebP automáticas, soporta múltiples colecciones por modelo con restricciones (singleFile, orden, conversiones específicas por colección), y usa el filesystem de Laravel (local o S3 sin cambio de código). La integración con Livewire y el modelo polimórfico `media` centraliza todos los archivos en un solo sistema.

---

### Clases Tailwind literales en JSON (color_classes)
**Decisión:** Almacenar clases Tailwind completas y literales en el JSON `color_classes` de sectores, en lugar de almacenar solo el nombre del color y construir clases dinámicamente.

**Alternativas consideradas:** `"color": "amber"` + concatenación `"bg-" + color + "-500"` en Blade.

**Razonamiento:** Tailwind v4 (y v3 en modo JIT) solo incluye en el CSS las clases que aparecen como texto completo en los templates escaneados. Las clases construidas dinámicamente en PHP/Blade no son detectadas y no se incluyen en el CSS compilado. El resultado son clases que existen en el HTML pero sin estilos. La solución es almacenar las clases completas: `"bg": "bg-amber-50"`, `"text": "text-amber-700"`, etc.

---

## Decisiones de producto

### Amber como color primario del design system
**Decisión:** Color primario `orange-500` (#f97316), sobreescribiendo la paleta `amber-*` de Tailwind para que todos los usos de `amber-*` en templates mapeen automáticamente a naranja.

**Alternativas consideradas:** Amber puro (#f59e0b), naranja con clase custom, paleta desde cero.

**Razonamiento:** Amber es el color del panel Filament (que no cambiamos). El sitio público usa naranja puro para tener personalidad propia y diferenciarse del admin. La técnica de sobreescribir `--color-amber-*` en `@theme` de Tailwind v4 evita cambiar todos los templates: `bg-amber-500` pasa a compilar como naranja automáticamente.

---

### Verificación de propietarios via constancia de RUT
**Decisión:** El propietario sube una imagen/PDF de la constancia de RUT para reclamar su negocio. El admin aprueba manualmente.

**Alternativas consideradas:** Verificación por código SMS/email, verificación automática via API DGI, sin verificación (auto-claim).

**Razonamiento:** El auto-claim sin verificación permite apropiación indebida de negocios. La verificación via DGI requeriría integración con una API de gobierno que puede no estar disponible o ser costosa. La constancia de RUT es un documento accesible para cualquier negocio formal uruguayo, la moderación manual es viable para el volumen actual, y el flujo es familiar para los usuarios.

---

### Página /precios con CTAs que pre-llenan asunto
**Decisión:** La página `/precios` tiene botones que llevan al formulario de contacto con el campo `asunto` pre-llenado via query param (`?asunto=alta-negocio`, `?asunto=upgrade-basico`, etc.).

**Alternativas consideradas:** Formulario de contacto directamente en /precios, formulario inline en /precios, mailto: links.

**Razonamiento:** Un formulario centralizado en `/contacto` permite gestionar todas las consultas desde el mismo recurso Filament (`ConsultaResource`). Los query params pre-llenan el asunto para que el admin sepa inmediatamente el contexto de cada consulta. El `asunto` viaja como hidden input POST para preservar el contexto sin exponerlo en el campo de mensaje.

---

## Decisiones de UX/UI

### Watermark en imágenes ilustrativas
**Decisión:** Mostrar un overlay "imagen ilustrativa" sobre las imágenes de categoría cuando se usan como fallback de portada de negocio.

**Razonamiento:** Sin el watermark, el usuario puede confundir una imagen genérica de categoría con la foto real del negocio. El overlay semitransparente es informativo sin ser intrusivo. Desaparece automáticamente cuando el negocio sube su propia portada.

### Horarios agrupados en el panel propietario
**Decisión:** En el dashboard de `/panel`, agrupar días consecutivos con el mismo horario en un solo rango ("Lun – Vie 09:00 – 18:00") en lugar de mostrar una fila por día.

**Razonamiento:** La mayoría de los negocios tienen el mismo horario de lunes a viernes. Mostrar 5 filas idénticas es ruido visual. El agrupamiento es más legible y más parecido a cómo los negocios comunican sus horarios en la realidad.
