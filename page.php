<?php get_header(); while ( have_posts() ) : the_post(); ?>
<div class="container page-wrap">
	<article class="entry">
		<h1 class="headline"><?php the_title(); ?></h1>
		<?php the_content(); ?>
	</article>
</div>
<?php endwhile; get_footer(); ?>
