<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) { define( 'DISALLOW_FILE_EDIT', true ); }

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'klonosphere' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	set_post_thumbnail_size( 1200, 630, true );
} );

add_action( 'wp_enqueue_scripts', function () {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();
	wp_enqueue_style( 'klonosphere', get_stylesheet_uri(), array(), filemtime( $dir . '/style.css' ) );
	wp_enqueue_script( 'klonosphere-app', $uri . '/assets/js/app.js', array(), filemtime( $dir . '/assets/js/app.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'klonosphere-app', 'KLO', array( 'ajax' => admin_url( 'admin-ajax.php' ) ) );
} );

/* ---------- Contenus : références et partenaires ---------- */
add_action( 'init', function () {
	$common = array(
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
		'hierarchical'        => true,
		'capability_type'     => 'post',
		'map_meta_cap'        => true,
	);
	register_post_type( 'klo_reference', array_merge( $common, array(
		'labels' => array(
			'name' => 'Références', 'singular_name' => 'Référence', 'menu_name' => 'Références',
			'add_new' => 'Ajouter', 'add_new_item' => 'Ajouter une référence', 'edit_item' => 'Modifier la référence',
			'new_item' => 'Nouvelle référence', 'search_items' => 'Rechercher', 'not_found' => 'Aucune référence', 'all_items' => 'Toutes les références',
		),
		'menu_icon' => 'dashicons-groups', 'menu_position' => 6,
	) ) );
	register_post_type( 'klo_partenaire', array_merge( $common, array(
		'labels' => array(
			'name' => 'Médias et partenaires', 'singular_name' => 'Média / partenaire', 'menu_name' => 'Médias / partenaires',
			'add_new' => 'Ajouter', 'add_new_item' => 'Ajouter un média ou partenaire', 'edit_item' => 'Modifier',
			'new_item' => 'Nouveau', 'search_items' => 'Rechercher', 'not_found' => 'Aucun partenaire', 'all_items' => 'Tous les médias / partenaires',
		),
		'supports' => array( 'title', 'editor', 'thumbnail' ),
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-megaphone', 'menu_position' => 7,
	) ) );

	register_taxonomy( 'klo_type_partenaire', 'klo_partenaire', array(
		'labels' => array(
			'name' => 'Catégories de partenaires', 'singular_name' => 'Catégorie de partenaire',
			'search_items' => 'Rechercher une catégorie', 'all_items' => 'Toutes les catégories',
			'edit_item' => 'Modifier la catégorie', 'add_new_item' => 'Ajouter une catégorie',
			'menu_name' => 'Catégories',
		),
		'public' => false, 'show_ui' => true, 'show_admin_column' => true,
		'hierarchical' => true, 'show_in_rest' => true,
	) );
} );

add_filter( 'enter_title_here', function ( $t, $post ) {
	if ( $post && in_array( $post->post_type, array( 'klo_reference', 'klo_partenaire' ), true ) ) {
		return 'Nom (ex : KLONE)';
	}
	return $t;
}, 10, 2 );

/* Case « À la une » pour les références */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'klo_ref_box', 'Mise en avant', 'klo_ref_box', 'klo_reference', 'side', 'high' );
	add_meta_box( 'klo_news_box', 'Informations de la carte actualité', 'klo_news_box', 'post', 'normal', 'high' );
	add_meta_box( 'klo_partner_link_box', 'Lien du partenaire', 'klo_partner_link_box', 'klo_partenaire', 'normal', 'high' );
} );

function klo_ref_box( $post ) {
	wp_nonce_field( 'klo_ref', 'klo_ref_nonce' );
	echo '<label><input type="checkbox" name="klo_featured" value="1" ' . checked( get_post_meta( $post->ID, '_klo_featured', true ), '1', false ) . '> Afficher dans « Une sélection de nos références »</label>';
}

function klo_partner_link_box( $post ) {
	wp_nonce_field( 'klo_partner_link', 'klo_partner_link_nonce' );
	$value = get_post_meta( $post->ID, '_klo_partner_url', true );
	echo '<p><label for="klo_partner_url"><strong>Site internet du partenaire (facultatif)</strong></label></p>';
	printf( '<input type="url" class="widefat" id="klo_partner_url" name="klo_partner_url" value="%s" placeholder="https://exemple.com">', esc_attr( $value ) );
	echo '<p class="description">Ajoutez le logo avec « Image mise en avant » et choisissez une catégorie dans la colonne de droite.</p>';
}

