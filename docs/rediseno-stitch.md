# Rediseño visual con referencia Stitch

Seguimiento del trabajo de adaptar el CSS/HTML real del theme a las 7 pantallas
generadas en Google Stitch (proyecto "Afectivalab Parenting Experience
Platform", ID `5378620144781432662`). Esto es solo referencia visual: la
lógica PHP existente (seguridad, datos, permisos) no cambia — se reestiliza
markup y CSS sobre lo que ya funciona.

Se trabaja **en orden, una pantalla a la vez**, verificando con los harnesses
de render antes de pasar a la siguiente.

## Fundamentos compartidos

Antes de tocar pantalla por pantalla, hay 3 cosas transversales:

- [x] **Paleta**: coincide casi 1:1 con `assets/css/tokens.css` (mismo morado
      `#6C4FD6`, verde `#38B36B`, tinta, borde, fondo). Falta el **amarillo**
      como tercer acento (`#F6B819` / oscuro `#D89906` / tenue `#FEF7DF`) —
      agregado a tokens.css.
- [x] **Tipografía**: ya usamos Fredoka + Nunito Sans vía Google Fonts
      (`inc/enqueue.php`), igual que Stitch. Sin cambios necesarios.
- [x] **Sombra táctil ("botón que se presiona")**: patrón nuevo de Stitch,
      sombra inferior sólida de color (no blur) para dar sensación de botón
      3D tipo Duolingo. Se agrega como utilidades opcionales en tokens.css
      (`--shadow-tactile-*`), **solo para botones/CTAs**, nunca para el
      isotipo ni los iconos de categoría (`docs/brand.md` es explícito: el
      logo es una silueta plana, sin sombra inferior — eso no cambia).
- [ ] **Header de app (logueado)**: Stitch dibuja, para las pantallas
      internas, una barra superior persistente con enlaces directos (Inicio,
      Ruta de aprendizaje, Mis hijos, Casos prácticos) + chips de monedas y
      racha + avatar — distinta del header actual (`header.php`), que usa un
      menú desplegable de usuario. Es un cambio transversal a todas las
      páginas internas, no solo a estas 7. **Pendiente decidir con el
      usuario** si se adopta ahora o se deja para después; no se toca
      `header.php` hasta confirmar.

## Orden de trabajo

| # | Pantalla Stitch | Archivo(s) reales | Estado |
|---|---|---|---|
| 1 | Panel Principal - Afectivalab | `page-templates/panel.php` + bloque `.panel-*` en `plataforma.css` | ✅ hecho |
| 2 | Vista de Curso - Camino Duolingo Mejorado | `single-afectivalab_curso.php` + `.camino__*` en `plataforma.css` | ✅ hecho |
| 3 | Vista de Microclase - Gamificada | `single-afectivalab_clase.php` + bloque clase en `plataforma.css` | ✅ hecho |
| 4 | Mis Hijos - Gamificado | `page-templates/mis-hijos.php` + `hijos.css` | ✅ hecho |
| 5 | Descargar Certificado - Afectivalab | `page-templates/certificado.php` + `certificado.css` | ✅ hecho |
| 6 | Editar Perfil de Hijo - Gamificado | `template-parts/hijo-form.php` + `hijos.css` (vista `?editar=ID` de /mis-hijos) | ✅ hecho |
| 7 | Mi Cuenta - Gamificada | `page-templates/mi-cuenta.php` + `cuenta.css` | ✅ hecho |

## Notas por pantalla

### 1. Panel Principal ✅
Primer pase (solo colores/grid) quedó muy por debajo de la referencia — el
usuario lo notó comparando contra la captura de Stitch. Segundo pase, más a
fondo, pero **sin inventar ningún dato**: todo lo que se agregó ya existía
en el modelo de datos, solo no se mostraba.

Agregado con datos 100% reales:
- Resumen del curso (`post_excerpt` / campo "Resumen" del form de curso).
- Línea "Clase X de Y: {título de la próxima clase} · {duración} min"
  (`_afectivalab_duracion` de la clase siguiente, ya calculada por
  `afectivalab_progreso_curso()`).
- Aviso "Desbloquea la insignia '{habilidad}' al terminar este curso"
  (`_afectivalab_habilidad` del curso — el mismo dato que ya se usa en
  "Habilidades adquiridas" una vez completado).
- Chip corto ("Te preocupa: {eje}" / nombre de la etapa) como complemento
  visual — la oración explicativa completa ("Porque marcaste...") se
  mantuvo intacta, no se reemplazó (hay un test que la exige a propósito:
  el padre necesita el porqué, no solo una etiqueta).
- Guía de inicio parental: de lista vertical a grid de 4 tarjetas.
- Ruta de cursos: de lista a grid de tarjetas con barra de progreso mini.

**Deliberadamente NO agregado** (Stitch lo muestra pero no es dato real ni
decisión tomada):
- Racha de días consecutivos — no hay tracking de racha en el modelo.
- Candado de "curso bloqueado" — el bloqueo secuencial real existe a nivel
  de *clase dentro de un curso* (`afectivalab_ruta_del_curso`), no entre
  cursos: la ruta completa es de navegación libre por decisión del cliente
  ("libremente pero primero la ruta"). Mostrar un candado ahí mentiría
  sobre el comportamiento real.
- Estrellas/nivel por curso, nombres de insignia inventados tipo "Escudo
  Valiente", "Cofre Semanal", monto de monedas en el botón de CTA (los
  cursos no dan monedas; las monedas son por misión).
