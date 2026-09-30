<?php
/**
 * El contenido de prueba en sí: cursos, microclases, misiones y casos.
 *
 * Vive aparte de inc/demo.php (que solo lo crea y lo borra) porque es largo:
 * son textos completos, pensados para que el sitio se vea y se pruebe como
 * se verá con contenido de verdad. Se basan en pautas ampliamente aceptadas
 * de crianza positiva, pero **no reemplazan la revisión del equipo del
 * cliente**: antes de publicar, ellos deciden qué queda, qué cambia y qué se
 * borra.
 *
 * Forma de cada clase (todas las claves salvo 't' y 'min' son opcionales):
 *   't'        título
 *   'min'      duración en minutos
 *   'resumen'  una línea (extracto de la clase, se ve en el mapa del curso)
 *   'intro'    párrafos de introducción
 *   'claves'   ideas clave (lista)
 *   'frases'   frases que ayudan (lista)
 *   'evitar'   lo que conviene evitar (lista)
 *   'practica' ejercicio para la semana
 *   'video'    id de YouTube (verificados con oEmbed al escribir esto)
 *   'mision'   array( tipo casa|taller, texto )
 *   'caso'     clave de afectivalab_demo_casos()
 *
 * Las fotos son de Unsplash (licencia libre de Unsplash) y los videos son de
 * canales públicos de YouTube (UNICEF, guiainfantil, AprendemosJuntos de
 * BBVA, entre otros); se incrustan, no se copian.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return array<int, array>
 */
