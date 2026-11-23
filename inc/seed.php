<?php
/**
 * À l'activation du thème (une seule fois) : crée les références, les médias,
 * les 2 premières actualités, les pages utiles et les réglages de base.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action( 'after_switch_theme', 'klo_seed_content', 20 );

function klo_seed_content() {
	if ( get_option( 'klo_seeded_v1' ) ) { return; }
	if ( function_exists( 'set_time_limit' ) ) { @set_time_limit( 180 ); }

	// Les types de contenus doivent exister pendant l'activation.
	if ( ! post_type_exists( 'klo_reference' ) ) { return; }

	$refs = array( 'KLONE', 'NOSTROMO', 'THE OCEAN', 'CULT OF LUNA', 'MONO', 'PG LOST', 'PSYKUP', 'HYPNO5E', 'KWOON', 'KADINJA', 'PATRON', 'BAD SITUATION', 'DALI', 'NO TERROR IN THE BANG', 'TREPALIUM', 'NIHIL', 'GOROD', 'MAUDITS', 'SEPTARIA', 'PËRL', '7 WEEKS', 'SEEDS OF MARY', 'UNCUT', 'STEP IN FLUID', 'MARCH OF SCYLLA', 'TSAR', 'HAMASAARI', 'WIZARD MUST DIE', 'KILLING SPREE', 'OLYP', 'SVMARINER', 'COULD SEED', 'NORD', 'TRANZAT', 'LIZZARD', 'MEMORIES OF A DEAD MAN', 'PERFECTO', 'HIGH ON WHEELS', 'ADAGIO', 'FANALO', 'ASH TWIN PROJECT', 'DJIIN', 'INNER LANDSCAPE', 'EREI CROSS', 'THE ASCENDING', 'CLEAVER', 'STÖMB', 'LUX INCERTA', 'ANNA SAGE', 'LOS DISSIDENTES DEL SUCIO MOTEL', 'SAPIENS', 'SANGHAM', 'OPPROBRE', 'FLAYED', 'EXANIMIS', 'LEWIS', 'SCARRED', 'BRIEG GUERVENO', 'LAG I RUN', 'ARO ORA', 'MAMA\'S GUN', 'SNA FU', 'DYSFUNCTIONAL BY CHOICE', 'LODZ', 'CANCEL THE APOCALYPSE', 'THE PITBULLS IN THE NURSERY', 'RAB', 'ZEUS !', 'ERLEN MEYER', 'NOEIN' );

	$press = array( 'ROCK N FOLK', 'ROLLING STONE', 'ROCK HARD', 'MY ROCK', 'METALLIAN', 'FIP METAL', 'LONGUEUR D’ONDE', 'GUITAR PART', 'GUITARE XTREME', 'BATTERIE MAG', 'BASSIST MAG', 'RADIO METAL', 'LA GROSSE RADIO', 'METAL ZONE', 'METALLORGIE', 'NEW NOISE', 'VECTEUR MAG', 'HARD FORCE', 'RADIO CAMPUS', 'FERAROCK' );

	$partner_categories = array( 'Presse & médias', 'Graphisme', 'Pressage disque', 'Promotion internationale', 'Distribution', 'Textile', 'Vinyle', 'Vidéo & clip', 'Autres partenaires' );
	$partner_term_ids = array();
	foreach ( $partner_categories as $category_name ) {
		$term = term_exists( $category_name, 'klo_type_partenaire' );
		if ( ! $term ) { $term = wp_insert_term( $category_name, 'klo_type_partenaire' ); }
		$partner_term_ids[ $category_name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
	}
	$press_term_id = isset( $partner_term_ids['Presse & médias'] ) ? $partner_term_ids['Presse & médias'] : 0;
	$base = time() - DAY_IN_SECONDS * 2;
	foreach ( $refs as $i => $name ) {
		$ts = $base + $i;
		$id = wp_insert_post( array(
			'post_type'     => 'klo_reference',
			'post_status'   => 'publish',
			'menu_order'    => $i + 1,
			'post_title'    => $name,
			'post_date'     => gmdate( 'Y-m-d H:i:s', $ts + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $ts ),
		) );
		if ( $id && ! is_wp_error( $id ) && $i < 10 ) { update_post_meta( $id, '_klo_featured', '1' ); }
	}
	foreach ( $press as $i => $name ) {
		$ts = $base + $i;
		$partner_id = wp_insert_post( array(
			'post_type'     => 'klo_partenaire',
			'post_status'   => 'publish',
			'post_title'    => $name,
			'post_date'     => gmdate( 'Y-m-d H:i:s', $ts + (int) ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
			'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $ts ),
		) );
		if ( $partner_id && ! is_wp_error( $partner_id ) && $press_term_id ) {
			wp_set_object_terms( $partner_id, array( $press_term_id ), 'klo_type_partenaire' );
		}
	}

	// Actualités
	$news = array(
		array(
			'title'   => 'KLONE — une nouvelle ère avec « Unchained »',
			'slug'    => 'klone-unchained',
			'date'    => '2026-10-23 09:00:00',
			'excerpt' => 'Le groupe prépare son nouvel album The Moon Is Witness, attendu le 12 mars 2027 chez V2 Records. Premier rendez-vous : « Unchained » le 23 octobre 2026.',
			'content' => "<p>KLONE ouvre un nouveau chapitre avec <em>The Moon Is Witness</em>, un nouvel album produit par Francis Castes. Le groupe retrouvera son public avec une série de titres dévoilés avant la sortie de l'album le 12 mars 2027.</p>\n<p>« Unchained », premier single attendu le 23 octobre, est construit autour d'un riff de guitare puissant et d'un refrain cathartique, entre énergie immédiate et atmosphères caractéristiques du groupe. Un deuxième extrait, « Mirage », est annoncé pour le 27 novembre 2026.</p>\n<p>La campagne accompagnera également les prochaines dates de concert de KLONE.</p>",
			'meta'    => array( '_klo_status' => 'Campagne en cours', '_klo_genre' => 'ROCK / PROGRESSIF', '_klo_dateline' => '23 octobre 2026 · prochain single', '_klo_art_name' => 'KLONE', '_klo_art_sub' => 'THE MOON IS WITNESS' ),
		),
		array(
			'title'   => 'GOROD — un nouvel album à découvrir',
			'slug'    => 'gorod-nouvel-album',
			'date'    => '2026-09-30 09:00:00',
			'excerpt' => "KLONOSPHERE accompagne la promotion du nouvel album de GOROD, disponible depuis la fin septembre 2026, autour des médias spécialisés et de l'actualité du groupe.",
			'content' => "<p>Groupe incontournable de la scène metal technique française, GOROD poursuit son parcours avec une nouvelle sortie à retrouver depuis la fin du mois de septembre 2026.</p>\n<p>L'accompagnement promotionnel s'inscrit dans une démarche de visibilité auprès des médias rock et metal, ainsi que dans le relais des actualités et des concerts du groupe.</p>\n<p>La pochette officielle, les liens d'écoute et les informations détaillées de cette sortie seront ajoutés au dossier dès validation.</p>",
			'meta'    => array( '_klo_status' => 'En promotion', '_klo_genre' => 'METAL / TECHNIQUE', '_klo_dateline' => 'Septembre–octobre 2026 · promotion', '_klo_art_name' => 'GOROD', '_klo_art_sub' => 'ACTUALITÉ DU GROUPE' ),
		),
	);
	foreach ( $news as $n ) {
		$id = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $n['title'],
			'post_name'    => $n['slug'],
			'post_date'    => $n['date'],
			'post_excerpt' => $n['excerpt'],
			'post_content' => $n['content'],
		) );
		if ( $id && ! is_wp_error( $id ) ) {
			foreach ( $n['meta'] as $k => $v ) { update_post_meta( $id, $k, $v ); }
		}
	}

	// Supprime l'article et la page d'exemple de WordPress.
	foreach ( array( 1 => 'post', 2 => 'page' ) as $pid => $pt ) {
		$p = get_post( $pid );
		if ( $p && $pt === $p->post_type && in_array( $p->post_name, array( 'hello-world', 'bonjour-tout-le-monde', 'sample-page', 'page-d-exemple' ), true ) ) {
			wp_delete_post( $pid, true );
		}
	}

	// Pages : accueil, actualités (liste complète), pages légales (brouillons à compléter).
	$home = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Accueil', 'post_name' => 'accueil' ) );
	$blog = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Actualités', 'post_name' => 'actualites' ) );
	wp_insert_post( array(
		'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Mentions légales', 'post_name' => 'mentions-legales',
		'post_content' => "<h2>Éditeur du site</h2>\n<p>KLONOSPHERE, association loi 1901, Poitiers, France.</p>\n<p>Responsable de la publication : [À COMPLÉTER]<br>Adresse postale : [À COMPLÉTER]<br>Contact : [À COMPLÉTER]<br>N° RNA / SIRET : [À COMPLÉTER]</p>\n<h2>Hébergeur</h2>\n<p>[À COMPLÉTER : nom, adresse et téléphone de l'hébergeur]</p>\n<h2>Propriété intellectuelle</h2>\n<p>Les textes, visuels et logos présents sur ce site sont la propriété de leurs auteurs. Toute reproduction sans autorisation est interdite.</p>",
	) );
	wp_insert_post( array(
		'post_type' => 'page', 'post_status' => 'draft', 'post_title' => 'Politique de confidentialité', 'post_name' => 'confidentialite',
		'post_content' => "<h2>Données collectées</h2>\n<p>Le formulaire de contact collecte le nom du projet, l'adresse e-mail, le besoin, une date, des liens et le message. Ces données servent uniquement à répondre à votre demande. Elles sont transmises par e-mail à KLONOSPHERE et ne sont pas enregistrées sur le site.</p>\n<h2>Durée de conservation</h2>\n<p>[À COMPLÉTER : durée de conservation des e-mails reçus]</p>\n<h2>Vos droits</h2>\n<p>Vous pouvez demander l'accès, la rectification ou la suppression de vos données en écrivant à : [À COMPLÉTER : adresse e-mail].</p>\n<h2>Cookies</h2>\n<p>[À COMPLÉTER : indiquer si le site dépose des cookies de mesure d'audience ou autres]</p>",
	) );

	update_option( 'show_on_front', 'page' );
	if ( $home && ! is_wp_error( $home ) ) { update_option( 'page_on_front', $home ); }
	if ( $blog && ! is_wp_error( $blog ) ) { update_option( 'page_for_posts', $blog ); }
	update_option( 'blogname', 'KLONOSPHERE' );
	update_option( 'blogdescription', 'Collectif artistique indépendant depuis 2001' );
	update_option( 'default_comment_status', 'closed' );
	update_option( 'default_ping_status', 'closed' );
	update_option( 'timezone_string', 'Europe/Paris' );
	update_option( 'date_format', 'j F Y' );
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules();

	update_option( 'klo_seeded_v1', 1 );
}