- Tarjeta de "misión pendiente" dentro del panel (hoy las misiones solo
  viven dentro de la microclase) y el widget de "Simulador de Caso" en el
  dashboard — ambas son ideas de producto nuevas, no de diseño.
- El header con nav visible + chips (transversal a todo el sitio logueado,
  sigue pendiente de decisión, ver "Fundamentos compartidos").

### 2–7
Se completan en el mismo orden de la tabla, un `docs/rediseno-stitch.md`
commit mental por pantalla. Cualquier idea de **producto** (no solo de
diseño) que aparezca en una captura de Stitch y no exista hoy en el theme
—como la personalización de ruta por "desafíos" en Mis Hijos, o el panel de
hábitos familiares en Mi Cuenta— se anota acá y se deja fuera del rediseño
hasta que el usuario decida si la construye.

- **Mis Hijos / Editar Perfil de Hijo**: Stitch agrega selección de "áreas de
  enfoque" (autoestima, bullying, pantallas, etc.) como filtros de
  personalización de ruta. Es funcionalidad nueva, no solo estilo — **no se
  construye sin decisión previa**, solo se adapta el resto del diseño.
- **Mi Cuenta**: Stitch agrega un dashboard de "hábitos familiares" con
  recordatorios diarios configurables. Mismo caso: funcionalidad nueva,
  fuera de alcance del rediseño visual hasta que se decida.

### 2. Vista de Curso ✅
Adoptado con datos reales:
- Barra superior: "Volver a mi ruta" (/panel; "Volver al inicio" sin sesión)
  y chip "Viendo la ruta de {hijo} · {edad} años" (`afectivalab_hijo_edad`).
- Tarjeta de presentación: chips (eje, N microclases, habilidad), título,
  resumen, y a la derecha la caja de progreso "{hijo} va X de Y
  microclases" + "% completado" + barra.
- Mapa en tarjeta propia con la píldora "Mapa de aprendizaje · {etapa}".
  Cada clase es nodo + tarjeta, alternando lados (izquierda/derecha); la
  tarjeta muestra estado (Completada/Bloqueada), duración, "Clase N: título",
  el resumen de la clase si lo tiene y, si la clase trae misión, el chip de
  la misión con sus monedas reales.
- La clase actual va centrada: nodo grande con "¡Ahora!" y tarjeta
  desplegada "¡Es tu turno!" con botón "Comenzar clase" (y "+N monedas"
  solo si esa clase tiene misión).
- Meta final: insignia de la habilidad + certificado, con fila del
  certificado "Bloqueado" o link "Descargar" cuando el curso está completo.
- Columna lateral: "Recompensas del curso" (insignia + certificado, X/2
  listas) y "Sobre este curso" (portada + descripción).

**Deliberadamente NO agregado**: estrellas por clase, "Afecti dice", misión
sorpresa / cofre, "+100 monedas extra" al terminar, racha semanal, tip del
día, "Respaldo psicológico" y la guía PDF como recompensa — nada de eso
existe en el modelo de datos. La línea del camino es recta y punteada, no la
curva de la referencia.

### 3. Microclase ✅
Adoptado con datos reales:
- "Volver al mapa del curso" + migas "Ruta de {hijo} › {curso}".
- Tarjeta de la clase: chips "Clase N de M", duración y "+N monedas al
  completar la misión" (solo si la clase trae misión); título; caja
  "Progreso del curso · X% completado" con barra y la pista "Te faltan K
  clases para la insignia '{habilidad}'".
