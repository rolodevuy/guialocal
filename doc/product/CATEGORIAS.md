# Sistema de Clasificación — Guía Local

Diseño del sistema de clasificación de negocios basado en **3 dimensiones independientes**.

Referencia: [ARCHITECTURE.md](../tech/ARCHITECTURE.md) · [ROADMAP.md](ROADMAP.md)

---

## Principio

Un negocio no se describe con una sola etiqueta.
Se separa la clasificación en tres dimensiones para evitar categorías infinitas y permitir búsquedas más inteligentes.

| Dimensión | Pregunta que responde | Tipo | Ejemplo |
|---|---|---|---|
| **Categoría** | ¿Qué es? | Jerárquica (3 niveles) | Panadería y dulces → panadería artesanal |
| **Atributo** | ¿Qué características tiene? | Tags planos (N:M) | wifi, delivery, pet friendly |
| **Contexto** | ¿En qué experiencia encaja? | Tags planos (N:M) | turismo, nocturno, gastronómico |

---

## 1. Categorías (qué es el negocio)

Estructura jerárquica de máximo **3 niveles**.

### Nivel 1 — familia visual
Navegación principal e imágenes genéricas.

Ejemplos:
- Restaurantes
- Cafés y bares
- Panadería y dulces
- Heladerías
- Compras
- Salud y bienestar
- Servicios profesionales
- Hogar y construcción
- Automotor
- Entretenimiento
- Turismo y alojamiento
- Educación

### Nivel 2 — tipo de negocio

| Familia | Tipos |
|---|---|
| Restaurantes | parrilla, sushi, pasta, comida internacional |
| Panadería y dulces | panadería, pastelería, churrería |
| Heladerías | heladería, gelato |

### Nivel 3 — especialización (opcional)

Ejemplo: Restaurantes → parrilla → parrilla uruguaya

### Implementación

```
categorias
├── id, nombre, slug, descripcion, icono, activo
├── parent_id  → FK nullable a categorias (self-referential)
├── nivel      → tinyint (1, 2 o 3)
├── popularidad_score
└── timestamps
```

Reglas:
- `parent_id = null` → nivel 1
- Nivel máximo: 3 (validado en admin)
- Un lugar pertenece a **una** categoría (la más específica disponible según su plan)

---

## 2. Atributos (cómo es el negocio)

Tags que describen características del lugar. Un lugar puede tener muchos atributos.

Ejemplos:
- delivery, take away, reservas
- wifi, estacionamiento, accesible
- pet friendly, vista al mar, 24 horas
- menú vegano, menú sin gluten

### Implementación

```
atributos
├── id, nombre, slug, icono
└── timestamps

atributo_lugar (pivot)
├── atributo_id → FK
└── lugar_id    → FK
```

Los atributos se adjuntan al **lugar** (no a la ficha), porque describen al espacio físico.

---

## 3. Contextos (en qué experiencia encaja)

Agrupan negocios de distintos rubros bajo una misma experiencia o situación.

Ejemplos:
- turismo, nocturno, gastronómico
- cultural, familiar, romántico
- negocios, eventos

### Implementación

```
contextos
├── id, nombre, slug, icono
└── timestamps

contexto_lugar (pivot)
├── contexto_id → FK
└── lugar_id    → FK
```

### Contextos vs Guías temáticas

- **Contexto** = clasificación administrativa del lugar (el admin marca "turismo", "nocturno")
- **Guía temática** (Etapa 3) = contenido editorial curado ("Los mejores cafés de Pocitos")

Son complementarios. Los contextos alimentan filtros; las guías son contenido editorial.

---

## 4. Visibilidad por plan

Los datos se almacenan completos siempre. La visibilidad se controla en la capa de presentación según el plan de la ficha.

| Feature | Gratuito | Básico | Premium |
|---|---|---|---|
| Categoría nivel 1 | ✅ | ✅ | ✅ |
| Categoría nivel 2 | ❌ | ✅ | ✅ |
| Categoría nivel 3 | ❌ | ❌ | ✅ |
| Atributos visibles | ❌ | ✅ | ✅ |
| Contextos visibles | ❌ | ❌ | ✅ |
| Imagen genérica del rubro | ✅ | ✅ | ✅ |
| Fotos propias | ❌ | ❌ | ✅ |

---

## 5. Ejemplo completo

**Panadería Rumbo al Este**

| Dimensión | Valor |
|---|---|
| Categoría | Panadería y dulces → panadería artesanal |
| Atributos | desayuno, take away, wifi |
| Contextos | turismo, gastronómico |

---

## 6. Escalabilidad futura

Si la cantidad de atributos supera los ~50, agruparlos en **grupos de atributos** (ej: "Servicios", "Accesibilidad", "Gastronomía") para organizar el admin. No es necesario ahora.
