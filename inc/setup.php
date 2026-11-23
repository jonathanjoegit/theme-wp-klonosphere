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

/** Retourne une version de cache sans déclencher d'avertissement si un fichier manque. */
function klo_asset_version( $path ) {
    $file = get_template_directory() . $path;
    if ( is_file( $file ) ) {
        return (string) filemtime( $file );
    }
    return (string) wp_get_theme()->get( 'Version' );
}

add_action( 'wp_enqueue_scripts', function () {
    $uri = get_template_directory_uri();
    wp_enqueue_style( 'klonosphere', get_stylesheet_uri(), array(), klo_asset_version( '/style.css' ) );
    wp_enqueue_script( 'klonosphere-app', $uri . '/assets/js/app.js', array(), klo_asset_version( '/assets/js/app.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
    wp_localize_script( 'klonosphere-app', 'KLO', array(
        'ajax'  => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'klo_contact' ),
    ) );
} );

/* ---------- Contenus : médias et partenaires ---------- */
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



/** Crée une page À propos éditable avec l’éditeur de blocs WordPress. */
add_action( 'init', function () {
	if ( get_option( 'klo_about_page_v14_created' ) ) { return; }
	$page = get_page_by_path( 'a-propos', OBJECT, 'page' );
	if ( ! $page ) {
		$content = <<<'KLOABOUT'
<h2>KLONOSPHERE</h2>
<p><em>Un réseau. Une expérience. Une vision indépendante.</em></p>
<p>Né à Poitiers en 2001 autour de groupes tels que KLONE, HACRIDE et TREPALIUM, KLONOSPHERE est un projet associatif loi 1901, construit autour de l’entraide, de la collaboration et du développement de la scène musicale indépendante.</p>
<p>Depuis plus de 25 ans, notre réseau s’est développé en France et à l’international, en multipliant les collaborations avec les artistes et les professionnels de l’industrie musicale.</p>
<p>Organisation de concerts et de festivals, booking, management, promotion, accompagnement de sorties : nos activités ont évolué au fil des années, mais notre philosophie reste la même : défendre les artistes, respecter leur identité et les accompagner dans leur développement sur le long terme.</p>
<h3>Un réseau au service des artistes.</h3>
<p>KLONOSPHERE s’appuie sur un vaste réseau de contacts et de partenaires professionnels. Nous travaillons avec la majorité des médias spécialisés en France, mais également avec de nombreux médias internationaux, des tourneurs, des labels, des distributeurs, des programmateurs et des acteurs de la promotion musicale.</p>
<p>Notre réseau s’étend également aux plateformes de streaming, aux curateurs de playlists éditoriales et indépendantes, ainsi qu’aux créateurs de contenu et influenceurs actifs sur Instagram, YouTube et les réseaux sociaux.</p>
<p>Nous collaborons notamment avec <strong>Season of Mist et Soundworks pour la distribution physique</strong>, ainsi qu’avec <strong>The Orchard (Sony Music) pour la distribution digitale</strong>.</p>
<h3>Une stratégie adaptée à chaque projet.</h3>
<p>Chaque artiste possède son identité, ses ambitions, ses moyens et ses objectifs. Nous privilégions donc une approche personnalisée, loin des stratégies standardisées.</p>
<p>Notre accompagnement peut prendre différentes formes : élaboration de stratégies de promotion et de développement, conseils en communication et publicité digitale, définition et optimisation des budgets, accompagnement des sorties, mise en relation avec des labels, tourneurs, distributeurs et autres partenaires professionnels.</p>
<p>Nous apportons également notre expertise dans la construction des campagnes promotionnelles, le choix des médias et des plateformes à privilégier, ainsi que dans les investissements publicitaires les plus pertinents selon les objectifs et les moyens de chaque projet.</p>
<h3>Notre philosophie.</h3>
<p>Au-delà des outils et des stratégies, nous défendons une certaine vision du développement artistique, fondée sur les relations humaines, l’indépendance, la confiance et le travail collectif.</p>
<p>Notre ambition n’est pas simplement de promouvoir une sortie ou un événement, mais de contribuer à la construction de projets solides et durables.</p>
<p><strong>Une approche sur mesure, un réseau solide et une même conviction depuis 2001 : accompagner les artistes dans leur développement, sans jamais perdre de vue leur identité.</strong></p>
KLOABOUT;
		wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'À propos', 'post_name' => 'a-propos', 'post_content' => $content ) );
	}
	update_option( 'klo_about_page_v14_created', 1 );
}, 25 );

