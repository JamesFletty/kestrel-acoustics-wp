<?php
/**
 * Single product.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$kestrel_id   = get_the_ID();
	$kestrel_freq = (string) get_post_meta( $kestrel_id, '_ka_frequency', true );
	?>
	<article <?php post_class( 'ka-product' ); ?>>
		<div class="ka-wrap">
			<nav class="ka-crumbs ka-mono" aria-label="<?php esc_attr_e( 'Breadcrumb', 'kestrel' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'ka_product' ) ); ?>"><?php esc_html_e( 'Products', 'kestrel' ); ?></a>
				<span aria-hidden="true">/</span><span><?php echo esc_html( kestrel_series( $kestrel_id )->name ?? '' ); ?></span>
			</nav>
			<div class="ka-product__hero">
				<div class="ka-product__copy">
					<h1 class="ka-product__title"><?php the_title(); ?></h1>
					<p class="ka-product__drivers ka-mono"><?php echo esc_html( (string) get_post_meta( $kestrel_id, '_ka_drivers', true ) ); ?></p>
					<?php if ( has_excerpt() ) : ?>
						<p class="ka-product__lead"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
					<p class="ka-product__price"><?php echo esc_html( kestrel_price( $kestrel_id ) ); ?></p>
					<a class="ka-btn ka-btn--signal" href="<?php echo esc_url( home_url( '/#listen' ) ); ?>"><?php esc_html_e( 'Hear it in the listening room', 'kestrel' ); ?></a>
				</div>
				<figure class="ka-product__art"><?php echo kestrel_product_art( $kestrel_id, 'hero' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></figure>
			</div>
			<?php echo kestrel_render_spec_strip( array( 'productId' => $kestrel_id, 'showName' => false ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<div class="ka-product__body">
				<div class="ka-prose"><?php the_content(); ?></div>
				<div class="ka-product__plot">
					<?php echo kestrel_response_svg( $kestrel_freq, 'single', true, 640, 280 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p class="ka-mono ka-sheet__caption"><?php echo esc_html( $kestrel_freq ); ?></p>
				</div>
			</div>
		</div>
	</article>
	<?php
endwhile;

get_footer();
