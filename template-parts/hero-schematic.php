<?php
/**
 * Hero schematic: driver front elevation + the flagship's measured-style
 * frequency response, generated from its spec fields.
 *
 * Expects $args['product'] (WP_Post|null).
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

$kestrel_product = $args['product'] ?? null;
$kestrel_freq    = $kestrel_product ? (string) get_post_meta( $kestrel_product->ID, '_ka_frequency', true ) : '40 Hz – 20 kHz ±2 dB';
$kestrel_ticks   = '';
for ( $kestrel_t = 0; $kestrel_t < 72; $kestrel_t++ ) {
	$kestrel_a      = deg2rad( $kestrel_t * 5 );
	$kestrel_len    = 0 === $kestrel_t % 6 ? 12 : 5;
	$kestrel_ticks .= sprintf( '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>', 190 + sin( $kestrel_a ) * 168, 200 - cos( $kestrel_a ) * 168, 190 + sin( $kestrel_a ) * ( 168 - $kestrel_len ), 200 - cos( $kestrel_a ) * ( 168 - $kestrel_len ) );
}
?>
<div class="ka-schematic">
	<div class="ka-schematic__driver">
		<svg viewBox="0 0 380 400" role="img" aria-label="<?php esc_attr_e( 'Front elevation of an 8 inch woven-carbon woofer with a ribbon tweeter above it', 'kestrel' ); ?>" focusable="false">
			<g stroke="#3A3F47" stroke-width="1"><?php echo $kestrel_ticks; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- numeric SVG. ?></g>
			<line x1="10" y1="200" x2="370" y2="200" stroke="#2A2E34" stroke-dasharray="3 5"/>
			<line x1="190" y1="20" x2="190" y2="380" stroke="#2A2E34" stroke-dasharray="3 5"/>
			<circle cx="190" cy="200" r="142" fill="#121317" stroke="#3A3F47"/>
			<circle cx="190" cy="200" r="128" fill="none" stroke="#FF4D1A" stroke-width="2"/>
			<circle cx="190" cy="200" r="112" fill="#17191D" stroke="#3A3F47"/>
			<circle cx="190" cy="200" r="92" fill="none" stroke="#2A2E34" stroke-dasharray="2 4"/>
			<circle cx="190" cy="200" r="70" fill="none" stroke="#2A2E34" stroke-dasharray="2 4"/>
			<circle cx="190" cy="200" r="40" fill="#22252A" stroke="#4A5059"/>
			<circle cx="190" cy="200" r="3" fill="#FF4D1A"/>
			<g fill="#0D0E10" stroke="#3A3F47"><circle cx="190" cy="44" r="6"/><circle cx="190" cy="356" r="6"/><circle cx="34" cy="200" r="6"/><circle cx="346" cy="200" r="6"/></g>
			<g class="ka-schematic__callouts">
				<line x1="280" y1="110" x2="330" y2="60" stroke="#E2E4E8" stroke-width=".8"/><text x="334" y="56" class="ka-schematic__t">Ø 200</text>
				<line x1="222" y1="226" x2="300" y2="320" stroke="#E2E4E8" stroke-width=".8"/><text x="304" y="334" class="ka-schematic__t">VC 38</text>
			</g>
		</svg>
	</div>
	<div class="ka-schematic__plot">
		<div class="ka-schematic__head ka-mono">
			<span><?php echo esc_html( $kestrel_product ? get_the_title( $kestrel_product ) : '' ); ?> · <?php esc_html_e( 'on-axis · 1 m · 2.83 V', 'kestrel' ); ?></span>
			<span class="ka-schematic__readout"><?php echo esc_html( $kestrel_freq ); ?></span>
		</div>
		<?php echo kestrel_response_svg( $kestrel_freq, 'hero', true, 760, 320 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<div class="ka-schematic__axis ka-mono"><span><?php esc_html_e( 'Frequency (Hz)', 'kestrel' ); ?></span><span><?php esc_html_e( 'SPL (dB, normalised)', 'kestrel' ); ?></span></div>
	</div>
</div>
