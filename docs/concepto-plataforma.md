# Concepto de la plataforma — Afectivalab

Fuente: documento de concepto compartido por el usuario (2026-09-19) + audio explicativo del mismo día ampliando y matizando el documento. Este archivo integra ambos como referencia de trabajo, no reemplaza los originales.

## 1. Idea central

Una plataforma de **acompañamiento psicológico para los padres/madres** (el producto es para el padre, no directamente para el niño) en la crianza y el bienestar familiar, organizada como **rutas de aprendizaje por hijo**, similar en experiencia a Duolingo pero para educación emocional y de crianza.

Objetivo explícito (del audio): que **no sea plana** como plataformas de cursos/videoclases tradicionales, sino más interactiva y experiencial.

Flujo básico:

1. El padre/madre se registra y crea **un perfil por cada hijo** (nombre, edad, etapa escolar).
2. Se le pregunta qué situaciones le preocupan actualmente (bullying, autoestima, celular, límites, sexualidad, ansiedad, amistades, rendimiento escolar, comunicación, etc.).
3. Con esos datos, la plataforma genera **una ruta personalizada** para ese hijo: una secuencia de temas/microcursos representados como un mapa vertical (nodos conectados, "🟢 completado", "🔵 pendiente").
4. La ruta es distinta según el grupo de edad del hijo.

## 2. Grupos de edad (5 etapas)

| Etapa | Edad | Enfoque principal |
|---|---|---|
| Primera infancia | 3–5 años | vínculo, emociones básicas, límites, autonomía, protección corporal, inicio de educación sexual |
| Niñez inicial | 6–8 años | autoestima, amistades, bullying inicial, normas, comunicación, uso de pantallas |
| Niñez media | 9–11 años | pubertad temprana, presión social, bullying/cyberbullying, autoestima, internet, cambios emocionales |
| Adolescencia inicial | 12–14 años | identidad, redes sociales, sexualidad, consentimiento, presión de grupo, comunicación y límites |
| Adolescencia media/tardía | 15–17 años | relaciones de pareja, sexualidad responsable, autonomía, proyecto de vida, salud emocional, toma de decisiones |

## 3. Categorías temáticas ("mundos")

En vez de un catálogo plano de "Cursos", la navegación se organiza como mapa de mundos temáticos:

- 🌱 **Crecer seguro** — autoestima, confianza, autonomía, resiliencia
- ❤️ **Conectar** — comunicación, vínculo, escucha, afectividad
- 🟢 **Emociones** — rabia, frustración, ansiedad, miedos, autorregulación
- 🛡️ **Proteger** — bullying, violencia, abuso, seguridad personal
- 🌐 **Mundo digital** — pantallas, videojuegos, redes sociales, cyberbullying, pornografía, grooming
- 👨👩👧 **Convivir** — normas, límites, disciplina, responsabilidades
- 💬 **Sexualidad y afectividad** — cuerpo, consentimiento, pubertad, relaciones
- 🏫 **Vida escolar** — motivación, aprendizaje, amistades, presión académica

Cada mundo tiene varios caminos adaptados por edad.

**Aclaración del audio**: la categorización por "mundos" existe como concepto (similar a agrupar diplomados por área: calidad, seguridad, etc.), pero el usuario aclaró que el **motor principal de navegación no son los mundos temáticos libres**, sino las **rutas por edad + prioridades del padre** (ver flujo en sección 1). Los mundos podrían quedar como un filtro/vista secundaria, no como el mecanismo central — hacer todo completamente navegable por mundos "sería volar mucho" respecto al alcance actual. A confirmar en la reunión si los mundos se implementan en el MVP o quedan para después.

## 4. Estructura de un curso y de una clase

**Curso** (ej. "Bullying · 8–10 años"): 6–8 microclases que se completan progresivamente, terminando en una **misión final** (caso integrador) que otorga una "habilidad adquirida".

**Cada microclase** (10–15 min total) combina:

| Elemento | Duración |
|---|---|
| Video principal | máx. 12 min |
| Caso interactivo | 2–4 min |
| Mini evaluación | 2–3 min |
| Recurso descargable | 1 página |
| Misión para casa | durante la semana |

No todas las clases usan todos los elementos; varían (video+caso+preguntas, video+checklist+caso, video+conversación simulada, solo caso interactivo, actividad padre-hijo, etc.)

**Aclaración del audio sobre recursos y misiones:**
- Recursos descargables: PDF, láminas, infografías — el formato queda a criterio de quien produce el contenido (docente/psicólogo a cargo).
- Hay **dos tipos de misión**, no uno solo:
  1. **Misión tipo "taller"**: el padre debe subir algo a la plataforma como evidencia (ej. registro fotográfico) de que la realizó.
  2. **Misión para casa**: una actividad a desarrollar en familia, sin necesidad de subir evidencia, solo marcarla como hecha.
