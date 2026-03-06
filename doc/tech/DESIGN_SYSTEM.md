# Design System — Guía Local

Sistema de diseño de referencia para el frontend público.
Stack: Tailwind CSS v4 + Alpine.js + Blade components.

---

## Stack de UI

| Herramienta   | Versión                  | Rol                                      |
|---------------|--------------------------|------------------------------------------|
| Tailwind CSS  | v4 (vía `@tailwindcss/vite`) | Estilos y layout                     |
| Alpine.js     | v3.15                    | Interacciones livianas (menú mobile)     |
| Blade         | Laravel 12               | Templates SSR                            |
| Livewire      | v3                       | Componentes reactivos (Etapa 2+)         |

---

## Paleta de colores

| Token             | Valor     | Uso                                       |
|-------------------|-----------|-------------------------------------------|
| `amber-500`       | `#f59e0b` | Color primario: botones, íconos, badges   |
| `amber-600`       | `#d97706` | Hover del primario                        |
| `amber-50`        | `#fffbeb` | Fondo suave en íconos y cards             |
| `amber-100`       | `#fef3c7` | Hover de fondos amber-50                  |
| `amber-200`       | `#fde68a` | Bordes hover en cards                     |
| `gray-900`        | `#111827` | Títulos principales                       |
| `gray-800`        | `#1f2937` | Subtítulos, footer bg                     |
| `gray-600`        | `#4b5563` | Texto secundario, nav links               |
| `gray-500`        | `#6b7280` | Descripciones, texto de apoyo             |
| `gray-400`        | `#9ca3af` | Placeholders, metadata                    |
| `gray-200`        | `#e5e7eb` | Números decorativos en category cards     |
| `gray-100`        | `#f3f4f6` | Bordes suaves, separadores                |
| `gray-50`         | `#f9fafb` | Fondo de secciones alternadas             |
| `white`           | `#ffffff` | Fondo hero, cards, nav                    |

Variables CSS en `resources/css/app.css`:
```css
@theme {
    --color-marca:        #f59e0b;
    --color-marca-oscuro: #d97706;
    --color-marca-claro:  #fef3c7;
}
```

---

## Tipografía

- **Font**: Instrument Sans (incluido por defecto en Laravel 12)
- **Pesos usados**: 400, 500, 600, 700, 800

| Estilo               | Clases Tailwind                               | Uso                          |
|----------------------|-----------------------------------------------|------------------------------|
| H1 hero              | `text-4xl sm:text-5xl font-extrabold`         | Título principal home        |
| H2 sección           | `text-xl sm:text-2xl font-bold text-gray-900` | Títulos de sección           |
| H3 card              | `font-bold text-gray-900 text-base`           | Nombre de negocio            |
| Body                 | `text-sm text-gray-500 leading-relaxed`       | Descripciones                |
| Label / meta         | `text-xs text-gray-400`                       | Categoría, zona, contadores  |
| Nav link             | `text-sm font-medium text-gray-600`           | Links de navegación          |

---

## Espaciado y layout

- **Max width**: `max-w-6xl mx-auto px-4 sm:px-6 lg:px-8`
- **Padding vertical secciones**: `py-12 sm:py-16`
- **Hero**: `py-16 sm:py-24`
- **Gap cards negocios**: `gap-6`
- **Gap cards categorías**: `gap-4`

---

## Border radius

| Uso                    | Clase          |
|------------------------|----------------|
| Cards grandes          | `rounded-2xl`  |
| Íconos / badges        | `rounded-xl`   |
| Botones pill (CTA)     | `rounded-full` |
| Quick-links hero       | `rounded-full` |
| Inputs / buscador      | `rounded-2xl`  |

---

## Sombras

| Uso                    | Clase                            |
|------------------------|----------------------------------|
| Card default           | `shadow-sm`                      |
| Card hover             | `hover:shadow-lg`                |
| Buscador hero          | `shadow-md`                      |
| Quick-links hover      | `hover:shadow-md`                |

---

## Estructura de secciones (home)

| Sección              | Background   | Separador                    |
|----------------------|--------------|------------------------------|
| Nav                  | `bg-white`   | `shadow-sm` sticky           |
| Hero                 | `bg-white`   | `border-b border-gray-100`   |
| Negocios destacados  | `bg-white`   | —                            |
| Categorías           | `bg-gray-50` | `border-t border-gray-100`   |
| CTA                  | `bg-white`   | `border-t border-gray-100`   |
| Footer               | `bg-gray-800`| —                            |

