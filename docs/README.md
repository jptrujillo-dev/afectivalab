# Documentación del proyecto — Afectivalab

Esta carpeta guarda el registro de trabajo del proyecto en formato Markdown. No es documentación de usuario final, es bitácora técnica del desarrollo.

## Índice

- [roadmap.md](roadmap.md) — lo que se hará: features, tareas y decisiones pendientes, en orden de prioridad.
- [bitacora.md](bitacora.md) — lo que se está haciendo / se hizo: registro cronológico de avances, cambios y decisiones tomadas.
- [concepto-plataforma.md](concepto-plataforma.md) — qué es la plataforma y cómo funciona para el usuario final (padres/madres): rutas por edad, mecánica de misiones, gamificación, asistente de IA. Incluye las preguntas abiertas para resolver en reunión.
- [brand.md](brand.md) — identidad visual vigente: paleta, tipografía, estructura del paquete de assets en `brand/afectivalab-v3/`.
- [prompts-iconos-ia.md](prompts-iconos-ia.md) — prompts de referencia usados para generar el set de iconos con IA (por si se necesita regenerar o extender el set más adelante).

## Convención

- Cada entrada nueva en `bitacora.md` se agrega arriba (orden cronológico inverso) con fecha.
- Cuando una tarea de `roadmap.md` se completa, se mueve el detalle a `bitacora.md` y se retira o marca en el roadmap.
- Si un tema crece demasiado (ej. una migración grande, integración específica), se le da su propio archivo dentro de `docs/` y se enlaza desde aquí.
- Antes de asumir que algo de estos documentos sigue vigente, contrastarlo con el estado real del proyecto (código, `brand/`, etc.) — los docs pueden quedar desactualizados si no se revisan al cerrar una tarea.