- Las mini evaluaciones son, sobre todo, una herramienta de planificación para el equipo docente/de contenido (ya se sabe cómo construirlas); no son el foco de la conversación de producto en este momento.

## 5. Minievaluaciones — mecánica diferenciadora

Después de una clase, se presenta un **caso con opciones de decisión** (tipo dilema, ej. "tu hijo de 8 años dice que no quiere volver al colegio", con opciones A/B/C/D).

Puntos clave de esta mecánica:
- No es un simple ✅/❌. Cada elección recibe una **explicación razonada** ("Buena elección. Primero conviene..." o similar), no solo si acertó.
- Después de la respuesta, el caso **continúa** con una nueva pregunta ("¿Qué harías ahora?"), como una historia interactiva ramificada, no una evaluación de una sola pregunta.

## 6. Misiones (acción fuera de la pantalla)

Al final de una clase se asigna una **misión en familia**: una acción concreta para hacer con el hijo (ej. "Pregúntale a tu hijo: ¿qué fue lo que mejor hiciste esta semana aunque te haya costado?").

El padre marca el estado:
- ☑ Lo hice
- ☐ Lo haré después

Completar misiones otorga puntos. El objetivo es que la plataforma **provoque acciones reales**, no solo consumo de contenido.

**Aclaración del audio — mecánica de recompensa concreta:**
- Al marcar una misión como completada (o subir la evidencia en el caso de misión-taller), se otorgan **monedas y/o estrellas**, similar a marcar como leído/descargado un recurso (aparece un check).
- Monedas/estrellas alimentan la **experiencia (XP)** del padre.
- Cada misión completada también avanza el **% de progreso del curso**.
- Al terminar un curso completo se obtiene una **"habilidad adquirida"** (ej. "Prevención y manejo del bullying") **y un certificado**. La idea es que el padre vaya acumulando certificados/habilidades a lo largo del tiempo, no solo insignias simbólicas.

## 7. Asistente con IA — "Tengo una situación ahora"

Botón/funcionalidad marcada como **muy importante**: un canal para que el padre describa un problema real que está viviendo ahora mismo (no un curso), y la IA responda con:

1. Qué hacer primero.
2. Qué evitar.
3. Preguntas sugeridas para hacerle al hijo.
4. Señales de alerta que requieren ayuda profesional.
5. Si involucra al colegio, pasos a seguir.
6. Recomendación del curso correspondiente al final.

Esto convierte la plataforma de "educativa" a **herramienta de acompañamiento activo**, no solo contenido pregrabado.

**Aclaración del audio**: esta funcionalidad es **deseable pero no imperativa/no bloqueante** para el desarrollo. El usuario pidió explícitamente investigar viabilidad técnica antes de comprometerla como parte del alcance inicial (a diferencia de las rutas, que sí son core del producto).

## 8. Personalización / home del usuario

En el perfil, en vez de un catálogo, se muestra:
- Saludo personalizado ("Buenas tardes, MARIA — Esta semana con tu hijo de 8 años").
- Una recomendación puntual con razón contextual (ej. "Cómo saber si un niño está teniendo problemas con sus compañeros — Porque Mateo se encuentra en una etapa en la que las relaciones sociales comienzan a tener mayor importancia").
- Progreso visual de la ruta actual (ej. barra de círculos llenos/vacíos).

La ruta **evoluciona automáticamente** conforme el hijo cumple años (concepto de "GPS de crianza": la app avisa periódicamente qué temas conviene trabajar en los próximos meses según la edad, ej. "tu hijo va a cumplir 9 años, puedes abarcar estos temas" — vía notificación o recordatorio).

**Aclaración del audio**: además de la sugerencia de siguiente curso, el perfil también debe mostrar **recordatorios de cursos pendientes/sin terminar** (ej. "Buenas tardes María — esta semana con tu hijo de 8 años, no te olvides de culminar el curso"), como mecanismo de retención y avance, no solo de descubrimiento de contenido nuevo.

## 9. Gamificación (inspirada en Duolingo)

- 🔥 Racha semanal
- ⭐ XP parental
- 🏅 Insignias
- 🗺️ Mapas desbloqueables
- 🎯 Misiones familiares
- 📊 Progreso
- 🏆 Retos de situaciones reales