/** Migre les anciens noms de groupes dans le contenu éditable de la page À propos.
 * Les anciens contenus sont conservés en base, mais le type de contenu Références
 * n'est plus enregistré et n'est plus utilisé par le thème.
 */
add_action( 'init', function () {
	if ( get_option( 'klo_about_groups_migrated_v18' ) ) { return; }
	$page = get_page_by_path( 'a-propos', OBJECT, 'page' );
	if ( ! $page ) { return; }

	$legacy_refs = get_posts( array(
		'post_type' => 'klo_reference', 'post_status' => 'publish',
		'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC',
	) );
	$names = wp_list_pluck( $legacy_refs, 'post_title' );
	if ( empty( $names ) ) {
		$names = array( 'KLONE', 'NOSTROMO', 'THE OCEAN', 'CULT OF LUNA', 'MONO', 'PG LOST', 'PSYKUP', 'HYPNO5E', 'KWOON', 'KADINJA', 'PATRON', 'BAD SITUATION', 'DALI', 'NO TERROR IN THE BANG', 'TREPALIUM', 'NIHIL', 'GOROD', 'MAUDITS', 'SEPTARIA', 'PËRL', '7 WEEKS', 'SEEDS OF MARY', 'UNCUT', 'STEP IN FLUID', 'MARCH OF SCYLLA', 'TSAR', 'HAMASAARI', 'WIZARD MUST DIE', 'KILLING SPREE', 'OLYP', 'SVMARINER', 'COULD SEED', 'NORD', 'TRANZAT', 'LIZZARD', 'MEMORIES OF A DEAD MAN', 'PERFECTO', 'HIGH ON WHEELS', 'ADAGIO', 'FANALO', 'ASH TWIN PROJECT', 'DJIIN', 'INNER LANDSCAPE', 'EREI CROSS', 'THE ASCENDING', 'CLEAVER', 'STÖMB', 'LUX INCERTA', 'ANNA SAGE', 'LOS DISSIDENTES DEL SUCIO MOTEL', 'SAPIENS', 'SANGHAM', 'OPPROBRE', 'FLAYED', 'EXANIMIS', 'LEWIS', 'SCARRED', 'BRIEG GUERVENO', 'LAG I RUN', 'ARO ORA', 'MAMA\'S GUN', 'SNA FU', 'DYSFUNCTIONAL BY CHOICE', 'LODZ', 'CANCEL THE APOCALYPSE', 'THE PITBULLS IN THE NURSERY', 'RAB', 'ZEUS !', 'ERLEN MEYER', 'NOEIN' );
	}
	$names = array_values( array_unique( array_filter( array_map( 'sanitize_text_field', $names ) ) ) );
	if ( ! empty( $names ) && false === strpos( $page->post_content, 'klo-about-groups' ) ) {
		$paragraph = '<p class="klo-about-groups">Au fil de son histoire, KLONOSPHERE a notamment collaboré avec ' . esc_html( implode( ', ', $names ) ) . '.</p>';
		$content = rtrim( $page->post_content ) . "\n\n" . $paragraph;
		wp_update_post( array( 'ID' => $page->ID, 'post_content' => $content ) );
	}
	update_option( 'klo_about_groups_migrated_v18', 1 );
}, 35 );

/** Répare le lien de l’actualité KLONE si l’article n’existe pas encore sur le site. */
add_action( 'init', function () {
	if ( get_option( 'klo_klone_article_checked_v14' ) ) { return; }
	$existing = get_page_by_path( 'klone-unchained', OBJECT, 'post' );
	if ( ! $existing ) {
		wp_insert_post( array(
			'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'KLONE — une nouvelle ère avec « Unchained »',
			'post_name' => 'klone-unchained', 'post_excerpt' => 'KLONE ouvre un nouveau chapitre avec son prochain album.',
			'post_content' => '<p>KLONE ouvre un nouveau chapitre. Retrouvez ici les informations et actualités autour de cette sortie.</p>',
		) );
	}
	update_option( 'klo_klone_article_checked_v14', 1 );
	flush_rewrite_rules( false );
}, 40 );

