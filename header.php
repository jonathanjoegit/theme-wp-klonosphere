<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php $b = klo_base(); $img = get_template_directory_uri() . '/assets/img/'; ?>
<a class="visually-hidden" href="#main">Aller au contenu</a>
<header class="nav">
<div class="container navinner">
	<?php $spotify_url = klo_opt( 'spotify' ); if ( ! $spotify_url ) { $spotify_url = 'https://open.spotify.com/playlist/'; } ?>
	<div class="social-links" aria-label="Réseaux sociaux">
		<?php $socials = array( 'instagram' => 'Instagram', 'facebook' => 'Facebook' ); foreach ( $socials as $key => $label ) : $url = klo_opt( $key ); if ( ! $url ) { continue; } ?>
			<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $label ); ?>">
				<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
					<?php if ( 'instagram' === $key ) : ?>
						<rect x="3" y="3" width="18" height="18" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r=".8" class="social-dot"></circle>
					<?php else : ?>
						<path class="social-fill" d="M13.5 21v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V3.6c-.3 0-1.3-.1-2.5-.1-2.5 0-4.2 1.5-4.2 4.3v2.1H7.3V13h2.8v8h3.4Z"></path>
					<?php endif; ?>
				</svg><span class="screen-reader-text"><?php echo esc_html( $label ); ?></span>
			</a>
		<?php endforeach; ?>
		<a class="spotify-link" href="<?php echo esc_url( $spotify_url ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Playlist KLONOSPHERE sur Spotify" title="Playlist – lien à définir">
			<svg viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10" fill="currentColor" stroke="none"></circle><path d="M6.2 9.2c3.9-1.2 8.7-.8 12 1.2M7 12.2c3.2-1 7.2-.6 10 .9M7.8 15c2.6-.7 5.6-.4 7.9.7" fill="none" stroke="#fff" stroke-width="1.45" stroke-linecap="round"></path></svg>
			<span class="screen-reader-text">Playlist – lien à définir</span>
		</a>
	</div>
	<button class="mobile-toggle" id="burger" type="button" aria-label="Ouvrir le menu" aria-controls="navlinks" aria-expanded="false">MENU <span aria-hidden="true">☰</span></button>
	<nav class="navlinks" id="navlinks" aria-label="Navigation principale">
		<a href="<?php echo esc_url( $b . '#histoire' ); ?>">À propos</a>
		<a href="<?php echo esc_url( $b . '#services' ); ?>">Services</a>
		<a href="<?php echo esc_url( $b . '#actualites' ); ?>">Actualités</a>
		<a href="<?php echo esc_url( $b . '#partenaires' ); ?>">Réseau</a>
		<a href="<?php echo esc_url( $b . '#contact' ); ?>">Contact</a>
	</nav>
</div></header>
<main id="main">
