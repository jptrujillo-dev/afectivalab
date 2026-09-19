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

## 5. Ilustraciones de personajes (hero, etapas, casos prácticos)

Esto es distinto a los íconos de arriba: son **ilustraciones de personajes** (familia, niños), como las de la imagen de referencia que compartiste. Yo no puedo generar este tipo de arte ilustrado directamente (no tengo una herramienta de generación de imágenes; solo puedo dibujar formas geométricas simples a mano en SVG, que no da un resultado creíble para caras/personajes). Por eso van aquí como prompts para que los generes con una IA de imagen, igual que el resto del set.

**Estilo**: el mismo que tu imagen de referencia — ilustración infantil tipo "EdTech/parenting app", con **cabezas grandes y proporciones tipo chibi** (más infantil y juguetón que un ilustración de adulto realista), ojos simples, mejillas sonrosadas, sombreado suave. Esto es justo lo que hace que la referencia "se sienta para niños"; el bloque de abajo lo pide explícitamente. Tonos de piel y cabello variados/diversos (no se restringen a la paleta de marca); la ropa sí puede usar los colores de marca (morado #6C4FD6 / #4E36A8, verde #38B36B / #279250) para mantener consistencia visual con el resto del sitio.

**Bloque de estilo maestro para personajes (pegar al inicio de cada prompt):**
```
Flat vector character illustration, warm and playful children's/parenting-app style (like modern flat illustration packs used in EdTech and parenting apps — think Freepik-style flat character illustrations). Childlike, slightly oversized rounded heads relative to the body (chibi-like proportions), simple dot or oval eyes with a small white highlight, rosy round cheek blush, simple curved smiling mouth, soft cel-shaded coloring with gentle one-tone shadows (no harsh outlines, no photorealism, no 3D render). Diverse, warm skin tones and varied hair styles/colors. Clothing may use these brand accent colors: purple #6C4FD6, green #38B36B. Transparent or plain white background, no text, no logos, no watermarks in the image.
```

### 5.1 Ilustración del hero (familia)

Va al lado derecho del texto principal de la home, como la ilustración de familia de tu referencia.

```
[bloque de estilo maestro para personajes]
Subject: a happy family portrait, front-facing, from the waist up — a father and mother close together with their child in the middle, all smiling warmly, in a gentle group-hug pose. One parent wears a piece of clothing in the purple accent color, the other in the green accent color, to tie into the brand. Warm, affectionate, reassuring mood. Square or portrait aspect ratio, plenty of even padding around the group so it can be placed next to text.
```

### 5.2 Ilustraciones de las 5 etapas (una por tarjeta de edad)

Un niño/a por tarjeta, medio cuerpo o solo cabeza y hombros, mirando al frente, sonriendo. Varía género, edad aparente y color de ropa/accesorio entre las 5 para que se sientan diferentes personas, no la misma repetida.

```
[bloque de estilo maestro para personajes]
Subject: a single [DESCRIPCIÓN], head-and-shoulders portrait, facing forward, smiling warmly at the viewer. Plain transparent background, generous padding, centered.
```

| Etapa | DESCRIPCIÓN |
|---|---|
| 3–5 años | happy toddler with a small tuft of hair, wearing a green t-shirt |
| 6–8 años | cheerful young boy with short dark hair, wearing a purple t-shirt, small backpack strap visible |
| 9–11 años | cheerful girl with hair in two braids, wearing a green jacket |
| 12–14 años | preteen boy with curly hair, wearing a purple hoodie |
| 15–17 años | teenage girl with long straight hair, wearing a green jacket, slightly more grown-up styling than the younger ones |

### 5.3 Ilustración de "Casos prácticos"

El niño pensativo con el globo de diálogo/casita, como en tu referencia.

```
[bloque de estilo maestro para personajes]
Subject: a young child sitting, resting their chin on both hands, looking thoughtful and slightly worried, wearing a backpack strap visible on one shoulder. Above/beside them, a small simple thought bubble containing a tiny flat school-house icon. Warm and gentle mood, not sad or distressing — this represents a child a parent is trying to understand, not a crisis. Transparent background, generous padding.
```

### 5.4 (Opcional) Avatares de testimonios

Hoy usamos círculos de color con la inicial del nombre (simple y funcional). Si prefieres avatares ilustrados como en tu referencia:

```
[bloque de estilo maestro para personajes]
Subject: a single adult parent, head-and-shoulders portrait, friendly warm smile, facing forward. Plain transparent background, centered, small even padding — will be displayed as a small circular avatar (crop-safe: keep the face centered with margin on all sides).
```

Genera una versión distinta por cada testimonio (María, Carlos, Ana) variando género/edad/apariencia.

### Cuando tengas los resultados

Compárteme los PNG (idealmente 1024px o más, fondo transparente) y yo los integro directamente en el theme: la ilustración del hero reemplaza/acompaña la tarjeta de la app, las 5 de etapas reemplazan los íconos actuales de "brote de hojas" en `stages.php`, y la de casos prácticos se agrega junto al demo interactivo en `showcase.php`. No hace falta que los recortes queden perfectos — lo ajusto con CSS.

---

## Notas finales

- **Consistencia es lo más importante**: mejor generar todo el set con la misma herramienta y el mismo bloque de estilo, en una sola sesión, que mezclar estilos de distintas IAs.
- Si el resultado sale con **texto/letras no pedidas** o con **degradados/sombras 3D**, es la señal más común de que el modelo ignoró el bloque de estilo — regenera insistiendo en "flat, no gradient, no text" al final del prompt.
- Todo lo generado con IA sale en **PNG rasterizado**, no vectorial. Para uso real en el sitio (favicon, iconos que escalan) puede convenir pasarlos luego a SVG (vectorizado) o simplemente usarlos como PNG a suficiente resolución si no se van a escalar mucho.
- Cuando tengas los resultados, compártelos aquí y los organizamos dentro de `brand/` junto con lo que ya existe.