/** Ajoute les partenaires demandés sans toucher aux contenus déjà administrés. */
add_action( 'init', 'klo_update_network_partners', 30 );
function klo_update_network_partners() {
	if ( get_option( 'klo_network_v14_seeded' ) || ! post_type_exists( 'klo_partenaire' ) ) { return; }
	$items = array(
		array( 'Season of Mist / Soundworks', 'Distribution physique', 'https://www.season-of-mist.com/home/' ),
		array( 'The Orchard (Sony Music)', 'Distribution digitale', 'https://www.theorchard.com/' ),
		array( 'Useless Pride', 'Textile & merchandising', 'https://www.uselesspride.com/pages/merch-printing' ),
		array( 'Hurricane', 'Pressage CD', 'https://www.hurricaneentertainment.com/' ),
		array( 'Vinyle Records', 'Pressage vinyle', '' ),
		array( 'Julien — Seeds of Mary', 'Graphisme', '' ),
		array( 'Joe', 'Conception de site web', '' ),
		array( 'Viral Propagande', 'Promotion internationale', '' ),
		array( 'Deviant Lab — Thibault Chaumont', 'Mixage & mastering', 'https://www.deviantlab.com/' ),
		array( 'Real Clip', 'Clips, teasers & vidéos', '' ),
		array( 'Inès', 'Partenaire', '' ),
	);
	foreach ( $items as $i => $item ) {
		$existing = get_page_by_title( $item[0], OBJECT, 'klo_partenaire' );
		if ( $existing ) { continue; }
		$id = wp_insert_post( array( 'post_type' => 'klo_partenaire', 'post_status' => 'publish', 'post_title' => $item[0], 'menu_order' => 100 + $i ) );
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_klo_partner_url', $item[2] );
			$term = term_exists( $item[1], 'klo_type_partenaire' );
			if ( ! $term ) { $term = wp_insert_term( $item[1], 'klo_type_partenaire' ); }
			if ( ! is_wp_error( $term ) && $term ) { $term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term; wp_set_object_terms( $id, array( $term_id ), 'klo_type_partenaire' ); }
		}
	}
	update_option( 'klo_network_v14_seeded', 1 );
}

add_filter( 'enter_title_here', function ( $t, $post ) {
	if ( $post && 'klo_partenaire' === $post->post_type ) {
		return 'Nom (ex : KLONE)';
	}
	return $t;
}, 10, 2 );

/* Métadonnées des actualités et partenaires */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'klo_news_box', 'Informations de la carte actualité', 'klo_news_box', 'post', 'normal', 'high' );
	add_meta_box( 'klo_partner_link_box', 'Lien du partenaire', 'klo_partner_link_box', 'klo_partenaire', 'normal', 'high' );
} );

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
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) { return; }
	if ( ! current_user_can( 'edit_post', $post_id ) ) { return; }

	// Les nonces et champs sont contrôlés comme scalaires pour éviter les entrées tableau.

	if ( 'klo_partenaire' === get_post_type( $post_id )
		&& isset( $_POST['klo_partner_link_nonce'] )
		&& is_string( $_POST['klo_partner_link_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['klo_partner_link_nonce'] ) ), 'klo_partner_link' ) ) {
		$url = isset( $_POST['klo_partner_url'] ) && is_scalar( $_POST['klo_partner_url'] )
			? esc_url_raw( wp_unslash( (string) $_POST['klo_partner_url'] ) )
			: '';
		if ( $url ) { update_post_meta( $post_id, '_klo_partner_url', $url ); } else { delete_post_meta( $post_id, '_klo_partner_url' ); }
	}

	if ( 'post' === get_post_type( $post_id )
		&& isset( $_POST['klo_news_nonce'] )
		&& is_string( $_POST['klo_news_nonce'] )
		&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['klo_news_nonce'] ) ), 'klo_news' ) ) {
		foreach ( array( 'klo_status', 'klo_genre', 'klo_dateline', 'klo_art_name', 'klo_art_sub' ) as $k ) {
			if ( isset( $_POST[ $k ] ) && is_scalar( $_POST[ $k ] ) ) {
				update_post_meta( $post_id, '_' . $k, sanitize_text_field( wp_unslash( (string) $_POST[ $k ] ) ) );
			}
		}
	}
} );

/* ---------- Capacité dédiée aux textes KLONOSPHERE ---------- */
function klo_migrate_editor_capabilities() {
    $editor = get_role( 'editor' );
    if ( $editor ) {
        if ( ! get_option( 'klo_editor_caps_migrated_v15' ) ) {
            // La version précédente accordait ce droit trop large pour modifier quelques textes.
            $editor->remove_cap( 'edit_theme_options' );
        }
        if ( ! $editor->has_cap( 'edit_klo_site_content' ) ) {
            $editor->add_cap( 'edit_klo_site_content' );
        }
    }
    $administrator = get_role( 'administrator' );
    if ( $administrator && ! $administrator->has_cap( 'edit_klo_site_content' ) ) {
        $administrator->add_cap( 'edit_klo_site_content' );
    }
    if ( ! get_option( 'klo_editor_caps_migrated_v15' ) ) {
        update_option( 'klo_editor_caps_migrated_v15', 1 );
    }
}
add_action( 'after_switch_theme', 'klo_migrate_editor_capabilities' );
add_action( 'admin_init', 'klo_migrate_editor_capabilities', 1 );

