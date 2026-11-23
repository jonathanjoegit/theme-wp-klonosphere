<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Types de besoin autorisés (même liste que le formulaire). */
function klo_contact_types() {
	return array(
		'Promotion / relations presse',
		'Stratégie / accompagnement',
		'Publicités digitales',
		'Conseil en visio',
		'Démarchage / réseau',
		'Autre demande',
	);
}

add_action( 'wp_ajax_nopriv_klo_contact', 'klo_contact_handler' );
add_action( 'wp_ajax_klo_contact', 'klo_contact_handler' );

function klo_contact_handler() {
	$ok_msg = 'Merci, votre message a bien été envoyé. Nous vous répondons dès que possible.';

	// Piège anti-robots : champ invisible rempli = faux succès, rien n'est envoyé.
	if ( ! empty( $_POST['website'] ) ) {
		wp_send_json_success( array( 'message' => $ok_msg ) );
	}

	// Temps passé sur la page (calculé côté navigateur) : un robot remplit en quelques ms.
	$elapsed = isset( $_POST['t'] ) ? (int) $_POST['t'] : 0;
	if ( $elapsed < 3000 ) {
		wp_send_json_error( array( 'message' => 'Merci de patienter quelques secondes avant d’envoyer, puis de réessayer.' ) );
	}

	// Limite : 5 envois par heure et par adresse IP.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '0';
	$key = 'klo_rl_' . md5( $ip );
	$cnt = (int) get_transient( $key );
	if ( $cnt >= 5 ) {
		wp_send_json_error( array( 'message' => 'Trop de messages envoyés. Merci de réessayer plus tard.' ) );
	}

	$band    = isset( $_POST['band'] ) ? sanitize_text_field( wp_unslash( $_POST['band'] ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$type    = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$date    = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : '';
	$links   = isset( $_POST['links'] ) ? sanitize_textarea_field( wp_unslash( $_POST['links'] ) ) : '';
	$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';

	if ( '' === $band || '' === $message || ! is_email( $email ) ) {
		wp_send_json_error( array( 'message' => 'Merci de remplir le nom du projet, un e-mail valide et votre message.' ) );
	}
	if ( ! in_array( $type, klo_contact_types(), true ) ) { $type = 'Autre demande'; }
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) { $date = ''; }
	$band    = mb_substr( $band, 0, 120 );
	$links   = mb_substr( $links, 0, 1000 );
	$message = mb_substr( $message, 0, 5000 );

	$to   = klo_opt( 'contact_to' );
	$host = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
	$from = klo_opt( 'contact_from' );
	if ( ! is_email( $from ) ) { $from = 'contact@' . $host; }
	if ( ! is_email( $to ) ) { $to = get_option( 'admin_email' ); }

	$safe_name = trim( str_replace( array( '<', '>', '"', ',', ';' ), '', $band ) );
	$subject   = 'Nouveau contact : ' . $type . ' (' . $band . ')';
	$body      = "Nouveau message depuis le site KLONOSPHERE\n\n"
		. "Groupe / projet : {$band}\n"
		. "E-mail : {$email}\n"
		. "Besoin : {$type}\n"
		. 'Échéance : ' . ( $date ? $date : 'à définir' ) . "\n"
		. 'Liens : ' . ( $links ? $links : '-' ) . "\n\n"
		. "Message :\n{$message}\n";

	$headers = array(
		'Content-Type: text/plain; charset=UTF-8',
		'From: KLONOSPHERE (site web) <' . $from . '>',
		'Reply-To: ' . ( $safe_name ? $safe_name : 'Contact' ) . ' <' . $email . '>',
	);

	set_transient( $key, $cnt + 1, HOUR_IN_SECONDS );

	if ( wp_mail( $to, $subject, $body, $headers ) ) {
		wp_send_json_success( array( 'message' => $ok_msg ) );
	}
	error_log( 'KLONOSPHERE : échec wp_mail() pour le formulaire de contact.' );
	wp_send_json_error( array( 'message' => 'L’envoi a échoué. Merci de réessayer dans quelques instants.' ) );
}
