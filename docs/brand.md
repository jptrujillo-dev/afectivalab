# Marca — Afectivalab

Paquete de marca vigente: **v3**, en [brand/afectivalab-v3/](../brand/afectivalab-v3/). Reemplaza la primera propuesta hecha en esta conversación (isotipo simple con sombra inferior) — esos archivos ya se retiraron del proyecto para no dejar dos versiones sueltas con el mismo nombre.

## Concepto del isotipo (v3)

Corazón compacto de lóbulos redondeados con un brote de dos hojas — afecto + crecimiento. A diferencia de la v1, es una **silueta plana sin borde/sombra inferior** (más limpia, menos "sticker 3D").

## Paleta de color

| Token | Hex | Uso |
|---|---|---|
| Morado primario | `#6C4FD6` | Color principal de marca |
| Morado oscuro | `#4E36A8` | Acentos, fondo de insignias/categorías en tono morado |
| Verde primario | `#38B36B` | Color secundario de marca |
| Verde oscuro | `#279250` | Acentos, fondo de insignias/categorías en tono verde |
| Tinta (texto/glifos oscuros) | `#2B2140` | Wordmark "afectiva", iconos de lección en línea oscura |
| Fondo claro (tarjeta favicon) | `#FBFAFF` | Fondo del favicon |
| Marfil (excepción puntual) | — | Solo para el diploma del icono "certificado" |

## Tipografía

**Fredoka SemiBold**, ya **convertida a trazos (paths)** dentro de los SVG del wordmark — no depende de tener la fuente instalada ni cargada vía Google Fonts para verse igual en cualquier parte. Para texto real de UI (no logo) en el sitio, sigue siendo la fuente recomendada a cargar vía Google Fonts.

## Estructura del paquete (`brand/afectivalab-v3/`)

```
afectivalab-v3/
├── README.md              — notas de uso del propio paquete
├── manifest.json          — índice de los 25 assets (grupo, nombre, rutas svg/png)
├── vista-previa.png       — lámina de referencia con todo el set (no es un sprite para usar en producción)
├── marca-v3.png           — logo + favicon en varios tamaños
├── logo/                  — isotipo y logo horizontal, color y blanco (svg + png 1024px / 2048px de ancho)
├── favicon/                — favicon.svg, favicon.ico (multi-resolución 16/32/48/64) y PNG sueltos (16/24/32/48/64/180/192/512)
├── categorias/            — 8 insignias circulares (una por eje temático)
├── gamificacion/          — 7 iconos (racha, XP, insignia, monedas, mapa, certificado, misión/reto)
├── lecciones/             — 6 iconos de estructura de clase, en tinta oscura
└── lecciones-morado/      — los mismos 6 iconos de lecciones, variante en morado
```

Cada asset viene en **SVG editable** y **PNG transparente**. Tamaño de uso recomendado (según el propio README del paquete): lecciones a 24px, categorías a 40px o más. Las versiones blancas necesitan fondo de color para verse (no tienen contraste sobre blanco).

### Categorías (8)
Crecer seguro, Conectar, Emociones, Proteger, Mundo digital, Convivir, Sexualidad y afectividad, Vida escolar — cada una como insignia circular en morado o verde con un glifo blanco simple adentro.

### Gamificación (7)
Racha semanal, XP/experiencia, Insignia/habilidad, Monedas, Mapa desbloqueable, Certificado, Misión/reto.

### Lecciones (6, ×2 variantes de color)
Video principal, Caso interactivo, Mini evaluación, Recurso descargable, Misión para casa, Misión tipo taller (con evidencia).

## Favicon — snippet de uso

```html
<link rel="icon" href="/brand/favicon/favicon.ico" sizes="any">
<link rel="icon" type="image/svg+xml" href="/brand/favicon/favicon.svg">
<link rel="apple-touch-icon" href="/brand/favicon/icon-180.png">
```

Verificado en esta sesión: `favicon.ico` contiene las 4 resoluciones (16/32/48/64) tal como indica el README del paquete; los SVG son XML bien formado; los PNG de favicon miden lo que dicen sus nombres (16/32/180/512px confirmados).

## Pendiente / decisiones abiertas

- **Validación de contenido "sensible"**: el icono de "Sexualidad y afectividad" se mantiene deliberadamente abstracto (globo de diálogo + corazón), sin imaginería corporal literal — revisar que el equipo de contenido esté de acuerdo con ese nivel de abstracción antes de darlo por definitivo.
- **Integración al theme**: estos assets aún no están conectados a ningún theme de WordPress (seguimos sin desarrollo activo). Cuando se arme el theme, este paquete es la fuente de verdad para logo, favicon e iconografía de categorías/gamificación/lecciones.
- Se eliminaron los archivos de la propuesta v1 (`brand/logo/`, `brand/favicon/`, `brand/reference/` a nivel raíz de `brand/`) para que no quede ambigüedad sobre cuál es la versión vigente.
