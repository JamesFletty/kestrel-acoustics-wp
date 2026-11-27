<?php
/**
 * Header with wordmark, navigation and mobile drawer.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

$kestrel_menu = array(
	'theme_location' => 'primary',
	'container'      => false,
	'menu_class'     => 'ka-menu',
	'depth'          => 1,
	'fallback_cb'    => 'kestrel_fallback_menu',
);
?>
<header class="ka-header" data-ka-header>
	<div class="ka-wrap ka-header__inner">
		<a class="ka-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<svg class="ka-brand__mark" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="10.5" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="6" fill="none" stroke="currentColor" stroke-width="1.5"/><circle cx="12" cy="12" r="2.4" fill="#FF4D1A"/></svg>
			<span class="ka-brand__name"><?php bloginfo( 'name' ); ?></span>
			<span class="ka-brand__sub"><?php esc_html_e( 'Acoustics', 'kestrel' ); ?></span>
		</a>
		<nav class="ka-nav" aria-label="<?php esc_attr_e( 'Primary', 'kestrel' ); ?>"><?php wp_nav_menu( $kestrel_menu ); ?></nav>
		<a class="ka-btn ka-btn--line ka-header__cta" href="<?php echo esc_url( home_url( '/#listen' ) ); ?>"><?php esc_html_e( 'Book a listening session', 'kestrel' ); ?></a>
		<button class="ka-toggle" type="button" aria-expanded="false" aria-controls="ka-drawer" data-ka-toggle>
			<span class="ka-mono"><?php esc_html_e( 'Menu', 'kestrel' ); ?></span>
		</button>
	</div>
	<div class="ka-drawer" id="ka-drawer" data-ka-drawer hidden>
		<nav aria-label="<?php esc_attr_e( 'Mobile', 'kestrel' ); ?>"><?php wp_nav_menu( array_merge( $kestrel_menu, array( 'menu_class' => 'ka-drawer__menu' ) ) ); ?></nav>
	</div>
</header>
