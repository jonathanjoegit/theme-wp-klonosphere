<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Vrai si un plugin SEO gère déjà les balises (SEOPress, Yoast, Rank Math, AIOSEO). */
function klo_seo_plugin_active() {
	return defined( 'SEOPRESS_VERSION' ) || defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' );
}

add_filter( 'document_title_separator', function () { return '|'; } );
add_filter( 'pre_get_document_title', function ( $t ) {
	if ( klo_seo_plugin_active() ) { return $t; }
	if ( is_front_page() ) { return 'KLONOSPHERE | Collectif artistique indépendant depuis 2001'; }
	return $t;
}, 5 );

add_action( 'wp_head', function () {
	$img = get_template_directory_uri() . '/assets/img/';

	// Icônes (sauf si une icône de site est définie dans WordPress).
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/png" sizes="48x48" href="' . esc_url( $img . 'favicon-48.png' ) . '">' . "\n";
		echo '<link rel="icon" type="image/png" sizes="512x512" href="' . esc_url( $img . 'favicon-512.png' ) . '">' . "\n";
		echo '<link rel="apple-touch-icon" href="' . esc_url( $img . 'apple-touch-icon.png' ) . '">' . "\n";
	}
	echo '<meta name="theme-color" content="#f6f4ef">' . "\n";

	if ( klo_seo_plugin_active() ) { return; }

	if ( is_front_page() ) {
		$title = 'KLONOSPHERE | Collectif artistique indépendant depuis 2001';
		$desc  = klo_opt( 'meta_desc' );
		$url   = home_url( '/' );
		$image = $img . 'og-image.jpg';
		$type  = 'website';
	} elseif ( is_singular() ) {
		$title = wp_get_document_title();
		$desc  = wp_strip_all_tags( get_the_excerpt() );
		$url   = get_permalink();
		$image = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'large' ) : $img . 'og-image.jpg';
		$type  = is_singular( 'post' ) ? 'article' : 'website';
	} else {
		return;
	}
	$desc = trim( preg_replace( '/\s+/', ' ', $desc ) );
	$desc = function_exists( 'mb_substr' ) ? mb_substr( $desc, 0, 200, 'UTF-8' ) : klo_contact_limit_text( $desc, 200 );
	if ( $desc ) { echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n"; }
	echo '<meta property="og:locale" content="fr_FR">' . "\n";
	echo '<meta property="og:site_name" content="KLONOSPHERE">' . "\n";
	echo '<meta property="og:type" content="' . esc_attr( $type ) . '">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	if ( $desc ) { echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n"; }
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . esc_url( $image ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	if ( is_front_page() ) {
		$same = array_values( array_filter( array( klo_opt( 'facebook' ), klo_opt( 'instagram' ), klo_opt( 'spotify' ) ) ) );
		$ld   = array(
			'@context'     => 'https://schema.org',
			'@type'        => 'Organization',
			'name'         => 'KLONOSPHERE',
			'url'          => home_url( '/' ),
			'logo'         => $img . 'favicon-512.png',
			'foundingDate' => '2001',
			'description'  => klo_opt( 'meta_desc' ),
			'address'      => array( '@type' => 'PostalAddress', 'addressLocality' => 'Poitiers', 'addressCountry' => 'FR' ),
			'sameAs'       => $same,
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . '</script>' . "\n";
	}
}, 5 );
