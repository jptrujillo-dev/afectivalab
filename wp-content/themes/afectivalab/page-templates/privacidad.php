<?php
/**
 * Ruta /privacidad — servida vía inc/routes.php (template_include).
 *
 * BORRADOR. Describe con precisión qué guarda hoy el código (verificado
 * contra inc/hijos.php, inc/account.php, inc/misiones.php, inc/casos.php,
 * inc/seguridad.php) pero no es un documento legal terminado: falta que lo
 * revise un abogado antes de publicarlo como definitivo, sobre todo por
 * tratarse de datos de menores. Ver el aviso al inicio de la página.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main class="legal-page">
	<div class="container legal-page__narrow">

		<header class="legal-hero reveal">
			<h1><?php esc_html_e( 'Política de privacidad', 'afectivalab' ); ?></h1>
			<p><?php esc_html_e( 'Cómo tratamos los datos de tu cuenta y los perfiles de tus hijos dentro de Afectivalab.', 'afectivalab' ); ?></p>
		</header>

		<div class="legal-borrador reveal">
			<strong><?php esc_html_e( 'Borrador pendiente de revisión legal.', 'afectivalab' ); ?></strong>
			<p>
				<?php esc_html_e( 'Este texto describe con precisión qué datos guarda hoy la plataforma y para qué se usan, pero todavía no fue revisado por un abogado. No lo tomes como la versión definitiva hasta que se confirme.', 'afectivalab' ); ?>
			</p>
		</div>

		<div class="legal-card legal-content reveal">

			<h2><?php esc_html_e( '1. Quién trata tus datos', 'afectivalab' ); ?></h2>
			<p>
				<?php
				printf(
					/* translators: 1: nombre del sitio, 2: enlace a la página de contacto. */
					esc_html__( '%1$s es responsable de los datos que se describen en esta página. Puedes escribirnos desde %2$s ante cualquier duda.', 'afectivalab' ),
					esc_html( get_bloginfo( 'name' ) ),
					'<a href="' . esc_url( home_url( '/contacto' ) ) . '">' . esc_html__( 'Contacto', 'afectivalab' ) . '</a>'
				);
				?>
			</p>

			<h2><?php esc_html_e( '2. Qué datos guardamos', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'De la cuenta del padre, madre o tutor: nombre, correo electrónico, contraseña (guardada de forma cifrada, nunca en texto plano) y, si la subes, una foto de perfil.', 'afectivalab' ); ?></p>
			<p><?php esc_html_e( 'De cada perfil de hijo que agregas: su nombre, el mes y año de nacimiento (no pedimos el día) y los temas que marcaste como una preocupación para él o ella. El perfil de un hijo no es una cuenta aparte: vive dentro de tu cuenta y solo tú puedes verlo o editarlo.', 'afectivalab' ); ?></p>
			<p><?php esc_html_e( 'De tu actividad en la plataforma: qué microclases completó cada hijo, las misiones resueltas (incluida la foto de evidencia si la misión la pide), las respuestas a los casos prácticos, y las monedas, estrellas y certificados que se van ganando.', 'afectivalab' ); ?></p>
			<p><?php esc_html_e( 'Datos técnicos: la dirección IP se usa para limitar los intentos de inicio de sesión y evitar accesos indebidos a tu cuenta, y el sitio usa cookies propias de WordPress para mantener tu sesión iniciada.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '3. Para qué los usamos', 'afectivalab' ); ?></h2>
			<ul>
				<li><?php esc_html_e( 'Armar la ruta de contenido recomendada para cada hijo, según su edad y tus preocupaciones.', 'afectivalab' ); ?></li>
				<li><?php esc_html_e( 'Llevar el registro de avance, misiones, casos y certificados de cada hijo.', 'afectivalab' ); ?></li>
				<li><?php esc_html_e( 'Proteger tu cuenta (límite de intentos de inicio de sesión, verificación de que cada perfil de hijo le pertenece a su padre).', 'afectivalab' ); ?></li>
				<li><?php esc_html_e( 'Comunicarnos contigo por correo cuando lo pides (por ejemplo, para restablecer tu contraseña).', 'afectivalab' ); ?></li>
			</ul>
			<p><?php esc_html_e( 'No vendemos tus datos ni los de tus hijos a terceros.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '4. Quién puede ver qué', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Tú ves los datos y el avance de tus propios hijos. El equipo que produce el contenido puede ver cifras agregadas (por ejemplo, cuántas familias empezaron un curso) para decidir qué contenido hace falta, pero no ve el nombre ni el avance individual de cada hijo. Quien administra el sitio tiene acceso técnico a la base de datos, como en cualquier plataforma.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '5. Menores de edad', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Los perfiles de hijo los crea y administra el padre, madre o tutor legal desde su propia cuenta — un menor no se registra por su cuenta ni tiene usuario propio. Si crees que se cargó información de un menor sin la autorización correspondiente, escríbenos y lo resolvemos.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '6. Cuánto tiempo guardamos los datos', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Mientras tu cuenta esté activa. Si quieres eliminar tu cuenta y la de tus hijos, escríbenos desde Contacto y lo gestionamos contigo.', 'afectivalab' ); ?></p>

			<h2><?php esc_html_e( '7. Cambios a esta política', 'afectivalab' ); ?></h2>
			<p><?php esc_html_e( 'Si actualizamos esta página de forma importante, te avisaremos dentro de la plataforma.', 'afectivalab' ); ?></p>

		</div>

	</div>
</main>

<?php get_footer(); ?>
