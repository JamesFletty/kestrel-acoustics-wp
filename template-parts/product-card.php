<?php
/**
 * Product card (inside a loop).
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

$kestrel_id   = get_the_ID();
$kestrel_rows = kestrel_spec_rows( $kestrel_id );
?>
<li class="ka-card" data-ka-reveal>
	<a class="ka-card__link" href="<?php the_permalink(); ?>">
		<figure class="ka-card__art"><?php echo kestrel_product_art( $kestrel_id, 'card' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in helper. ?></figure>
		<div class="ka-card__body">
			<h3 class="ka-card__title"><?php the_title(); ?></h3>
			<p class="ka-card__drivers ka-mono"><?php echo esc_html( (string) get_post_meta( $kestrel_id, '_ka_drivers', true ) ); ?></p>
			<dl class="ka-card__specs">
				<?php foreach ( array_slice( $kestrel_rows, 0, 2, true ) as $kestrel_label => $kestrel_value ) : ?>
					<div><dt><?php echo esc_html( $kestrel_label ); ?></dt><dd><?php echo esc_html( $kestrel_value ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
			<p class="ka-card__price"><?php echo esc_html( kestrel_price( $kestrel_id ) ); ?><span aria-hidden="true">→</span></p>
		</div>
	</a>
</li>
