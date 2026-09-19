# Prompts para generar imágenes con IA

Documento vivo: acá solo quedan los prompts **pendientes de generar**. Lo ya entregado se archiva abajo tachado, con dónde vive el resultado — no hace falta releer el prompt completo para saber si algo ya está hecho.

## Cómo usarlos

1. Copia el **bloque de estilo maestro** correspondiente (íconos planos vs. personajes ilustrados, son distintos) al inicio del prompt.
2. Pide siempre **fondo transparente**, formato **PNG** (nunca JPEG — no soporta transparencia; ninguna IA de imagen genera SVG real) y **sin texto/letras**.
3. Resolución mínima **1024×1024** (avatares chicos pueden ir en 512×512), proporción **1:1** siempre.
4. Genera varias variaciones y elegí la más consistente con el resto del set ya entregado antes de seguir.

## Bloque de estilo maestro — íconos planos (logo, categorías, gamificación, lecciones)

```
Flat 2D vector icon illustration, thick clean rounded outlines, minimal flat coloring with a single darker shade used only as a thin bottom-edge rim for subtle depth (no gradients, no realistic shading, no drop shadows, no 3D render, no glossy highlights). Playful but professional style, similar to modern gamified learning app icons (Duolingo-like), NOT childish or overly cartoonish, NOT clipart. Centered composition with even padding, square canvas, transparent background. No text, no letters, no numbers in the image.
Color palette (use only these): primary purple #6C4FD6, dark purple accent #4E36A8, primary green #38B36B, dark green accent #279250, dark ink #2B2140 for outlines/details when needed.
```

## Bloque de estilo maestro — personajes ilustrados (familia, niños)

```
Flat vector character illustration, warm and playful children's/parenting-app style (like modern flat illustration packs used in EdTech and parenting apps — think Freepik-style flat character illustrations). Childlike, slightly oversized rounded heads relative to the body (chibi-like proportions), simple dot or oval eyes with a small white highlight, rosy round cheek blush, simple curved smiling mouth, soft cel-shaded coloring with gentle one-tone shadows (no harsh outlines, no photorealism, no 3D render). Diverse, warm skin tones and varied hair styles/colors. Clothing may use these brand accent colors: purple #6C4FD6, green #38B36B. Transparent background, no text, no logos, no watermarks in the image.
```

---

## Pendientes

Nada por ahora. Las páginas de registro/ingreso (ver bitácora) reutilizaron `hero-familia.webp` ya existente — no hicieron falta imágenes nuevas.

Cuando surja algo nuevo (por ejemplo, ilustraciones para "mi cuenta", certificados, o los propios cursos), va acá con: nombre de archivo sugerido, resolución, y el prompt completo ya armado (estilo + sujeto en un solo bloque, listo para copiar).

---

## Archivo — ya entregado

- ~~**1. Marca / isotipo** (logo color/blanco, favicon)~~ → `brand/afectivalab-v3/logo/`, `brand/afectivalab-v3/favicon/`
- ~~**2. Categorías / "mundos" temáticos** (8 insignias)~~ → `brand/afectivalab-v3/categorias/`
- ~~**3. Gamificación** (racha, XP, insignia, monedas, mapa, certificado, misión)~~ → `brand/afectivalab-v3/gamificacion/`
- ~~**4. Estructura de lección** (video, caso interactivo, evaluación, recurso, misiones)~~ → `brand/afectivalab-v3/lecciones/` y `lecciones-morado/`
- ~~**5. Personajes ilustrados** (familia del hero, 5 niños de etapas, niño de casos prácticos, 3 avatares)~~ → `brand/afectivalab-v3/afectivalab_personajes/` (originales) e integrados como WebP en `wp-content/themes/afectivalab/assets/images/`

Los prompts completos de cada uno quedaron en el historial de git de este archivo (`git log -- docs/prompts-iconos-ia.md`) por si alguna vez hay que regenerar algo en el mismo estilo.
