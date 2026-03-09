# Planes de negocio — Guía Local

Definición de los tres tiers disponibles para fichas de negocios.

---

## Resumen de planes

| Feature | Gratuito | Básico | Premium |
|---|:---:|:---:|:---:|
| Ficha pública (nombre, descripción, contacto) | ✅ | ✅ | ✅ |
| Categoría y zona | ✅ | ✅ | ✅ |
| Horarios y "Abierto ahora" | ✅ | ✅ | ✅ |
| Panel de autogestión `/panel` | ✅ | ✅ | ✅ |
| Estadísticas de visitas | ❌ | ✅ | ✅ |
| Logo propio | ❌ | ✅ | ✅ |
| WhatsApp flotante en ficha pública | ❌ | ✅ | ✅ |
| Promociones activas | ❌ | 1 | ∞ |
| Galería de fotos | ❌ | máx. 3 | máx. 10 |
| Destacado en resultados y home | ❌ | ❌ | ✅ |
| Badge "Premium ★" en ficha pública | ❌ | ❌ | ✅ |

---

## Cómo se implementa

### `featured_score` (orden en resultados)

El `featured_score` se recalcula automáticamente cada vez que se guarda una ficha (`Ficha::booted()`):

| Condición | Puntos |
|---|---|
| Plan `premium` | +50 |
| Plan `basico` | +20 |
| Plan `gratuito` | +0 |
| `featured = true` (toggle admin) | +30 |

Los listados en `/negocios` y el carousel de home ordenan por `featured_score DESC`, luego por `updated_at`.

### Soft gating (`Ficha::PLAN_LIMITS`)

Los límites están definidos como constante en `app/Models/Ficha.php`:

```php
const PLAN_LIMITS = [
    'gratuito' => ['visitas' => false, 'whatsapp' => false, 'promociones' => 0,            'fotos' => 0,  'logo' => false, 'destacado' => false],
    'basico'   => ['visitas' => true,  'whatsapp' => true,  'promociones' => 1,            'fotos' => 3,  'logo' => true,  'destacado' => false],
    'premium'  => ['visitas' => true,  'whatsapp' => true,  'promociones' => PHP_INT_MAX,  'fotos' => 10, 'logo' => true,  'destacado' => true ],
];
```

Se consulta con `$ficha->planIncluye('whatsapp')` → devuelve `bool|int`.

### Dónde se aplica el gating

| Punto de control | Qué hace |
|---|---|
| `panel/dashboard.blade.php` | Stats con overlay de candado si el plan no las incluye. Sección "¿Qué incluye tu plan?" con ✅/❌ por feature. Banner de upgrade con texto diferenciado por plan. |
| `negocios/show.blade.php` | Botón WhatsApp flotante solo si `$ficha->planIncluye('whatsapp')` |

### Tipo de gating: Soft

Las restricciones son **informativas, no técnicas**. El admin puede igualmente cargar más fotos o promos desde Filament. El gating afecta lo que el dueño ve en su panel y lo que aparece en la ficha pública.

Si en el futuro se requiere hard gating (bloquear técnicamente), el punto de control es `PanelController::update()` y los validadores de store de promos/resenas.

---

## Gestión de planes

Los planes se asignan manualmente desde Filament:
`Admin → Fichas → Editar → tab Configuración → campo "Plan"`

No hay integración de pago automatizada.

---

## Upgrade flow para dueños

El panel muestra un banner de upgrade con CTA que linkea a:
```
/contacto?asunto=upgrade-premium
```
Esto pre-llena el mensaje de contacto: _"Hola, me interesa conocer más sobre el plan Premium para mi negocio."_

El texto del banner varía según el plan actual:
- **Gratuito**: menciona tanto Básico como Premium con sus diferencias
- **Básico**: solo menciona Premium con énfasis en posicionamiento y fotos
