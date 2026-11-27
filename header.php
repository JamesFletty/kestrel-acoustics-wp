<?php
/**
 * Document head and site header.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#0D0E10">
	<meta name="color-scheme" content="dark">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ka-skip" href="#main"><?php esc_html_e( 'Skip to content', 'kestrel' ); ?></a>
<?php get_template_part( 'template-parts/site-header' ); ?>
<main id="main" class="ka-main" tabindex="-1">
