# Prompts para generar iconos con IA de imagen

Paquete de prompts listos para usar en Midjourney, DALL·E, Ideogram, Recraft, etc. Escritos en inglés porque casi todos los modelos de imagen entienden y siguen mejor instrucciones en inglés que en español, incluso si tu marca es en español.

## Cómo usarlos

1. **Sube como referencia de estilo** las imágenes en [reference/](../brand/reference/) (`estilo-referencia-icono.png` y `estilo-referencia-logo.png`) — son el render del isotipo que ya definimos (corazón + hoja). La mayoría de herramientas aceptan una imagen de referencia:
   - Midjourney: `--sref <url-de-la-imagen>` (tienes que subir la imagen a una URL pública o usar `/blend`).
   - DALL·E / ChatGPT: adjunta la imagen y pide "match this exact flat icon style".
   - Ideogram / Recraft: tienen un campo de "style reference" o "character reference" directo para subir el archivo.
2. Copia el **bloque de estilo maestro** (abajo) al inicio de cada prompt — es lo que mantiene consistencia entre todos los iconos del set.
3. Pide siempre **fondo transparente o blanco liso** y **sin texto/letras** (los modelos de imagen no renderizan texto de forma confiable; el wordmark "afectivalab" lo manejamos aparte, con la tipografía real Fredoka, no generado por IA).
4. Pide **varias variaciones** (4 semillas / regenerar 2-3 veces) y elige la más consistente con el resto del set antes de seguir con el siguiente icono.
5. Resolución mínima recomendada: **1024×1024 px**, cuadrada, para que se puedan escalar hacia abajo sin perder nitidez.

## Bloque de estilo maestro (pegar al inicio de cada prompt)

```
Flat 2D vector icon illustration, thick clean rounded outlines, minimal flat coloring with a single darker shade used only as a thin bottom-edge rim for subtle depth (no gradients, no realistic shading, no drop shadows, no 3D render, no glossy highlights). Playful but professional style, similar to modern gamified learning app icons (Duolingo-like), NOT childish or overly cartoonish, NOT clipart. Centered composition with even padding, square canvas, transparent or plain white background. No text, no letters, no numbers in the image.
Color palette (use only these): primary purple #6C4FD6, dark purple accent #4E36A8, primary green #38B36B, dark green accent #279250, dark ink #2B2140 for outlines/details when needed.
```

---

## 1. Marca / isotipo principal

Ya tenemos una versión propia (ver [brand/logo/](../brand/logo/)), pero si quieres explorar alternativas con IA antes de decidir:

**Icono a color:**
```
[bloque de estilo maestro]
Subject: a single heart shape with a small leaf sprouting from the top center, symbolizing affection and family growth. Heart filled in primary purple, leaf filled in primary green, both with the dark-shade bottom rim for depth.
```

**Versión blanca (para fondos de color):**
```
[bloque de estilo maestro]
Subject: the exact same heart-with-leaf icon, but entirely in solid white, with the bottom-rim depth detail shown as white at ~35% opacity. Meant to be placed on a solid purple or green background.
```

**Favicon / app icon (con fondo tipo tarjeta):**
```
[bloque de estilo maestro]
Subject: the heart-with-leaf icon centered on a rounded-square card background in very light lavender (#FBFAFF), like a mobile app icon. Slightly bolder, simplified details so it stays legible at very small sizes (16-32px).
```

---

## 2. Iconos de categorías / "mundos" temáticos

Son 8 insignias circulares, una por eje temático de la plataforma. Pide que cada una sea un círculo o cuadrado redondeado de color (alternando tonos morado/verde de la paleta para diferenciarlas sin salirse de la marca) con un glifo simple en blanco o en tinta oscura adentro.

**Plantilla base (repetir cambiando "Subject" y el color de fondo):**
```
[bloque de estilo maestro]
Subject: a circular badge icon, background filled solid [COLOR], with a simple centered line-glyph icon in white inside representing [CONCEPTO]. The glyph should be simple enough to read clearly at 40px size.
```

| # | Categoría | COLOR de fondo sugerido | CONCEPTO (glifo) |
|---|---|---|---|
| 1 | Crecer seguro | primary green | a small sprouting plant / seedling |
| 2 | Conectar | primary purple | two hands gently holding, or a heart with a small sound/speech wave |
| 3 | Emociones | dark purple accent | a simple face outline surrounded by three small emotion clouds/bubbles |
| 4 | Proteger | dark green accent | a rounded shield with a small heart inside |
| 5 | Mundo digital | primary purple | a rounded tablet/screen shape with a small wifi signal above it |
| 6 | Convivir | primary green | a simple house shape with a small family silhouette (two adults, one child) inside |
| 7 | Sexualidad y afectividad | dark purple accent | a speech bubble with a small heart inside (keep it soft/abstract, not literal body imagery) |
| 8 | Vida escolar | dark green accent | a simple open book with a small graduation cap above it |

---

## 3. Iconos de gamificación

**Plantilla:**
```
[bloque de estilo maestro]
Subject: [CONCEPTO], as a single flat icon (no badge/circle background this time, just the icon shape itself with even padding).
```

| Concepto | CONCEPTO (descripción para el prompt) |
|---|---|
| Racha semanal | a stylized flame icon, filled in primary purple with dark-purple bottom rim shading |
| XP / experiencia | a five-pointed star with a small burst/sparkle lines around it, filled in primary green |
| Insignia / habilidad adquirida | a round medal with a ribbon tail at the bottom, medal face in primary purple, ribbon in primary green |
| Monedas | a single round coin, filled in primary green with a darker green rim, subtle simple emboss line near the edge (no numbers or symbols on the face) |
| Mapa desbloqueable | a simple winding path/route icon with a small location pin at the end, path in dark purple accent, pin in primary green |
| Certificado | a rolled diploma/scroll tied with a ribbon, scroll in a warm off-white, ribbon in primary purple |
| Misión / reto | a small flag on a short pole, flag filled in primary green, pole in dark ink |

---

## 4. Iconos de estructura de lección

Estos van dentro de cada microclase (ver estructura del curso), tamaño pequeño tipo bullet/label icon.

**Plantilla:**
```
[bloque de estilo maestro]
Subject: [CONCEPTO], small flat outline-style icon, single color in dark ink (#2B2140) or primary purple, very simple, works at 24px size.
```

| Elemento | CONCEPTO |
|---|---|
| Video principal | a rounded play-button triangle inside a rounded square frame |
| Caso interactivo | a speech bubble with a small branching fork/path inside it |
| Mini evaluación | a small clipboard with a single checkmark |
| Recurso descargable | a simple document/page shape with a small downward arrow below it |
| Misión para casa | a small house outline with a tiny heart inside |
| Misión tipo taller (con evidencia) | a small camera or upload-arrow icon, to represent uploading a photo as proof |

---

## Notas finales

- **Consistencia es lo más importante**: mejor generar todo el set con la misma herramienta y el mismo bloque de estilo, en una sola sesión, que mezclar estilos de distintas IAs.
- Si el resultado sale con **texto/letras no pedidas** o con **degradados/sombras 3D**, es la señal más común de que el modelo ignoró el bloque de estilo — regenera insistiendo en "flat, no gradient, no text" al final del prompt.
- Todo lo generado con IA sale en **PNG rasterizado**, no vectorial. Para uso real en el sitio (favicon, iconos que escalan) puede convenir pasarlos luego a SVG (vectorizado) o simplemente usarlos como PNG a suficiente resolución si no se van a escalar mucho.
- Cuando tengas los resultados, compártelos aquí y los organizamos dentro de `brand/` junto con lo que ya existe.
