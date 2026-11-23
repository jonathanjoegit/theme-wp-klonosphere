</main>
<?php $b = klo_base(); $img = get_template_directory_uri() . '/assets/img/'; $ml = klo_legal_page( 'mentions-legales' ); $pc = klo_legal_page( 'confidentialite' ); ?>
<footer class="footer"><div class="container footer-row">
	<div>
		<div class="brand"><img class="logo-img" src="<?php echo esc_url( $img . 'logo-nav.webp' ); ?>" width="820" height="276" loading="lazy" alt="KLONOSPHERE"></div>
		<small>Association loi 1901 · Poitiers, France · Depuis 2001</small>
		<small>© <?php echo esc_html( gmdate( 'Y' ) ); ?> KLONOSPHERE. Tous droits réservés.</small>
	</div>
	<div class="footer-links">
		<a href="<?php echo esc_url( $b . '#histoire' ); ?>">À propos</a>
		<a href="<?php echo esc_url( $b . '#services' ); ?>">Services</a>
		<a href="<?php echo esc_url( $b . '#partenaires' ); ?>">Médias</a>
		<a href="<?php echo esc_url( $b . '#contact' ); ?>">Contact</a>
		<?php if ( $ml ) : ?><a href="<?php echo esc_url( get_permalink( $ml ) ); ?>">Mentions légales</a><?php endif; ?>
		<?php if ( $pc ) : ?><a href="<?php echo esc_url( get_permalink( $pc ) ); ?>">Confidentialité</a><?php endif; ?>
	</div>
</div></footer>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
<?php wp_footer(); ?>
</body>
</html>
