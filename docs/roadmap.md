# Roadmap

Lo que se hará, en orden de prioridad. Sin fechas fijas todavía — se ajusta según lo que salga de la reunión con el usuario y del avance real.

## 0. Fundacional (antes de escribir código de la plataforma)

- [ ] Definir si se inicializa control de versiones (git) para el proyecto, y con qué alcance de `.gitignore` (proteger `wp-config.php` y `.vscode/sftp.json`, que contienen credenciales reales).
- [ ] Resolver las preguntas abiertas de producto listadas en [concepto-plataforma.md](concepto-plataforma.md#preguntas-abiertas-para-la-reunión-usuario-mencionó-reunión-al-día-siguiente-de-este-audio):
  - Rango exacto del segundo grupo de edad (¿6–8 u 6–9 años?).
  - ¿Los "mundos" temáticos van en el MVP o quedan para después?
  - ¿Se investiga ya el asistente de IA o se deja totalmente para una fase posterior?
  - Formato del certificado (PDF, insignia visual, o ambos).
  - Modelo de negocio / acceso (suscripción, por hijo, etc.) — aún no mencionado por el usuario.
- [ ] Decidir el motor de contenido/plataforma: LMS vía plugin de WordPress (LearnDash, Tutor LMS, etc.) vs. desarrollo a medida (CPTs propios). Afecta directamente cómo se modelan rutas, cursos, progreso y misiones.
- [ ] Validar con el usuario el paquete de marca v3 ([brand.md](brand.md)) como definitivo, en particular el icono de "Sexualidad y afectividad".

## 1. Modelo de datos y arquitectura (una vez resuelto lo anterior)

- [ ] Modelo de datos: perfiles de padre + N perfiles de hijo, progreso por ruta/curso/clase por hijo.
- [ ] Modelo de las 40 rutas (5 edades × 8 ejes) y cómo se arma la ruta personalizada según edad + preocupaciones marcadas por el padre.
- [ ] Sistema de misiones: distinguir misión "taller" (con evidencia subida) de misión "en casa" (solo marcar hecha), y su almacenamiento.
- [ ] Sistema de gamificación: monedas, estrellas, XP, insignias/habilidades, certificados — persistencia y reglas.
- [ ] Sistema de notificaciones/recordatorios: cursos pendientes + "GPS de crianza" por cumpleaños del hijo.

## 2. Construcción

- [ ] Theme (o child theme) base del sitio, integrando la marca de `brand/afectivalab-v3/` (logo, favicon, paleta, tipografía Fredoka).
- [ ] Pantalla de inicio / ruta visual tipo Duolingo (pendiente: el usuario debía enviar una imagen de referencia de esta pantalla — no ha llegado aún).
- [ ] Registro de padre + alta de hijos (edad, preocupaciones).
- [ ] Estructura de curso/microclase (video, caso interactivo, mini evaluación, recurso descargable, misión).
- [ ] Mecánica de casos de decisión ramificados (opción A/B/C/D con explicación y continuación).

## Fase posterior (fuera del alcance inicial)

- Asistente de IA "Tengo una situación ahora" (si se confirma viable).
- Temas ampliados: TDAH, TEA, separación de padres, duelo, problemas de conducta, hábitos de estudio, alimentación, sueño, adolescencia, orientación vocacional.
- Categorización por "mundos" como navegación libre (si no entra en el MVP).