- Video en marco blanco con borde; si es un enlace externo (no YouTube ni
  Vimeo), un botón de play verde hecho en CSS.
- Contenido como bloque "Lo esencial de esta clase" y el caso práctico con
  la situación destacada y opciones A/B más marcadas.
- Misión como tarjeta destacada (borde amarillo): tipo, "+N monedas", el
  texto, zona para subir la foto (misión de taller) que muestra el nombre del
  archivo elegido, y botones "La haremos después" / "Ya la hicimos · reclamar
  N monedas" con spinner al enviar.
- Cierre "¿Qué desbloqueas a continuación?" con la clase siguiente real (o
  la insignia y el certificado si es la última).
- Arreglo de paso: el error de la foto de evidencia (tipo/peso/falta) se
  perdía en silencio; ahora se muestra en la tarjeta
  (`afectivalab_mision_error()` en `inc/misiones.php`).

**Deliberadamente NO agregado**: capítulos del video, reproductor propio,
"Consejo clave" sobre el video, guía PDF, audio, tarjeta post-it, "Di esto /
Evita esto", campo de notas de la misión (no se guarda en ningún lado),
"cofre semanal", "+50 monedas familiares", "Nivel 3" en las migas, insignia
inventada "Escudo Valiente" y el aviso flotante de celebración. El contenido
de la clase lo escribe el equipo en el editor; si quieren bloques tipo "Di
esto / Evita esto" pueden armarlos ahí.

### 4 y 6. Mis Hijos y Editar Perfil ✅
Las dos viven en /mis-hijos: la lista con el formulario de alta (4) y, con
`?editar=ID`, una vista de edición a dos columnas (6).

Adoptado con datos reales (`afectivalab_hijo_resumen()` en `inc/hijos.php`):
- Cabecera con píldora, título y botón "+ Agregar hijo o hija" (se oculta al
  llegar al máximo de 5; el texto ya no dice "todos los que quieras").
- Tarjetas a dos columnas alternando morado/verde: avatar con la inicial,
  "Ruta activa" / "En espera", edad · etapa, curso en marcha con su % y
  barra, temas que le preocupan, y estadísticas (clases terminadas =
  estrellas, insignias = cursos completos, cursos en su ruta). Botones "Ver
  su ruta", "Editar" y "Quitar".
- Formulario: cabecera con ícono, nombre y fecha lado a lado, temas como
  tarjetas con check animado y contador "N elegidos", botón táctil.
- Edición: volver + migas, columna izquierda con identidad, logros y curso
  en marcha; a la derecha el formulario y "Quitar este perfil".
- "Quitar" ya no usa la ventana nativa del navegador: modal propio animado,
  para todo el sitio (`main.js` + `.confirmar-modal` en `base.css`).

**Deliberadamente NO agregado**: niveles ("Lv. 3", "Pequeño Explorador"),
monedas por hijo (las monedas son del padre), racha, avatares ilustrados,
posición en la familia, pausar notificaciones, reiniciar ruta, límite de
"1 a 3 prioridades" (hoy se pueden marcar todas), el banner de
neurociencia y el "Validado clínicamente".

### 5. Certificado ✅
Adoptado: felicitación "¡Felicidades, {hijo}!" arriba (no se imprime), el
diploma con marco degradado morado/verde/amarillo, "Se otorga con orgullo y
cariño a", nombre, curso, habilidad adquirida, pie con acompañante, sello y
fecha, y abajo "Descargar (guardar como PDF)" + "Volver al curso". Al
imprimir solo queda el diploma, con sus colores.

**Deliberadamente NO agregado**: firma de una directora pedagógica, código
de verificación "ID: AFECTIVA-…" (no existe validación), dedicatoria
editable, modo "para colorear", descarga en PNG y botón compartir (el enlace
pide sesión y solo lo abre el padre, no serviría compartirlo).

### 7. Mi Cuenta ✅
El usuario compartió el HTML/CSS fuente real del mockup de Stitch (no solo
capturas), lo que permitió comparar contra la implementación real con mucha
más precisión que a ojo.

Adoptado (solo estilo, mismos datos reales de antes):
- `.account-card`, `.account-stats__item` y `.account-insignias__item` pasan
  de fondo gris plano a fondo blanco + borde 2px + una sombra de "reborde
  inferior sólido" (`0 6px 0 <color>` o `0 4px 0 <color>`, sin blur) más la
  sombra difusa de siempre — mismo lenguaje de "tarjeta con relieve" del
  mockup. Acotado a `cuenta.css`, no se tocó `--shadow-card` global.
