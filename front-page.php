<?php
get_header();
$img = get_template_directory_uri() . '/assets/img/';

$hero_lines = klo_lines( klo_opt( 'hero_title' ) );
$h_title    = '';
foreach ( $hero_lines as $i => $line ) {
	if ( $i > 0 ) { $h_title .= '<br>'; }
	$h_title .= ( 1 === $i ) ? '<span>' . esc_html( $line ) . '</span>' : esc_html( $line );
}
$hist_lines = array_map( 'esc_html', klo_lines( klo_opt( 'histoire_title' ) ) );

$refs_all = new WP_Query( array( 'post_type' => 'klo_reference', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
$refs_ft  = new WP_Query( array( 'post_type' => 'klo_reference', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true, 'meta_key' => '_klo_featured', 'meta_value' => '1' ) );
$press    = new WP_Query( array( 'post_type' => 'klo_partenaire', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
$news     = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 6, 'ignore_sticky_posts' => true ) );
$ref_count = (int) $refs_all->post_count;
?>
<section class="masthead-logo" aria-label="Identité KLONOSPHERE"><div class="container masthead-logo-inner">
	<img src="<?php echo esc_url( $img . 'logo.webp' ); ?>" width="2048" height="689" alt="KLONOSPHERE, logo officiel noir et or, orbites dorées" decoding="async" fetchpriority="high">
	<div class="masthead-caption">COLLECTIF INDÉPENDANT   /   PROMOTION   /   STRATÉGIE   /   DÉVELOPPEMENT ARTISTIQUE</div>
</div></section>

<section class="hero" id="accueil">
	<div class="orbital-hero" aria-hidden="true"><div class="orbit-ring"></div><div class="orbit-ring tilt"></div><div class="orbit-ring opposite"></div><div class="orbit-crosshair"></div><div class="orbit-core"></div><div class="orbit-point p1"></div><div class="orbit-point p2"></div><div class="orbit-point p3"></div><div class="orbit-label">KLONOSPHERE / DEPUIS 2001</div></div>
	<div class="container hero-inner">
		<div class="kicker"><?php echo esc_html( klo_opt( 'hero_kicker' ) ); ?></div>
		<h1 class="headline h1"><?php echo $h_title; // phpcs:ignore -- échappé ligne par ligne ?></h1>
		<p class="hero-desc"><?php echo esc_html( klo_opt( 'hero_desc' ) ); ?></p>
		<div style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn" href="#services">Découvrir nos services ↗</a><a class="btn alt" href="#contact">Présenter votre projet ↗</a></div>
		<div class="hero-bottom"><span class="kicker"><span class="genre-label">Spécialisé dans le </span><strong>ROCK</strong><span> / </span><strong>METAL</strong><span> / </span><strong>POST-ROCK</strong><span> / les musiques </span><strong>ALTERNATIVES</strong></span></div>
		<div class="hero-proof">DÉVELOPPEMENT ARTISTIQUE<span> / </span>PROMOTION<span> / </span>RÉSEAU</div>
	</div>
</section>

<section class="stripe" id="histoire"><span class="chapter-num" aria-hidden="true">01 / KLONOSPHERE</span><div class="container">
	<div><span class="kicker">Notre ADN</span><h2 class="headline"><?php echo implode( '<br>', $hist_lines ); // phpcs:ignore ?></h2></div>
	<div>
		<p><?php echo esc_html( klo_opt( 'histoire_p1' ) ); ?></p>
		<p><?php echo esc_html( klo_opt( 'histoire_p2' ) ); ?></p>
		<strong><?php echo esc_html( klo_opt( 'histoire_strong' ) ); ?></strong>
	</div>
</div>
</section>

<section class="about-references section" aria-labelledby="references-title">
	<div class="container">
		<header class="section-heading">
			<div><span class="kicker">Ils nous ont fait confiance</span><h2 class="headline" id="references-title">Nos références</h2></div>
			<p><?php echo esc_html( klo_opt( 'refs_intro' ) ); ?></p>
		</header>
		<div class="roster-toolbar"><div><span class="kicker">Groupes et artistes</span> <span class="roster-counter"><?php echo esc_html( $ref_count ); ?> artistes et groupes</span></div>
			<label class="roster-search" for="roster-search">Rechercher <input id="roster-search" placeholder="Nom d'un groupe…" type="search"></label></div>
		<ul class="grid roster all-references" aria-label="Liste des groupes et artistes référencés"><?php foreach ( $refs_all->posts as $r ) : ?><li class="roster-item"><?php echo esc_html( $r->post_title ); ?></li><?php endforeach; ?></ul>
		<p class="note" hidden id="roster-empty" role="status">Aucun groupe ne correspond à cette recherche.</p>
	</div>
</section>

<section class="section" id="services"><span class="chapter-num" aria-hidden="true">02 / KLONOSPHERE</span><div class="container">
	<div class="section-heading"><div><span class="kicker">Notre savoir-faire</span><h2 class="headline">Nos services</h2></div><p><?php echo esc_html( klo_opt( 'services_intro' ) ); ?></p></div>
	<div class="grid services">
		<?php for ( $n = 1; $n <= 6; $n++ ) : ?>
			<div class="service"><span class="service-num"><?php echo esc_html( sprintf( '%02d', $n ) ); ?> /</span><h3><?php echo esc_html( klo_opt( "svc{$n}_t" ) ); ?></h3><p><?php echo esc_html( klo_opt( "svc{$n}_p" ) ); ?></p></div>
		<?php endfor; ?>
	</div>
</div></section>



<section class="stripe" id="partenaires"><span class="chapter-num" aria-hidden="true">04 / KLONOSPHERE</span><div class="container">
	<div><span class="kicker">Partenaires &amp; relais</span><h2 class="headline">Un réseau construit sur le terrain.</h2></div>
	<div>
		<p><?php echo esc_html( klo_opt( 'partners_intro' ) ); ?></p>
		<div class="press-grid"><?php foreach ( $press->posts as $i => $p ) : $terms = get_the_terms( $p->ID, 'klo_type_partenaire' ); $term_label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Partenaire'; ?>
			<div class="press-logo">
				<?php $partner_url = get_post_meta( $p->ID, '_klo_partner_url', true ); if ( $partner_url ) : ?><a href="<?php echo esc_url( $partner_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Site de <?php echo esc_attr( $p->post_title ); ?>"><?php endif; ?>
				<?php if ( has_post_thumbnail( $p->ID ) ) : ?>
					<?php echo get_the_post_thumbnail( $p->ID, 'medium', array( 'alt' => esc_attr( $p->post_title ), 'loading' => 'lazy' ) ); ?>
				<?php else : ?><span><?php echo esc_html( $p->post_title ); ?></span><?php endif; ?>
				<?php if ( $partner_url ) : ?></a><?php endif; ?>
				<small><?php echo esc_html( $term_label ); ?></small>
			</div><?php endforeach; ?></div>
		<p class="press-more">… et beaucoup d’autres.</p>
	</div>
</div></section>

<section class="section" id="actualites"><span class="chapter-num" aria-hidden="true">05 / KLONOSPHERE</span><div class="container">
	<div class="section-heading"><div><span class="kicker">Vie des projets</span><h2 class="headline">Actualités</h2></div><p><?php echo esc_html( klo_opt( 'news_intro' ) ); ?></p></div>
	<div class="grid news news-featured"><?php foreach ( $news->posts as $i => $p ) { klo_news_card( $p, $i ); } ?></div>
	<?php
	$fb = klo_opt( 'facebook' ); $ig = klo_opt( 'instagram' ); $sp = klo_opt( 'spotify' );
	$all_news = (int) wp_count_posts( 'post' )->publish;
	$posts_page = (int) get_option( 'page_for_posts' );
	?>
	<div style="display:flex;gap:13px;align-items:center;flex-wrap:wrap;margin-top:37px">
		<?php if ( $all_news > 6 && $posts_page ) : ?><a class="btn" href="<?php echo esc_url( get_permalink( $posts_page ) ); ?>">Toutes les actualités ↗</a><?php endif; ?>
		<?php if ( $fb ) : ?><a class="btn alt" href="<?php echo esc_url( $fb ); ?>" rel="noopener noreferrer" target="_blank">Facebook ↗</a><?php endif; ?>
		<?php if ( $ig ) : ?><a class="btn alt" href="<?php echo esc_url( $ig ); ?>" rel="noopener noreferrer" target="_blank">Instagram ↗</a><?php endif; ?>
		<?php if ( $sp ) : ?><a class="btn alt" href="<?php echo esc_url( $sp ); ?>" target="_blank" rel="noopener noreferrer">Playlist – lien à définir</a><?php else : ?><span class="btn alt is-disabled">Playlist – lien à définir</span><?php endif; ?>
	</div>
</div></section>

<section class="section" id="contact"><span class="chapter-num" aria-hidden="true">06 / KLONOSPHERE</span><div class="container contact-grid">
	<div>
		<span class="kicker">Un projet à nous présenter ?</span><h2 class="headline">Parlons-en.</h2>
		<p><?php echo wp_kses( klo_opt( 'contact_intro' ), array( 'strong' => array(), 'em' => array(), 'br' => array(), 'a' => array( 'href' => array() ) ) ); ?></p>
		<p><strong>Merci de préparer :</strong></p>
		<ul class="check"><?php foreach ( klo_lines( klo_opt( 'contact_list' ) ) as $li ) : ?><li><?php echo esc_html( $li ); ?></li><?php endforeach; ?></ul>
		<p class="note"><?php echo esc_html( klo_opt( 'contact_note' ) ); ?></p>
	</div>
	<div>
		<form id="klo-contact" method="post" action="#contact" novalidate>
			<div class="input-grid">
				<div class="field"><label for="band">Groupe / projet *</label><input id="band" name="band" placeholder="Nom du projet" required maxlength="120"></div>
				<div class="field"><label for="email">Votre e-mail *</label><input id="email" name="email" type="email" placeholder="vous@exemple.com" required maxlength="150" autocomplete="email"></div>
			</div>
			<div class="input-grid">
				<div class="field"><label for="type">Votre besoin</label><select id="type" name="type"><?php foreach ( klo_contact_types() as $t ) : ?><option><?php echo esc_html( $t ); ?></option><?php endforeach; ?></select></div>
				<div class="field"><label for="date">Date de sortie / échéance</label><input id="date" name="date" type="date"></div>
			</div>
			<div class="field"><label for="links">Liens de musique, EPK et réseaux</label><input id="links" name="links" placeholder="https://..." maxlength="1000"></div>
			<div class="field"><label for="message">Présentation et objectifs *</label><textarea id="message" name="message" required maxlength="5000" placeholder="Parlez-nous de votre projet, du calendrier, de votre budget indicatif et de vos attentes."></textarea></div>
			<div class="klo-hp" aria-hidden="true"><label for="website">Ne pas remplir</label><input id="website" name="website" type="text" tabindex="-1" autocomplete="off"></div>
			<button class="btn" type="submit">Envoyer ma demande ↗</button>
			<?php $pc = klo_legal_page( 'confidentialite' ); ?>
			<p class="form-legal">Vos données servent uniquement à répondre à votre demande<?php if ( $pc ) : ?> (<a href="<?php echo esc_url( get_permalink( $pc ) ); ?>">politique de confidentialité</a>)<?php endif; ?>.</p>
		</form>
	</div>
</div></section>
<?php get_footer(); ?>
