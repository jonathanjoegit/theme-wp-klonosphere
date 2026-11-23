<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/** Préfixe des liens d'ancre : vide sur l'accueil, sinon l'URL de l'accueil. */
function klo_base() {
	return is_front_page() ? '' : home_url( '/' );
}

/** Page légale publiée (par son slug), sinon null. */
function klo_legal_page( $slug ) {
	$p = get_page_by_path( $slug );
	return ( $p && 'publish' === $p->post_status ) ? $p : null;
}

/** Carte d'actualité (même rendu que la maquette). */
function klo_news_card( $post, $i = 0 ) {
	$id     = $post->ID;
	$status = get_post_meta( $id, '_klo_status', true );
	$genre  = get_post_meta( $id, '_klo_genre', true );
	$line   = get_post_meta( $id, '_klo_dateline', true );
	$name   = get_post_meta( $id, '_klo_art_name', true );
	$sub    = get_post_meta( $id, '_klo_art_sub', true );
	if ( '' === $line ) { $line = get_the_date( 'j F Y', $id ); }
	if ( '' === $name ) { $name = mb_strtoupper( get_the_title( $id ) ); }
	$art_class = 'news-art-placeholder';
	?>
	<article class="news-card" id="actualite-<?php echo esc_attr( $post->post_name ); ?>">
		<div class="news-top"><span class="news-status"><?php echo esc_html( $status ? $status : 'Actualité' ); ?></span><span><?php echo esc_html( $genre ); ?></span></div>
		<?php if ( has_post_thumbnail( $id ) ) : ?>
			<div class="news-art"><?php echo get_the_post_thumbnail( $id, 'large', array( 'alt' => esc_attr( get_the_title( $id ) ), 'loading' => 'lazy' ) ); ?></div>
		<?php else : ?>
			<div class="news-art <?php echo esc_attr( $art_class ); ?>" aria-hidden="true"><span><?php echo esc_html( $name ); ?></span><?php if ( $sub ) : ?><small><?php echo esc_html( $sub ); ?></small><?php endif; ?></div>
		<?php endif; ?>
		<time datetime="<?php echo esc_attr( get_the_date( 'c', $id ) ); ?>"><?php echo esc_html( $line ); ?></time>
		<h3><?php echo esc_html( get_the_title( $id ) ); ?></h3>
		<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt( $post ) ) ); ?></p>
		<details class="news-details"><summary>Lire l'article complet <span aria-hidden="true">↗</span></summary>
			<div class="article-content"><?php echo apply_filters( 'the_content', $post->post_content ); // phpcs:ignore ?></div>
		</details>
		<a class="news-more" href="<?php echo esc_url( get_permalink( $id ) ); ?>">Page dédiée ↗</a>
	</article>
	<?php
}
