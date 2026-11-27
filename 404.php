<?php
/**
 * 404.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ka-section ka-wrap ka-404" aria-labelledby="ka-404-title">
	<p class="ka-mono ka-section__label"><?php esc_html_e( 'Error 404 / −∞ dB', 'kestrel' ); ?></p>
	<h1 id="ka-404-title" class="ka-hero__title"><?php esc_html_e( 'Signal lost.', 'kestrel' ); ?></h1>
	<p class="ka-section__intro"><?php esc_html_e( 'That page has been unplugged. Try the catalogue instead.', 'kestrel' ); ?></p>
	<p class="ka-hero__actions"><a class="ka-btn ka-btn--signal" href="<?php echo esc_url( get_post_type_archive_link( 'ka_product' ) ); ?>"><?php esc_html_e( 'All products', 'kestrel' ); ?></a><a class="ka-btn ka-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'kestrel' ); ?></a></p>
</section>
<?php
get_footer();
