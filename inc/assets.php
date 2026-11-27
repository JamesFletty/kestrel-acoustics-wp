<?php
/**
 * Assets: filemtime() cache busting, defer strategy, no jQuery.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cache-busting version for a theme-relative file.
 *
 * @param string $relative Relative path.
 */
function kestrel_asset_version( string $relative ): string {
	$path = KESTREL_DIR . '/' . ltrim( $relative, '/' );
	return file_exists( $path ) ? (string) filemtime( $path ) : KESTREL_VERSION;
}

add_action( 'wp_enqueue_scripts', 'kestrel_enqueue_assets' );

/**
 * Enqueue styles and scripts.
 */
function kestrel_enqueue_assets(): void {
	$deps = array();
	foreach ( array( 'base', 'layout', 'components' ) as $name ) {
		$relative = "assets/css/{$name}.css";
		wp_enqueue_style( "kestrel-{$name}", get_theme_file_uri( $relative ), $deps, kestrel_asset_version( $relative ) );
		$deps = array( "kestrel-{$name}" );
	}

	$args = array(
		'strategy'  => 'defer',
		'in_footer' => true,
	);
	wp_enqueue_script( 'kestrel-main', get_theme_file_uri( 'assets/js/main.js' ), array(), kestrel_asset_version( 'assets/js/main.js' ), $args );

	// Tabs are registered; templates that print a spec sheet enqueue them.
	wp_register_script( 'kestrel-spec-tabs', get_theme_file_uri( 'assets/js/spec-tabs.js' ), array(), kestrel_asset_version( 'assets/js/spec-tabs.js' ), $args );

	wp_dequeue_style( 'classic-theme-styles' );
}

add_action( 'wp_head', 'kestrel_preload_fonts', 3 );

/**
 * Preload self-hosted variable fonts (no third-party font CDN).
 */
function kestrel_preload_fonts(): void {
	foreach ( array( 'archivo-wdth.woff2', 'jetbrains-mono-wght.woff2' ) as $font ) {
		printf( "<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url( get_theme_file_uri( 'assets/fonts/' . $font ) ) );
	}
}