function afectivalab_demo_cursos() {
	return array(

		// ===================== PRIMERA INFANCIA (3 a 5) =====================
		array(
			'titulo'      => 'Rabietas y primeras emociones',
			'etapa'       => 'primera-infancia',
			'eje'         => 'emociones',
			'habilidad'   => 'Acompañar rabietas con calma',
			'imagen'      => '1517545084371-4a575dde2a02',
			'resumen'     => 'Qué pasa en la cabeza de un niño pequeño cuando estalla, qué hacer durante la rabieta y cómo poner límites sin gritos ni castigos.',
			'descripcion' => array(
				'Entre los 2 y los 5 años las rabietas son parte normal del desarrollo: el cerebro de tu hijo todavía no tiene las herramientas para frenar una emoción intensa, y su forma de pedir ayuda es estallar. No es manipulación ni falta de educación; es inmadurez, y se acompaña.',
				'En este curso vas a entender qué dispara las rabietas, qué hacer en el momento (y qué no), cómo reconectar cuando pasa la tormenta y cómo sostener un límite con firmeza y cariño a la vez. Todo con ejemplos de situaciones reales: el supermercado, la hora de dormir, el parque.',
			),
			'aprenderas'  => array(
				'Reconocer las señales que anuncian una rabieta y prevenir las más evitables.',
				'Mantener la calma cuando tu hijo pierde el control.',
				'Poner nombre a las emociones para que aprenda a identificarlas.',
				'Sostener un límite sin gritar y sin ceder por cansancio.',
			),
			'clases'      => array(
				array(
					't'        => 'Por qué a esta edad estallan',
					'min'      => 10,
					'resumen'  => 'La rabieta no es un desafío: es un cerebro que todavía está aprendiendo a frenar.',
					'video'    => 'fJazWB1BCI0',
					'intro'    => array(
						'La parte del cerebro que nos ayuda a esperar, a tolerar un "no" y a calmarnos (la corteza prefrontal) es la que más tarda en madurar: sigue desarrollándose hasta pasados los 20 años. A los 3 o 4 años, cuando algo frustra a tu hijo, la emoción llega con toda su fuerza y no hay un freno interno que la contenga.',
						'Por eso las rabietas aparecen sobre todo cuando se juntan tres cosas: cansancio, hambre o sobreestimulación; un deseo muy concreto ("quiero ESE juguete"); y un límite que lo frustra. Conocer esos disparadores te permite adelantarte a muchas de ellas.',
					),
					'claves'   => array(
						'Una rabieta es una emoción desbordada, no un intento de manipularte.',
						'El cansancio y el hambre multiplican las rabietas: cuida los horarios de sueño y comida.',
						'Los cambios bruscos de actividad cuestan: avisa antes ("en cinco minutos nos vamos del parque").',
						'Dar opciones pequeñas ("¿te pones la polera roja o la azul?") le devuelve algo de control.',
					),
					'practica' => 'Durante esta semana, anota en qué momentos del día aparecen las rabietas y qué había pasado justo antes. Al final de la semana busca el patrón: ¿son a la misma hora?, ¿después de salir?, ¿con hambre?',
				),
				array(
					't'        => 'Qué hacer durante la rabieta',
					'min'      => 9,
					'resumen'  => 'Tu calma es su mejor regulador: menos palabras, más presencia.',
					'video'    => 'yarus6E2Gn8',
					'caso'     => 'rabieta-supermercado',
					'intro'    => array(
						'En plena rabieta tu hijo no puede escuchar razones: la parte de su cerebro que razona está "desconectada". Explicarle, sermonearle o amenazarle en ese momento solo suma ruido. Lo que sí le llega es tu tono, tu cuerpo y tu calma.',
						'Tu tarea no es apagar la emoción de inmediato, sino asegurarte de que nadie se lastime y quedarte cerca, disponible, hasta que baje la intensidad. Los niños aprenden a calmarse "prestando" la calma de un adulto.',
					),
					'claves'   => array(
						'Primero la seguridad: aleja objetos peligrosos y evita que se golpee o golpee a otros.',
						'Baja a su altura, habla poco y despacio.',
						'Nombra lo que ves: "Estás muy enojado porque querías seguir jugando".',
						'No cedas al límite para que pare: le enseñarías que gritar funciona.',
						'Si necesitas calmarte tú, respira y cuenta hasta diez antes de hablar.',
					),
					'frases'   => array(
						'"Estoy aquí contigo. Cuando estés listo, te abrazo."',
						'"Está bien enojarse. No está bien pegar."',
						'"Entiendo que querías el dulce. La respuesta sigue siendo no."',
					),
					'evitar'   => array(
						'Gritar más fuerte que él para que se calle.',
						'Burlarte o decirle "pareces un bebé".',
						'Amenazar con cosas que no vas a cumplir ("te dejo aquí").',
					),
					'practica' => 'La próxima rabieta, prueba la regla de "pocas palabras": solo una frase para nombrar la emoción y otra para el límite. Después, quédate cerca en silencio. Observa cuánto dura comparado con otras veces.',
				),
				array(
					't'        => 'Qué hacer después, cuando se calma',
					'min'      => 8,
					'resumen'  => 'La reconexión después de la tormenta es donde de verdad se aprende.',
					'intro'    => array(
						'Cuando la rabieta termina, muchos padres pasan página rápido o, al revés, aprovechan para dar el sermón. El momento después es valioso por otra razón: es cuando tu hijo puede volver a escuchar y aprender algo de lo que pasó.',
						'Lo primero es reconectar: un abrazo, un momento tranquilo juntos. Luego, con pocas palabras y sin culpas, puedes poner en palabras lo que ocurrió y pensar juntos qué hacer la próxima vez.',
					),
					'claves'   => array(
						'Reconecta antes de enseñar: primero el abrazo, después la conversación.',
						'Resume lo que pasó en dos o tres frases simples.',
						'Busquen juntos una alternativa: "La próxima vez que te enojes, ¿qué podemos hacer?".',
						'Si hubo un daño (algo roto, alguien golpeado), ayúdalo a repararlo.',
					),
					'frases'   => array(
						'"Fue un momento difícil. Ya pasó y te quiero igual."',
						'"Te enojaste mucho porque teníamos que irnos. La próxima vez podemos avisarte antes."',
					),
					'practica' => 'Después de la próxima rabieta, cuando ya esté tranquilo, lean juntos un cuento sobre emociones o dibujen "cómo se ve el enojo". Es una forma de hablar de lo que pasó sin que se sienta regañado.',
				),
				array(
					't'        => 'Poner un límite sin gritar',
					'min'      => 11,
					'resumen'  => 'Firme en el qué, amable en el cómo: los límites que sí se sostienen.',
					'video'    => 'W-OhwNL72Co',
					'mision'   => array( 'casa', 'Elijan juntos un "rincón de la calma" en casa: un cojín, un peluche y un par de cuentos. Muéstrale que puede ir allí cuando se sienta muy enojado o triste, y que tú también lo usarás cuando lo necesites.' ),
					'intro'    => array(
						'Los niños necesitan límites para sentirse seguros: les dicen hasta dónde pueden llegar y que hay un adulto a cargo. El problema no es poner límites, sino cómo los ponemos. Un límite dicho a gritos enseña que quien grita manda; un límite dicho con calma y sostenido en el tiempo enseña que las reglas son confiables.',
						'La clave está en ser firme en lo que decides ("no se pega", "a las ocho vamos a dormir") y amable en la forma de decirlo, reconociendo lo que siente tu hijo aunque no le des lo que quiere.',
					),
					'claves'   => array(
						'Pocos límites, claros y siempre los mismos.',
						'Anticipa: explica la regla antes de la situación, no en medio del conflicto.',
						'Valida la emoción y mantén el límite: "Sé que quieres más tele, y ya terminó".',
						'Cumple lo que dices; si no vas a cumplirlo, no lo digas.',
						'Los adultos de la casa deben ponerse de acuerdo en las reglas básicas.',
					),
					'frases'   => array(
						'"Puedes estar enojado, y aun así es hora de bañarse."',
						'"No te voy a dejar pegar. Te voy a ayudar a calmarte."',
					),
					'practica' => 'Escoge un límite que hoy te cueste sostener (por ejemplo, la hora de dormir). Decide con tu pareja o con quien cuide a tu hijo cómo se va a decir y qué pasará si no se cumple. Sostenlo igual durante siete días.',
				),
			),
		),

		array(
			'titulo'      => 'Mi cuerpo es mío: cuidado y protección',
			'etapa'       => 'primera-infancia',
			'eje'         => 'proteger',
			'habilidad'   => 'Educación en autocuidado',
			'imagen'      => '1644941002474-6ee8ab0ee8cb',
			'resumen'     => 'Cómo enseñar a tu hijo, con palabras simples y sin miedo, que su cuerpo le pertenece y a quién pedir ayuda.',
			'descripcion' => array(
				'La prevención del abuso empieza mucho antes de lo que imaginamos, y empieza en casa: con los nombres correctos de las partes del cuerpo, con el derecho a decir "no" a un abrazo que no quiere dar y con la certeza de que puede contarte cualquier cosa sin que lo retes.',
				'Este curso te da herramientas concretas, adaptadas a niños de 3 a 5 años, para hablar del cuerpo con naturalidad, enseñar la diferencia entre secretos buenos y malos, y armar juntos su círculo de adultos de confianza. Sin asustar: educar en autocuidado es educar en seguridad y autoestima.',
			),
			'aprenderas'  => array(
				'Nombrar las partes del cuerpo con naturalidad.',
				'Enseñar que su cuerpo le pertenece y que puede decir "no".',
				'Diferenciar secretos que se guardan de secretos que se cuentan.',
				'Identificar juntos a los adultos de confianza.',
			),
			'clases'      => array(
				array(
					't'        => 'Nombrar el cuerpo sin vergüenza',
					'min'      => 9,
					'resumen'  => 'Los nombres correctos protegen: un niño que sabe nombrar su cuerpo puede contar lo que le pasa.',
					'video'    => 'NUu0wqfYCag',
					'intro'    => array(
						'Muchas familias usan apodos para las partes íntimas por pudor o costumbre. El problema es que, si un niño no tiene palabras claras para su cuerpo, le cuesta mucho más contar si algo le incomoda o le duele, y a los adultos les cuesta más entenderle.',
						'Usar los nombres correctos (pene, vulva, nalgas) con la misma naturalidad con la que decimos "rodilla" transmite un mensaje poderoso: de tu cuerpo se puede hablar, y conmigo puedes hablar de todo.',
					),
					'claves'   => array(
						'Usa los nombres correctos desde pequeños, con tono tranquilo.',
						'Aprovecha momentos cotidianos: el baño, vestirse, un cuento.',
						'Explica que las partes íntimas son las que cubre el traje de baño.',
						'Si te da pudor, practica primero a solas: el tono se contagia.',
					),
					'practica' => 'Durante el baño de esta semana, juega a nombrar partes del cuerpo ("¿dónde están tus codos?, ¿y tus rodillas?") e incluye las partes íntimas con la misma naturalidad.',
				),
				array(
					't'        => 'Puedes decir "no"',
					'min'      => 10,
					'resumen'  => 'Respetar su "no" en lo cotidiano le enseña que su cuerpo le pertenece.',
					'video'    => 'A__DSCnzNQM',
					'intro'    => array(
						'"Dale un beso a la tía." Es una frase que casi todos hemos dicho. Pero cuando obligamos a un niño a dar afecto que no quiere dar, sin querer le enseñamos que los deseos de los adultos valen más que lo que él siente en su cuerpo.',
						'Respetar su "no" en las situaciones pequeñas (un abrazo, las cosquillas que ya no quiere) es la mejor práctica para que sepa decir "no" en una situación que de verdad lo ponga en riesgo.',
					),
					'claves'   => array(
						'Ofrece alternativas al beso obligatorio: chocar los cinco, decir hola con la mano.',
						'Si pide que paren las cosquillas, se paran de inmediato.',
						'Explica que nadie puede tocar sus partes íntimas, salvo para cuidarlo o curarlo y con un adulto de confianza presente.',
						'Enséñale que también debe respetar el "no" de los demás.',
					),
					'frases'   => array(
						'"Tu cuerpo es tuyo. Si no quieres un abrazo, puedes decir que no."',
						'"Si alguien te toca y no te gusta, me lo cuentas. Nunca te vas a meter en problemas por contarme."',
					),
					'practica' => 'Esta semana, cuando lleguen visitas o vean a familiares, avisa antes a tu hijo que puede saludar como quiera. Si alguien insiste en el beso, apóyalo con amabilidad: "Hoy prefiere saludar con la mano".',
				),
				array(
					't'        => 'Mi círculo de confianza',
					'min'      => 8,
					'resumen'  => 'Saber a quién acudir es tan importante como saber decir "no".',
					'mision'   => array( 'taller', 'Dibujen juntos una mano grande: en cada dedo, tu hijo dibuja o nombra a un adulto de su confianza al que le contaría si algo le preocupa. Péguenla en un lugar visible y súbela como foto.' ),
					'intro'    => array(
						'Los niños necesitan saber que hay más de un adulto al que pueden acudir. A veces el adulto que haría daño es alguien cercano, y a veces el niño siente que no puede contarle a mamá o papá por miedo a que se enojen o se pongan tristes.',
						'Armar juntos su "círculo de confianza" le da un mapa claro: estas son las personas a las que puedes contarle cualquier cosa, y si una no te escucha, se lo dices a otra.',
					),
					'claves'   => array(
						'Elige con tu hijo al menos tres adultos de confianza, dentro y fuera de casa.',
						'Explícale que si un adulto no le cree, debe contárselo a otro.',
						'Repasen el círculo de vez en cuando; puede cambiar con el tiempo.',
					),
					'practica' => 'Hagan la misión de esta clase y conversen sobre por qué eligió a cada persona. Cuéntale a esos adultos que forman parte de su círculo.',
				),
				array(
					't'        => 'Secretos que se cuentan',
					'min'      => 9,
					'resumen'  => 'Las sorpresas se guardan; los secretos que incomodan, siempre se cuentan.',
					'intro'    => array(
						'Quien abusa de un niño casi siempre le pide que guarde un secreto, a veces con regalos y a veces con amenazas. Por eso es tan importante que tu hijo aprenda, desde pequeño, la diferencia entre una sorpresa y un secreto.',
						'Una sorpresa es algo alegre que se guarda poco tiempo y después todos lo saben, como un regalo de cumpleaños. Un secreto que lo hace sentir raro, triste o asustado, o que involucra su cuerpo, siempre se cuenta a un adulto de confianza.',
					),
					'claves'   => array(
						'En tu familia, cambia la palabra "secreto" por "sorpresa" para lo que sí se guarda.',
						'Explica que ningún adulto debe pedirle que guarde secretos sobre su cuerpo.',
						'Si alguna vez te cuenta algo difícil, mantén la calma, créele y agradécele que te lo haya dicho.',
					),
					'frases'   => array(
						'"Las sorpresas son alegres y duran poquito. Los secretos que te hacen sentir mal siempre se cuentan."',
						'"Gracias por contarme. Hiciste muy bien. No es tu culpa."',
					),
					'practica' => 'Jueguen a "¿sorpresa o secreto?": inventa situaciones simples (un regalo para la abuela, alguien que le pide que no cuente que le tocó) y que tu hijo decida cuál es cuál.',
				),
			),
		),

		// ===================== NIÑEZ INICIAL (6 a 8) =====================
		array(
			'titulo'      => 'Autoestima: que se sienta capaz',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'crecer-seguro',
			'habilidad'   => 'Refuerzo de la autoestima',
			'imagen'      => '1549068294-04a001ee0638',
			'resumen'     => 'Cómo acompañar a tu hijo para que confíe en lo que puede hacer, sin exigirle de más ni sobreprotegerlo.',
			'descripcion' => array(
				'La autoestima no se construye con elogios constantes, sino con experiencias: sentirse querido tal como es, probar cosas nuevas, equivocarse y descubrir que puede volver a intentarlo. Entre los 6 y los 8 años tu hijo empieza a compararse con otros y a formarse una idea de "cómo es", y lo que escucha de ti se convierte en su voz interior.',
				'En este curso vas a aprender a elogiar el esfuerzo y no solo el resultado, a acompañar la frustración sin rescatarlo de todo, y a manejar las comparaciones con hermanos y compañeros. Incluye una misión en familia para practicar lo aprendido.',
			),
			'aprenderas'  => array(
				'Qué es la autoestima a esta edad y cómo se forma.',
				'Elogiar de forma que construya, no que presione.',
				'Acompañar la frustración sin resolverle todo.',
				'Manejar las comparaciones dentro y fuera de casa.',
			),
			'clases'      => array(
				array(
					't'        => 'Qué es la autoestima a esta edad',
					'min'      => 11,
					'resumen'  => 'La autoestima es la mirada que tu hijo tiene de sí mismo, y se forma en gran parte con tu mirada.',
					'video'    => 'GiStBvLRTZk',
					'intro'    => array(
						'La autoestima es la valoración que una persona hace de sí misma: cuánto se quiere, cuánto confía en sus capacidades y cuánto siente que merece ser tratada con respeto. En los primeros años se forma sobre todo a partir de cómo lo miran y le hablan los adultos importantes de su vida.',
						'Un niño con buena autoestima no es el que cree que lo hace todo perfecto, sino el que se siente querido aunque se equivoque, y por eso se anima a intentar cosas nuevas.',
					),
					'claves'   => array(
						'Tiene dos partes: sentirse querido (valía) y sentirse capaz (competencia).',
						'Las etiquetas pesan: "eres flojo" o "eres torpe" se convierten en su voz interior.',
						'Pasar tiempo de calidad con él le dice "eres importante para mí".',
						'Darle responsabilidades a su medida le demuestra que confías en él.',
					),
					'practica' => 'Observa durante un día qué frases le dices a tu hijo sobre cómo es él ("eres…"). Anótalas y revisa cuáles quieres seguir usando y cuáles cambiar.',
				),
				array(
					't'        => 'Elogiar el esfuerzo, no el resultado',
					'min'      => 9,
					'resumen'  => '"Qué inteligente eres" no ayuda tanto como "te esforzaste mucho en esto".',
					'video'    => 'a9Scb0dxCZk',
					'intro'    => array(
						'Los elogios genéricos ("¡eres un genio!", "¡qué lindo!") parecen positivos, pero tienen un efecto inesperado: el niño aprende que su valor depende de los resultados, y empieza a evitar los desafíos por miedo a dejar de ser "el inteligente".',
						'Elogiar el esfuerzo, las estrategias y la perseverancia le enseña que las capacidades se desarrollan con práctica. Es lo que en psicología se llama "mentalidad de crecimiento".',
					),
					'claves'   => array(
						'Describe lo que ves en vez de calificar: "Usaste muchos colores y te quedó muy ordenado".',
						'Reconoce el proceso: "Lo intentaste varias veces hasta que salió".',
						'Pregúntale qué opina él de su trabajo antes de dar tu opinión.',
						'No hace falta elogiarlo todo: el elogio sincero vale más.',
					),
					'frases'   => array(
						'"Se nota que practicaste mucho."',
						'"¿Cómo se te ocurrió resolverlo así?"',
						'"Aunque no ganaste, jugaste en equipo todo el partido."',
					),
					'evitar'   => array(
						'"Eres el más inteligente de tu curso."',
						'"Qué fácil, eso lo hace cualquiera."',
					),
					'practica' => 'Durante tres días, cambia al menos un elogio genérico por uno que describa el esfuerzo o la estrategia. Fíjate en cómo reacciona tu hijo.',
				),
				array(
					't'        => 'Cuando se frustra y quiere rendirse',
					'min'      => 12,
					'resumen'  => 'La frustración es una oportunidad para aprender a perseverar, si no lo rescatamos de todo.',
					'intro'    => array(
						'Ver a nuestro hijo frustrado nos duele, y el impulso natural es resolverle el problema: terminarle la tarea, armarle el juguete, decirle "déjalo, no importa". Pero cada vez que lo rescatamos le quitamos la oportunidad de descubrir que puede con más de lo que cree.',
						'Acompañar la frustración significa quedarte cerca, validar lo que siente y ayudarle a encontrar el siguiente paso, sin hacerlo por él.',
					),
					'claves'   => array(
						'Valida primero: "Es frustrante cuando algo no sale".',
						'Divide el desafío en pasos pequeños.',
						'Pregunta en vez de resolver: "¿Qué podrías probar ahora?".',
						'Propón una pausa si la emoción es muy intensa, y vuelvan después.',
						'Cuéntale tus propios errores y cómo los resolviste.',
					),
					'frases'   => array(
						'"Todavía no te sale. Todavía."',
						'"Respiremos un momento y lo miramos de nuevo."',
					),
					'practica' => 'Escoge una actividad que le cueste (un rompecabezas, andar en bicicleta, una tarea). Acompáñalo sin hacerla por él, usando solo preguntas y ánimo. Celebra el esfuerzo al final, aunque no la termine.',
				),
				array(
					't'        => 'Comparaciones con hermanos y compañeros',
					'min'      => 10,
					'resumen'  => 'Cada hijo necesita sentirse valorado por quién es, no en relación con los demás.',
					'video'    => 'NC8Gf_mA8UU',
					'intro'    => array(
						'"Mira a tu hermana, ella sí ordena su pieza." Las comparaciones suelen decirse con buena intención, para motivar, pero casi siempre producen lo contrario: resentimiento hacia el otro y la sensación de no ser suficiente.',
						'A esta edad además aparecen las comparaciones en el colegio: notas, habilidades, quién corre más rápido. Tu rol es ayudarle a mirar su propio progreso, no el de los demás.',
					),
					'claves'   => array(
						'Habla de la conducta que esperas, sin mencionar a otro niño.',
						'Compara a tu hijo solo consigo mismo: "Hace un mes esto te costaba mucho".',
						'Busca y nombra lo que es único de cada hijo.',
						'Si él se compara ("soy el peor"), escúchalo antes de contradecirlo.',
					),
					'practica' => 'Escribe tres cualidades de cada uno de tus hijos que no tengan que ver con notas ni logros (por ejemplo: es generoso, tiene buen humor). Busca un momento para decírselas.',
				),
				array(
					't'        => 'Misión en familia: tres cosas que hiciste bien',
					'min'      => 8,
					'resumen'  => 'Una rutina simple para cerrar la semana mirando lo que salió bien.',
					'mision'   => array( 'casa', 'Pregúntale a tu hijo: ¿qué fue lo que mejor hiciste esta semana, aunque te haya costado? Anoten juntos tres cosas y cuéntale tú también tres cosas tuyas.' ),
					'intro'    => array(
						'Los rituales familiares pequeños tienen un efecto grande. Dedicar unos minutos a la semana a recordar juntos lo que salió bien entrena la mirada de tu hijo para reconocer sus propios logros, en lugar de fijarse solo en lo que falló.',
						'Lo importante es que no sean solo resultados ("saqué un 7"), sino también esfuerzos y actitudes ("ayudé a un compañero", "no me rendí con la tarea de matemáticas").',
					),
					'claves'   => array(
						'Hazlo en un momento tranquilo: la cena, el camino al colegio, antes de dormir.',
						'Participa tú también: los niños aprenden más de lo que ven que de lo que oyen.',
						'Guarden las listas en una caja o cuaderno para releerlas más adelante.',
					),
					'practica' => 'Hagan la misión de esta clase y conviértanla en un ritual de todos los domingos durante un mes.',
				),
			),
		),

		array(
			'titulo'      => 'Bullying: prevenir y actuar',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'proteger',
			'habilidad'   => 'Prevención y manejo del bullying',
			'imagen'      => '1528820713738-a43de1b61084',
			'resumen'     => 'Cómo detectar a tiempo una situación de bullying, cómo hablarlo con tu hijo y con el colegio, y qué hacer si ya está pasando.',
			'descripcion' => array(
				'El bullying no es una pelea entre compañeros ni "cosas de niños": es un maltrato repetido en el tiempo, con una diferencia de poder, que puede dejar huellas profundas. Muchas veces los niños no lo cuentan, por vergüenza o por miedo a empeorar las cosas, así que el papel de la familia para detectarlo es clave.',
				'En este curso aprenderás a distinguir el bullying de un conflicto puntual, a reconocer las señales de alerta, a abrir la conversación sin presionar, a coordinarte con el colegio y también qué hacer si es tu hijo quien molesta. Incluye un caso práctico para practicar cómo reaccionar.',
			),
			'aprenderas'  => array(
				'Diferenciar bullying de un conflicto entre compañeros.',
				'Reconocer las señales de alerta en casa.',
				'Abrir la conversación sin presionar.',
				'Coordinarte con el colegio de forma efectiva.',
				'Qué hacer si tu hijo es quien molesta.',
			),
			'clases'      => array(
				array(
					't'        => 'Qué es y qué no es bullying',
					'min'      => 12,
					'resumen'  => 'Repetición, intención y desequilibrio de poder: las tres señas del acoso escolar.',
					'video'    => 'TFBYc2UBs3g',
					'intro'    => array(
						'No toda pelea es bullying. Dos compañeros que discuten por un juego, o un niño que un día dice algo hiriente, están teniendo un conflicto: es parte de aprender a convivir y hay que acompañarlo, pero no es acoso.',
						'Hablamos de bullying cuando se cumplen tres condiciones: la agresión se repite en el tiempo, hay intención de dañar, y existe un desequilibrio de poder (por fuerza, edad, popularidad o porque son varios contra uno). Puede ser físico, verbal, social (excluir, esparcir rumores) o digital.',
					),
					'claves'   => array(
						'Conflicto: puntual y entre iguales. Bullying: repetido y con desequilibrio de poder.',
						'La exclusión y los rumores también son bullying, aunque no dejen marcas.',
						'Los testigos tienen un rol enorme: pueden frenarlo o reforzarlo.',
						'Nunca es culpa del niño que lo sufre.',
					),
					'practica' => 'Conversa con tu hijo sobre qué es el bullying usando un ejemplo de un cuento o una película. Pregúntale si ha visto algo parecido en su colegio, sin presionar.',
				),
				array(
					't'        => 'Señales de alerta en casa',
					'min'      => 10,
					'resumen'  => 'Cambios de ánimo, excusas para no ir al colegio y objetos que desaparecen: qué mirar.',
					'video'    => 'FZc7lUl2BIc',
					'caso'     => 'bullying-colegio',
					'intro'    => array(
						'La mayoría de los niños que sufren bullying no lo cuentan directamente. Lo que sí suele aparecer son cambios: en su ánimo, en sus ganas de ir al colegio, en su cuerpo o en sus cosas.',
						'Una señal aislada no significa necesariamente acoso, pero varias juntas, o un cambio brusco, merecen una conversación tranquila.',
					),
					'claves'   => array(
						'No quiere ir al colegio o se queja seguido de dolor de guatita o de cabeza en la mañana.',
						'Vuelve con ropa rota, sin sus útiles o sin la colación.',
						'Deja de mencionar a sus amigos o no lo invitan a ningún cumpleaños.',
						'Está más irritable, triste, duerme mal o tiene pesadillas.',
						'Evita ciertos lugares o recorridos.',
					),
					'practica' => 'Durante una semana, dedica diez minutos al día a conversar sobre su día con preguntas concretas ("¿con quién jugaste en el recreo?") y anota si notas alguno de estos cambios.',
				),
				array(
					't'        => 'Cómo abrir la conversación',
					'min'      => 11,
					'resumen'  => 'Preguntas que abren, reacciones que cierran: cómo lograr que te cuente.',
					'intro'    => array(
						'Si sospechas que algo pasa, la forma en que preguntes marca la diferencia. Un interrogatorio o una reacción muy alarmada puede hacer que tu hijo se cierre, sienta que te preocupa de más o tema que vayas al colegio a "hacer un escándalo".',
						'Busca un momento relajado (un paseo, cocinar juntos, el auto) y empieza de forma indirecta. Lo más importante es que sienta que puede contarte sin que pierdas el control.',
					),
					'claves'   => array(
						'Pregunta de forma indirecta: "¿Hay alguien en tu curso a quien molesten?".',
						'Escucha sin interrumpir ni juzgar, aunque lo que cuente te duela.',
						'Créele y agradece que te lo cuente.',
						'No le prometas guardar el secreto: prométele que lo van a resolver juntos.',
						'Evita decir "defiéndete" o "pégale tú también".',
					),
					'frases'   => array(
						'"Gracias por contarme. No es tu culpa."',
						'"Vamos a buscar juntos una solución. No estás solo."',
					),
					'practica' => 'Elige un momento sin apuro y prueba una pregunta indirecta. Si no quiere hablar, respétalo y dile que puede contarte cuando quiera.',
				),
				array(
					't'        => 'Hablar con el colegio: qué pedir',
					'min'      => 13,
					'resumen'  => 'Una reunión preparada y por escrito es mucho más efectiva que un reclamo en caliente.',
					'video'    => '1NcfrStBJLo',
					'intro'    => array(
						'El colegio tiene la responsabilidad de proteger a tu hijo y, en la mayoría de los países, la obligación de activar un protocolo cuando se denuncia un caso de acoso. Para que eso funcione, conviene llegar con información clara y una actitud de colaboración.',
						'Pide una reunión con el profesor jefe o encargado de convivencia, lleva anotados hechos concretos (qué pasó, cuándo, quiénes) y sal de la reunión con acuerdos y plazos.',
					),
					'claves'   => array(
						'Anota fechas, hechos y nombres antes de la reunión.',
						'Pide que te expliquen el protocolo de convivencia del colegio.',
						'Acuerden medidas concretas y una fecha para revisar cómo va.',
						'Deja registro por correo de lo conversado.',
						'No confrontes directamente al otro niño ni a su familia.',
					),
					'practica' => 'Si hoy no hay un problema, pide igual en el colegio el protocolo de convivencia escolar y léelo. Saber cómo funciona te dará tranquilidad si alguna vez lo necesitas.',
				),
				array(
					't'        => 'Si tu hijo es quien molesta',
					'min'      => 12,
					'resumen'  => 'Enterarte de que tu hijo agrede a otros duele, pero también es una oportunidad de enseñarle.',
					'video'    => 'CG66cygStGw',
					'mision'   => array( 'taller', 'Hagan juntos un dibujo o una carta sobre cómo se sintió esta semana en el colegio y con quién le gusta jugar. Conversen sobre lo que dibujó y súbanlo como foto de recuerdo.' ),
					'intro'    => array(
						'Ningún padre quiere escuchar que su hijo molesta a otros. La primera reacción suele ser negarlo o defenderlo. Pero los niños que ejercen bullying también necesitan ayuda: a veces imitan lo que ven, buscan pertenecer a un grupo o están pasando por algo difícil.',
						'La respuesta no es el castigo humillante, sino ayudarlo a entender el daño que causó, a ponerse en el lugar del otro y a reparar.',
					),
					'claves'   => array(
						'Escucha la información del colegio sin ponerte a la defensiva.',
						'Habla con tu hijo con calma: quieres entender, no solo castigar.',
						'Ayúdalo a ponerse en el lugar del otro: "¿Cómo crees que se sintió?".',
						'Busquen juntos una forma de reparar el daño.',
						'Revisa qué modelos de trato ve en casa y en las pantallas.',
					),
					'practica' => 'Conversa con tu hijo sobre una situación (real o de una película) en que alguien fue excluido o molestado. Pregúntale qué podría haber hecho cada personaje para ayudar.',
				),
			),
		),

		array(
			'titulo'      => 'Hablar de todo sin que se cierre',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'conectar',
			'habilidad'   => 'Comunicación con tu hijo',
			'imagen'      => '1475609471617-0ef53b59cff5',
			'resumen'     => 'Cómo preguntar para que te cuente, cómo escuchar sin corregir de inmediato y qué hacer cuando no quiere hablar.',
			'descripcion' => array(
				'"¿Cómo te fue?" "Bien." "¿Qué hiciste?" "Nada." Es la conversación más repetida del mundo entre padres e hijos. Y detrás de ese "nada" suele haber un día lleno de cosas que tu hijo no sabe cómo contar, o que no le dan ganas de contar en ese momento.',
				'La comunicación que construyas ahora es la base de la confianza que tendrán en la adolescencia. En este curso vas a aprender preguntas que sí abren conversación, a escuchar sin saltar a dar consejos y a encontrar los momentos del día en que tu hijo está más dispuesto a hablar.',
			),
			'aprenderas'  => array(
				'Por qué los niños responden con monosílabos.',
				'Preguntas concretas que invitan a contar.',
				'Escuchar primero y aconsejar después.',
				'Aprovechar los momentos en que se abren.',
			),
			'clases'      => array(
				array(
					't'        => 'Por qué responde "nada" y "bien"',
					'min'      => 10,
					'resumen'  => 'No es que no quiera contarte: es que la pregunta es demasiado grande.',
					'video'    => 'A_Jm1mN-eo4',
					'intro'    => array(
						'Cuando le preguntas "¿cómo te fue?" a un niño de 7 años, le estás pidiendo que resuma seis u ocho horas de experiencias en una frase. Es una tarea difícil, y más aún si está cansado o con hambre al salir del colegio.',
						'Además, muchos niños necesitan un tiempo para "aterrizar" antes de hablar. No es rechazo: es que todavía están procesando su día.',
					),
					'claves'   => array(
						'Las preguntas generales invitan a respuestas generales.',
						'Justo a la salida del colegio no suele ser el mejor momento.',
						'El tono de interrogatorio cierra; el tono de curiosidad abre.',
						'Contar tú algo de tu día invita a que él también cuente.',
					),
					'practica' => 'Hoy, en vez de preguntar "¿cómo te fue?", cuéntale algo pequeño de tu día (algo gracioso, algo que te costó) y espera. Muchas veces el niño responde con una historia suya.',
				),
				array(
					't'        => 'Preguntas que sí abren conversación',
					'min'      => 9,
					'resumen'  => 'Preguntas concretas, divertidas y específicas para reemplazar el "¿cómo te fue?".',
					'video'    => '3DZu7s-8rwQ',
					'intro'    => array(
						'Las preguntas concretas son más fáciles de responder y suelen llevar a otras historias. En lugar de pedirle que resuma el día, pregúntale por un momento específico, una persona o una emoción.',
						'Tener un pequeño repertorio de preguntas te ayuda a no caer siempre en las mismas. Y si alguna vez no quiere responder, está bien: la puerta queda abierta.',
					),
					'frases'   => array(
						'"¿Qué fue lo más divertido de hoy? ¿Y lo más aburrido?"',
						'"¿Con quién te sentaste en la colación?"',
						'"Si pudieras repetir un momento de hoy, ¿cuál sería?"',
						'"¿Alguien hizo algo amable hoy? ¿Y tú?"',
						'"¿Pasó algo que te hizo enojar o sentir incómodo?"',
					),
					'claves'   => array(
						'Varía las preguntas para que no se sienta como un trámite.',
						'Respeta si no quiere contestar en ese momento.',
						'Recuerda lo que te contó y pregúntale después: "¿Cómo siguió lo de…?".',
					),
					'practica' => 'Escribe en un papel cinco preguntas de esta clase y pruébalas durante la semana. Anota cuál funcionó mejor con tu hijo.',
				),
				array(
					't'        => 'Escuchar sin corregir de inmediato',
					'min'      => 11,
					'resumen'  => 'Si cada vez que cuenta algo recibe un sermón, dejará de contar.',
					'intro'    => array(
						'Cuando tu hijo te cuenta que se peleó con un amigo o que le fue mal en una prueba, el impulso es dar la solución o la lección: "Tienes que estudiar más", "No debiste decirle eso". Aunque tengas razón, si la conversación siempre termina en consejo o reto, tu hijo aprende que contarte tiene un costo.',
						'Escuchar de verdad significa primero entender lo que siente, repetirlo con tus palabras y recién después, si hace falta, pensar juntos qué hacer.',
					),
					'claves'   => array(
						'Primero escucha hasta el final, sin interrumpir.',
						'Refleja la emoción: "Parece que te dio mucha rabia".',
						'Pregunta antes de aconsejar: "¿Quieres que pensemos juntos qué hacer?".',
						'Guarda el sermón: muchas veces basta con haber sido escuchado.',
					),
					'evitar'   => array(
						'"Eso te pasa por…"',
						'"No es para tanto."',
						'Mirar el celular mientras te cuenta.',
					),
					'practica' => 'La próxima vez que te cuente un problema, cuenta mentalmente hasta cinco antes de responder y empieza por nombrar lo que crees que siente.',
				),
				array(
					't'        => 'El momento del día que mejor funciona',
					'min'      => 8,
					'resumen'  => 'Los niños se abren cuando no se sienten observados: el auto, la cocina, antes de dormir.',
					'mision'   => array( 'casa', 'Durante una semana, reserva 15 minutos al día de "tiempo especial" con tu hijo, sin pantallas y haciendo lo que él elija. Al final de la semana, pregúntale qué fue lo que más le gustó.' ),
					'intro'    => array(
						'Muchas de las conversaciones más importantes con los hijos no ocurren sentados frente a frente, sino haciendo otra cosa al lado: en el auto, cocinando, armando un rompecabezas o justo antes de dormir, con la luz apagada.',
						'Cuando no hay contacto visual directo y las manos están ocupadas, la presión baja y es más fácil contar cosas difíciles.',
					),
					'claves'   => array(
						'Identifica en qué momentos tu hijo suele hablar más.',
						'Protege ese momento de interrupciones y pantallas.',
						'El rato antes de dormir es especialmente valioso: consérvalo.',
						'El tiempo de juego compartido también es comunicación.',
					),
					'practica' => 'Haz la misión de esta clase: el "tiempo especial" diario es una de las herramientas más simples y efectivas para fortalecer el vínculo.',
				),
			),
		),

		array(
			'titulo'      => 'Pantallas y videojuegos en casa',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'mundo-digital',
			'habilidad'   => 'Acuerdos digitales en casa',
			'imagen'      => '1495654794940-1c0cd2aeedc1',
			'resumen'     => 'Cómo poner límites de pantalla que se sostengan, qué contenidos elegir y cómo evitar la pelea diaria al apagar.',
			'descripcion' => array(
				'Las pantallas llegaron para quedarse, y prohibirlas del todo no suele funcionar. Pero tampoco es sano dejarlas sin ningún límite: el tiempo frente a una pantalla compite con el sueño, el juego libre, el movimiento y la conversación en familia, que es lo que más necesita un niño para desarrollarse.',
				'En este curso vas a ver cuánto tiempo es razonable a esta edad, cómo armar acuerdos que tu hijo entienda y pueda cumplir, cómo manejar el momento de apagar y cómo acompañar lo que mira y con quién juega.',
			),
			'aprenderas'  => array(
				'Qué recomiendan los pediatras según la edad.',
				'Armar un plan familiar de pantallas.',
				'Manejar la frustración al apagar.',
				'Elegir y acompañar contenidos y juegos.',
			),
			'clases'      => array(
				array(
					't'        => 'Cuánto es demasiado a esta edad',
					'min'      => 10,
					'resumen'  => 'Más que minutos exactos, importa qué desplazan las pantallas: sueño, juego y familia.',
					'video'    => 'GGQMIHkdtmI',
					'intro'    => array(
						'Las sociedades de pediatría suelen recomendar que los niños en edad escolar tengan un tiempo de pantalla recreativo limitado y constante, y que nunca reemplace las horas de sueño, la actividad física, las tareas ni el tiempo en familia.',
						'Más útil que contar minutos es mirar el equilibrio del día: ¿durmió lo suficiente?, ¿se movió?, ¿jugó sin pantallas?, ¿conversaron? Si la respuesta es sí, las pantallas tienen su lugar.',
					),
					'claves'   => array(
						'Nada de pantallas una hora antes de dormir: alteran el sueño.',
						'Sin pantallas en las comidas ni en el dormitorio.',
						'Prefiere contenidos de calidad, sin publicidad y acordes a su edad.',
						'Los adultos somos el modelo: revisa tu propio uso.',
					),
					'practica' => 'Durante tres días, anota cuánto tiempo pasa tu hijo frente a pantallas y en qué momentos. No cambies nada todavía: solo observa.',
				),
				array(
					't'        => 'Acuerdos que se pueden cumplir',
					'min'      => 12,
					'resumen'  => 'Reglas claras, pocas y visibles, acordadas en familia.',
					'video'    => 'WZnEh2r8pkA',
					'intro'    => array(
						'Las reglas de pantallas que funcionan tienen tres características: son pocas, son claras (se sabe exactamente cuándo y cuánto) y son siempre las mismas, sin depender del humor del adulto ese día.',
						'Si tu hijo participa en armarlas, es mucho más probable que las respete. No se trata de negociar todo, sino de explicarle el porqué y escuchar sus propuestas.',
					),
					'claves'   => array(
						'Define cuándo sí (por ejemplo, después de las tareas) y cuándo no (comidas, antes de dormir).',
						'Usa un temporizador visible en vez de "un ratito más".',
						'Escriban las reglas y péguenlas en un lugar visible.',
						'Las reglas también valen para los adultos en los momentos compartidos.',
					),
					'practica' => 'Arma con tu hijo un borrador de tres reglas de pantallas para la casa. Pruébenlas una semana y ajústenlas juntos.',
				),
				array(
					't'        => 'La rabieta cuando se apaga',
					'min'      => 9,
					'resumen'  => 'Anticipar, avisar y validar: cómo hacer que el apagado sea menos dramático.',
					'caso'     => 'pantallas-apagar',
					'intro'    => array(
						'Los videojuegos y los videos están diseñados para que queramos seguir: siempre hay un nivel más, un capítulo más. Por eso es tan difícil para un niño cortar, y es normal que se frustre. No es que sea malcriado; es que le estás pidiendo algo difícil.',
						'Lo que más ayuda es anticipar el final, dejar que termine lo que está haciendo si es posible y tener preparado qué viene después.',
					),
					'claves'   => array(
						'Avisa con anticipación: "En diez minutos se apaga".',
						'Cierra en un punto natural: al terminar la partida o el capítulo.',
						'Valida la frustración sin ceder: "Sé que querías seguir".',
						'Ten preparada una actividad atractiva para después.',
					),
					'practica' => 'Esta semana, usa siempre el mismo aviso previo y un temporizador. Observa si con los días el momento de apagar se vuelve más fácil.',
				),
				array(
					't'        => 'Qué mira y con quién juega',
					'min'      => 11,
					'resumen'  => 'Acompañar el contenido es tan importante como limitar el tiempo.',
					'mision'   => array( 'casa', 'Pídele a tu hijo que te enseñe a jugar su videojuego o que te muestre su video favorito. Jueguen o miren juntos 20 minutos y conversen sobre qué le gusta de ese contenido.' ),
					'intro'    => array(
						'No todo el tiempo de pantalla es igual. No es lo mismo ver un documental juntos que pasar una hora viendo videos cortos uno tras otro, ni jugar un juego de construcción que un juego en línea con desconocidos.',
						'Interesarte por lo que tu hijo mira y juega te permite conocer sus gustos, detectar contenidos inadecuados y mantener abierta la conversación para cuando aparezca algo que lo incomode.',
					),
					'claves'   => array(
						'Revisa la clasificación por edad de juegos y aplicaciones.',
						'Activa controles parentales, pero no los uses como única protección.',
						'Desactiva el chat con desconocidos en los juegos en línea.',
						'Explícale que si algo lo asusta o incomoda, puede contártelo sin que le quites el juego.',
					),
					'practica' => 'Haz la misión de esta clase. Además, revisa la configuración de privacidad de las aplicaciones y juegos que usa.',
				),
			),
		),

		array(
			'titulo'      => 'Hermanos: celos, peleas y límites',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'convivir',
			'habilidad'   => 'Convivencia entre hermanos',
			'imagen'      => '1605713288610-00c1c630ca1e',
			'resumen'     => 'Cómo manejar los celos y las peleas entre hermanos, cuándo intervenir y cómo poner reglas de convivencia justas.',
			'descripcion' => array(
				'Los hermanos se quieren y se pelean, a veces en el mismo minuto. Los celos y los conflictos son normales: compiten por lo más valioso que tienen, que es la atención y el cariño de sus padres. Bien acompañadas, esas peleas son un entrenamiento extraordinario para aprender a negociar, ceder y reparar.',
				'En este curso vas a aprender a reconocer los celos, a decidir cuándo intervenir y cuándo dejar que resuelvan solos, a no hacer de juez en cada pelea y a construir reglas de convivencia que valgan para todos.',
			),
			'aprenderas'  => array(
				'Entender de dónde vienen los celos.',
				'Cuándo intervenir en una pelea y cómo.',
				'Evitar el rol de juez permanente.',
				'Reglas de convivencia claras y justas.',
			),
			'clases'      => array(
				array(
					't'        => 'De dónde vienen los celos',
					'min'      => 10,
					'resumen'  => 'Los celos no se eliminan: se acompañan dando a cada hijo su lugar.',
					'video'    => 'qrgfiAEaNiQ',
					'intro'    => array(
						'Los celos entre hermanos son una emoción natural: aparecen cuando un niño siente que puede perder el amor o la atención de sus padres. Son especialmente intensos con la llegada de un hermano nuevo, pero pueden aparecer a cualquier edad.',
						'Negarlos ("no digas eso, tú quieres a tu hermano") no los hace desaparecer; los esconde. Lo que ayuda es permitir que los exprese con palabras y asegurarle, con hechos, que su lugar está a salvo.',
					),
					'claves'   => array(
						'Permite que exprese lo que siente, sin permitir que lastime.',
						'Tiempo a solas con cada hijo, aunque sean 10 minutos.',
						'Tratar a cada hijo según lo que necesita, no exactamente igual.',
						'Evita las comparaciones, aunque sean positivas.',
					),
					'frases'   => array(
						'"A veces te molesta que tu hermana tenga mi atención. Es normal sentir eso."',
						'"Te quiero a ti de una manera única. Nadie ocupa tu lugar."',
					),
					'practica' => 'Esta semana, organiza un momento a solas con cada uno de tus hijos, haciendo algo que ese hijo elija.',
				),
				array(
					't'        => 'Cuándo intervenir en una pelea',
					'min'      => 11,
					'resumen'  => 'No toda pelea necesita un adulto: aprende a graduar tu intervención.',
					'video'    => 'rbVuyvnVt0A',
					'intro'    => array(
						'Si intervenimos en cada discusión, los hermanos no aprenden a resolver sus conflictos: aprenden a llamar al adulto. Si nunca intervenimos, el más fuerte siempre gana. El punto medio es graduar nuestra intervención según la situación.',
						'Una buena regla: si es una discusión verbal entre iguales, observa desde lejos; si suben los decibeles, acércate y ayúdalos a hablar; si hay golpes o uno está claramente en desventaja, separa y protege.',
					),
					'claves'   => array(
						'Discusión menor: deja que intenten resolverla.',
						'Conflicto que escala: acércate como mediador, no como juez.',
						'Agresión física: separa de inmediato, sin buscar culpables en ese momento.',
						'Después de la calma, ayuda a que cada uno diga qué necesita.',
					),
					'frases'   => array(
						'"Veo a dos niños que quieren el mismo juguete. ¿Qué se les ocurre?"',
						'"Primero nos calmamos, después conversamos."',
					),
					'practica' => 'La próxima pelea, espera treinta segundos antes de intervenir y observa si pueden resolverla solos. Si no, prueba el rol de mediador con la primera frase de esta clase.',
				),
				array(
					't'        => 'No ser el juez de todo',
					'min'      => 9,
					'resumen'  => 'Buscar culpables alimenta la competencia; buscar soluciones la calma.',
					'mision'   => array( 'casa', 'Reúne a tus hijos y armen juntos tres "reglas de la casa" para cuando se enojan entre ellos (por ejemplo: no se pega, se pide las cosas, se puede pedir tiempo para calmarse). Escríbanlas y péguenlas en un lugar visible.' ),
					'intro'    => array(
						'"¡Él empezó!" "¡No, fue ella!" Cuando actuamos como jueces, cada pelea se convierte en un juicio donde los hermanos compiten por ganar nuestro favor, y el que pierde queda resentido con el otro.',
						'En lugar de averiguar quién tiene la culpa (casi nunca lo sabremos con certeza), es más útil enfocarse en el problema y en cómo solucionarlo juntos.',
					),
					'claves'   => array(
						'Describe el problema sin culpar: "Hay un control y dos personas que quieren usarlo".',
						'Pídeles que propongan soluciones.',
						'Si no llegan a acuerdo, el recurso se guarda por un rato.',
						'Reconoce cuando resuelvan bien un conflicto.',
					),
					'practica' => 'Haz la misión de esta clase. Cuando haya una pelea, remítete a las reglas que armaron juntos en lugar de decidir tú quién tiene la razón.',
				),
				array(
					't'        => 'Límites con cariño para todos',
					'min'      => 10,
					'resumen'  => 'Reglas iguales, consecuencias lógicas y reparación en vez de castigo.',
					'video'    => 'KechAjsR3uM',
					'intro'    => array(
						'En una casa con varios hijos, las reglas claras son un alivio para todos: evitan discusiones y dan la sensación de justicia. Lo importante es que valgan para todos, que las consecuencias tengan relación con lo que pasó y que el foco esté en reparar, no en humillar.',
						'La disciplina positiva propone reemplazar el castigo por consecuencias lógicas: si se rompió algo, se ayuda a arreglarlo; si se dijo algo hiriente, se busca una forma de reparar.',
					),
					'claves'   => array(
						'Las reglas básicas valen para todos los hermanos, adaptadas a la edad.',
						'Consecuencias relacionadas y proporcionales a lo que pasó.',
						'Reparar es más educativo que castigar.',
						'Evita castigar a todos por lo que hizo uno.',
					),
					'practica' => 'Revisa si hoy usas algún castigo que no tenga relación con la falta (por ejemplo, sin postre por pelear). Piensa una consecuencia lógica para reemplazarlo.',
				),
			),
		),

		array(
			'titulo'      => 'Colegio: tareas, motivación y hábitos',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'vida-escolar',
			'habilidad'   => 'Hábitos de estudio en casa',
			'imagen'      => '1588072432836-e10032774350',
			'resumen'     => 'Cómo acompañar las tareas sin hacerlas por él, cuidar su motivación por aprender y construir hábitos de estudio.',
			'descripcion' => array(
				'Los primeros años de colegio son cuando se forman los hábitos y la relación con el aprendizaje que tu hijo llevará por muchos años. Si las tareas se convierten en una pelea diaria, lo que aprende no es matemáticas, sino que estudiar es desagradable.',
				'En este curso vas a encontrar ideas prácticas para armar una rutina de tareas, crear un espacio de estudio, acompañar sin sustituir y cuidar la motivación, sobre todo cuando le va mal.',
			),
			'aprenderas'  => array(
				'Armar una rutina de tareas que funcione.',
				'Acompañar sin hacer la tarea por él.',
				'Cuidar la motivación por aprender.',
				'Qué hacer cuando le va mal en el colegio.',
			),
			'clases'      => array(
				array(
					't'        => 'Una rutina de tareas sin peleas',
					'min'      => 10,
					'resumen'  => 'Mismo lugar, mismo horario y descansos claros: la rutina ahorra discusiones.',
					'video'    => 'UkCK69FPGCY',
					'intro'    => array(
						'Cuando no hay una rutina, cada tarde es una negociación: "¿ahora o después?", "¿cuánto falta?". La rutina elimina esa negociación porque el momento de las tareas deja de ser una decisión diaria.',
						'La rutina ideal considera un tiempo para descansar y comer algo al llegar del colegio, un horario fijo para las tareas y algo agradable después.',
					),
					'claves'   => array(
						'Un horario fijo, después de un descanso breve.',
						'Bloques cortos de trabajo con pausas (por ejemplo, 20 minutos y 5 de descanso).',
						'Empezar por lo más difícil, cuando hay más energía.',
						'Revisar juntos la mochila y la agenda al terminar.',
					),
					'practica' => 'Arma con tu hijo un horario de la tarde en una hoja: descanso, tareas, juego, cena, dormir. Pruébenlo una semana.',
				),
				array(
					't'        => 'Acompañar sin hacerla por él',
					'min'      => 9,
					'resumen'  => 'Tu rol es estar disponible, no ser el que resuelve.',
					'intro'    => array(
						'Cuando vemos que nuestro hijo se equivoca en la tarea, es tentador corregirle todo o incluso terminarla nosotros para que quede bien. Pero la tarea es de él, y sus errores le sirven a la profesora para saber qué necesita reforzar.',
						'Acompañar es estar cerca, ayudar a organizarse, responder dudas con preguntas y animarlo cuando se frustra.',
					),
					'claves'   => array(
						'Ayúdalo a entender la instrucción, no a resolver el ejercicio.',
						'Responde con preguntas: "¿Qué te pide este ejercicio?".',
						'Deja que se equivoque: la profesora necesita ver sus errores reales.',
						'Retírate un poco a medida que gana autonomía.',
					),
					'frases'   => array(
						'"¿Qué parte entendiste y cuál no?"',
						'"Muéstrame cómo lo pensaste."',
					),
					'practica' => 'La próxima tarea, siéntate cerca pero haciendo otra cosa (leyendo, por ejemplo). Ayuda solo cuando te lo pida y responde con una pregunta.',
				),
				array(
					't'        => 'La motivación se cuida',
					'min'      => 11,
					'resumen'  => 'Curiosidad, logros pequeños y sentido: lo que de verdad motiva a aprender.',
					'video'    => '5WnjvtvuzVA',
					'intro'    => array(
						'Los premios y castigos pueden funcionar a corto plazo, pero no construyen el gusto por aprender. La motivación que dura viene de la curiosidad, de sentirse capaz y de entender para qué sirve lo que se aprende.',
						'Tú puedes alimentar esa motivación mostrándote interesado en lo que aprende, conectando lo del colegio con la vida diaria y celebrando el esfuerzo.',
					),
					'claves'   => array(
						'Muestra interés genuino: "¿Qué fue lo más interesante que aprendiste hoy?".',
						'Conecta lo que aprende con la vida real: cocinar, comprar, viajar.',
						'Metas pequeñas y alcanzables dan sensación de logro.',
						'Evita premiar cada nota: premia el esfuerzo con tiempo juntos.',
					),
					'practica' => 'Busca una forma de usar esta semana algo que tu hijo esté aprendiendo en el colegio en una situación real (por ejemplo, pagar en una tienda si está aprendiendo sumas).',
				),
				array(
					't'        => 'Un rincón para estudiar',
					'min'      => 8,
					'resumen'  => 'Un espacio fijo, ordenado y sin pantallas ayuda a concentrarse.',
					'mision'   => array( 'taller', 'Armen juntos el rincón de estudio de tu hijo: que elija dónde, qué útiles tendrá a mano y cómo decorarlo. Tomen una foto del resultado y súbanla.' ),
					'intro'    => array(
						'No hace falta un escritorio especial: basta con un lugar fijo, con buena luz, sin televisión cerca y con los útiles a mano. Tener siempre el mismo lugar ayuda al cerebro a entrar en "modo estudio".',
						'Si tu hijo participa en armarlo, lo sentirá suyo y le costará menos sentarse ahí.',
					),
					'claves'   => array(
						'Buena luz y una silla cómoda a su altura.',
						'Útiles a mano para no levantarse a cada rato.',
						'Lejos del televisor y sin celular durante las tareas.',
						'Que tenga algo personal: un dibujo, una foto.',
					),
					'practica' => 'Haz la misión de esta clase y usen el rincón todos los días de la semana.',
				),
			),
		),

		array(
			'titulo'      => 'Miedos y preocupaciones',
			'etapa'       => 'ninez-inicial',
			'eje'         => 'emociones',
			'habilidad'   => 'Acompañar los miedos infantiles',
			'imagen'      => '1497340525489-441e8427c980',
			'resumen'     => 'Qué miedos son normales a esta edad, cómo acompañarlos sin agrandarlos ni ridiculizarlos, y cuándo pedir ayuda.',
			'descripcion' => array(
				'Miedo a la oscuridad, a los monstruos, a quedarse solo, a equivocarse frente al curso. Los miedos son parte del desarrollo y cumplen una función: nos protegen. A esta edad aparecen muchos nuevos, porque tu hijo empieza a imaginar más y a entender que en el mundo hay riesgos.',
				'En este curso vas a aprender a distinguir los miedos esperables de los que necesitan más atención, a acompañarlos sin agrandarlos ni ridiculizarlos, y a enseñarle a tu hijo herramientas para calmarse.',
			),
			'aprenderas'  => array(
				'Qué miedos son esperables a cada edad.',
				'Validar el miedo sin agrandarlo.',
				'Herramientas simples para calmarse.',
				'Señales de que conviene consultar a un profesional.',
			),
			'clases'      => array(
				array(
					't'        => 'Los miedos que son normales',
					'min'      => 10,
					'resumen'  => 'Cada edad trae sus miedos: conocerlos te ayuda a no alarmarte.',
					'video'    => 'wWx1bbIWvrc',
					'intro'    => array(
						'Entre los 4 y los 8 años son muy comunes los miedos a la oscuridad, a los monstruos, a los ruidos fuertes, a los animales y a separarse de los padres. Hacia los 7 u 8 años empiezan a aparecer miedos más "reales": a que les pase algo a los padres, a los ladrones, a hacer el ridículo.',
						'La mayoría de estos miedos son pasajeros y disminuyen con el tiempo y con un acompañamiento tranquilo.',
					),
					'claves'   => array(
						'Los miedos cambian con la edad y la mayoría son pasajeros.',
						'Tu reacción influye: si te alarmas, el miedo crece.',
						'Las noticias o películas no aptas para su edad pueden despertar miedos nuevos.',
					),
					'practica' => 'Conversa con tu hijo sobre sus miedos con una pregunta abierta: "¿Hay algo que te dé miedo últimamente?". Solo escucha y anota lo que te cuente.',
				),
				array(
					't'        => 'Validar sin agrandar',
					'min'      => 9,
					'resumen'  => 'Ni "no seas miedoso" ni sobreprotección: el punto medio que ayuda.',
					'video'    => 'O6_M8z_df8E',
					'intro'    => array(
						'Hay dos errores frecuentes frente a los miedos. Uno es minimizarlos o burlarse ("no seas miedoso, los monstruos no existen"), lo que hace que el niño se sienta solo con su miedo. El otro es sobreprotegerlo y evitar todo lo que le asusta, lo que le confirma que hay algo muy peligroso.',
						'Lo que ayuda es reconocer que el miedo es real para él, acompañarlo y animarlo a enfrentar la situación de a poco.',
					),
					'claves'   => array(
						'Reconoce la emoción: "Entiendo que te da miedo".',
						'Transmite confianza: "Estoy seguro de que puedes, y yo te acompaño".',
						'Enfrenten la situación de a poco, en pasos pequeños.',
						'Celebra cada pequeño avance.',
					),
					'frases'   => array(
						'"Tener miedo no significa que no seas valiente. Ser valiente es hacerlo aunque dé miedo."',
					),
					'practica' => 'Elige un miedo pequeño de tu hijo y armen juntos una "escalera" de tres pasos para enfrentarlo (por ejemplo, con la oscuridad: dormir con la luz del pasillo, después con una lamparita, después con la puerta entreabierta).',
				),
				array(
					't'        => 'Herramientas para calmarse',
					'min'      => 11,
					'resumen'  => 'Respiración, palabras y objetos de apoyo: un botiquín para los momentos de miedo.',
					'video'    => 'l0GgkwFbfqc',
					'intro'    => array(
						'Cuando un niño tiene miedo, su cuerpo reacciona: el corazón se acelera, respira rápido, se tensa. Enseñarle a reconocer esas señales y a calmar su cuerpo le da una sensación enorme de control.',
						'Las técnicas deben ser simples y practicarse en momentos tranquilos, para que después pueda usarlas cuando las necesite.',
					),
					'claves'   => array(
						'Respiración de la vela: inhalar oliendo una flor, exhalar soplando una vela.',
						'Frases que se repite a sí mismo: "Estoy a salvo", "Esto va a pasar".',
						'Un objeto de apoyo: un peluche, una linterna, una foto.',
						'Practicar las técnicas cuando está tranquilo, no solo en el momento del miedo.',
					),
					'practica' => 'Practiquen juntos la respiración de la vela antes de dormir durante toda la semana, aunque no tenga miedo.',
				),
				array(
					't'        => 'Cuándo pedir ayuda',
					'min'      => 8,
					'resumen'  => 'Si el miedo le impide hacer su vida normal, es momento de consultar.',
					'mision'   => array( 'casa', 'Armen juntos una "caja de la calma" con cosas que ayuden a tu hijo cuando tiene miedo: un peluche, una linterna, una tarjeta con la respiración de la vela y un dibujo de su lugar favorito.' ),
					'intro'    => array(
						'La mayoría de los miedos infantiles se resuelven con tiempo y acompañamiento. Pero algunos se vuelven tan intensos o duraderos que empiezan a afectar la vida diaria: no puede dormir, no quiere ir al colegio, deja de hacer cosas que le gustaban.',
						'En esos casos, consultar con un psicólogo infantil no es exagerar: es darle a tu hijo herramientas a tiempo.',
					),
					'claves'   => array(
						'Consulta si el miedo dura varios meses o va en aumento.',
						'Consulta si le impide ir al colegio, dormir o separarse de ti.',
						'Consulta si aparece después de un hecho difícil (un accidente, una pérdida).',
						'Pedir ayuda es una forma de cuidar, no un fracaso como padre.',
					),
					'practica' => 'Haz la misión de esta clase. Si notas alguna de las señales descritas, conversa con el pediatra o el orientador del colegio.',
				),
			),
		),

		// ===================== NIÑEZ MEDIA (9 a 11) =====================
		array(
			'titulo'      => 'Cyberbullying y el primer celular',
			'etapa'       => 'ninez-media',
			'eje'         => 'mundo-digital',
			'habilidad'   => 'Seguridad digital',
			'imagen'      => '1616422403639-282145aa3e73',
			'resumen'     => 'Qué acordar antes del primer celular, cómo prevenir el ciberacoso y qué hacer si le escribe un desconocido.',
			'descripcion' => array(
				'Entre los 9 y los 11 años muchos niños reciben su primer celular o empiezan a usar chats y redes. Es un paso grande: abre la puerta a amistades, juegos y aprendizajes, pero también al ciberacoso, a contenidos inadecuados y al contacto con desconocidos.',
				'Este curso te ayuda a preparar ese paso: qué conversar antes, qué reglas acordar, cómo reconocer el ciberacoso y qué hacer si algo sale mal. El objetivo no es vigilar todo, sino que tu hijo sepa cuidarse y sepa que puede acudir a ti.',
			),
			'aprenderas'  => array(
				'Cuándo y con qué reglas dar el primer celular.',
				'Reconocer el ciberacoso y cómo actuar.',
				'Enseñar a cuidar la privacidad.',
				'Qué hacer si un desconocido le escribe.',
			),
			'clases'      => array(
				array(
					't'        => 'El primer celular: cuándo y con qué reglas',
					'min'      => 12,
					'resumen'  => 'Más que la edad, importa la madurez y los acuerdos previos.',
					'video'    => 'cPh8-HiTf6E',
					'intro'    => array(
						'No hay una edad "correcta" para el primer celular, pero sí algunas preguntas útiles: ¿para qué lo necesita?, ¿sabe cuidar sus cosas?, ¿te cuenta cuando algo le preocupa?, ¿respeta los límites de pantalla que ya tienen?',
						'Si decides dárselo, conviene hacerlo con acuerdos claros desde el primer día. Es mucho más fácil empezar con reglas que ponerlas después.',
					),
					'claves'   => array(
						'Empieza con funciones limitadas y amplíalas a medida que demuestre responsabilidad.',
						'El celular duerme fuera del dormitorio.',
						'Tú conoces las contraseñas, al menos al principio.',
						'Nada de descargar aplicaciones sin preguntar.',
						'Revisen juntos la configuración de privacidad.',
					),
					'practica' => 'Aunque todavía no tenga celular, conversen sobre qué reglas le parecerían justas. Escucharlo antes hará que las reglas se sientan compartidas.',
				),
				array(
					't'        => 'Qué es el ciberacoso',
					'min'      => 11,
					'resumen'  => 'Insultos, exclusión y fotos compartidas sin permiso: el bullying que sigue en casa.',
					'video'    => 'YyanosTnD14',
					'intro'    => array(
						'El ciberacoso es el bullying que ocurre a través de pantallas: mensajes hirientes, grupos de chat de los que se excluye a alguien, fotos o videos compartidos para burlarse, cuentas falsas. Su particularidad es que no termina al salir del colegio: sigue en el celular, en la casa, a cualquier hora.',
						'Muchos niños no lo cuentan por miedo a que les quiten el celular. Por eso es clave que sepan que contarte nunca va a traer ese castigo.',
					),
					'claves'   => array(
						'Señales: se pone nervioso al recibir mensajes, deja de usar el celular de golpe o lo usa a escondidas.',
						'Pídele que no responda a las agresiones y que guarde capturas de pantalla.',
						'Bloqueen y denuncien al agresor en la plataforma.',
						'Informa al colegio si involucra a compañeros.',
						'Contarte nunca debe significar perder el celular.',
					),
					'practica' => 'Dile explícitamente a tu hijo: "Si alguna vez alguien te molesta por internet, cuéntame. No te voy a quitar el celular por eso". Que lo escuche con esas palabras.',
				),
				array(
					't'        => 'Privacidad: qué se publica y qué no',
					'min'      => 10,
					'resumen'  => 'Lo que se sube a internet queda: enséñale a pensar antes de publicar.',
					'video'    => 'PoLP2oB9oMU',
					'intro'    => array(
						'Los niños suelen compartir información personal sin darse cuenta de los riesgos: el nombre del colegio en el uniforme de una foto, la ubicación, el horario de sus actividades. Y una vez que algo se publica o se envía, se pierde el control sobre dónde termina.',
						'Enseñarle a pensar antes de publicar es una habilidad que le servirá toda la vida.',
					),
					'claves'   => array(
						'Nunca compartir dirección, colegio, teléfono ni ubicación en tiempo real.',
						'Perfiles privados y solo con personas que conoce en persona.',
						'Pedir permiso antes de subir fotos de otros.',
						'La regla de la abuela: no publiques nada que no te gustaría que ella viera.',
					),
					'practica' => 'Revisen juntos la configuración de privacidad de una aplicación que use (o que vaya a usar) y conversen sobre qué información es mejor no compartir.',
				),
				array(
					't'        => 'Si alguien desconocido le escribe',
					'min'      => 13,
					'resumen'  => 'Cómo reconocer un contacto riesgoso y qué hacer: no responder, guardar y contar.',
					'mision'   => array( 'casa', 'Escriban juntos el "acuerdo del celular" de la familia: cinco reglas que ambos se comprometen a cumplir (también tú). Fírmenlo y guárdenlo en un lugar visible.' ),
					'intro'    => array(
						'Algunos adultos usan juegos en línea y redes para contactar niños haciéndose pasar por otros niños. Suelen empezar con halagos y regalos (monedas del juego, por ejemplo), piden secreto y luego fotos o encuentros. Esto se conoce como grooming.',
						'Tu hijo necesita saber que esto existe, sin aterrarlo, y tener clara una regla: si un desconocido le escribe o alguien le pide algo que lo incomoda, no responde, no borra nada y te cuenta.',
					),
					'claves'   => array(
						'Nunca aceptar solicitudes de personas que no conoce en persona.',
						'Nunca enviar fotos ni datos personales a nadie en línea.',
						'Si alguien pide secreto, es una señal de alerta.',
						'No borrar los mensajes: sirven para denunciar.',
						'Ante cualquier caso sospechoso, denuncia a la plataforma y a las autoridades.',
					),
					'frases'   => array(
						'"Si alguien te pide que no me cuentes algo, eso es justo lo que tienes que contarme."',
					),
					'practica' => 'Haz la misión de esta clase. Incluye en el acuerdo qué hacer si alguien desconocido le escribe.',
				),
			),
		),

		array(
			'titulo'      => 'Pubertad: los cambios que vienen',
			'etapa'       => 'ninez-media',
			'eje'         => 'sexualidad-afectividad',
			'habilidad'   => 'Acompañamiento en la pubertad',
			'imagen'      => '1531983412531-1f49a365ffed',
			'resumen'     => 'Cómo hablar de los cambios del cuerpo antes de que lleguen, con información clara y sin incomodidad.',
			'descripcion' => array(
				'La pubertad llega cada vez antes: en muchas niñas los primeros cambios aparecen entre los 8 y los 10 años, y en los niños un poco después. Si tu hijo no sabe lo que va a pasar, los cambios pueden asustarlo o avergonzarlo, y buscará respuestas en internet o con amigos, no siempre en fuentes confiables.',
				'Este curso te ayuda a anticiparte: qué cambios vienen, cómo explicarlos con palabras simples, cómo cuidar su intimidad y cómo responder las preguntas incómodas sin evitar el tema.',
			),
			'aprenderas'  => array(
				'Por qué conviene hablar antes de que empiecen los cambios.',
				'Explicar los cambios del cuerpo con naturalidad.',
				'Respetar el pudor y la privacidad.',
				'Responder preguntas difíciles con calma.',
			),
			'clases'      => array(
				array(
					't'        => 'Por qué conviene hablarlo antes',
					'min'      => 10,
					'resumen'  => 'Un cambio que se espera asusta mucho menos que uno que sorprende.',
					'video'    => '55oC1rpKvXE',
					'intro'    => array(
						'Muchas personas adultas recuerdan su primera menstruación o sus primeros cambios con miedo o vergüenza, porque nadie les había explicado nada. Hablar antes permite que tu hijo viva esos cambios como algo esperado y natural.',
						'No se trata de una única "gran conversación", sino de muchas conversaciones pequeñas que se van profundizando con el tiempo.',
					),
					'claves'   => array(
						'Empieza antes de los primeros cambios, hacia los 8 o 9 años.',
						'Muchas conversaciones cortas funcionan mejor que una larga.',
						'Aprovecha oportunidades: una publicidad, un libro, una pregunta.',
						'Si no sabes algo, está bien decir "no sé, lo buscamos juntos".',
					),
					'practica' => 'Busca en una biblioteca o librería un libro sobre pubertad adecuado para su edad y léanlo juntos, o déjaselo a mano y ofrécete a conversarlo.',
				),
				array(
					't'        => 'Los cambios del cuerpo, con nombre propio',
					'min'      => 12,
					'resumen'  => 'Qué cambia en niñas y niños, explicado con palabras claras.',
					'intro'    => array(
						'Los cambios de la pubertad se deben a las hormonas y ocurren a ritmos muy distintos en cada persona. Es importante que tu hijo sepa que no hay un tiempo "correcto": algunos compañeros cambiarán antes y otros después, y ambas cosas son normales.',
						'Conviene que tanto niñas como niños conozcan los cambios de ambos sexos: ayuda a entender y respetar a los demás.',
					),
					'claves'   => array(
						'En todos: crecimiento rápido, vello, sudor con más olor, acné, cambios de ánimo.',
						'En las niñas: desarrollo de las mamas y la menstruación.',
						'En los niños: cambio de voz, crecimiento de testículos y pene, eyaculaciones nocturnas.',
						'Explica la higiene que acompaña los cambios: ducha diaria, desodorante.',
					),
					'practica' => 'Prepara con tu hija o hijo un pequeño "kit" para la pubertad: desodorante, toallas higiénicas (si corresponde) y un libro. Entrégaselo explicando para qué es cada cosa.',
				),
				array(
					't'        => 'Pudor y privacidad en casa',
					'min'      => 9,
					'resumen'  => 'Su nueva necesidad de intimidad es sana: respetarla fortalece la confianza.',
					'intro'    => array(
						'Con la pubertad aparece el pudor: tu hijo puede empezar a cerrar la puerta del baño, no querer cambiarse frente a otros o incomodarse con comentarios sobre su cuerpo. Es una señal sana de que está construyendo su identidad.',
						'Respetar esa necesidad de privacidad le enseña que su cuerpo es suyo y que los demás deben respetarlo.',
					),
					'claves'   => array(
						'Golpea la puerta antes de entrar a su pieza o al baño.',
						'Evita comentarios sobre su cuerpo, aunque sean cariñosos, frente a otros.',
						'Nunca bromas sobre sus cambios o su peso.',
						'Que sepa que puede preguntarte en privado.',
					),
					'practica' => 'Conversa con el resto de la familia (hermanos, abuelos) sobre la importancia de no hacer comentarios sobre el cuerpo de tu hijo.',
				),
				array(
					't'        => 'Preguntas incómodas: cómo responder',
					'min'      => 11,
					'resumen'  => 'Una respuesta honesta y breve vale más que un silencio incómodo.',
					'mision'   => array( 'casa', 'Deja en un lugar visible una "caja de preguntas" donde tu hijo pueda escribir en un papel, de forma anónima si quiere, lo que quiera saber sobre los cambios del cuerpo. Respóndelas juntos una vez por semana.' ),
					'intro'    => array(
						'"¿Cómo se hacen los bebés?" "¿Qué es eso que vi en internet?" Las preguntas llegan cuando menos lo esperamos. Si reaccionamos con enojo, risa nerviosa o cambiando de tema, el mensaje es que de eso no se habla, y la próxima vez preguntará en otro lado.',
						'No necesitas tener todas las respuestas. Lo importante es mostrar que estás disponible y responder de forma honesta, breve y adecuada a su edad.',
					),
					'claves'   => array(
						'Pregunta primero qué sabe o qué escuchó: "¿Tú qué crees?".',
						'Responde lo que pregunta, sin ir más allá de lo necesario.',
						'Si te toma por sorpresa, puedes decir "buena pregunta, déjame pensarlo y te respondo esta noche", y cumplirlo.',
						'Agradece que te pregunte a ti.',
					),
					'practica' => 'Haz la misión de esta clase.',
				),
			),
		),

		// ===================== ADOLESCENCIA INICIAL (12 a 14) =====================
		array(
			'titulo'      => 'Hablar con tu hijo adolescente',
			'etapa'       => 'adolescencia-inicial',
			'eje'         => 'conectar',
			'habilidad'   => 'Comunicación en la adolescencia',
			'imagen'      => '1510535043828-3d1edc68071e',
			'resumen'     => 'Cómo mantener el vínculo cuando tu hijo se aleja, hablar de redes sociales sin pelear y negociar acuerdos.',
			'descripcion' => array(
				'La adolescencia trae un cambio natural: tu hijo empieza a separarse de la familia para construir su propia identidad. Pasa más tiempo con amigos, se encierra en su pieza, cuestiona tus opiniones. No es que ya no te necesite: te necesita de otra manera.',
				'En este curso vas a aprender a seguir conectado con tu hijo en esta etapa: cómo hablarle para que te escuche, cómo abordar el uso de redes sociales sin convertirlo en una guerra y cómo negociar acuerdos que ambos respeten.',
			),
			'aprenderas'  => array(
				'Entender los cambios de la adolescencia.',
				'Comunicarte sin sermones ni interrogatorios.',
				'Acompañar el uso de redes sociales.',
				'Negociar acuerdos y sostenerlos.',
			),
			'clases'      => array(
				array(
					't'        => 'Qué le está pasando',
					'min'      => 11,
					'resumen'  => 'Cerebro en obra, emociones intensas y la búsqueda de quién es.',
					'video'    => 'q01tGOj1kqA',
					'intro'    => array(
						'En la adolescencia el cerebro está en plena reorganización. Las zonas que procesan las emociones y la búsqueda de recompensas están muy activas, mientras que las que controlan los impulsos y planifican todavía están madurando. Por eso los adolescentes sienten todo con mucha intensidad y a veces toman decisiones arriesgadas.',
						'A la vez, están construyendo su identidad: quiénes son, qué les gusta, a qué grupo pertenecen. Para eso necesitan diferenciarse de sus padres, y eso a veces se ve como rechazo.',
					),
					'claves'   => array(
						'Las emociones intensas son parte del desarrollo, no un ataque personal.',
						'Los amigos se vuelven centrales: es sano.',
						'Necesita más privacidad y autonomía, con límites claros.',
						'Sigue necesitando tu presencia, aunque no lo diga.',
					),
					'practica' => 'Recuerda cómo eras tú a su edad: qué te importaba, qué te molestaba de tus padres. Escríbelo y piensa qué te habría ayudado.',
				),
				array(
					't'        => 'Hablar para que te escuche',
					'min'      => 12,
					'resumen'  => 'Menos sermón, más preguntas: cómo conversar con un adolescente.',
					'video'    => '0ge0SyFW4Cg',
					'intro'    => array(
						'Los adolescentes son especialmente sensibles a sentirse juzgados o tratados como niños. Si cada conversación se convierte en un sermón, dejarán de escuchar a los pocos segundos.',
						'Funciona mejor preguntar su opinión, escuchar sin interrumpir, compartir tu punto de vista brevemente y aceptar que no siempre van a estar de acuerdo.',
					),
					'claves'   => array(
						'Escucha más de lo que hablas.',
						'Pregunta su opinión: "¿Tú qué piensas de…?".',
						'Evita los sermones largos: di lo importante en pocas frases.',
						'Elige el momento: no en medio de un enojo ni frente a sus amigos.',
						'Reconoce cuando tiene razón.',
					),
					'frases'   => array(
						'"No tienes que contarme todo, pero quiero que sepas que puedes."',
						'"Me preocupa esto. ¿Cómo lo ves tú?"',
					),
					'practica' => 'Esta semana, pregúntale su opinión sobre un tema que le interese (una serie, una noticia, un juego) y escúchalo sin dar la tuya a menos que te la pida.',
				),
				array(
					't'        => 'Redes sociales sin guerra',
					'min'      => 10,
					'resumen'  => 'Acompañar, no espiar: cómo hablar de redes con un adolescente.',
					'video'    => 'Kf3xfX6u4DM',
					'caso'     => 'adolescente-celular',
					'intro'    => array(
						'Las redes sociales son hoy parte central de la vida social de los adolescentes. Prohibirlas suele llevar a que las usen a escondidas; ignorarlas deja a tu hijo solo frente a riesgos como la comparación constante, el ciberacoso o los contenidos dañinos.',
						'El camino intermedio es acompañar: interesarte por lo que hace en redes, conversar sobre lo que ve y cómo se siente, y acordar límites razonables, sobre todo en horarios y privacidad.',
					),
					'claves'   => array(
						'Interésate sin juzgar: "¿Qué te gusta de esta red?".',
						'Conversen sobre la comparación: las redes muestran solo lo mejor de cada uno.',
						'Acuerden horarios sin celular: comidas y noche.',
						'Revisen juntos la privacidad de sus perfiles.',
						'Que sepa que puede contarte si algo le hace mal.',
					),
					'practica' => 'Pregúntale a tu hijo a quién sigue en redes y por qué. Pídele que te muestre algo que le haya gustado esta semana.',
				),
				array(
					't'        => 'Acuerdos que se negocian',
					'min'      => 11,
					'resumen'  => 'Más autonomía a cambio de más responsabilidad: la lógica de los acuerdos.',
					'video'    => 'U_g0nLbr3Vs',
					'mision'   => array( 'casa', 'Siéntense juntos a negociar un acuerdo sobre un tema que hoy genere conflicto (horario de llegada, uso del celular, tareas de la casa). Escriban qué se compromete cada uno y qué pasará si no se cumple. Revísenlo en dos semanas.' ),
					'intro'    => array(
						'Con un adolescente, las reglas impuestas sin explicación suelen generar rebeldía. Los acuerdos negociados, en cambio, lo hacen responsable de algo que él mismo aceptó.',
						'Negociar no significa ceder en todo: hay límites no negociables (la seguridad, el respeto) y otros que sí pueden conversarse (horarios, permisos). La idea es que la autonomía crezca a medida que demuestra responsabilidad.',
					),
					'claves'   => array(
						'Distingue lo no negociable de lo negociable.',
						'Escucha su propuesta antes de dar la tuya.',
						'Acuerden consecuencias claras si no se cumple.',
						'Cumple tú también tu parte del acuerdo.',
						'Revisen los acuerdos cada cierto tiempo.',
					),
					'practica' => 'Haz la misión de esta clase.',
				),
			),
		),

		// ===================== ADOLESCENCIA MEDIA Y TARDÍA (15 a 17) =====================
		array(
			'titulo'      => 'Amor y límites con tu adolescente',
			'etapa'       => 'adolescencia-media',
			'eje'         => 'convivir',
			'habilidad'   => 'Límites en la adolescencia',
			'imagen'      => '1557176278-3326a3193580',
			'resumen'     => 'Cómo sostener límites con un hijo casi adulto: salidas, riesgos, conflictos y la confianza que se construye.',
			'descripcion' => array(
				'Entre los 15 y los 17 años tu hijo está cada vez más cerca de la vida adulta: sale solo, toma decisiones importantes, tiene relaciones de pareja. Tu rol cambia de "controlar" a "acompañar", pero los límites siguen siendo necesarios: le dan un marco de seguridad mientras aprende a cuidarse.',
				'En este curso vas a trabajar cómo equilibrar libertad y cuidado, cómo hablar de los riesgos (alcohol, drogas, relaciones) sin sermones, cómo manejar los conflictos sin romper el vínculo y cómo construir una confianza que funcione en ambas direcciones.',
			),
			'aprenderas'  => array(
				'Equilibrar libertad y cuidado.',
				'Hablar de riesgos sin sermones ni miedo.',
				'Manejar conflictos sin romper el vínculo.',
				'Construir confianza en ambas direcciones.',
			),
			'clases'      => array(
				array(
					't'        => 'Libertad y cuidado',
					'min'      => 11,
					'resumen'  => 'Los límites siguen siendo necesarios, pero cambian de forma.',
					'video'    => 'U_g0nLbr3Vs',
					'intro'    => array(
						'Un adolescente de 16 años necesita más libertad que uno de 12, y eso es sano: solo así aprende a tomar decisiones. Pero más libertad no significa ausencia de límites. Los estudios muestran que los adolescentes con padres cálidos y a la vez firmes corren menos riesgos que los de padres muy permisivos o muy autoritarios.',
						'La clave es explicar el porqué de cada límite, que tenga que ver con su seguridad y que se ajuste a medida que demuestra responsabilidad.',
					),
					'claves'   => array(
						'Calidez y firmeza a la vez: el estilo que más protege.',
						'Explica el porqué de cada límite.',
						'Pocos límites, pero claros y sostenidos.',
						'Amplía la libertad a medida que demuestra responsabilidad.',
					),
					'practica' => 'Haz una lista de los límites que hoy tiene tu hijo. Marca cuáles son de seguridad (no negociables) y cuáles podrían ampliarse si demuestra responsabilidad.',
				),
				array(
					't'        => 'Hablar de riesgos sin sermones',
					'min'      => 12,
					'resumen'  => 'Alcohol, drogas, relaciones: información y confianza antes que prohibición.',
					'video'    => 'UIZoPQ3xrkE',
					'intro'    => array(
						'Los adolescentes van a enfrentarse a situaciones de riesgo: una fiesta con alcohol, un amigo que ofrece algo, una relación que presiona. No podemos estar ahí, pero sí podemos prepararlos con información clara y con la seguridad de que pueden llamarnos.',
						'Las conversaciones basadas solo en el miedo o en la prohibición suelen funcionar poco. Funcionan mejor las conversaciones honestas, donde tu hijo pueda preguntar y opinar.',
					),
					'claves'   => array(
						'Habla con información, no solo con advertencias.',
						'Pregúntale qué ve en su entorno y qué opina.',
						'Acuerden un "código de rescate": puede llamarte a cualquier hora para que lo busques, sin sermón en el momento.',
						'Habla de consentimiento y respeto en las relaciones.',
						'Tu propio consumo es un modelo.',
					),
					'frases'   => array(
						'"Si alguna vez estás en una situación en la que no te sientes seguro, llámame. Te voy a buscar y hablamos después, con calma."',
					),
					'practica' => 'Propón a tu hijo el "código de rescate" de esta clase y acuerden cómo funcionaría.',
				),
				array(
					't'        => 'Conflictos sin romper el vínculo',
					'min'      => 10,
					'resumen'  => 'Discutir es inevitable; lo importante es cómo se discute y cómo se repara.',
					'intro'    => array(
						'Los conflictos con un adolescente son inevitables: está afirmando su independencia y va a chocar con tus reglas. Lo que marca la diferencia no es evitar las discusiones, sino cómo se discute y qué pasa después.',
						'Discutir con respeto, pedir una pausa cuando la cosa se calienta y reparar después son habilidades que tu hijo aprenderá viéndote a ti.',
					),
					'claves'   => array(
						'Si la discusión escala, pide una pausa: "Hablemos en una hora".',
						'Critica la conducta, no a la persona.',
						'Nada de insultos, gritos ni portazos (tampoco de tu parte).',
						'Después, repara: pedir disculpas cuando corresponde enseña más que cualquier sermón.',
					),
					'frases'   => array(
						'"Estoy muy enojado y no quiero decir algo de lo que me arrepienta. Hablemos más tarde."',
						'"Ayer te grité y no estuvo bien. Te pido disculpas."',
					),
					'practica' => 'Piensa en la última discusión fuerte con tu hijo. Si hubo algo de lo que te arrepientes, busca un momento para reparar.',
				),
				array(
					't'        => 'Confianza en ambas direcciones',
					'min'      => 9,
					'resumen'  => 'La confianza se construye con coherencia, respeto por su privacidad y tiempo compartido.',
					'mision'   => array( 'casa', 'Planifiquen juntos una actividad solo para ustedes dos durante el próximo mes, elegida por tu hijo (una comida, un partido, una salida). Sin celulares durante la actividad.' ),
					'intro'    => array(
						'La confianza no se exige: se construye. Tu hijo confiará en ti si ve que cumples lo que dices, que respetas su privacidad y que no usas lo que te cuenta en su contra. Y tú podrás confiar en él a medida que cumpla los acuerdos.',
						'El tiempo compartido, aunque sea poco, sigue siendo fundamental: es lo que mantiene el vínculo vivo en una etapa en que es fácil que cada uno haga su vida.',
					),
					'claves'   => array(
						'Cumple lo que prometes, también en lo pequeño.',
						'Respeta su privacidad: no leas sus mensajes sin un motivo grave.',
						'No uses lo que te cuenta como arma en una discusión.',
						'Busca tiempos compartidos, aunque sean cortos.',
					),
					'practica' => 'Haz la misión de esta clase.',
				),
			),
		),
	);
}

