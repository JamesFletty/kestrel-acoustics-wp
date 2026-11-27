<?php
/**
 * Products archive: full catalogue grouped by series.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ka-section ka-wrap ka-archive" aria-labelledby="ka-archive-title">
	<header class="ka-section__head">
		<p class="ka-mono ka-section__label"><?php esc_html_e( 'Catalogue', 'kestrel' ); ?></p>
		<h1 id="ka-archive-title" class="ka-section__title ka-archive__title"><?php esc_html_e( 'All products', 'kestrel' ); ?></h1>
	</header>
	<?php if ( have_posts() ) : ?>
		<ul class="ka-grid" role="list">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/product-card' );
			endwhile;
			?>
		</ul>
	<?php else : ?>
		<p><?php esc_html_e( 'The catalogue is being measured. Check back soon.', 'kestrel' ); ?></p>
	<?php endif; ?>
</section>
<?php
get_footer();
