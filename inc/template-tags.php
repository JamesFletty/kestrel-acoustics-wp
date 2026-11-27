<?php
/**
 * Template helpers: spec rows, pricing, product drawings, response curves.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

/**
 * Non-empty published specs for a product, in schema order.
 *
 * @param int $post_id Post ID.
 * @return array<string, string> label => value
 */
function kestrel_spec_rows( int $post_id ): array {
	$rows = array();
	foreach ( kestrel_spec_fields() as $key => $field ) {
		if ( empty( $field['spec'] ) ) {
			continue;
		}
		$value = trim( (string) get_post_meta( $post_id, $key, true ) );
		if ( '' !== $value ) {
			$rows[ $field['label'] ] = $value;
		}
	}
	return $rows;
}

/**
 * Formatted price, e.g. "$4,200 per pair".
 *
 * @param int $post_id Post ID.
 */
function kestrel_price( int $post_id ): string {
	$price = (string) get_post_meta( $post_id, '_ka_price', true );
	if ( '' === $price ) {
		return '';
	}
	$unit = (string) get_post_meta( $post_id, '_ka_price_unit', true );
	return trim( '$' . number_format_i18n( (float) $price ) . ' ' . $unit );
}

/**
 * First series term.
 *
 * @param int $post_id Post ID.
 */
function kestrel_series( int $post_id ): ?object {
	$terms = get_the_terms( $post_id, 'ka_series' );
	return ( $terms && ! is_wp_error( $terms ) ) ? $terms[0] : null;
}

/**
 * Parse "38 Hz – 22 kHz" into [low, high] in Hz.
 *
 * @param string $text Frequency response text.
 * @return array{0: float, 1: float}
 */
function kestrel_parse_range( string $text ): array {
	preg_match_all( '/([\d.]+)\s*(k?)Hz/i', $text, $m, PREG_SET_ORDER );
	$values = array_map( static fn( $x ) => (float) $x[1] * ( '' !== $x[2] ? 1000 : 1 ), $m );
	return array( $values[0] ?? 40.0, $values[1] ?? 20000.0 );
}

/**
 * Frequency-response plot as SVG, computed from the product's own spec.
 * Log x-axis 10 Hz – 100 kHz, 2nd-order low roll-off, 4th-order high.
 *
 * @param string $range_text Frequency response text.
 * @param string $uid        Unique ID prefix.
 * @param bool   $detailed   Draw grid labels and tolerance band.
 * @param int    $width      Plot width.
 * @param int    $height     Plot height.
 */
function kestrel_response_svg( string $range_text, string $uid, bool $detailed = false, int $width = 640, int $height = 260 ): string {
	[ $low, $high ] = kestrel_parse_range( $range_text );
	$pad_l = $detailed ? 48 : 0;
	$pad_b = $detailed ? 28 : 0;
	$plot_w = $width - $pad_l;
	$plot_h = $height - $pad_b;
	$x     = static fn( float $f ): float => $pad_l + ( log10( $f ) - 1 ) / 4 * $plot_w;
	$y     = static fn( float $db ): float => $plot_h / 2 - $db * ( $plot_h / 48 );

	$points = array();
	for ( $i = 0; $i <= 160; $i++ ) {
		$f  = 10 ** ( 1 + $i / 40 );
		$db = 0.6 * sin( log( $f ) * 3.1 ) * ( $f > $low && $f < $high ? 1 : 0.3 );
		if ( $f < $low ) {
			$db -= 12 * log( $low / $f, 2 );
		} elseif ( $f > $high ) {
			$db -= 24 * log( $f / $high, 2 );
		}
		$points[] = sprintf( '%.1f,%.1f', $x( $f ), $y( max( -22, $db ) ) );
	}

	$grid = '';
	if ( $detailed ) {
		foreach ( array( 20, 50, 100, 200, 500, 1000, 2000, 5000, 10000, 20000 ) as $f ) {
			$label = $f >= 1000 ? ( $f / 1000 ) . 'k' : (string) $f;
			$grid .= sprintf( '<line x1="%1$.1f" y1="0" x2="%1$.1f" y2="%2$d" class="ka-plot__grid"/><text x="%1$.1f" y="%3$d" class="ka-plot__label" text-anchor="middle">%4$s</text>', $x( $f ), $plot_h, $plot_h + 20, $label );
		}
		foreach ( array( 12, 6, 0, -6, -12, -18 ) as $db ) {
			$grid .= sprintf( '<line x1="%1$d" y1="%2$.1f" x2="%3$d" y2="%2$.1f" class="ka-plot__grid%5$s"/><text x="%6$d" y="%4$.1f" class="ka-plot__label" text-anchor="end">%7$s</text>', $pad_l, $y( $db ), $width, $y( $db ) + 4, 0 === $db ? ' ka-plot__grid--zero' : '', $pad_l - 10, ( $db > 0 ? '+' : '' ) . $db );
		}
		$grid .= sprintf( '<rect x="%1$.1f" y="%2$.1f" width="%3$.1f" height="%4$.1f" class="ka-plot__band"/>', $x( $low ), $y( 1.5 ), $x( $high ) - $x( $low ), $y( -1.5 ) - $y( 1.5 ) );
		$grid .= sprintf( '<line x1="%1$.1f" y1="0" x2="%1$.1f" y2="%2$d" class="ka-plot__marker"/><line x1="%3$.1f" y1="0" x2="%3$.1f" y2="%2$d" class="ka-plot__marker"/>', $x( $low ), $plot_h, $x( $high ) );
	} else {
		$grid .= sprintf( '<line x1="0" y1="%1$.1f" x2="%2$d" y2="%1$.1f" class="ka-plot__grid ka-plot__grid--zero"/>', $y( 0 ), $width );
	}

	return sprintf(
		'<svg class="ka-plot%5$s" viewBox="0 0 %1$d %2$d" role="img" aria-label="%3$s" focusable="false">%4$s<polyline class="ka-plot__trace" points="%6$s"/></svg>',
		$width,
		$height,
		/* translators: %s: frequency response */
		esc_attr( sprintf( __( 'Frequency response plot: %s', 'kestrel' ), $range_text ) ),
		$grid,
		$detailed ? ' ka-plot--detailed' : '',
		implode( ' ', $points )
	);
}