add_action( 'switch_theme', function () {
    foreach ( array( 'editor', 'administrator' ) as $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->remove_cap( 'edit_klo_site_content' );
        }
    }
} );

// Autorise l'ouverture du Customizer aux éditeurs uniquement pour les réglages KLONOSPHERE.
add_filter( 'map_meta_cap', function ( $caps, $cap, $user_id, $args ) {
    if ( 'customize' === $cap && user_can( $user_id, 'edit_klo_site_content' ) ) {
        return array( 'edit_klo_site_content' );
    }
    return $caps;
}, 10, 4 );

add_action( 'admin_menu', function () {
	add_menu_page( 'Guide KLONOSPHERE', 'Guide du site', 'edit_posts', 'klo-guide', 'klo_admin_guide_page', 'dashicons-welcome-learn-more', 4 );
	add_menu_page( 'Textes du site', 'Textes du site', 'edit_klo_site_content', 'customize.php?autofocus[panel]=klo_texts', '', 'dashicons-edit', 5 );
	if ( ! current_user_can( 'manage_options' ) ) {
		remove_menu_page( 'themes.php' );
		remove_menu_page( 'tools.php' );
		remove_menu_page( 'edit-comments.php' );
	}
}, 999 );

function klo_admin_guide_page() {
	if ( ! current_user_can( 'edit_posts' ) ) { return; }
	$about_page = get_page_by_path( 'a-propos', OBJECT, 'page' );
	$about_url  = $about_page ? get_edit_post_link( $about_page->ID, 'raw' ) : admin_url( 'edit.php?post_type=page' );
	$links = array(
		array( 'Modifier la page « À propos »', $about_url, 'Mettre à jour la présentation de KLONOSPHERE : textes en gras, titres, listes, liens et images avec l’éditeur WordPress.' ),
		array( 'Nouvelle actualité', admin_url( 'post-new.php' ), 'Publier une actualité, une sortie, un single, un clip, une tournée ou une campagne. Vous pouvez ajouter une image mise en avant.' ),
		array( 'Gérer les médias et partenaires', admin_url( 'edit.php?post_type=klo_partenaire' ), 'Ajouter un partenaire, son logo, sa catégorie et son site internet.' ),
		array( 'Modifier les textes du site', admin_url( 'customize.php?autofocus[panel]=klo_texts' ), 'Modifier les textes de présentation, les introductions et les coordonnées.' ),
	);
	echo '<div class="wrap"><h1>Bienvenue dans l’administration de KLONOSPHERE</h1><p>Les modifications se font ici, sans toucher au code du site.</p><div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;max-width:1100px">';
	foreach ( $links as $link ) {
		printf( '<section style="background:#fff;border:1px solid #dcdcde;padding:20px"><h2 style="margin-top:0;font-size:18px"><a href="%1$s">%2$s →</a></h2><p>%3$s</p></section>', esc_url( $link[1] ), esc_html( $link[0] ), esc_html( $link[2] ) );
	}
	echo '</div><hr style="max-width:1100px;margin:28px 0"><h2>Publier une actualité en 5 étapes</h2><ol style="max-width:850px;line-height:1.8"><li>Ouvrir <strong>Articles → Ajouter un article</strong>.</li><li>Donner un titre clair : groupe + sortie ou actualité.</li><li>Écrire le texte dans la zone principale. Utiliser les blocs WordPress pour les titres, paragraphes et liens.</li><li>Ajouter un résumé dans le champ <strong>Extrait</strong> : 2 à 3 phrases qui apparaîtront sur la page d’accueil.</li><li>Ajouter une photo du groupe ou la pochette dans <strong>Image mise en avant</strong> (paysage, idéalement 1200 × 630 px), puis cliquer sur <strong>Publier</strong>.</li></ol><p><strong>Astuce :</strong> dans « Informations de la carte actualité », le statut et le style sont facultatifs. Laisser les champs vides si vous n’en avez pas besoin.</p><h2>Ajouter un partenaire</h2><p>Ouvrir « Médias / partenaires → Ajouter », saisir son nom, choisir sa catégorie, ajouter son logo en « Image mise en avant » et renseigner son site internet si utile.</p></div>';
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
