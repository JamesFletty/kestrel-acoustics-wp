<?php
/**
 * Theme setup.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'kestrel_setup' );

/**
 * Register theme features.
 */
function kestrel_setup(): void {
	load_theme_textdomain( 'kestrel', KESTREL_DIR . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'dark-editor-style' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	remove_theme_support( 'core-block-patterns' );
	add_editor_style( array( 'assets/css/base.css' ) );

	register_nav_menus(
		array(
			'primary' => __( 'Primary navigation', 'kestrel' ),
			'footer'  => __( 'Footer navigation', 'kestrel' ),
		)
	);

	add_image_size( 'ka-product', 1200, 900, true );
	add_image_size( 'ka-hero', 2400, 1200, true );
}

add_filter( 'excerpt_length', static fn(): int => 24, 20 );

add_action( 'wp_head', 'kestrel_head_meta', 2 );

/**
 * Meta description + favicon (yields to SEO plugins / Site Icon).
 */
function kestrel_head_meta(): void {
	if ( ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) ) {
		$description = is_singular() && has_excerpt() ? get_the_excerpt() : get_bloginfo( 'description' );
		if ( $description ) {
			printf( "<meta name=\"description\" content=\"%s\">\n", esc_attr( wp_strip_all_tags( $description ) ) );
		}
	}
	if ( ! ( function_exists( 'has_site_icon' ) && has_site_icon() ) ) {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><rect width="32" height="32" rx="4" fill="#0D0E10"/><circle cx="16" cy="16" r="10" fill="none" stroke="#E2E4E8" stroke-width="2"/><circle cx="16" cy="16" r="4" fill="#FF4D1A"/></svg>';
		printf( "<link rel=\"icon\" href=\"data:image/svg+xml,%s\">\n", rawurlencode( $svg ) );
	}
}