function klo_news_box( $post ) {
	wp_nonce_field( 'klo_news', 'klo_news_nonce' );
	$f = array(
		'klo_status'   => array( 'Statut (ex : Campagne en cours)', 'Campagne en cours' ),
		'klo_genre'    => array( 'Style (ex : ROCK / PROGRESSIF)', 'ROCK / METAL' ),
		'klo_dateline' => array( 'Ligne de date (ex : 23 octobre 2026 · prochain single). Vide = date de publication', '' ),
		'klo_art_name' => array( 'Visuel sans image : nom affiché en grand (ex : KLONE)', '' ),
		'klo_art_sub'  => array( 'Visuel sans image : petite ligne (ex : THE MOON IS WITNESS)', '' ),
	);
	echo '<p style="margin-top:0">Le résumé de la carte est le champ « Extrait » de l’article. Si vous ajoutez une « Image mise en avant », elle remplace le visuel typographique. Format conseillé : 1200 x 630 px, paysage.</p>';
	foreach ( $f as $k => $v ) {
		printf(
			'<p><label style="display:block;font-weight:600;margin-bottom:3px" for="%1$s">%2$s</label><input type="text" class="widefat" id="%1$s" name="%1$s" value="%3$s" placeholder="%4$s"></p>',
			esc_attr( $k ), esc_html( $v[0] ), esc_attr( get_post_meta( $post->ID, '_' . $k, true ) ), esc_attr( $v[1] )
		);
	}
}

add_action( 'save_post', function ( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }
	if ( isset( $_POST['klo_ref_nonce'] ) && wp_verify_nonce( $_POST['klo_ref_nonce'], 'klo_ref' ) ) {
		if ( ! empty( $_POST['klo_featured'] ) ) { update_post_meta( $post_id, '_klo_featured', '1' ); } else { delete_post_meta( $post_id, '_klo_featured' ); }
	}
	if ( isset( $_POST['klo_partner_link_nonce'] ) && wp_verify_nonce( $_POST['klo_partner_link_nonce'], 'klo_partner_link' ) && 'klo_partenaire' === get_post_type( $post_id ) ) {
		$url = isset( $_POST['klo_partner_url'] ) ? esc_url_raw( wp_unslash( $_POST['klo_partner_url'] ) ) : '';
		if ( $url ) { update_post_meta( $post_id, '_klo_partner_url', $url ); } else { delete_post_meta( $post_id, '_klo_partner_url' ); }
	}
	if ( isset( $_POST['klo_news_nonce'] ) && wp_verify_nonce( $_POST['klo_news_nonce'], 'klo_news' ) ) {
		foreach ( array( 'klo_status', 'klo_genre', 'klo_dateline', 'klo_art_name', 'klo_art_sub' ) as $k ) {
			if ( isset( $_POST[ $k ] ) ) { update_post_meta( $post_id, '_' . $k, sanitize_text_field( wp_unslash( $_POST[ $k ] ) ) ); }
		}
	}
} );

/* ---------- Rôle Éditeur simplifié ---------- */
add_action( 'after_switch_theme', function () {
	$r = get_role( 'editor' );
	if ( $r ) { $r->add_cap( 'edit_theme_options' ); } // accès à « Textes du site » (Personnaliser)
} );

add_action( 'admin_menu', function () {
	add_menu_page( 'Guide KLONOSPHERE', 'Guide du site', 'edit_posts', 'klo-guide', 'klo_admin_guide_page', 'dashicons-welcome-learn-more', 4 );
	add_menu_page( 'Textes du site', 'Textes du site', 'edit_theme_options', 'customize.php?autofocus[panel]=klo_texts', '', 'dashicons-edit', 5 );
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'edit-comments.php' );
	}
}, 999 );

