<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'customize_register', 'klo_customize_register', 20 );
function klo_customize_register( $wp_customize ) {
	$d = klo_defaults();

	$kses = array(
		'strong' => array(), 'em' => array(), 'br' => array(),
		'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
	);

	$sections = array(
		'accueil' => array( 'Accueil', array(
			'hero_kicker' => array( 'Petite phrase au-dessus du titre', 'text' ),
			'hero_title'  => array( 'Grand titre (une ligne par ligne, la 2e ligne est en couleur)', 'textarea' ),
			'hero_desc'   => array( 'Texte de présentation', 'textarea' ),
		) ),
		'histoire' => array( 'À propos', array(
			'histoire_title'  => array( 'Titre (une ligne par ligne)', 'textarea' ),
			'histoire_p1'     => array( 'Paragraphe 1', 'textarea' ),
			'histoire_p2'     => array( 'Paragraphe 2', 'textarea' ),
			'histoire_strong' => array( 'Phrase en gras', 'textarea' ),
		) ),
		'services' => array( 'Services', array(
			'services_intro' => array( 'Texte d’introduction', 'textarea' ),
			'svc1_t' => array( 'Service 1 : titre', 'text' ), 'svc1_p' => array( 'Service 1 : texte', 'textarea' ),
			'svc2_t' => array( 'Service 2 : titre', 'text' ), 'svc2_p' => array( 'Service 2 : texte', 'textarea' ),
			'svc3_t' => array( 'Service 3 : titre', 'text' ), 'svc3_p' => array( 'Service 3 : texte', 'textarea' ),
			'svc4_t' => array( 'Service 4 : titre', 'text' ), 'svc4_p' => array( 'Service 4 : texte', 'textarea' ),
			'svc5_t' => array( 'Service 5 : titre', 'text' ), 'svc5_p' => array( 'Service 5 : texte', 'textarea' ),
			'svc6_t' => array( 'Service 6 : titre', 'text' ), 'svc6_p' => array( 'Service 6 : texte', 'textarea' ),
		) ),
		'listes' => array( 'Références, réseau et actualités (textes d’intro)', array(
			'refs_intro'     => array( 'Introduction des références', 'textarea' ),
			'partners_intro' => array( 'Introduction du réseau / médias', 'textarea' ),
			'news_intro'     => array( 'Introduction des actualités', 'textarea' ),
		) ),
		'contact' => array( 'Contact et réseaux', array(
			'contact_intro' => array( 'Texte au-dessus du formulaire (gras autorisé)', 'textarea' ),
			'contact_list'  => array( 'Liste « Merci de préparer » (un élément par ligne)', 'textarea' ),
			'contact_note'  => array( 'Petite note sous la liste', 'textarea' ),
			'contact_to'    => array( 'Adresse e-mail qui reçoit les messages du formulaire', 'email' ),
			'contact_from'  => array( 'Adresse d’expédition (laisser vide en cas de doute)', 'email' ),
			'facebook'      => array( 'Lien Facebook (icône dans l’en-tête)', 'url' ),
			'instagram'     => array( 'Lien Instagram (icône dans l’en-tête)', 'url' ),
			'spotify'       => array( 'Lien de la playlist Spotify (facultatif, à ajouter plus tard)', 'url' ),
			'meta_desc'     => array( 'Description du site pour Google (150 à 160 caractères)', 'textarea' ),
		) ),
	);

	$wp_customize->add_panel( 'klo_texts', array( 'title' => 'Textes du site', 'priority' => 1 ) );

	foreach ( $sections as $sid => $sec ) {
		$wp_customize->add_section( 'klo_sec_' . $sid, array( 'title' => $sec[0], 'panel' => 'klo_texts' ) );
		foreach ( $sec[1] as $key => $f ) {
			if ( 'contact_intro' === $key ) {
				$san = function ( $v ) use ( $kses ) { return wp_kses( $v, $kses ); };
			} elseif ( 'url' === $f[1] ) {
				$san = 'esc_url_raw';
			} elseif ( 'email' === $f[1] ) {
				$san = 'sanitize_email';
			} elseif ( 'textarea' === $f[1] ) {
				$san = 'sanitize_textarea_field';
			} else {
				$san = 'sanitize_text_field';
			}
			$wp_customize->add_setting( 'klo_' . $key, array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $san,
			) );
			$wp_customize->add_control( 'klo_' . $key, array(
				'label'   => $f[0],
				'section' => 'klo_sec_' . $sid,
				'type'    => $f[1],
			) );
		}
	}

	// Simplifie l'interface pour les non-administrateurs.
	if ( ! current_user_can( 'manage_options' ) ) {
		foreach ( array( 'themes', 'title_tagline', 'static_front_page', 'custom_css', 'colors', 'header_image', 'background_image' ) as $s ) {
			$wp_customize->remove_section( $s );
		}
		$wp_customize->remove_panel( 'nav_menus' );
		$wp_customize->remove_panel( 'widgets' );
	}
}
