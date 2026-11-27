<?php
/**
 * Spec sheets + comparison table as accessible tabs (WAI-ARIA APG).
 * Without JS the tablist stays hidden and every sheet is shown in sequence.
 *
 * Expects $args['products'] (WP_Post[]).
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

$kestrel_products = $args['products'] ?? array();
if ( ! $kestrel_products ) {
	return;
}
wp_enqueue_script( 'kestrel-spec-tabs' );
$kestrel_fields = array_filter( kestrel_spec_fields(), static fn( $f ) => ! empty( $f['spec'] ) );
?>
<div class="ka-tabs" data-ka-tabs>
	<div class="ka-tabs__list" role="tablist" aria-label="<?php esc_attr_e( 'Spec sheets', 'kestrel' ); ?>" hidden>
		<?php foreach ( $kestrel_products as $kestrel_i => $kestrel_p ) : ?>
			<button type="button" role="tab" class="ka-tab" id="ka-tab-<?php echo esc_attr( (string) $kestrel_p->ID ); ?>" aria-controls="ka-panel-<?php echo esc_attr( (string) $kestrel_p->ID ); ?>" aria-selected="<?php echo 0 === $kestrel_i ? 'true' : 'false'; ?>" tabindex="<?php echo 0 === $kestrel_i ? '0' : '-1'; ?>"><?php echo esc_html( get_the_title( $kestrel_p ) ); ?></button>
		<?php endforeach; ?>
		<button type="button" role="tab" class="ka-tab ka-tab--compare" id="ka-tab-compare" aria-controls="ka-panel-compare" aria-selected="false" tabindex="-1"><?php esc_html_e( 'Compare all', 'kestrel' ); ?></button>
	</div>

	<?php foreach ( $kestrel_products as $kestrel_p ) : ?>
		<?php $kestrel_freq = (string) get_post_meta( $kestrel_p->ID, '_ka_frequency', true ); ?>
		<section class="ka-panel" role="tabpanel" id="ka-panel-<?php echo esc_attr( (string) $kestrel_p->ID ); ?>" aria-labelledby="ka-tab-<?php echo esc_attr( (string) $kestrel_p->ID ); ?>" tabindex="0">
			<div class="ka-sheet">
				<div class="ka-sheet__intro">
					<p class="ka-mono ka-sheet__series"><?php echo esc_html( ( kestrel_series( $kestrel_p->ID )->name ?? '' ) . ' / ' . __( 'Spec sheet', 'kestrel' ) ); ?></p>
					<h3 class="ka-sheet__title"><?php echo esc_html( get_the_title( $kestrel_p ) ); ?></h3>
					<p class="ka-sheet__excerpt"><?php echo esc_html( get_the_excerpt( $kestrel_p ) ); ?></p>
					<p class="ka-sheet__price"><?php echo esc_html( kestrel_price( $kestrel_p->ID ) ); ?></p>
					<a class="ka-link" href="<?php echo esc_url( get_permalink( $kestrel_p ) ); ?>"><?php esc_html_e( 'Full product page', 'kestrel' ); ?></a>
				</div>
				<div class="ka-sheet__plot">
					<?php echo kestrel_response_svg( $kestrel_freq, 'sheet-' . $kestrel_p->ID, true, 560, 240 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p class="ka-mono ka-sheet__caption"><?php esc_html_e( 'On-axis, 1 m, 2.83 V. Shaded band = ±1.5 dB.', 'kestrel' ); ?></p>
				</div>
				<dl class="ka-sheet__specs">
					<?php foreach ( kestrel_spec_rows( $kestrel_p->ID ) as $kestrel_label => $kestrel_value ) : ?>
						<div><dt><?php echo esc_html( $kestrel_label ); ?></dt><dd><?php echo esc_html( $kestrel_value ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>
	<?php endforeach; ?>

	<section class="ka-panel" role="tabpanel" id="ka-panel-compare" aria-labelledby="ka-tab-compare" tabindex="0">
		<div class="ka-compare">
			<table>
				<caption class="screen-reader-text"><?php esc_html_e( 'Specification comparison of all products', 'kestrel' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Spec', 'kestrel' ); ?></th>
						<?php foreach ( $kestrel_products as $kestrel_p ) : ?>
							<th scope="col"><a href="<?php echo esc_url( get_permalink( $kestrel_p ) ); ?>"><?php echo esc_html( get_the_title( $kestrel_p ) ); ?></a></th>
						<?php endforeach; ?>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $kestrel_fields as $kestrel_key => $kestrel_field ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $kestrel_field['label'] ); ?></th>
							<?php foreach ( $kestrel_products as $kestrel_p ) : ?>
								<td><?php echo esc_html( (string) get_post_meta( $kestrel_p->ID, $kestrel_key, true ) ?: '—' ); ?></td>
							<?php endforeach; ?>
						</tr>
					<?php endforeach; ?>
					<tr class="ka-compare__price">
						<th scope="row"><?php esc_html_e( 'Price', 'kestrel' ); ?></th>
						<?php foreach ( $kestrel_products as $kestrel_p ) : ?>
							<td><?php echo esc_html( kestrel_price( $kestrel_p->ID ) ); ?></td>
						<?php endforeach; ?>
					</tr>
				</tbody>
			</table>
		</div>
	</section>
</div>