**Aclaración del audio — prioridad dentro de la gamificación**: de esta lista, el usuario marcó como prioritarios solo dos conceptos:
1. **Insignias como habilidades adquiridas** (ligadas a completar cursos, ver sección 6).
2. **Misiones que otorgan experiencia** (ver sección 6).

El resto (rachas, mapas desbloqueables, retos, etc.) quedan como **sugerencias abiertas**, con libertad para el equipo de desarrollo/diseño de decidir cuáles implementar y cómo.

## 10. Alcance de contenido

**Matriz inicial**: 5 etapas de edad × 8 temas fundamentales → **40 rutas iniciales**, reutilizando contenido entre rutas donde aplique.

**Temas prioritarios (orden de desarrollo sugerido en el documento fuente):**
1. Autoestima y seguridad emocional
2. Bullying y cyberbullying
3. Educación sexual y protección
4. Comunicación padre-hijo
5. Manejo emocional
6. Pantallas, redes sociales y seguridad digital
7. Normas, disciplina y límites
8. Amistades y relaciones sociales

**Fase posterior (no prioritaria):** TDAH, TEA, separación de padres, duelo, problemas de conducta, hábitos de estudio, alimentación, sueño, adolescencia, orientación vocacional.

## 11. Visión / objetivo declarado

> "Una plataforma que acompaña a los padres durante cada etapa del crecimiento de sus hijos."

Del audio: la intención es que una familia **entre desde que el hijo es pequeño y se quede hasta los 17 años**, capacitándose de forma continua — de ahí el concepto de "GPS de crianza" con notificaciones/alertas conforme el hijo va cumpliendo años y cambiando de etapa.

## 12. Referencia visual

El usuario quiere que la experiencia visual se parezca a **Duolingo**: una ruta no plana, de forma curva (no una lista vertical recta), gamificada e interactiva, mezclando nodos de video con nodos de cuestionario/caso. Menciona también como referencia otra plataforma ya vista (club de lectura) por su nivel de interactividad visual.

Pendiente por parte del usuario:
- Enviar una **imagen de referencia** del menú/pantalla de inicio (aún no compartida en esta conversación).
- Explicar el funcionamiento con más detalle para que, a partir de esa pantalla de inicio, se puedan derivar las demás pantallas.

Da libertad de diseño/implementación dentro de lo técnicamente razonable.

## Preguntas abiertas para la reunión (usuario mencionó reunión al día siguiente de este audio)

Dudas que el propio usuario dejó explícitamente abiertas o que quedan ambiguas entre el documento y el audio:

1. **Rango de edad exacto del segundo grupo**: el documento dice 6–8 años ("Niñez inicial"), pero en el audio el usuario dice en un momento "de 6 hasta los 9 años" al dar el ejemplo de un hijo de 8 años. Confirmar si el corte real es 6–8 o 6–9 (esto afectaría los límites de las 5 etapas y el total de "40 rutas").
2. **Mundos temáticos vs. rutas por edad**: confirmar si los "mundos" (sección 3) se implementan como filtro/vista adicional en el MVP, o quedan fuera del alcance inicial (el usuario se inclina por dejar el motor principal solo en rutas por edad + intereses, por simplicidad).
3. **Asistente de IA ("Tengo una situación ahora")**: no es requisito bloqueante; queda pendiente investigar viabilidad (proveedor, costos, seguridad/derivación ante señales de riesgo real) antes de comprometerlo al alcance.
4. **Certificación**: el audio menciona certificados por curso completado — falta definir si es un PDF descargable generado automáticamente, una insignia visual, o ambos.
5. **Motor de contenido / plataforma base**: aún no definido si se construye sobre un LMS/plugin de WordPress (LearnDash, Tutor LMS, etc.) o a medida — el usuario no lo ha mencionado, es una decisión técnica pendiente de nuestro lado.
6. **Modelo de negocio/acceso** (suscripción, pago por hijo, etc.): no mencionado aún en documento ni audio.

## Notas para el desarrollo (a definir más adelante, no ahora)

- Modelo de datos: perfiles de padre + N perfiles de hijo, progreso por ruta/curso/clase por hijo, registro de misiones (con o sin evidencia subida).
- ¿Multi-hijo implica que el padre alterna entre "vistas" de ruta por hijo?
- Motor de contenido: ¿cursos como CPT de WordPress, o LMS externo/plugin versus solución a medida?
- Sistema de gamificación (monedas, estrellas, XP, insignias/habilidades, certificados) — persistencia y lógica de reglas.
- Sistema de subida de evidencia para misiones tipo "taller" (almacenamiento de imágenes, moderación/revisión si aplica).
- Sistema de notificaciones/recordatorios (cursos pendientes + "GPS de crianza" por cumpleaños del hijo).