/**
 * Los casos prácticos de ejemplo. Cada uno tiene uno o dos pasos, y cada
 * opción su propia explicación: no hay "respuesta incorrecta" en rojo, hay
 * una recomendada y otras con su porqué (mismo criterio que inc/casos.php).
 *
 * @return array<string, array>
 */
function afectivalab_demo_casos() {
	return array(
		'bullying-colegio'     => array(
			'paso1' => array(
				'situacion' => 'Tu hijo de 8 años dice: "No quiero volver al colegio mañana." ¿Qué harías primero?',
				'opciones'  => array(
					array( 'Le digo que tiene que ir, todos tenemos problemas.', 'Puede generar más resistencia. Restarle importancia a lo que siente puede hacer que la próxima vez prefiera no contarte nada.', false ),
					array( 'Le pregunto con calma qué pasó.', 'Buena elección. Primero conviene generar un espacio seguro para que hable. Si presionas demasiado, podría cerrarse.', true ),
					array( 'Llamo de inmediato al colegio.', 'Todavía es pronto para eso. Conviene entender primero qué está pasando con tu hijo antes de involucrar al colegio.', false ),
					array( 'Le permito quedarse en casa.', 'Evita el tema en vez de resolverlo. Puede ser un alivio momentáneo, pero no ayuda a entender lo que le está pasando.', false ),
				),
			),
			'paso2' => array(
				'situacion' => 'Tu hijo te cuenta que un compañero se burla de él todos los días en el recreo. ¿Qué harías ahora?',
				'opciones'  => array(
					array( 'Le digo que no le dé importancia, los niños son así.', 'Minimizar lo que le pasa puede hacer que no vuelva a contarte. Sus emociones son reales y merecen tomarse en serio.', false ),
					array( 'Le pregunto cómo se siente y qué le gustaría que pasara, y le cuento que voy a hablar con su profesora.', 'Buena elección. Involucrarlo en la solución lo ayuda a sentirse acompañado, y avisar al colegio es necesario si la burla se repite.', true ),
					array( 'Hablo directamente con el otro niño a la salida.', 'Puede generar más conflicto y exponer a tu hijo. Conviene coordinar con el colegio antes de actuar por tu cuenta.', false ),
				),
			),
		),
		'rabieta-supermercado' => array(
			'paso1' => array(
				'situacion' => 'En el supermercado tu hija de 4 años pide un chocolate. Le dices que no y se tira al suelo gritando. La gente mira. ¿Qué haces?',
				'opciones'  => array(
					array( 'Le compro el chocolate para que se calme.', 'Se calma rápido, pero aprende que el grito funciona. La próxima vez lo intentará con más fuerza.', false ),
					array( 'Me agacho a su altura, le digo que entiendo que está enojada y que la respuesta sigue siendo no, y espero cerca.', 'Buena elección. Validas lo que siente sin cambiar el límite, y tu calma la ayuda a calmarse.', true ),
					array( 'Le grito que se levante porque nos están mirando.', 'Tu enojo suma intensidad a la suya. Además, la preocupación por lo que piensen otros suele hacernos reaccionar peor.', false ),
					array( 'Me alejo unos pasos y le digo que me voy sin ella.', 'Amenazar con abandonarla le genera miedo, no calma, y es una amenaza que no vas a cumplir.', false ),
				),
			),
			'paso2' => array(
				'situacion' => 'Ya en el auto, tu hija está tranquila pero callada. ¿Qué haces?',
				'opciones'  => array(
					array( 'Le explico largamente por qué se portó mal.', 'Un sermón largo suele sentirse como castigo y no ayuda a que entienda. Mejor pocas palabras y reconexión.', false ),
					array( 'Le doy un abrazo y le digo que fue un momento difícil, y que la próxima vez podemos acordar antes de entrar qué vamos a comprar.', 'Buena elección. Primero reconectas y después le das una herramienta concreta para la próxima vez.', true ),
					array( 'No digo nada, ya pasó.', 'Dejarlo pasar no es un error grave, pero se pierde una oportunidad de reconectar y de aprender algo juntas.', false ),
				),
			),
		),
		'pantallas-apagar'     => array(
			'paso1' => array(
				'situacion' => 'Tu hijo de 7 años lleva una hora jugando videojuegos. Es hora de cenar y cuando le dices que apague, grita: "¡Un ratito más!". ¿Qué haces?',
				'opciones'  => array(
					array( 'Le desenchufo la consola sin decir nada.', 'Cortar de golpe suele provocar una reacción mucho más fuerte, y perder el avance del juego se siente injusto para él.', false ),
					array( 'Le digo que termine la partida en la que está y que después se apaga, y me quedo a su lado hasta que lo haga.', 'Buena elección. Cerrar en un punto natural hace el corte más llevadero, y tu presencia ayuda a que cumpla.', true ),
					array( 'Lo dejo seguir otro rato para evitar la pelea.', 'Evitas la pelea hoy, pero le enseñas que insistir funciona y el problema se repetirá mañana.', false ),
				),
			),
		),
		'adolescente-celular'  => array(
			'paso1' => array(
				'situacion' => 'Descubres que tu hija de 13 años tiene una cuenta en una red social que no conocías, con el perfil público. ¿Qué haces?',
				'opciones'  => array(
					array( 'Le quito el celular por un mes.', 'El castigo puede hacer que la próxima vez se esconda mejor. No resuelve el riesgo ni abre la conversación.', false ),
					array( 'Le digo que la vi, le pregunto qué le gusta de esa red y le pido que revisemos juntas la privacidad.', 'Buena elección. Abres la conversación sin acusar y te enfocas en lo importante: que su perfil sea seguro.', true ),
					array( 'No digo nada y la sigo revisando a escondidas.', 'Espiar a escondidas daña la confianza si lo descubre, y no le enseña a cuidarse sola.', false ),
				),
			),
			'paso2' => array(
				'situacion' => 'Mientras revisan la cuenta, ves que un desconocido le escribe seguido y le hace muchos halagos. ¿Qué haces?',
				'opciones'  => array(
					array( 'Le explico con calma por qué eso es una señal de alerta, bloqueamos y denunciamos juntas el perfil, y guardamos capturas.', 'Buena elección. Le enseñas a reconocer el riesgo y a actuar, sin culparla.', true ),
					array( 'Me enojo con ella por hablar con desconocidos.', 'Culparla puede hacer que no te cuente la próxima vez. La responsabilidad es del adulto que la contacta.', false ),
					array( 'Le escribo yo al desconocido para que la deje en paz.', 'Responder puede alertarlo o complicar una denuncia. Es mejor no interactuar, guardar la evidencia y denunciar.', false ),
				),
			),
		),
	);
}
