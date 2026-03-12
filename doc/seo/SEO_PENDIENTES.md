# SEO — Pendientes

Auditoría basada en Seobility SEO Checker (2026-03-11) para `https://guialocal.uy/`

**Puntuación inicial: 76%**

## Resumen por sección

| Sección | Score | Estado |
|---|---|---|
| Metadatos | 100% | OK |
| Estructura de página | 95% | OK |
| Servidor | 98% | OK |
| Enlaces | 63% | Mejorable |
| Calidad de página | 48% | Problema |
| Factores externos | 3% | Crítico |

## Resuelto (2026-03-11)

- [x] H1 alineado con title y meta description
- [x] Encabezados H2 únicos y descriptivos
- [x] Encabezados H3 verificados — sin duplicados (sectores, negocios, newsletter: todos únicos)
- [x] Más contenido textual en home (+sección SEO descriptiva)
- [x] Enlaces internos contextuales (mapa, categorías, abiertos, contacto)
- [x] **Redes sociales / compartir**: botones share en home y ficha de negocio (WhatsApp, Facebook, X, copiar link)
- [x] **Apple Touch Icon**: generado apple-touch-icon.png (180x180) desde favicon.png + meta tag en layout

## Pendientes — Código / On-page

- [~] **Rendimiento**: tiempo de respuesta 0.65s (recomendado < 0.4s). Optimizado: query única para destacados por sector (eliminadas N+1), zona preferida resuelta desde colección en memoria, cachés de rutas/config/vistas activadas. Pendiente: medir mejora en producción
- [x] **Enlaces externos**: agregados links a redes sociales (Instagram, Facebook, X) en footer con rel="noopener"
- [x] **URLs con parámetros dinámicos**: agregado rel="nofollow" a links con ?abiertos=1 y ?zona=slug
- [~] **Textos ancla repetidos/largos**: las cards de negocio son `<a>` completos (nombre+desc+categoría = texto ancla largo y "Ver →" repetido). Trade-off UX vs SEO — no se puede mejorar sin romper el diseño de cards clickeables

## Pendientes — Factores externos (linkbuilding)

- [ ] Registrar en Google Business Profile
- [ ] Crear perfiles en redes sociales (Facebook, Instagram, LinkedIn) con link
- [ ] Registrar en directorios uruguayos (Páginas Amarillas, Gallito, etc.)
- [ ] Contactar medios/blogs locales para nota de lanzamiento
- [ ] Pedir a negocios registrados que enlacen a su ficha desde sus webs
- [ ] Publicar contenido compartible (guías, artículos) para backlinks orgánicos
