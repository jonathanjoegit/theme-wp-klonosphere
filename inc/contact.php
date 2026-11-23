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

/** Limite une chaîne en caractères UTF-8, même si mbstring n'est pas installé. */
function klo_contact_limit_text( $value, $limit ) {
    $value = (string) $value;
    if ( function_exists( 'mb_substr' ) ) {
        return mb_substr( $value, 0, $limit, 'UTF-8' );
    }
    if ( preg_match( '/^.{0,' . absint( $limit ) . '}/us', $value, $matches ) ) {
        return $matches[0];
    }
    return substr( $value, 0, absint( $limit ) );
}

/** Lit un champ POST scalaire, le borne avant assainissement et refuse les tableaux injectés. */
function klo_contact_post_field( $key, $limit, $textarea = false ) {
    if ( ! isset( $_POST[ $key ] ) || ! is_scalar( $_POST[ $key ] ) ) {
        return '';
    }
    $value = klo_contact_limit_text( wp_unslash( (string) $_POST[ $key ] ), $limit );
    return $textarea ? sanitize_textarea_field( $value ) : sanitize_text_field( $value );
}

function klo_contact_handler() {
    $ok_msg = 'Merci, votre message a bien été envoyé. Nous vous répondons dès que possible.';

    // L'endpoint accepte uniquement des POST de taille raisonnable.
    $method = isset( $_SERVER['REQUEST_METHOD'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ) : '';
    if ( 'POST' !== $method ) {
        wp_send_json_error( array( 'message' => 'Méthode de requête non autorisée.' ), 405 );
    }
    $content_length = isset( $_SERVER['CONTENT_LENGTH'] ) ? absint( $_SERVER['CONTENT_LENGTH'] ) : 0;
    if ( $content_length > 16384 ) {
        wp_send_json_error( array( 'message' => 'Le formulaire dépasse la taille autorisée.' ), 413 );
    }

    // Refuse les champs nonce non scalaires avant de les transmettre à WordPress.
    if ( ! isset( $_POST['nonce'] ) || ! is_string( $_POST['nonce'] ) || ! check_ajax_referer( 'klo_contact', 'nonce', false ) ) {
        wp_send_json_error( array( 'message' => 'Le formulaire a expiré. Actualisez la page puis réessayez.' ), 403 );
    }

    // Piège anti-robots : champ invisible rempli = faux succès, rien n'est envoyé.
    if ( ! empty( $_POST['website'] ) ) {
        wp_send_json_success( array( 'message' => $ok_msg ) );
    }

    // Le délai améliore le filtrage, mais n'est pas considéré comme une protection suffisante.
    $elapsed = isset( $_POST['t'] ) && is_scalar( $_POST['t'] ) ? absint( $_POST['t'] ) : 0;
    if ( $elapsed < 3000 ) {
        wp_send_json_error( array( 'message' => 'Merci de patienter quelques secondes avant d’envoyer, puis de réessayer.' ), 400 );
    }

    $remote_ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
    $ip = filter_var( $remote_ip, FILTER_VALIDATE_IP ) ? $remote_ip : 'unknown';
    $key = 'klo_rl_' . hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) );
    $count = (int) get_transient( $key );
    if ( $count >= 5 ) {
        wp_send_json_error( array( 'message' => 'Trop de messages envoyés. Merci de réessayer plus tard.' ), 429 );
    }

    $band    = klo_contact_post_field( 'band', 120 );
    $email   = sanitize_email( klo_contact_post_field( 'email', 254 ) );
    $type    = klo_contact_post_field( 'type', 80 );
    $date    = klo_contact_post_field( 'date', 10 );
    $links   = klo_contact_post_field( 'links', 1000, true );
    $message = klo_contact_post_field( 'message', 5000, true );

    if ( '' === $band || '' === $message || ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Merci de remplir le nom du projet, un e-mail valide et votre message.' ), 400 );
    }
    if ( ! in_array( $type, klo_contact_types(), true ) ) {
        $type = 'Autre demande';
    }
    if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
        $date = '';
    } else {
        $parts = array_map( 'intval', explode( '-', $date ) );
        if ( 3 !== count( $parts ) || ! checkdate( $parts[1], $parts[2], $parts[0] ) ) {
            $date = '';
        }
    }

    $to = klo_opt( 'contact_to' );
    $host = preg_replace( '/^www\./', '', (string) wp_parse_url( home_url(), PHP_URL_HOST ) );
    $from = klo_opt( 'contact_from' );
    if ( ! is_email( $from ) && $host && preg_match( '/^[a-z0-9.-]+$/i', $host ) ) {
        $from = 'wordpress@' . $host;
    }
    if ( ! is_email( $from ) ) {
        $from = get_option( 'admin_email' );
    }
    if ( ! is_email( $to ) ) {
        $to = get_option( 'admin_email' );
    }
    if ( ! is_email( $to ) || ! is_email( $from ) ) {
        wp_send_json_error( array( 'message' => 'La configuration e-mail du site doit être vérifiée par un administrateur.' ), 500 );
    }

    $safe_name = sanitize_text_field( $band );
    $subject = 'Nouveau contact KLONOSPHERE : ' . $type . ' (' . $safe_name . ')';
    $body = "Nouveau message depuis le site KLONOSPHERE\n\n"
        . "Groupe / projet : {$band}\n"
        . "E-mail : {$email}\n"
        . "Besoin : {$type}\n"
        . 'Échéance : ' . ( $date ? $date : 'à définir' ) . "\n"
        . 'Liens : ' . ( $links ? $links : '-' ) . "\n\n"
        . "Message :\n{$message}\n";

    $headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: KLONOSPHERE (site web) <' . $from . '>',
        'Reply-To: ' . $email,
    );

    // Comptabilise aussi les échecs d'envoi pour empêcher les tentatives en rafale.
    set_transient( $key, $count + 1, HOUR_IN_SECONDS );

    if ( wp_mail( $to, $subject, $body, $headers ) ) {
        wp_send_json_success( array( 'message' => $ok_msg ) );
    }
    error_log( 'KLONOSPHERE : échec wp_mail() pour le formulaire de contact.' );
    wp_send_json_error( array( 'message' => 'L’envoi a échoué. Merci de réessayer dans quelques instants.' ), 500 );
}