- Insignias: ícono más grande (56px) con fondo **sólido** de color (antes
  era `-tint`), rotando entre los 3 acentos de marca.
- Checkbox de preferencias: se cambió la reconstrucción manual con
  `appearance: none` + `::after` por `accent-color` nativo — más simple, y
  es el mismo patrón que usa el propio mockup (`accent-secondary`).
- Preferencias: cada una pasa a ser su propia fila con fondo (`--color-bg-alt`)
  en vez de solo un divisor.

**Deliberadamente NO adoptado** (dato o funcionalidad inventada, no estilo):
- Racha de días y "nivel" del padre (badge "Niv. 4", "Guía Empática del
  Hogar") — ya decidido con el usuario que se omiten, no hay tracking real.
- El contador "4 de 12 ganadas" en insignias — implica un catálogo fijo de
  12 insignias posibles que no existe; las insignias son dinámicas (tantas
  como cursos con habilidad completados).
- Categorías inventadas por insignia ("Autocontrol", "Apego seguro", "Límites
  sanos") — nuestro modelo solo tiene el nombre real de la habilidad y de
  qué hijo es.
- El botón único "Guardar cambios" al final que guarda todo junto con un
  toast — no calza con el modelo real (cada tarjeta tiene su propio
  `<form>`/submit independiente: foto, datos, preferencias, cancelar).
- El azul que Stitch usa en su paleta custom (`coral`, y `blue-500` genérico
  de Tailwind en una insignia) — no existe en `docs/brand.md` (3 acentos:
  morado/verde/amarillo), así que no se agrega un cuarto color de marca.
- El header con chips de racha/monedas + nav visible — sigue pendiente de
  decisión transversal (ver "Fundamentos compartidos"), no se tocó.

Se armó un preview HTML aparte (mismos `tokens.css`/`base.css`/`cuenta.css`
reales, publicado como Artifact) para verificar el resultado antes de subir
nada al servidor — mismo espíritu que "verificando con los harnesses de
render antes de pasar a la siguiente", pero visual en vez de solo
"no rompe".

Dos rondas más tras revisar el preview:
- **Tarjeta "Mi perfil"**: avatar con aro de gradiente + sombra inferior,
  "Cambiar foto"/"Guardar" en una fila compacta (antes: un botón ancho
  suelto abajo), separador claro antes del formulario de nombre/correo, y
  "Cambiar contraseña" movido a la sección de solo lectura (junto a "Rol")
  en vez de quedar como link suelto en el aire.
- **"Tu suscripción"**: el usuario notó que faltaba el nombre del plan y el
  precio real que paga — se agregó `pmpro_getLevel()` +
  `pmpro_formatPrice()` + `pmpro_translate_billing_period()` a
  `afectivalab_suscripcion_resumen()` (dato real de PMPro, no
  hardcodeado). Mismo estilo de tarjeta con gradiente/sombra por acento que
  el resto de la página.
- **"Tus pedidos"**: con más de 5, la lista pasa a tener scroll interno en
  vez de crecer sin límite.
- **Insignias**: el ícono real `juego-insignia.svg` es una ilustración
  multicolor con colores fijos (no responde a `currentColor`), así que no
  combinaba bien puesto sobre un círculo de fondo sólido rotado como pedía
  el mockup. Se creó `assets/icons/insignia.svg` — un ícono de trazo simple
  (mismo estilo que `user.svg`/`camera.svg`: `currentColor`, sin relleno)
  para poder ponerlo en blanco sobre el círculo sólido de color, y cada
  tarjeta de insignia pasó a tener el gradiente sutil + sombra de "reborde
  inferior" del mismo acento, igual que el resto de tarjetas de la página.

Tercera ronda: `.account-stats` (4 fijos) pasó de `grid` a fila deslizable
con `scroll-snap` puro CSS. Se probó también ensanchar `.account-layout` a
`1350px`, pero el usuario aclaró que el ancho de 980px ya estaba bien —
revertido; el pedido real era solo sobre el comportamiento de las
insignias con muchos ítems, no sobre el ancho general de la página.

Cuarta ronda — el slider real de "Insignias desbloqueadas": el usuario
pidió específicamente que se vean 3 a la vez con flechas para avanzar de
una en una (no solo un scroll libre). Implementado con `.account-insignias__item`
a `flex: 0 0 calc((100% - 2 * var(--space-3)) / 3)` (exactamente 3 caben
en el ancho de la tarjeta) + dos botones de flecha
(`.account-insignias__flecha`, reusando `chevron-down.svg` rotado por
CSS) que llaman `scrollBy()` calculando el ancho real de una tarjeta en
`assets/js/cuenta.js` (`iniciarSliderInsignias()`) — las flechas se
deshabilitan solas al llegar a cada extremo. Progressive enhancement real:
`overflow-x: auto` + `scroll-snap-type` siguen funcionando con swipe/rueda
del mouse si el script no llega a cargar; las flechas son solo un atajo
sobre ese mismo scroll, no el único modo de navegarlo. El bloque de avatar
en `cuenta.js` tenía un `return` temprano que hubiera cortado la
ejecución del slider si alguna vez faltara el formulario de foto en la
página — se separaron en dos funciones independientes.

Quinta ronda — con una foto real subida (no solo la inicial de
placeholder que se usó en las rondas anteriores), el aro decorativo
(padding 6px + gradiente + sombra sólida + borde blanco 4px, ~10px de
borde combinado) se veía pesado y competía con la imagen en vez de leerse
como "un círculo limpio". Simplificado a un borde delgado (3px sólido +
3px blanco, sin gradiente ni sombra). Aparte, dos correcciones de
validación no relacionadas al estilo: el límite de la foto de perfil baja
de 3MB a 1MB (`inc/account.php`, `MB_IN_BYTES` en vez de `3 * MB_IN_BYTES`)
— los tipos permitidos (JPG/PNG/WEBP) ya estaban bien. Se agregó cobertura
real de `afectivalab_handle_avatar_upload()` a `test-account.php`
(scratchpad, no forma parte del repo): antes solo se mockeaba sin
probarla.

Sexta ronda — con una foto real de verdad subida (no el placeholder), el
círculo seguía sin cerrar del todo abajo. Causa real: `.avatar-picker__preview`
es un `<div>` normal alrededor de `.user-avatar`, que en `base.css` es
`display: inline-flex` — un div normal trata a un hijo inline-flex como
texto en línea y le reserva el espacio de un descendente de fuente (la
"g"/"y"), el clásico "hueco fantasma" debajo de imágenes/inline-block. Eso
hacía que el aro contenedor fuera más alto que ancho — un óvalo, no un
círculo. Fix real: `display: flex` en `.avatar-picker__preview` (no en
`.user-avatar`, que ya lo era).

Dos pedidos más en la misma ronda:
- **Loader al guardar**: nuevo `.btn__spinner` + `iniciarLoaderFormularios()`
  en `cuenta.js` — un solo listener genérico por `<form>` de la página
  (foto, datos, suscripción, preferencias) en vez de repetir la lógica en
  cada uno. Se verificó que no choca con el `data-confirm` de "Cancelar
  renovación automática" (main.js): ese intercepta el evento `click` del
  botón, así que si el usuario cancela el diálogo nativo, el `submit` del
  formulario nunca llega a dispararse y el loader no se activa ahí.
- **"Cambiar contraseña" no hacía nada**: `page-templates/recuperar.php` y
  `restablecer.php` redirigían a la home a cualquiera que ya tuviera
  sesión — tenía sentido cuando ese flujo era solo para gente deslogueada,
  pero ahora el link de /mi-cuenta lo reusa a propósito. Se quitó esa
  redirección en ambas páginas; `reset_password()` de WordPress no cierra
  la sesión activa, así que el usuario sigue logueado después de cambiar
  su contraseña, que es el comportamiento esperado.

Séptima ronda — el usuario señaló, con razón, que mandar a alguien que
**ya está logueado** por el flujo de "olvidé mi contraseña" (correo + key
de un solo uso) no tiene sentido: pidió en cambio un modal con
contraseña actual/nueva/repetir, validación en vivo, loader al enviar y
"contraseña actualizada" antes de cerrarse solo. Se revirtió el cambio de
la ronda anterior (recuperar.php/restablecer.php vuelven a redirigir a
quien ya tiene sesión) y se implementó de verdad:
- `afectivalab_ajax_cambiar_password()` en `inc/account.php`, mismo patrón
  ya usado en el proyecto para el login (`afectivalab_ajax_login()` en
  `inc/auth.php`): `check_ajax_referer()` + `wp_send_json_error()`/`_success()`
  vía `admin-ajax.php`, no un endpoint REST nuevo. Valida la contraseña
  actual con `wp_check_password()`, exige 8+ caracteres y que las dos
  nuevas coincidan. Detalle importante: `wp_set_password()` por sí sola
  invalida la sesión del usuario (cambia el hash que valida la cookie) —
  hay que reautenticar de inmediato (`wp_clear_auth_cookie()` +
  `wp_set_current_user()` + `wp_set_auth_cookie()`) para que la misma
  pestaña no quede deslogueada justo después de cambiar su propia
  contraseña.
- Modal en `mi-cuenta.php` con el mismo lenguaje visual (blur + reborde
  inferior) que el resto de modales de la página. JS en `cuenta.js`
  (`iniciarModalPassword()`): validación en vivo del largo mínimo y de que
  las contraseñas nuevas coincidan (mismo patrón `.form-field.is-invalid`/
  `is-valid` que ya usa `/registro`), envío por `fetch` + `FormData`
  (mismo patrón que `auth.js` usa para el login), mensaje de éxito dentro
  del modal y cierre automático después de ~1.6s. El spinner del botón lo
  pone el `iniciarLoaderFormularios()` genérico de la ronda anterior (el
  modal solo necesita quitarlo al terminar, ya que a diferencia de los
  demás formularios de la página este no recarga sola).
- Cobertura de test agregada a `test-account.php` (scratchpad): sin
  sesión, nonce inválido, contraseña actual incorrecta, nueva muy corta,
  las dos nuevas no coinciden, nueva igual a la actual, y el caso feliz
  (incluye verificar que se reautentica después del cambio).

Octava ronda, 4 correcciones sobre lo ya construido:
1. **Error de foto sin validar en el cliente**: antes, si la persona elegía
   un archivo con formato o tamaño inválido, no se enteraba hasta después
   de tocar "Guardar" y esperar la recarga completa (el único aviso era el
   del servidor). Se agregó la misma validación (JPG/PNG/WEBP, 1MB) del
   lado del cliente, apenas se elige el archivo, sin sacarle a PHP la
   validación real — solo avisa antes, si el JS no carga sigue funcionando
   igual que antes.
2. **Botón "Cambiar contraseña" con borde/fondo raro**: el elemento pasó de
   `<a>` a `<button>` en la ronda anterior, pero el CSS seguía pensado
   para un link — un `<button>` nativo trae su propio borde/fondo/padding
   del navegador que no se resetea solo. Faltaba `border: none; background:
   none; padding: 0;` etc.
3. **Sin ojo de mostrar/ocultar contraseña**: existía la función
   `afectivalab_password_toggle()` y su JS (`auth.js`) para esto, ya
   usados en /registro — pero `/mi-cuenta` no carga `auth.js`. En vez de
   cargarlo entero (trae login por AJAX, validación de registro, nada de
   eso aplica acá), se duplicó solo el toggle y el medidor de fortaleza en
   `cuenta.css`/`cuenta.js` (mismo comentario ya en `auth.css` sobre las
   primitivas compartidas anticipaba este caso).
4. **Sin medidor de fortaleza**: mismo caso — se agregó
   `afectivalab_password_strength_meter()` al campo de contraseña nueva,
   reusando el mismo cálculo de puntaje (`passwordScore()`) ya usado en
   /registro, no uno inventado de cero.

Novena ronda — bug real, no de estilo: el usuario subía una foto nueva, le
daba "Guardar", la página recargaba, pero la foto seguía siendo la
anterior — sin ningún mensaje de error ni de éxito. Causa:
`iniciarLoaderFormularios()` (de la ronda del loader) deshabilitaba el
botón de submit de forma síncrona, todavía **dentro** del propio handler
del evento `submit`. Cuando eso pasa antes de que el navegador termine de
serializar el formulario, puede terminar viendo el botón ya deshabilitado
y dejar su `name`/`value` (`afectivalab_avatar_submit=1`) afuera del POST
— y `afectivalab_handle_avatar_upload()` corta de entrada, en silencio,
si ese campo no llega (`if ( empty( $_POST['afectivalab_avatar_submit'] ) )
{ return $result; }`), sin ningún error que mostrar. Mismo problema, en
espíritu, que ya había mordido antes en este proyecto con
`form.submit()`/`requestSubmit()` sin pasar el botón. Fix: la
deshabilitación se encola con `setTimeout(fn, 0)` en vez de ejecutarse
síncronamente, para que el navegador ya haya capturado los datos del
envío real primero. No se pudo demostrar en el preview de Artifact (no
hay backend real ahí que reciba el POST) — es un bug de comportamiento
del navegador ante un submit nativo, verificado leyendo el código, no
visualmente.
