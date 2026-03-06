# Sistema de Diseño

## Stack de UI

| Herramienta | Versión | Rol |
|---|---|---|
| Tailwind CSS | v4 (vía `@tailwindcss/vite`) | Estilos y layout |
| Alpine.js | v3.15 | Interacciones livianas (menú mobile, toggles) |
| Blade | Laravel 12 | Templates SSR |
| Livewire | v3 | Componentes reactivos (Etapa 2+) |

---

## Paleta de colores

Por definir al implementar el layout (Paso 13). Se configurará en `resources/css/app.css` usando el bloque `@theme` de Tailwind v4.

Referencia de color primario del admin: **Amber** (Filament).

---

## Tipografía

Por defecto: `Instrument Sans` (incluido en el stack de Laravel 12).

---

## Componentes planificados (Paso 13+)

- **Layout principal** (`layouts/app.blade.php`): header + nav + footer
- **Card de negocio**: imagen, nombre, categoría, zona, teléfono
- **Card de categoría**: ícono + nombre + cantidad de negocios
- **Buscador**: input tipo GET apuntando a `/negocios?q=`
- **Paginación**: componente nativo de Laravel con Tailwind
- **Menú mobile**: toggle con Alpine.js (`x-show`, `x-on:click`)

---

## Notas

- El diseño es **SSR-first**: prioridad a rendimiento y SEO.
- No se usa JavaScript pesado (React/Vue) en el frontend público.
- Alpine.js solo para interacciones que no requieren datos del servidor.
- Tailwind v4 usa `@import 'tailwindcss'` + directivas `@theme` (sin `tailwind.config.js`).