---

## Componentes

### `<x-cat-icon :name="$icono" class="..." />`

Blade component en `resources/views/components/cat-icon.blade.php`.
Renderiza el SVG de categoría según el campo `icono` del modelo Categoria.

| Valor `icono`   | Categoría               | SVG usado             |
|-----------------|-------------------------|-----------------------|
| `utensils`      | Restaurantes            | Building storefront   |
| `coffee`        | Cafés y Bares           | Fire                  |
| `cake`          | Panaderías y Pastelerías| Star                  |
| `pill`          | Farmacias               | Plus-circle           |
| `shopping-cart` | Supermercados           | Shopping bag          |
| `heart-pulse`   | Salud y Bienestar       | Heart                 |
| `briefcase`     | Servicios Profesionales | Briefcase             |
| `shirt`         | Indumentaria y Calzado  | Tag                   |

> Los SVGs son aproximaciones visuales (Heroicons v2 outline). Mejora pendiente: íconos más específicos por categoría.

---

### Pills de filtro (zona / categoría)

Barra horizontal de pills sobre el grid de negocios, en páginas de categoría y zona.
Solo se renderizan si hay más de una opción disponible.

```html
<div class="mb-6 flex flex-wrap items-center gap-2">
    <!-- Pill activa -->
    <a href="..." class="px-4 py-1.5 rounded-full text-sm font-medium bg-amber-500 text-white transition-colors">
        Todas
    </a>
    <!-- Pill inactiva -->
    <a href="..." class="px-4 py-1.5 rounded-full text-sm font-medium bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors">
        Zona / Categoría
    </a>
</div>
```

| Estado   | Clases                                        |
|----------|-----------------------------------------------|
| Activa   | `bg-amber-500 text-white`                     |
| Inactiva | `bg-gray-100 text-gray-600 hover:bg-gray-200` |

---

### Logo en sidebar de ficha

El logo se muestra solo si el negocio tiene media en la colección `logo`. Se centra y tiene altura máxima para evitar logos gigantes.

```html
@if($negocio->hasMedia('logo'))
<div class="flex justify-center pb-1">
    <img src="{{ $negocio->getFirstMediaUrl('logo') }}"
         alt="Logo {{ $negocio->nombre }}"
         class="max-h-20 max-w-full object-contain rounded-2xl">
</div>
@endif
```

---

### Card de negocio

```html
<a class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all">
    <!-- foto h-48 relativa + badge PREMIUM absolute top-3 right-3 -->
    <div class="p-4">
        <h3 class="font-bold text-gray-900 group-hover:text-amber-600">Nombre</h3>
        <p class="text-sm text-gray-500 mt-1 line-clamp-2">Descripción</p>
        <div class="flex justify-between mt-3">
            <span class="text-xs text-gray-400">Categoría · Zona</span>
            <span class="text-xs text-amber-600">Ver más →</span>
        </div>
    </div>
</a>
```

**Placeholder (sin portada):** fondo degradado `from-amber-50 to-amber-100` con `<x-cat-icon>` centrado en `text-amber-300`:
```html
<div class="h-48 bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
    <x-cat-icon :name="$negocio->categoria->icono ?? 'default'" class="w-14 h-14 text-amber-300" />
</div>
```

Badge PREMIUM:
```html
<span class="absolute top-3 right-3 text-xs font-bold bg-amber-500 text-white px-2.5 py-1 rounded-full uppercase tracking-wide">
    Premium
</span>
```

---

### Card de categoría

```html
<a class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all p-5 flex flex-col">
    <span class="text-2xl font-extrabold text-gray-200 leading-none mb-3 self-start">{{ count }}</span>
    <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center mb-3">
        <x-cat-icon :name="$icono" class="w-6 h-6 text-amber-500" />
    </div>
    <p class="font-semibold text-gray-800 text-sm">{{ nombre }}</p>
    <p class="text-xs text-gray-400 mt-0.5">X negocios</p>
</a>
```

---

### Botón primario (CTA pill)

```html
<a class="inline-block px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-full text-sm shadow-sm hover:shadow-md transition-all">
    Registrar mi negocio
</a>
```

### Botón nav

