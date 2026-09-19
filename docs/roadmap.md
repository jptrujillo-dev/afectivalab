# Roadmap

Lo que se hará, en orden de prioridad. Sin fechas fijas todavía — se ajusta según lo que salga de la reunión con el usuario y del avance real.

## 0. Fundacional (antes de escribir código de la plataforma)

- [x] Control de versiones: git local inicializado, con `.gitignore` que protege `wp-config.php` y `.vscode/sftp.json`.
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

- [x] Theme propio desde cero (`wp-content/themes/afectivalab`, sin depender de Twenty Twenty-Five), integrando la marca (logo, favicon, paleta, Fredoka) y un helper de íconos SVG (`afectivalab_icon()`) que garantiza que nunca se usen emojis/símbolos de texto en la UI.
- [x] Página de inicio (home) construida a partir de la referencia visual del usuario: hero, franja de features, "cómo funciona" (3 pasos), etapas por edad (5), ruta recomendada + caso práctico interactivo (funcional en JS, sin backend todavía), testimonios y CTA final. Contenido en español con los datos reales del concepto (ejes, edades, ejemplo de Mateo/8 años).
- [x] Ilustraciones de personajes (familia del hero, 5 niños de etapas, niño de casos prácticos, 3 avatares de testimonios) generadas por el usuario con IA a partir de los prompts de [prompts-iconos-ia.md](prompts-iconos-ia.md) e integradas al theme como WebP optimizado.
- [x] Probar el theme en un WordPress real — confirmado por el usuario en `afectivalab.agenciamagneto.org`, con capturas reales desde el celular.
- [x] Registro e ingreso de padres: `/registro` y `/ingresar` reales y funcionales (no maquetas) — cuentas de WordPress de verdad, con un rol propio `afectivalab_padre`. Rutas resueltas por reescritura de URL (`inc/routes.php`), no dependen de crear Páginas en el escritorio.
- [ ] Alta de hijos por perfil (edad, preocupaciones) — el registro de padre ya existe, falta el siguiente paso: crear el perfil del hijo dentro de la cuenta.
- [ ] Verificación de correo al registrarse (hoy la cuenta queda activa de inmediato; requiere confirmar que el envío de mail funcione en Hostinger, típicamente con SMTP configurado aparte).
- [ ] Página de "Mi cuenta" / dashboard a la que redirige el login (hoy redirige al home).
- [ ] Estructura real de curso/microclase como contenido de WordPress (por ahora la sección de home usa datos de ejemplo hardcodeados, no contenido dinámico).
- [ ] Mecánica de casos de decisión ramificados con persistencia real (la demo de home es solo front-end, sin guardar progreso).
- [ ] Páginas reales para los enlaces del menú y footer que hoy son placeholders (`/nosotros`, `/privacidad`, `/terminos`, `/contacto`). `/registro` e `/ingresar` ya están resueltos.
- [ ] Página propia de "olvidé mi contraseña" (hoy usa el flujo por defecto de `wp-login.php`, funcional pero sin la marca del sitio).
- [ ] Configurar el menú "Menú principal" en Apariencia > Menús (por ahora el header usa un menú de respaldo hardcodeado en `inc/nav.php`).

## Fase posterior (fuera del alcance inicial)

- Asistente de IA "Tengo una situación ahora" (si se confirma viable).
- Temas ampliados: TDAH, TEA, separación de padres, duelo, problemas de conducta, hábitos de estudio, alimentación, sueño, adolescencia, orientación vocacional.
- Categorización por "mundos" como navegación libre (si no entra en el MVP).
