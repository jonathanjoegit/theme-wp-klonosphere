<?php get_header(); while ( have_posts() ) : the_post(); ?>
<div class="container page-wrap">
	<article class="entry">
		<span class="kicker"><?php echo esc_html( get_post_meta( get_the_ID(), '_klo_status', true ) ); ?></span>
		<h1 class="headline"><?php echo esc_html( get_the_title() ); ?></h1>
		<p><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></p>
		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'class' => 'single-featured-image', 'loading' => 'eager' ) ); } ?>
		<?php the_content(); ?>
		<p><a class="linkline" href="<?php echo esc_url( home_url( '/#actualites' ) ); ?>">← Retour aux actualités</a></p>
	</article>
</div>
<?php endwhile; get_footer(); ?>