```html
<a class="px-4 py-2 rounded-lg text-sm font-medium bg-amber-500 text-white hover:bg-amber-600 transition-colors">
    Contacto
</a>
```

---

### Buscador hero

```html
<div class="flex gap-2 bg-white border border-gray-200 rounded-2xl shadow-md p-2">
    <input type="text" placeholder="Buscar negocio o categoría"
           class="flex-1 px-4 py-3 text-sm text-gray-700 bg-transparent outline-none placeholder-gray-400 min-w-0">
    <select class="px-3 py-3 text-sm text-gray-600 bg-gray-50 border-l border-gray-200 outline-none rounded-xl sm:w-40 shrink-0">
        <option>Buscar en...</option>
    </select>
    <button class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm whitespace-nowrap transition-colors shadow-sm">
        Buscar
    </button>
</div>
```

---

### Quick-links hero

```html
<a class="flex flex-col items-center gap-2 group">
    <div class="w-14 h-14 rounded-full bg-white border-2 border-gray-100 shadow-sm
                flex items-center justify-center
                group-hover:border-amber-300 group-hover:shadow-md transition-all">
        <!-- SVG w-6 h-6 text-amber-500 -->
    </div>
    <span class="text-xs sm:text-sm font-medium text-gray-600 group-hover:text-amber-600 transition-colors">
        Label
    </span>
</a>
```

---

### Breadcrumb

```html
<nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
    <a href="/" class="hover:text-amber-600 transition-colors">Inicio</a>
    <span>›</span>
    <span class="text-gray-600">Página actual</span>
</nav>
```

---

### Sidebar de filtros

```html
<aside class="lg:w-56 shrink-0">
    <div class="bg-white rounded-2xl border border-gray-100 p-4 sticky top-24">
        <!-- links con amber active -->
    </div>
</aside>
```

Link activo: `text-amber-700 bg-amber-50 font-semibold rounded-lg px-3 py-2 text-sm`
Link inactivo: `text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg px-3 py-2 text-sm`

---

## Transiciones estándar

| Uso                     | Clases                                |
|-------------------------|---------------------------------------|
| Color / borde           | `transition-colors`                   |
| Card completa           | `transition-all`                      |
| Imagen hover            | `group-hover:scale-105 duration-300`  |
| Sombra                  | `transition-shadow`                   |

---

## Estados interactivos

| Elemento           | Default                    | Hover / Active               |
|--------------------|----------------------------|------------------------------|
| Nav link           | `text-gray-600`            | `text-amber-600 bg-amber-50` |
| Nav link activo    | `text-amber-600 bg-amber-50` | —                          |
| Card negocio       | `shadow-sm border-gray-100`| `shadow-lg`                  |
| Card categoría     | `border-gray-100`          | `border-amber-200 shadow-md` |
| Filtro activo      | `bg-amber-50 text-amber-700 font-semibold` | —          |
| Pill filtro activa | `bg-amber-500 text-white`                  | —          |
| Pill filtro inact. | `bg-gray-100 text-gray-600`               | `hover:bg-gray-200` |

---

## Íconos del sistema

Todos los íconos del sistema usan **Heroicons v2 outline**:
```html
<svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="..."/>
</svg>
```

Íconos en uso:
- **Lupa**: nav (search), hero quick-link
- **Pin de mapa**: logo, hero quick-link, dirección en ficha
- **Teléfono, email, globe**: sidebar ficha de negocio
- **Hamburger / X**: menú mobile
- **Cuadrícula 2×2**: hero quick-link "Explorar categorías"

---

## Notas arquitecturales

- Diseño **SSR-first**: prioridad a rendimiento y SEO, sin JS pesado en frontend público.
- Alpine.js solo para interacciones sin datos del servidor (nav mobile, toggles).
- Tailwind v4: `@import 'tailwindcss'` + `@theme {}` en `app.css` (sin `tailwind.config.js`).
- No se usa React/Vue en el frontend público.

---

## Pendientes / mejoras futuras

- [ ] Íconos de categoría más específicos (tenedor+cuchillo, taza, etc.) — usuario diseñará SVGs propios
- [ ] Modo oscuro con `dark:` de Tailwind
- [ ] Componente Blade `<x-negocio-card>` reutilizable
- [ ] Evaluar Inter vs Instrument Sans en pantalla
- [x] Tokens de color como variables CSS en `app.css` (`--color-marca`, `--color-marca-oscuro`, `--color-marca-claro`)