/**
 * Front-view technical drawing of a product. Monitors get a cabinet with
 * drivers sized from the "Drivers" spec; analog gear gets a 2U rack panel.
 *
 * @param int    $post_id Post ID.
 * @param string $context Unique context for IDs.
 */
function kestrel_product_art( int $post_id, string $context = 'card' ): string {
	if ( has_post_thumbnail( $post_id ) ) {
		return (string) get_the_post_thumbnail( $post_id, 'ka-product', array( 'loading' => 'lazy', 'decoding' => 'async' ) );
	}

	$series  = kestrel_series( $post_id );
	$drivers = strtolower( (string) get_post_meta( $post_id, '_ka_drivers', true ) );
	$uid     = 'ka-art-' . $post_id . '-' . sanitize_key( $context );
	$label   = sprintf( /* translators: %s product */ __( 'Technical front view of the %s', 'kestrel' ), get_the_title( $post_id ) );

	if ( $series && 'analog' === $series->slug ) {
		$is_eq = str_contains( $drivers, 'band' );
		$knobs = '';
		$count = $is_eq ? 8 : 2;
		for ( $i = 0; $i < $count; $i++ ) {
			$cx     = $is_eq ? 150 + ( $i % 4 ) * 52 : 250 + $i * 66;
			$cy     = $is_eq ? ( $i < 4 ? 128 : 178 ) : 152;
			$r      = $is_eq ? 15 : 24;
			$angle  = deg2rad( -120 + ( ( $i * 47 ) % 240 ) );
			$knobs .= sprintf( '<circle cx="%1$d" cy="%2$d" r="%3$d" fill="#24272D" stroke="#4A5059"/><circle cx="%1$d" cy="%2$d" r="%4$d" fill="#2E3238"/><line x1="%1$d" y1="%2$d" x2="%5$.1f" y2="%6$.1f" stroke="#E2E4E8" stroke-width="2" stroke-linecap="round"/>', $cx, $cy, $r, $r - 5, $cx + sin( $angle ) * ( $r - 4 ), $cy - cos( $angle ) * ( $r - 4 ) );
		}
		$meter = $is_eq
			? '<g><rect x="60" y="118" width="54" height="70" rx="3" fill="#0D0E10" stroke="#3A3F47"/><circle cx="74" cy="134" r="4" fill="#FF4D1A"/><text x="87" y="138" class="ka-art__t">IN</text><circle cx="74" cy="156" r="4" fill="#3A3F47"/><text x="87" y="160" class="ka-art__t">M/S</text><circle cx="74" cy="178" r="4" fill="#3A3F47"/></g>'
			: '<g><rect x="60" y="112" width="140" height="82" rx="4" fill="#E9E1CF"/><path d="M78 176a52 52 0 0 1 104 0" fill="none" stroke="#0D0E10" stroke-width="1.2"/><path d="M160 140a52 52 0 0 1 22 36" fill="none" stroke="#FF4D1A" stroke-width="3"/><line x1="130" y1="182" x2="110" y2="136" stroke="#0D0E10" stroke-width="1.6"/><text x="130" y="190" text-anchor="middle" class="ka-art__t ka-art__t--dark">VU</text></g>';
		$body = '<rect x="20" y="92" width="360" height="120" rx="4" fill="#1A1C20" stroke="#3A3F47"/>'
			. '<circle cx="34" cy="108" r="4" fill="#0D0E10"/><circle cx="34" cy="196" r="4" fill="#0D0E10"/><circle cx="366" cy="108" r="4" fill="#0D0E10"/><circle cx="366" cy="196" r="4" fill="#0D0E10"/>'
			. '<line x1="48" y1="92" x2="48" y2="212" stroke="#2A2E34"/><line x1="352" y1="92" x2="352" y2="212" stroke="#2A2E34"/>'
			. $meter . $knobs
			. '<rect x="336" y="140" width="8" height="22" rx="2" fill="#FF4D1A"/>'
			. '<text x="60" y="106" class="ka-art__t">KESTREL</text>';
	} else {
		preg_match( '/([\d.]+)\s*in/', $drivers, $m );
		$woofer   = (float) ( $m[1] ?? 6 );
		$is_sub   = ! str_contains( $drivers, 'amt' );
		$has_mid  = str_contains( $drivers, 'mid' );
		$cab_h    = $is_sub ? 200 : min( 250, 150 + $woofer * 8 );
		$cab_w    = $is_sub ? 200 : min( 190, 110 + $woofer * 6 );
		$cx       = 200;
		$top      = 150 - $cab_h / 2;
		$wr       = min( $cab_w * 0.42, 22 + $woofer * 5.6 );
		$wy       = $is_sub ? 150 : $top + $cab_h - $wr - 18;
		$rings    = '';
		foreach ( array( 1, 0.86, 0.62, 0.3 ) as $i => $scale ) {
			$rings .= sprintf( '<circle cx="%1$d" cy="%2$.1f" r="%3$.1f" fill="%4$s" stroke="%5$s"%6$s/>', $cx, $wy, $wr * $scale, array( '#101114', '#1C1E23', '#22252A', '#2B2F35' )[ $i ], 0 === $i ? '#FF4D1A' : '#3A3F47', 0 === $i ? ' stroke-width="1.5"' : '' );
		}
		$upper = '';
		if ( ! $is_sub ) {
			$ty     = $top + 30;
			$upper .= sprintf( '<rect x="%1$d" y="%2$.1f" width="36" height="24" rx="3" fill="#0D0E10" stroke="#4A5059"/>', $cx - 18, $ty );
			for ( $p = 0; $p < 7; $p++ ) {
				$upper .= sprintf( '<line x1="%1$d" y1="%2$.1f" x2="%1$d" y2="%3$.1f" stroke="#6B717B"/>', $cx - 13 + $p * 4.3, $ty + 4, $ty + 20 );
			}
			if ( $has_mid ) {
				$upper .= sprintf( '<circle cx="%1$d" cy="%2$.1f" r="26" fill="#1C1E23" stroke="#3A3F47"/><circle cx="%1$d" cy="%2$.1f" r="10" fill="#2B2F35"/>', $cx, $ty + 64 );
			}
		}
		$port = sprintf( '<rect x="%1$d" y="%2$.1f" width="40" height="8" rx="4" fill="#0D0E10"/>', $cx - 20, $top + $cab_h - 14 );
		$body = sprintf( '<rect x="%1$.1f" y="%2$.1f" width="%3$.1f" height="%4$.1f" rx="10" fill="#1A1C20" stroke="#3A3F47"/>', $cx - $cab_w / 2, $top, $cab_w, $cab_h )
			. $upper . $rings . ( $is_sub ? '' : $port )
			. sprintf( '<circle cx="%1$.1f" cy="%2$.1f" r="2.5" fill="#FF4D1A"/>', $cx + $cab_w / 2 - 14, $top + 14 );
	}

	return sprintf(
		'<svg class="ka-art" viewBox="0 0 400 300" role="img" aria-label="%1$s" focusable="false" data-uid="%2$s"><g class="ka-art__ticks" stroke="#2A2E34"><line x1="0" y1="150" x2="14" y2="150"/><line x1="386" y1="150" x2="400" y2="150"/><line x1="200" y1="0" x2="200" y2="10"/><line x1="200" y1="290" x2="200" y2="300"/></g>%3$s</svg>',
		esc_attr( $label ),
		esc_attr( $uid ),
		$body
	);
}

/**
 * Menu fallback until a menu is assigned.
 *
 * @param array $args wp_nav_menu() args.
 */
function kestrel_fallback_menu( array $args = array() ): string {
	$items = array(
		array( __( 'Monitors', 'kestrel' ), home_url( '/#lineup' ) ),
		array( __( 'Analog', 'kestrel' ), home_url( '/#analog' ) ),
		array( __( 'Compare', 'kestrel' ), home_url( '/#compare' ) ),
		array( __( 'The Lab', 'kestrel' ), home_url( '/#lab' ) ),
		array( __( 'All products', 'kestrel' ), get_post_type_archive_link( 'ka_product' ) ),
	);
	$html = sprintf( '<ul class="%s">', esc_attr( $args['menu_class'] ?? 'ka-menu' ) );
	foreach ( $items as [ $label, $url ] ) {
		$html .= sprintf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	$html .= '</ul>';
	if ( $args['echo'] ?? true ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
	}
	return $html;
}