function klo_admin_guide_page() {
	if ( ! current_user_can( 'edit_posts' ) ) { return; }
	$links = array(
		array( 'Nouvelle actualité', admin_url( 'post-new.php' ), 'Pour annoncer une sortie, un single, un clip, une tournée ou une campagne.' ),
		array( 'Gérer les groupes et artistes', admin_url( 'edit.php?post_type=klo_reference' ), 'Ajouter un nom, corriger son orthographe ou modifier son ordre d’affichage.' ),
		array( 'Gérer les médias et partenaires', admin_url( 'edit.php?post_type=klo_partenaire' ), 'Ajouter un partenaire, son logo, sa catégorie et son site internet.' ),
		array( 'Modifier les textes du site', admin_url( 'customize.php?autofocus[panel]=klo_texts' ), 'Modifier les textes de présentation, les introductions et les coordonnées.' ),
	);
	echo '<div class="wrap"><h1>Bienvenue dans l’administration de KLONOSPHERE</h1><p>Les modifications se font ici, sans toucher au code du site.</p><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;max-width:1100px">';
	foreach ( $links as $link ) {
		printf( '<section style="background:#fff;border:1px solid #dcdcde;padding:20px"><h2 style="margin-top:0;font-size:18px"><a href="%1$s">%2$s →</a></h2><p>%3$s</p></section>', esc_url( $link[1] ), esc_html( $link[0] ), esc_html( $link[2] ) );
	}
	echo '</div><hr style="max-width:1100px;margin:28px 0"><h2>Publier une actualité en 5 étapes</h2><ol style="max-width:850px;line-height:1.8"><li>Ouvrir <strong>Articles → Ajouter un article</strong>.</li><li>Donner un titre clair : groupe + sortie ou actualité.</li><li>Écrire le texte dans la zone principale. Utiliser les blocs WordPress pour les titres, paragraphes et liens.</li><li>Ajouter un résumé dans le champ <strong>Extrait</strong> : 2 à 3 phrases qui apparaîtront sur la page d’accueil.</li><li>Ajouter une photo du groupe ou la pochette dans <strong>Image mise en avant</strong> (paysage, idéalement 1200 × 630 px), puis cliquer sur <strong>Publier</strong>.</li></ol><p><strong>Astuce :</strong> dans « Informations de la carte actualité », le statut et le style sont facultatifs. Laisser les champs vides si vous n’en avez pas besoin.</p><h2>Ajouter un partenaire</h2><p>Ouvrir « Médias / partenaires → Ajouter », saisir son nom, choisir sa catégorie, ajouter son logo en « Image mise en avant » et renseigner son site internet si utile.</p><h2>Modifier les références</h2><p>Dans « Références », ajouter ou modifier un groupe. Le champ « Ordre » dans « Attributs » permet de régler sa position dans la liste : 1 apparaît avant 2, etc.</p></div>';
}


/* ---------- Commentaires désactivés ---------- */
add_action( 'init', function () {
	remove_post_type_support( 'post', 'comments' );
	remove_post_type_support( 'post', 'trackbacks' );
	remove_post_type_support( 'page', 'comments' );
}, 100 );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 10 );
add_action( 'admin_bar_menu', function ( $bar ) { $bar->remove_node( 'comments' ); }, 999 );

/* ---------- Durcissement léger ---------- */
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
add_filter( 'the_generator', '__return_empty_string' );
add_filter( 'login_errors', function () { return 'Identifiants incorrects.'; } );

// Pas de liste des identifiants via l'API ni les pages auteur.
add_filter( 'rest_endpoints', function ( $e ) {
	if ( ! is_user_logged_in() ) {
		unset( $e['/wp/v2/users'], $e['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $e;
} );
add_action( 'template_redirect', function () {
	if ( is_author() ) { wp_safe_redirect( home_url( '/' ), 301 ); exit; }
} );
add_filter( 'wp_sitemaps_add_provider', function ( $provider, $name ) {
	return 'users' === $name ? false : $provider;
}, 10, 2 );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	$screen = get_current_screen();
	if ( $screen && 'klo_partenaire' === $screen->post_type ) { wp_enqueue_media(); }
} );


/* ---------- Migration légère des partenaires vers les catégories v1.1 ---------- */
add_action( 'init', function () {
	if ( get_option( 'klo_partner_tax_migrated_v11' ) ) { return; }
	$categories = array( 'Presse & médias', 'Graphisme', 'Pressage disque', 'Promotion internationale', 'Distribution', 'Textile', 'Vinyle', 'Vidéo & clip', 'Autres partenaires' );
	$term_ids = array();
	foreach ( $categories as $category ) {
		$term = term_exists( $category, 'klo_type_partenaire' );
		if ( ! $term ) { $term = wp_insert_term( $category, 'klo_type_partenaire' ); }
		$term_ids[ $category ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
	}
	if ( ! empty( $term_ids['Presse & médias'] ) ) {
		$partners = get_posts( array( 'post_type' => 'klo_partenaire', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids' ) );
		foreach ( $partners as $partner_id ) {
			$current = wp_get_object_terms( $partner_id, 'klo_type_partenaire', array( 'fields' => 'ids' ) );
			if ( empty( $current ) || is_wp_error( $current ) ) {
				wp_set_object_terms( $partner_id, array( $term_ids['Presse & médias'] ), 'klo_type_partenaire' );
			}
		}
	}
	update_option( 'klo_partner_tax_migrated_v11', 1 );
}, 99 );


/* ---------- Ordre éditorial des références ---------- */
add_action( 'init', function () {
	if ( get_option( 'klo_reference_order_migrated_v12' ) ) { return; }
	$ids = get_posts( array( 'post_type' => 'klo_reference', 'post_status' => 'any', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'ASC', 'fields' => 'ids' ) );
	foreach ( $ids as $index => $id ) {
		wp_update_post( array( 'ID' => $id, 'menu_order' => $index + 1 ) );
	}
	update_option( 'klo_reference_order_migrated_v12', 1 );
}, 100 );
