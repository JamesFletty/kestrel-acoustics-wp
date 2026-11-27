<?php
/**
 * Block: kestrel/spec-strip — a server-rendered strip of headline specs for
 * one product (or the flagship when no product is chosen).
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'kestrel_register_blocks' );

/**
 * Register from block.json with a PHP render callback.
 */
function kestrel_register_blocks(): void {
	register_block_type( KESTREL_DIR . '/blocks/spec-strip', array( 'render_callback' => 'kestrel_render_spec_strip' ) );
}

/**
 * Resolve the product to display: explicit ID, else the flagship, else first.
 *
 * @param int $product_id Requested ID.
 */
function kestrel_resolve_product( int $product_id = 0 ): ?WP_Post {
	if ( $product_id ) {
		$post = get_post( $product_id );
		return ( $post && 'ka_product' === $post->post_type ) ? $post : null;
	}
	$query = new WP_Query(
		array(
			'post_type'      => 'ka_product',
			'posts_per_page' => 1,
			'no_found_rows'  => true,
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- single, cached row.
				array(
					'key'   => '_ka_flagship',
					'value' => '1',
				),
			),
		)
	);
	return $query->posts[0] ?? null;
}

/**
 * Render callback.
 *
 * @param array $attributes Block attributes.
 */
function kestrel_render_spec_strip( array $attributes = array() ): string {
	$atts    = wp_parse_args( $attributes, array( 'productId' => 0, 'showName' => true ) );
	$product = kestrel_resolve_product( absint( $atts['productId'] ) );
	if ( ! $product ) {
		return '';
	}

	$rows = array_slice( kestrel_spec_rows( $product->ID ), 0, 5, true );
	ob_start();
	?>
	<div class="ka-strip">
		<?php if ( $atts['showName'] ) : ?>
			<div class="ka-strip__name">
				<span class="ka-mono"><?php esc_html_e( 'Spec strip', 'kestrel' ); ?></span>
				<a href="<?php echo esc_url( get_permalink( $product ) ); ?>"><?php echo esc_html( get_the_title( $product ) ); ?></a>
			</div>
		<?php endif; ?>
		<dl class="ka-strip__list">
			<?php foreach ( $rows as $label => $value ) : ?>
				<div class="ka-strip__item"><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
	</div>
	<?php
	return (string) ob_get_clean();
}
