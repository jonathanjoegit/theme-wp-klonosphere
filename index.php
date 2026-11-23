<?php get_header(); ?>
<div class="container page-wrap">
	<span class="kicker">Vie des projets</span>
	<h1 class="headline">Actualités</h1>
	<div class="post-list">
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
		<article>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<?php if ( has_post_thumbnail() ) : ?><a class="post-list-thumbnail" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?></a><?php endif; ?>
			<h2><a href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></h2>
			<p><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></p>
			<a class="linkline" href="<?php echo esc_url( get_permalink() ); ?>">Lire la suite ↗</a>
		</article>
	<?php endwhile; the_posts_pagination( array( 'prev_text' => '←', 'next_text' => '→' ) ); else : ?>
		<p>Aucune actualité pour le moment.</p>
	<?php endif; ?>
	</div>
</div>
<?php get_footer(); ?>
