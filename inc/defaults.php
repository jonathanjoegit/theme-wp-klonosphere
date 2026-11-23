<?php
/**
 * Textes par défaut du site (repris de la maquette).
 * Modifiables ensuite dans Apparence > Personnaliser > Textes du site.
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function klo_defaults() {
	return array(
		'hero_kicker'   => 'Collectif artistique indépendant depuis 2001',
		'hero_title'    => "FAIRE GRANDIR\nVOS PROJETS.\nFAIRE ENTENDRE\nVOTRE MUSIQUE.",
		'hero_desc'     => "Promotion, relations presse, développement artistique, stratégie digitale et conseils. Nous accompagnons les artistes et leurs projets avec exigence, proximité et une connaissance concrète du terrain.",

		'histoire_title'  => "Un collectif.\nUne histoire.\nUn réseau.",
		'histoire_p1'     => "Né à Poitiers en 2001 autour de groupes tels que KLONE, HACRIDE et TREPALIUM, KLONOSPHERE est un projet associatif loi 1901, construit autour de l'entraide et du développement de la scène indépendante.",
		'histoire_p2'     => "Organisation de concerts et de festivals, booking, management, promotion, accompagnement de sorties et collaborations avec des partenaires professionnels : nos activités ont évolué, mais notre approche reste la même.",
		'histoire_strong' => "Une stratégie adaptée à chaque projet, de la rigueur dans notre travail et nos conseils afin de vous développer sur le long terme.",

		'services_intro' => "Interventions à la carte ou accompagnement global. On privilégie les décisions utiles, le bon calendrier et les outils adaptés.",
		'svc1_t' => 'Relations presse & promotion',
		'svc1_p' => 'Communiqués, dossiers de presse, ciblage médias, diffusion, suivi des chroniques et interviews, bilans de campagne.',
		'svc2_t' => 'Stratégie & développement',
		'svc2_p' => 'Audit de projet, positionnement, rétroplanning, plan de sortie, conseil de carrière et accompagnement personnalisé.',
		'svc3_t' => 'Digital & publicités',
		'svc3_p' => 'Campagnes Meta, YouTube/Google et outils Spotify lorsque le projet y est éligible. Coordination des contenus et analyse.',
		'svc4_t' => 'Réseau & démarchage',
		'svc4_p' => 'Approche de labels, tourneurs, distributeurs, médias, playlists, créateurs de contenus et influenceurs pertinents.',
		'svc5_t' => 'Diffusion & partenaires',
		'svc5_p' => 'Orientation vers des partenaires de distribution physique et numérique, préparation des éléments et coordination des sorties.',
		'svc6_t' => 'Conseils en visio',
		'svc6_p' => 'Échanges ciblés pour répondre aux questions sur vos sorties, budgets, contrats, promotion et priorités de développement.',

		'refs_intro'     => 'Une sélection de groupes et d’artistes avec lesquels KLONOSPHERE a collaboré au fil des années : accompagnement, promotion, collectif et événements.',
		'partners_intro' => 'Notre travail s’appuie sur des relations de longue date avec la presse musicale, les radios et les médias spécialisés. Voici quelques-uns de nos interlocuteurs réguliers, parmi beaucoup d’autres.',
		'news_intro'     => 'Un espace consacré aux groupes actuellement en promotion, avec un article dédié à chaque projet.',

		'contact_intro' => "Plus votre demande arrive tôt, plus nous pouvons préparer une campagne cohérente. <strong>Idéalement : 8 à 12 semaines avant une sortie</strong> pour une campagne complète, à ajuster selon le projet et la disponibilité.",
		'contact_list'  => "Présentation du groupe et liens d'écoute privés/publics\nSortie prévue, dates clés et objectif de la demande\nEPK : bio, photos HD, visuels, vidéo si disponible\nRéférences, réseaux sociaux et chiffres utiles\nTerritoires visés et budget indicatif",
		'contact_note'  => "Les demandes sont étudiées selon l'intérêt artistique, le calendrier et les disponibilités. Aucun résultat presse ou playlist ne peut être garanti.",
		'contact_to'    => 'klonosphere@hotmail.com',
		'contact_from'  => '',

		'facebook'  => 'https://www.facebook.com/KLONOSPHEREPR',
		'instagram' => 'https://www.instagram.com/klonosphere/',
		'spotify'   => 'https://open.spotify.com/playlist/',
		'spotify_label' => 'Playlist – lien à définir',

		'meta_desc' => 'KLONOSPHERE, collectif artistique indépendant depuis 2001. Promotion, relations presse, stratégie, développement de projets rock, metal, post-rock et musiques alternatives.',
	);
}

/** Valeur d'un texte (réglage Personnaliser, sinon défaut). */
function klo_opt( $key ) {
	$d = klo_defaults();
	$v = get_theme_mod( 'klo_' . $key, isset( $d[ $key ] ) ? $d[ $key ] : '' );
	return is_string( $v ) ? $v : '';
}

/** Texte multi-lignes en tableau de lignes non vides. */
function klo_lines( $text ) {
	return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ), 'strlen' ) );
}
