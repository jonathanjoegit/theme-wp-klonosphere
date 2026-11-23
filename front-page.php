<?php
get_header();
$img = get_template_directory_uri() . '/assets/img/';

$hero_lines = klo_lines( klo_opt( 'hero_title' ) );
$h_title    = '';
foreach ( $hero_lines as $i => $line ) {
	if ( $i > 0 ) { $h_title .= '<br>'; }
	$h_title .= ( 1 === $i ) ? '<span>' . esc_html( $line ) . '</span>' : esc_html( $line );
}
$press    = new WP_Query( array( 'post_type' => 'klo_partenaire', 'posts_per_page' => -1, 'orderby' => 'menu_order date', 'order' => 'ASC', 'no_found_rows' => true ) );
$news     = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 6, 'ignore_sticky_posts' => true ) );
$about_page = get_page_by_path( 'a-propos', OBJECT, 'page' );
?>
<section class="masthead-logo" aria-label="Identité KLONOSPHERE"><div class="container masthead-logo-inner">
	<a class="masthead-logo-link" href="<?php echo esc_url( wp_make_link_relative( home_url( '/' ) ) ); ?>" aria-label="Retour à l’accueil KLONOSPHERE">
		<img src="<?php echo esc_url( $img . 'logo.webp' ); ?>" width="2048" height="689" alt="KLONOSPHERE, logo officiel noir et or, orbites dorées" decoding="async" fetchpriority="high">
	</a>
	<div class="masthead-caption">COLLECTIF INDÉPENDANT   /   PROMOTION   /   STRATÉGIE   /   DÉVELOPPEMENT ARTISTIQUE</div>
</div></section>

<section class="hero" id="accueil">
	<div class="orbital-hero" aria-hidden="true"><div class="orbit-ring"></div><div class="orbit-ring tilt"></div><div class="orbit-ring opposite"></div><div class="orbit-crosshair"></div><div class="orbit-core"></div><div class="orbit-point p1"></div><div class="orbit-point p2"></div><div class="orbit-point p3"></div><div class="orbit-label">KLONOSPHERE / DEPUIS 2001</div></div>
	<div class="container hero-inner">
		<div class="kicker"><?php echo esc_html( klo_opt( 'hero_kicker' ) ); ?></div>
		<h1 class="headline h1"><?php echo $h_title; // phpcs:ignore -- échappé ligne par ligne ?></h1>
		<p class="hero-desc"><?php echo esc_html( klo_opt( 'hero_desc' ) ); ?></p>
		<div style="display:flex;gap:12px;flex-wrap:wrap"><a class="btn" href="#services">Découvrir nos services ↗</a><a class="btn alt" href="#contact">Présenter votre projet ↗</a></div>
		<div class="hero-bottom"><div class="genre-line"><span class="genre-label">Spécialisé dans le</span><ul class="genre-list" aria-label="Styles musicaux"><li>Rock</li><li>Metal</li><li>Post-rock</li><li>Musiques alternatives</li></ul></div></div>
		<div class="hero-proof">DÉVELOPPEMENT ARTISTIQUE<span> / </span>PROMOTION<span> / </span>RÉSEAU</div>
	</div>
</section>

<section class="about-content section" id="histoire" aria-label="À propos de KLONOSPHERE">
	<div class="container">
		<div class="about-copy">
			<?php if ( $about_page ) { echo apply_filters( 'the_content', $about_page->post_content ); } ?>
		</div>
	</div>
</section>

<section class="section" id="services"><div class="container">
	<div class="section-heading"><div><span class="kicker">Notre savoir-faire</span><h2 class="headline">Nos services</h2></div><p><?php echo esc_html( klo_opt( 'services_intro' ) ); ?></p></div>
	<div class="grid services">
		<?php for ( $n = 1; $n <= 6; $n++ ) : ?>
			<div class="service"><span class="service-num"><?php echo esc_html( sprintf( '%02d', $n ) ); ?> /</span><h3><?php echo esc_html( klo_opt( "svc{$n}_t" ) ); ?></h3><p><?php echo esc_html( klo_opt( "svc{$n}_p" ) ); ?></p></div>
		<?php endfor; ?>
	</div>
</div></section>



<section class="section network-section" id="partenaires" aria-labelledby="network-title">
	<div class="container">
		<header class="section-heading"><div><span class="kicker">Notre équipe de partenaires</span><h2 class="headline" id="network-title">Un réseau de compétences.</h2></div><p><?php echo esc_html( klo_opt( 'partners_intro' ) ); ?></p></header>
		<div class="network-grid"><?php foreach ( $press->posts as $p ) : $terms = get_the_terms( $p->ID, 'klo_type_partenaire' ); $term_label = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'Partenaire'; $partner_url = get_post_meta( $p->ID, '_klo_partner_url', true ); ?>
			<article class="network-card">
                <?php $logo_id = get_post_thumbnail_id( $p->ID ); ?>
                <?php if ( $partner_url ) : ?><a class="network-card-link" href="<?php echo esc_url( $partner_url ); ?>" target="_blank" rel="noopener noreferrer"><?php endif; ?>
                    <?php if ( $logo_id ) : ?>
                        <span class="network-card-logo"><?php echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'network-logo', 'alt' => $p->post_title, 'loading' => 'lazy' ) ); ?></span>
                    <?php endif; ?>
                    <span class="network-card-main"><?php echo esc_html( $p->post_title ); ?><?php if ( $partner_url ) : ?> <span aria-hidden="true">↗</span><?php endif; ?></span>
                <?php if ( $partner_url ) : ?></a><?php endif; ?>
                <p><?php echo esc_html( $term_label ); ?></p>
            </article>
		<?php endforeach; ?></div>
	</div>
</section>

<section class="section" id="actualites"><div class="container">
	<div class="section-heading"><div><span class="kicker">Vie des projets</span><h2 class="headline">Actualités</h2></div><p><?php echo esc_html( klo_opt( 'news_intro' ) ); ?></p></div>
	<div class="grid news news-featured"><?php foreach ( $news->posts as $i => $p ) { klo_news_card( $p, $i ); } ?></div>
	<?php
	$fb = klo_opt( 'facebook' ); $ig = klo_opt( 'instagram' );
	$all_news = (int) wp_count_posts( 'post' )->publish;
	$posts_page = (int) get_option( 'page_for_posts' );
	?>
	<div class="news-follow">
		<?php if ( $all_news > 6 && $posts_page ) : ?><a class="btn" href="<?php echo esc_url( get_permalink( $posts_page ) ); ?>">Toutes les actualités ↗</a><?php endif; ?>
		<?php if ( $fb || $ig ) : ?>
			<p>Suivez-nous sur les réseaux :</p>
			<div class="news-social-links">
				<?php if ( $fb ) : ?><a href="<?php echo esc_url( $fb ); ?>" rel="noopener noreferrer" target="_blank">Facebook ↗</a><?php endif; ?>
				<?php if ( $ig ) : ?><a href="<?php echo esc_url( $ig ); ?>" rel="noopener noreferrer" target="_blank">Instagram ↗</a><?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</div></section>

<section class="section" id="contact"><div class="container contact-grid">
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
