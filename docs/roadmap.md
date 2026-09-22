# Roadmap

Lo que se hará, en orden de prioridad. Sin fechas fijas todavía — se ajusta según lo que salga de la reunión con el usuario y del avance real.

## 0. Fundacional (antes de escribir código de la plataforma)

- [x] Control de versiones: git local inicializado, con `.gitignore` que protege `wp-config.php` y `.vscode/sftp.json`.
- [x] Resolver las preguntas abiertas de producto — respondidas por el cliente el 2026-09-22, ver [concepto-plataforma.md](concepto-plataforma.md#decisiones-confirmadas-por-el-cliente-2026-09-22).
- [x] Decidir el motor de contenido: **desarrollo a medida**, no un plugin LMS (el "alumno" es un perfil de hijo dentro de la cuenta del padre, algo que ningún LMS modela).
- [ ] Definir el detalle de la suscripción con el cliente: planes, precio, si se cobra por cuenta o por hijo, pasarela de pago, y qué pasa con el progreso si la suscripción vence.
- [ ] Validar con el usuario el paquete de marca v3 ([brand.md](brand.md)) como definitivo, en particular el icono de "Sexualidad y afectividad".

## 1. Modelo de datos y arquitectura

- [x] Tipos de contenido propios: `afectivalab_curso` y `afectivalab_clase` (microclase), con las taxonomías `afectivalab_etapa` (5 etapas) y `afectivalab_eje` (8 ejes) sembradas desde código — ver `inc/content.php`. Los rangos de edad van como term meta (no solo en el nombre) porque la ruta personalizada se arma buscando qué etapa contiene la edad del hijo.
- [x] Modelo de datos del hijo: `afectivalab_hijo`, un contenido propio con `post_author` = el padre (ver `inc/hijos.php`). Se guarda **mes y año de nacimiento**, no la edad: una edad escrita a mano queda desactualizada sola, y el "GPS de crianza" necesita saber cuándo el hijo cambia de etapa. Las preocupaciones se guardan como post meta (slugs de eje) y no como términos, para que el conteo de cada eje en el escritorio siga contando solo cursos.
- [x] Armado de la ruta personalizada (`inc/ruta.php`): los cursos de la etapa del hijo, con los que el padre marcó como preocupación primero y el resto en el orden de prioridad de los ejes. No se guarda: se calcula cada vez a partir de la edad de hoy, que es lo que hace que un hijo cambie de etapa solo al cumplir años.
- [x] Progreso por hijo (`inc/progreso.php`): lista de microclases completadas guardada en el perfil del hijo, no en la cuenta del padre. De ahí salen el % del curso, la clase que toca y el desbloqueo en cadena.
- [ ] Sistema de misiones: distinguir misión "taller" (con evidencia subida) de misión "en casa" (solo marcar hecha), y su almacenamiento.
- [ ] Sistema de gamificación: monedas, estrellas, XP, insignias/habilidades, certificados — persistencia y reglas.
- [ ] Certificado: el cliente confirmó **PDF descargable + insignia visual**. Falta definir qué datos lleva (nombre, curso, fecha, firma de quién).
- [ ] Sistema de notificaciones/recordatorios: cursos pendientes + "GPS de crianza" por cumpleaños del hijo.
- [ ] Control de acceso por suscripción: hoy los cursos son públicos (`public => true`), falta la capa que decida quién puede verlos.

## 2. Construcción

- [x] Theme propio desde cero (`wp-content/themes/afectivalab`, sin depender de Twenty Twenty-Five), integrando la marca (logo, favicon, paleta, Fredoka) y un helper de íconos SVG (`afectivalab_icon()`) que garantiza que nunca se usen emojis/símbolos de texto en la UI.
- [x] Página de inicio (home) construida a partir de la referencia visual del usuario: hero, franja de features, "cómo funciona" (3 pasos), etapas por edad (5), ruta recomendada + caso práctico interactivo (funcional en JS, sin backend todavía), testimonios y CTA final. Contenido en español con los datos reales del concepto (ejes, edades, ejemplo de Mateo/8 años).
- [x] Ilustraciones de personajes (familia del hero, 5 niños de etapas, niño de casos prácticos, 3 avatares de testimonios) generadas por el usuario con IA a partir de los prompts de [prompts-iconos-ia.md](prompts-iconos-ia.md) e integradas al theme como WebP optimizado.
- [x] Probar el theme en un WordPress real — confirmado por el usuario en `afectivalab.agenciamagneto.org`, con capturas reales desde el celular.
- [x] Registro e ingreso de padres: `/registro` y `/ingresar` reales y funcionales (no maquetas) — cuentas de WordPress de verdad, con un rol propio `afectivalab_padre`. Rutas resueltas por reescritura de URL (`inc/routes.php`), no dependen de crear Páginas en el escritorio.
- [x] Validación en vivo con JS en los formularios de auth (nombre, email, contraseña, confirmación) — ayuda visual únicamente, la validación real sigue siendo del lado del servidor en `inc/auth.php`.
- [x] Medidor de fortaleza de contraseña (Baja/Media/Alta) en `/registro` y `/restablecer`.
- [x] Flujo completo de "olvidé mi contraseña": `/recuperar` (pide el correo, envía el link con `wp_mail()`) y `/restablecer` (valida la key con las funciones nativas de WordPress y permite elegir una nueva). Reemplaza el flujo por defecto de `wp-login.php`.
- [x] Envío de correo: el cliente confirmó que el SMTP ya está configurado en el hosting, así que `/recuperar` no necesita nada adicional. Igual conviene probarlo con un correo real alguna vez.
- [x] Rol de instructor (`afectivalab_instructor`) para que el equipo del cliente cargue el contenido: puede crear, editar y publicar cursos y microclases (y editar los de sus colegas), pero no borrar lo ajeno ni tocar el blog. Ver `inc/roles.php`.
- [x] Campos de microclase en el escritorio: curso al que pertenece, orden, duración y video — con las dos formas que pidió el cliente, enlace de YouTube/Vimeo o archivo subido a la librería de medios. Ver `inc/content-admin.php`.
- [x] Alta de hijos: página `/mis-hijos` con lista de perfiles, alta, edición y baja, más el selector de los 8 temas que le preocupan al padre. El registro ahora termina ahí en vez de en la home, porque sin al menos un hijo la cuenta no sirve de nada.
- [x] Panel del padre en `/panel`, al que ahora redirige el login: saludo por hora, selector de hijo, el curso que toca con la razón por la que se recomienda, la ruta completa y las habilidades adquiridas. Se dejó aparte de `/mi-cuenta`, que sigue siendo solo los datos de la cuenta — mezclar "tu avance" con "cambia tu foto" confunde las dos cosas.
- [x] Plantillas de front-end para curso y microclase: `single-afectivalab_curso.php` dibuja las microclases como un camino serpenteante estilo Duolingo (hecha / te toca / bloqueada) rematado en el nodo de certificado, y `single-afectivalab_clase.php` muestra el video, el contenido y el botón de marcarla como vista.
- [ ] Vista de un curso para quien ya lo terminó: hoy vuelve a ver el camino completo en verde, que está bien, pero no hay dónde descargar el certificado (falta el certificado en sí).
- [ ] Vista de exploración libre por mundo temático — el cliente confirmó que sí va, como navegación secundaria a la ruta personalizada.
- [x] ~~Verificación de correo al registrarse~~ — el cliente confirmó que **no es obligatoria**; la cuenta queda activa de inmediato, como está hoy.
- [x] Chip de usuario en el header (avatar + nombre) con menú desplegable ("Mi cuenta" / "Salir"), en vez del saludo de texto plano anterior.
- [x] Página `/mi-cuenta`: primera versión, solo con subida de foto de perfil (JPG/PNG/WEBP, máx. 3MB) — el avatar sale en el chip del header apenas se guarda. Falta convertirla en el dashboard real (progreso, hijos, etc.) al que redirige el login.
- [x] Contenido de prueba: botón en **Cursos > Contenido de prueba** que crea 7 cursos y 32 microclases de relleno repartidos en 3 etapas, para ver el panel y el camino funcionando antes de cargar el contenido real. Todo queda marcado con la meta `_afectivalab_demo` y se borra con otro botón. **Hay que borrarlo antes de abrir el sitio al público.**
- [ ] La home sigue usando datos de ejemplo hardcodeados en `template-parts/home/showcase.php` (la ruta de Mateo y el caso práctico). Ahora que hay contenido real, esa sección podría alimentarse de la base de datos.
- [ ] Mecánica de casos de decisión ramificados con persistencia real (la demo de home es solo front-end, sin guardar progreso).
- [ ] Páginas reales para los enlaces del menú y footer que hoy son placeholders (`/nosotros`, `/privacidad`, `/terminos`, `/contacto`). `/registro`, `/ingresar`, `/recuperar` e `/restablecer` ya están resueltos.
- [ ] Configurar el menú "Menú principal" en Apariencia > Menús (por ahora el header usa un menú de respaldo hardcodeado en `inc/nav.php`).

## Fase posterior (fuera del alcance inicial)

- Asistente de IA "Tengo una situación ahora" — **en stand by** por decisión del cliente (2026-09-22): no se desarrolla ni se investiga por ahora.
- Temas ampliados: TDAH, TEA, separación de padres, duelo, problemas de conducta, hábitos de estudio, alimentación, sueño, adolescencia, orientación vocacional.
